<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Event;
use App\Bundle\Base\Form\EventType;
use App\Bundle\Base\Repository\EditionFileRepository;
use App\Bundle\Base\Repository\EditionRepository;
use App\Bundle\Base\Repository\EventRepository;
use App\Bundle\Base\Repository\SpeakerRepository;
use App\Bundle\Base\Repository\SubsectionRepository;
use App\Bundle\Base\Services\User as UserService;
use App\Bundle\Base\Traits\AccessControl;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/manager")
 *
 * Class ManagerEventsController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class ManagerEventsController extends AbstractController
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
     * ManagerController constructor.
     *
     * @param EventRepository $eventRepository
     * @param EditionRepository $editionRepository
     * @param SubsectionRepository $subsectionRepository
     * @param SpeakerRepository $speakerRepository
     * @param EditionFileRepository $fileRepository
     * @param ParameterBagInterface $parameterBag
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
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
        UserService $userService
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
     * @Route("/", name="manager_index", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $results = $paginator->paginate($this->eventRepository->list(), $request->query->get('page', 1), 20);

        return $this->render('@Base/gestor/tabs/events/list.html.twig', [
            'events' => $results,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
        ]);
    }

    /**
     * @Route("/new", name="manager_new", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EVENTS_NEW');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $event = new Event();

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/gestor/tabs/events/form.html.twig', [
                'form' => $form->createView(),
                'event' => $event,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/gestor/tabs/events/partials/_form.html.twig', [
                'form' => $form->createView(),
                'event' => $event,
            ]), 400);
        }

        $event->setCreatedAt(new \DateTime('now'));

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();
            $this->addFlash('success', 'Event created');
        } catch (\Exception $e) {
            return new Response('', 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }

    /**
     * @Route("/{id}/edit", name="manager_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Event $event
     *
     * @return Response
     */
    public function edit(Request $request, Event $event)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EVENTS_EDIT');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        if (null !== $event->getDeletedAt()) {
            return new Response('', 500);
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/gestor/tabs/events/form.html.twig', [
                'form' => $form->createView(),
                'event' => $event,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/gestor/tabs/events/partials/_form.html.twig', [
                'form' => $form->createView(),
                'event' => $event,
            ]), 400);
        }

        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Event updated');

        return new Response('', 200);
    }

    /**
     * @Route("/{id}", name="manager_remove", methods={"DELETE"})
     *
     * @param Request $request
     * @param Event $event
     *
     * @return Response
     */
    public function remove(Request $request, Event $event)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $event->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($event);
        $entityManager->flush();
        $this->addFlash('success', 'Event removed');

        return new Response('', 200);
    }
}
