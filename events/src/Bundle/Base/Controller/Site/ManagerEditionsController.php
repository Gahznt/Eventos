<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Certificate;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Form\EditionType;
use App\Bundle\Base\Repository\EditionFileRepository;
use App\Bundle\Base\Repository\EditionRepository;
use App\Bundle\Base\Repository\EventRepository;
use App\Bundle\Base\Repository\SpeakerRepository;
use App\Bundle\Base\Repository\SubsectionRepository;
use App\Bundle\Base\Services\SystemEvaluationConfig as SystemEvaluationConfigService;
use App\Bundle\Base\Services\User as UserService;
use App\Bundle\Base\Traits\AccessControl;
use Doctrine\ORM\Mapping\Entity;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/manager")
 *
 * Class ManagerEditionsController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class ManagerEditionsController extends AbstractController
{
    use AccessControl;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var EditionRepository
     */
    private $editionRepository;

    /**
     * @var SubsectionRepository
     */
    private $subsectionRepository;

    /**
     * @var SpeakerRepository
     */
    private $speakerRepository;

    /**
     * @var EditionFileRepository
     */
    private $fileRepository;

    /**
     * @var Breadcrumbs
     */
    private $breadcrumbsService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SystemEvaluationConfigService
     */
    private $systemEvaluationConfigService;

    /**
     * @var string
     */
    private $certificateLayoutPath = Certificate::LAYOUT_PATH;

    /**
     * ManagerEditionsController constructor.
     *
     * @param EventRepository $eventRepository
     * @param EditionRepository $editionRepository
     * @param SubsectionRepository $subsectionRepository
     * @param SpeakerRepository $speakerRepository
     * @param EditionFileRepository $fileRepository
     * @param ParameterBagInterface $parameterBag
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     * @param UserService $userService
     * @param SystemEvaluationConfigService $systemEvaluationConfig
     */
    public function __construct(
        EventRepository $eventRepository,
        EditionRepository $editionRepository,
        SubsectionRepository $subsectionRepository,
        SpeakerRepository $speakerRepository,
        EditionFileRepository $fileRepository,
        ParameterBagInterface $parameterBag,
        Breadcrumbs $breadcrumbs,
        UrlGeneratorInterface $urlGenerator,
        UserService $userService,
        SystemEvaluationConfigService $systemEvaluationConfig
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $this->parameterBag = $parameterBag;
        $this->eventRepository = $eventRepository;
        $this->editionRepository = $editionRepository;
        $this->subsectionRepository = $subsectionRepository;
        $this->speakerRepository = $speakerRepository;
        $this->fileRepository = $fileRepository;
        $this->breadcrumbsService = $breadcrumbs;
        $this->urlGenerator = $urlGenerator;
        $this->userService = $userService;
        $this->systemEvaluationConfigService = $systemEvaluationConfig;

        $this->filesystem = new Filesystem();

        $this->certificateLayoutPath = str_replace('#KERNEL#', $parameterBag->get('kernel.project_dir'), $this->certificateLayoutPath);
    }

    /**
     * @return array
     */
    protected function getMenuBreadcumb()
    {
        return [
            ['label' => 'MANAGER_MB_DASHBOARD', 'href' => '/'],
            ['label' => 'MANAGER_MB_EVENTS', 'href' => '/gestor', 'active' => true],
        ];
    }

    /**
     * @Route("/{eventId}/editions", name="manager_editions_index", methods={"GET"})
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     *
     * @return Response
     */
    public function editionsIndex(PaginatorInterface $paginator, Request $request)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $event = $this->eventRepository->findOneBy([
            'id' => $request->get('eventId'),
            'deletedAt' => null,
        ]);

        if (! $event) {
            return new Response('', 404);
        }

        $results = $paginator->paginate($this->editionRepository->list($event->getId()), $request->query->get('page', 1), 20);

        return $this->render('@Base/gestor/tabs/editions/list.html.twig', [
            'event' => $event,
            'editions' => $results,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
        ]);
    }

    /**
     * @param FormInterface $form
     * @param Entity $edition
     */
    protected function saveCertificateLayoutFile(FormInterface $form, Edition $edition)
    {
        $uploadPath = $this->certificateLayoutPath . $edition->getId();
        if (! $this->filesystem->exists($uploadPath)) {
            $this->filesystem->mkdir($uploadPath, 0755);
        }

        /** @var File $file */
        $file = $form->get('certificateLayoutPath')->getData();
        if ($file) {
            $filename = uniqid() . '.' . $file->guessExtension();
            try {
                $file->move($uploadPath, $filename);

                $edition->setCertificateLayoutPath($filename);
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * @Route("/{eventId}/editions/new", name="manager_editions_new", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function editionsNew(Request $request)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $event = $this->eventRepository->findOneBy([
            'id' => $request->get('eventId'),
            'deletedAt' => null,
        ]);

        if (! $event) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS', $this->urlGenerator->generate('manager_editions_index', ['eventId' => $event->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS_NEW');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $edition = new Edition();
        $edition->setEvent($event);

        $form = $this->createForm(EditionType::class, $edition);
        $form->handleRequest($request);

        if ($request->isXmlHttpRequest()) {
            if ($form->isSubmitted() && $form->isValid()) {
                try {

                    $edition->setCreatedAt(new \DateTime('now'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($edition);
                    $this->saveCertificateLayoutFile($form, $edition);
                    $entityManager->flush();

                    $ip = $request->getClientIp();
                    $this->systemEvaluationConfigService->register(
                        0, 0, 0, 0, $ip, $edition, $user, 0, 0, 0, 0, 0, 0, 0, 0, 0
                    );

                    $this->addFlash('success', 'Edition created');

                    return new JsonResponse([], 201);
                } catch (\Exception $e) {
                    return new Response($e->getMessage(), 500);
                }

            } else {

                return new Response($this->renderView('@Base/gestor/tabs/editions/partials/_form.html.twig', [
                    'event' => $event,
                    'form' => $form->createView(),
                    'edition' => $edition,
                ]), 400);
            }
        }

        return $this->render('@Base/gestor/tabs/editions/form.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
            'edition' => $edition,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
        ]);
    }

    /**
     * @Route("/editions/{id}/edit", name="manager_editions_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Edition $edition
     *
     * @return Response
     */
    public function editionsEdit(Request $request, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (null !== $edition->getDeletedAt()) {
            return new Response('', 500);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS', $this->urlGenerator->generate('manager_editions_index', ['eventId' => $edition->getEvent()->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS_EDIT');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $form = $this->createForm(EditionType::class, $edition);
        $form->handleRequest($request);

        if ($request->isXmlHttpRequest()) {


            if ($form->isSubmitted() && $form->isValid()) {

                $edition->setUpdatedAt(new \DateTime());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($edition);
                $this->saveCertificateLayoutFile($form, $edition);
                $entityManager->flush();

                $this->addFlash('success', 'Edition updated');

                return new Response('', 200);
            } else {
                return new Response($this->renderView('@Base/gestor/tabs/editions/partials/_form.html.twig', [
                    'event' => $edition->getEvent(),
                    'form' => $form->createView(),
                    'edition' => $edition,
                ]), 400);
            }
        }

        return $this->render('@Base/gestor/tabs/editions/form.html.twig', [
            'event' => $edition->getEvent(),
            'form' => $form->createView(),
            'edition' => $edition,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
        ]);
    }

    /**
     * @Route("/editions/{id}", name="manager_editions_remove", methods={"DELETE"})
     *
     * @param Request $request
     * @param Edition $edition
     *
     * @return Response
     */
    public function editionsRemove(Request $request, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $edition->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $edition->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($edition);
        $entityManager->flush();
        $this->addFlash('success', 'Edition removed');

        return new Response('', 200);
    }
}
