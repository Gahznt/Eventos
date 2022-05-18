<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\Thesis;
use App\Bundle\Base\Form\ThesisType;
use App\Bundle\Base\Traits\AccessControl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 *
 * @Route("thesis")
 *
 * Class PanelSubmissionController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class ThesisSubmissionController extends AbstractController
{
    use AccessControl;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string|string[]
     */
    private $uploadPath = Thesis::UPLOAD_PATH;

    /**
     * ThesisSubmissionController constructor.
     *
     * @param ParameterBagInterface $parameterBag
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        Breadcrumbs $breadcrumbs,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Events');
        $this->uploadPath = str_replace('#KERNEL#', $parameterBag->get('kernel.project_dir'), $this->uploadPath);
        $this->filesystem = new Filesystem();
    }

    /**
     * @Route("/{edition}/submission", name="thesis_submission", methods={"GET", "POST"})
     *
     * @param Edition $edition
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param MailerInterface $mailer
     *
     * @return Response
     */
    public function index(Edition $edition, Request $request, TranslatorInterface $translator, MailerInterface $mailer): Response
    {
        if (
            ! $edition
            || null !== $edition->getDeletedAt()
            || ! $edition->getSystemEvaluationConfigs()
            || ! $edition->getSystemEvaluationConfigs()[0]
            || ! $edition->getSystemEvaluationConfigs()[0]->getThesisSubmissionAvailable()
            || ! $this->getUser()
        ) {
            return new Response('', 404);
        }

        /*$panelRepository = $this->getDoctrine()->getRepository(Panel::class);
        if ($panelRepository->getNumberOfPanelsPanelistByEdition($edition->getId(), $this->getUser()->getId()) >= 1) {
            $this->addFlash('error.dashboard', 'PANEL_QUANTITY_EXCEEDED');
            return $this->redirectToRoute('dashboard_user_index');
        }*/

        $this->get('twig')->addGlobal('pageTitle', 'Submissão de Tese');
        $entity = new Thesis();

        $form = $this->createForm(ThesisType::class, $entity, ['edition' => $edition]);
        $form->handleRequest($request);

        if (! $form->isSubmitted() || ! $form->isValid()) {
            if ($form->isSubmitted() && ! $form->isValid()) {
                $this->addFlash('error', 'Ocorreu um erro ao validar os dados.');
            }

            return $this->render('@Base/thesis/submission.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'entity' => $entity,
                //'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        $uploadPath = $this->uploadPath . $edition->getId();
        if (! $this->filesystem->exists($uploadPath)) {
            $this->filesystem->mkdir($uploadPath, 0755);
        }

        $file = $form->get('thesisFilePath')->getData();
        if ($file) {
            $filename = 'thesis' . '_' . uniqid() . '.' . $file->guessExtension();
            try {
                $file->move($uploadPath, $filename);

                $entity->setThesisFilePath($filename);
            } catch (\Exception $e) {
                $entity->setThesisFilePath('');
            }
        }

        $file = $form->get('agreementFilePath')->getData();
        if ($file) {
            $filename = 'agreement' . '_' . uniqid() . '.' . $file->guessExtension();
            try {
                $file->move($uploadPath, $filename);

                $entity->setAgreementFilePath($filename);
            } catch (\Exception $e) {
                $entity->setAgreementFilePath('');
            }
        }

        $entity->setEdition($edition);
        $entity->setCreatedAt(new \DateTime());
        $entity->setUser($this->getUser());

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entity);
            $entityManager->flush();
            $this->addFlash('success', 'Tese submetida com sucesso!');

            $email = new Email();
            $email
                ->from('ANPAD <noreply@anpad.org.br>')
                ->to($entity->getUser()->getEmail())
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('replyto@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject($translator->trans('THESIS_SUBMISSION_EMAIL_TITLE', [
                    '%edition%' => $entity->getEdition()->getName(),
                ]))
                ->text($translator->trans('THESIS_SUBMISSION_EMAIL_BODY_TEXT', [
                    '%edition%' => $entity->getEdition()->getName(),
                    '%title%' => $entity->getTitle(),
                    '%theme%' => $entity->getUserThemes()->getDetails()->getTitle(),
                    '%authorName%' => $entity->getUser()->getName(),
                    '%authorEmail%' => $entity->getUser()->getEmail(),
                ]))
                ->html($translator->trans('THESIS_SUBMISSION_EMAIL_BODY_HTML', [
                    '%edition%' => $entity->getEdition()->getName(),
                    '%title%' => $entity->getTitle(),
                    '%theme%' => $entity->getUserThemes()->getDetails()->getTitle(),
                    '%authorName%' => $entity->getUser()->getName(),
                    '%authorEmail%' => $entity->getUser()->getEmail(),
                ]));

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {

            }

            return $this->redirectToRoute('thesis_submission', ['edition' => $edition->getId()]);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }

    /**
     * @Route("/{id}/show", name="thesis_submission_show", methods={"GET"})
     *
     * @param Thesis $entity
     * @param Request $request
     *
     * @return Response
     */
    public function show(Thesis $entity, Request $request, Breadcrumbs $breadcrumbs): Response
    {
        if (
            ! $this->getUser()
            || $this->getUser()->getId() !== $entity->getUser()->getId()
        ) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $breadcrumbs->addItem('THESIS');

        $this->get('twig')->addGlobal('pageTitle', 'Ver Tese');


        return $this->render('@Base/thesis/show.html.twig', [
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="thesis_submission_delete", methods={"POST"})
     *
     * @param Thesis $entity
     * @param Request $request
     *
     * @return Response
     */
    public function delete(Thesis $entity, Request $request): Response
    {
        if (
            ! $this->getUser()
            || $this->getUser()->getId() !== $entity->getUser()->getId()
        ) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        if (
            Thesis::EVALUATION_STATUS_WAITING !== $entity->getStatus()
            || ! $entity->getEdition()->getSystemEvaluationConfigs()
            || ! $entity->getEdition()->getSystemEvaluationConfigs()[0]
            || ! $entity->getEdition()->getSystemEvaluationConfigs()[0]->getThesisSubmissionAvailable()
        ) {
            return new Response('', 404);
        }

        $entity->setStatus(Thesis::EVALUATION_STATUS_CANCELED);

        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success.dashboard', 'THESIS_DELETE_MSG');

        return $this->redirect('/');
    }

    /**
     * @Route("/{id}/confirm", name="thesis_confirm_participation", methods={"GET"})
     *
     * @param Thesis $thesis
     * @param Request $request
     *
     * @return Response
     */
    public function confirm(Thesis $thesis, Request $request): Response
    {
        if (
            ! $this->getUser()
            || $this->getUser()->getId() !== $thesis->getUser()->getId()
        ) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        try {
            $conn = $this->getDoctrine()->getManager()->getConnection();

            $sql = ' UPDATE thesis SET confirmed = :confirmed WHERE id = :id ';

            $stmt = $conn->prepare($sql);
            $stmt->execute(['confirmed' => Thesis::CONFIRMED_YES, 'id' => $thesis->getId()]);

            $this->addFlash('success.dashboard', 'THESIS_CONFIRMED_MSG');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error, imposible set confirmed.');
        }

        return $this->redirect('/');
    }
}
