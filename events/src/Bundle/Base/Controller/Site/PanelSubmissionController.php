<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\Panel;
use App\Bundle\Base\Entity\PanelEvaluationLog;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Form\PanelsPanelistsType;
use App\Bundle\Base\Form\PanelType;
use App\Bundle\Base\Repository\EditionRepository;
use App\Bundle\Base\Services\PanelEvaluationLog as PanelEvaluationLogService;
use App\Bundle\Base\Traits\AccessControl;
use Cassandra\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
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
 * @Route("panel_submission")
 *
 * Class PanelSubmissionController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class PanelSubmissionController extends AbstractController
{
    use AccessControl;

    /**
     *
     */
    const INIT_STEP = 1;
    /**
     *
     */
    const PREFIX_PANEL_UPLOAD_FILE_NAME = 'panel';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string|string[]
     */
    private $uploadPath = Panel::UPLOAD_PATH;

    /**
     * @var EditionRepository
     */
    private $editionRepository;

    /**
     * @var PanelEvaluationLogService
     */
    private $panelEvaluationLogService;


    /**
     * PanelSubmissionController constructor.
     *
     * @param EditionRepository $editionRepository
     * @param ParameterBagInterface $parameterBag
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        EditionRepository $editionRepository,
        ParameterBagInterface $parameterBag,
        Breadcrumbs $breadcrumbs,
        UrlGeneratorInterface $urlGenerator,
        PanelEvaluationLogService $panelEvaluationLogService
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Events');
        $this->editionRepository = $editionRepository;
        $this->uploadPath = str_replace('#KERNEL#', $parameterBag->get('kernel.project_dir'), $this->uploadPath);
        $this->filesystem = new Filesystem();
        $this->panelEvaluationLogService = $panelEvaluationLogService;
    }

    /**
     * @Route("/{edition}/index", name="panel_submission_index", methods={"GET", "POST"})
     *
     * @param Edition $edition
     * @param Request $request
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
            || ! $edition->getSystemEvaluationConfigs()[0]->getPanelSubmissionAvailable()
            || ! $this->getUser()
        ) {
            return new Response('', 404);
        }

        $panelRepository = $this->getDoctrine()->getRepository(Panel::class);
        if ($panelRepository->getNumberOfPanelsPanelistByEdition($edition->getId(), $this->getUser()->getId()) >= 1) {
            $this->addFlash('error.dashboard', 'PANEL_QUANTITY_EXCEEDED');
            return $this->redirectToRoute('dashboard_user_index');
        }

        $this->get('twig')->addGlobal('pageTitle', 'PANEL_TITLE');
        $panels = new Panel();
        $panels->setEditionId($edition);
        $panels->setCreatedAt(new \DateTime());

        $step = (int)$request->get('step', self::INIT_STEP);

        PanelType::$step = $step;

        if ($step < 2) {
            PanelsPanelistsType::$validationEnabled = false;
        }

        $form = $this->createForm(PanelType::class, $panels);
        $form->handleRequest($request);

        if (! $form->isSubmitted() || empty($form->get('proponentId')->getData())) {
            $userRepository = $this->getDoctrine()->getRepository(User::class);
            $user = $userRepository->find($this->getUser()->getId());
            $form->get('proponentIdFake')->setData($user->getName());
            $form->get('proponentId')->setData($user);
        }

        if ($form->isSubmitted() && $request->isXmlHttpRequest()) {

            // validação da tela 2 (Painelistas)
            $mapUsers = [];
            if ($step > 1) {
                if (count($form->get('panelsPanelists')) > 0) {
                    foreach ($form->get('panelsPanelists') as $key => $panelsPanelist) {
                        /** @var User $panelist */
                        $panelist = $panelsPanelist->get('panelistId')->getData();
                        if (! $panelist) {
                            continue;
                        }

                        // valida se o usuário logado está na lista
                        if ((int)$panelist->getId() === (int)$this->getUser()->getId()) {
                            $form->get('panelsPanelists')->addError(new FormError('O usuário logado não deve estar na lista'));
                        }

                        if (in_array($panelist->getId(), $mapUsers)) {
                            $panelsPanelist->get('panelistId')->addError(new FormError('Já cadastrado'));
                        }

                        $mapUsers[] = $panelist->getId();

                        $panelRepository = $this->getDoctrine()->getRepository(Panel::class);
                        if ($panelRepository->getNumberOfPanelsPanelistByEdition($edition->getId(), $panelist->getId()) >= 1) {
                            $panelsPanelist->get('panelistId')->addError(new FormError(sprintf('O painelista %s atingiu o limite máximo de %d painel submetido.<br />Os participantes poderão compor somente uma proposta de painel.', $panelist->getName(), 1)));
                        }
                    }
                }
            }

            // validação da tela 2 (Proponente)
            if ($step > 2) {
                /** @var User $proponent */
                $proponent = $form->get('proponentId')->getData();
                if ($proponent) {
                    // valida se o usuário logado foi selecionado
                    if ((int)$proponent->getId() !== (int)$this->getUser()->getId()) {
                        $form->get('proponentId')->addError(new FormError('O usuário logado deve ser o proponente'));
                    }

                    if (in_array($proponent->getId(), $mapUsers)) {
                        $form->get('proponentId')->addError(new FormError('Já cadastrado'));
                    }

                    // $mapUsers[] = $proponent->getId();

                    $panelRepository = $this->getDoctrine()->getRepository(Panel::class);
                    if ($panelRepository->getNumberOfPanelsPanelistByEdition($edition->getId(), $proponent->getId()) >= 1) {
                        $form->get('proponentId')->addError(new FormError(sprintf('O proponente %s atingiu o limite máximo de %d painel submetido.<br />Os participantes poderão compor somente uma proposta de painel.', $proponent->getName(), 1)));
                    }
                }
            }

            if ($form->isSubmitted() && $form->isValid()) {
                if ($step > 2) {

                    if (! $this->filesystem->exists($this->uploadPath)) {
                        $this->filesystem->mkdir($this->uploadPath, 0755);
                    }

                    if (
                        $request->files->get('panel') &&
                        isset($request->files->get('panel')['proponentCurriculumPdfPath']) &&
                        ! empty($request->files->get('panel')['proponentCurriculumPdfPath'])
                    ) {
                        $fileProponent = $request->files->get('panel')['proponentCurriculumPdfPath'];
                        $newFilename = self::PREFIX_PANEL_UPLOAD_FILE_NAME . '_' . uniqid() . "_proponent." . $fileProponent->guessExtension();
                        $panels->setProponentCurriculumPdfPath($newFilename);
                        $fileProponent->move($this->uploadPath, $newFilename);
                    }

                    if (
                        $request->files->get('panel') &&
                        isset($request->files->get('panel')['panelsPanelists']) &&
                        ! empty($request->files->get('panel')['panelsPanelists'])
                    ) {

                        if (! $this->filesystem->exists($this->uploadPath)) {
                            $this->filesystem->mkdir($this->uploadPath, 0755);
                        }

                        foreach ($request->files->get('panel')['panelsPanelists'] as $key => $file) {
                            if (isset($file['proponentCurriculumPdfPath'])) {
                                $modifyPanelists = $panels->getPanelsPanelists()[$key];
                                $newFilename = self::PREFIX_PANEL_UPLOAD_FILE_NAME . '_' . uniqid() . "_{$key}_." . $file['proponentCurriculumPdfPath']->guessExtension();
                                $modifyPanelists->setProponentCurriculumPdfPath($newFilename);
                                $file['proponentCurriculumPdfPath']->move($this->uploadPath, $newFilename);
                            }
                        }
                    }

                    $panels->setStatusEvaluation(Panel::PANEL_EVALUATION_STATUS['PANEL_EVALUATION_STATUS_WAITING']);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($panels);
                    $entityManager->flush();

                    $panelists = '';
                    foreach ($panels->getPanelsPanelists() as $userPanelist) {
                        $panelists .= sprintf("%s - %s<br>", $userPanelist->getPanelistId()->getName(), $userPanelist->getPanelistId()->getEmail());
                    }
                    $user = $this->getUser();

                    $email = new Email();

                    $proponent = sprintf("%s - %s<br>", $panels->getProponentId()->getName(), $panels->getProponentId()->getEmail());

                    $subject = $translator->trans('PANEL_SUBMISSION_EMAIL_TITLE', [
                        '%edition%' => $panels->getEditionId()->getNamePortuguese(),
                    ]);

                    $languages = array_flip(Panel::LANGUAGE);

                    $content = [
                        '%edition%' => $panels->getEditionId()->getNamePortuguese(),
                        '%title%' => $panels->getTitle(),
                        '%division%' => $panels->getDivisionId()->getPortuguese(),
                        '%language%' => $languages[$panels->getLanguage()],
                        '%justification%' => $panels->getJustification(),
                        '%suggestion%' => $panels->getSuggestion(),
                        '%proponent%' => $proponent,
                        '%panelists%' => $panelists,
                    ];

                    $email
                        ->from('ANPAD <noreply@anpad.org.br>')
                        ->to($user->getEmail())
                        ->subject($subject)
                        ->text($translator->trans('PANEL_SUBMISSION_EMAIL_BODY_TEXT', $content))
                        ->html($translator->trans('PANEL_SUBMISSION_EMAIL_BODY_HTML', $content));

                    try {
                        $mailer->send($email);
                    } catch (TransportExceptionInterface $e) {

                    }

                    foreach ($panels->getPanelsPanelists() as $userPanelist) {
                        $email = new Email();
                        $email
                            ->from('ANPAD <noreply@anpad.org.br>')
                            ->to($userPanelist->getPanelistId()->getEmail())
                            ->subject($subject)
                            ->text($translator->trans('PANEL_SUBMISSION_EMAIL_BODY_TEXT', $content))
                            ->html($translator->trans('PANEL_SUBMISSION_EMAIL_BODY_HTML', $content));

                        try {
                            $mailer->send($email);
                        } catch (TransportExceptionInterface $e) {

                        }
                    }

                    $this->panelEvaluationLogService->register(
                        $panels,
                        $this->getUser(),
                        $request->getClientIp(),
                        PanelEvaluationLog::ACTION_SUBMISSION,
                        null,
                        true
                    );

                    return new JsonResponse(['saved' => true, 'pass' => true], 200, ['x-step' => $step]);
                } else {
                    return new JsonResponse(['saved' => false, 'pass' => true], 200, ['x-step' => $step + 1]);
                }

            } else {

                $response = new Response($this->renderView('@Base/panel/partials/_index.html.twig', [
                    'form' => $form->createView(),
                    'panels' => $panels,
                    'step' => $step,
                ]));

                $response->setStatusCode(500)
                    ->headers->set('x-step', $step);

                return $response;
            }
        }

        return $this->render('@Base/panel/index.html.twig', [
            'form' => $form->createView(),
            'panels' => $panels,
            'step' => $step,
        ]);
    }

    /**
     * @param Form $copyFrom
     * @param Form $copyTo
     */
    private function copyErrorsRecursively(Form &$copyFrom, Form &$copyTo)
    {
        /** @var $error FormError */
        foreach ($copyFrom->getErrors() as $error) {
            $copyTo->addError($error);
        }

        foreach ($copyFrom->all() as $key => $child) {
            if ($copyTo->has($key)) {
                $childTo = $copyTo->get($key);
                self::copyErrorsRecursively($child, $childTo);
            }
        }
    }

    /**
     * @Route("/{panel}/delete", name="panel_submission_delete", methods={"POST"})
     *
     * @param Panel $panel
     * @param Request $request
     *
     * @return Response
     */
    public function delete(Panel $panel, Request $request): Response
    {
        $this->isOwnerUser($panel->getProponentId());

        $panel->setDeletedAt(new \DateTime());
        $panel->setStatusEvaluation(Panel::PANEL_EVALUATION_STATUS['PANEL_EVALUATION_STATUS_CANCELED']);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($panel);
        $entityManager->flush();

        $this->addFlash('success.dashboard', 'PANEL_DELETE_MSG');

        return $this->redirect('/');
    }

    /**
     * @Route("/{panel}/show", name="panel_show", methods={"GET"})
     *
     * @param Panel $panel
     * @param Request $request
     * @param Breadcrumbs $breadcrumbs
     *
     * @return void
     */
    public function show(Panel $panel, Request $request, Breadcrumbs $breadcrumbs): Response
    {
        if(! $this->isAuthorizedPanelist($panel->getPanelsPanelists())) {
            $this->isOwnerUser($panel->getProponentId());
        }

        $breadcrumbs->addItem('PANEL');

        $this->get('twig')->addGlobal('pageTitle', 'Ver Painel');

        return $this->render('@Base/panel/show/show.html.twig', [
            'data' => $panel,
        ]);
    }
}
