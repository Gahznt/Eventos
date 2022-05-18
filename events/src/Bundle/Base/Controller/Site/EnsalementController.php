<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Activity;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\SystemEnsalementRooms as Room;
use App\Bundle\Base\Entity\SystemEnsalementScheduling as Scheduling;
use App\Bundle\Base\Entity\SystemEnsalementSessions;
use App\Bundle\Base\Entity\SystemEnsalementSessions as Session;
use App\Bundle\Base\Entity\SystemEnsalementSlots;
use App\Bundle\Base\Entity\SystemEnsalementSlots as Slot;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Form\EnsalementGeneralSearchType;
use App\Bundle\Base\Form\EnsalementGeneralType;
use App\Bundle\Base\Form\EnsalementPriorityType;
use App\Bundle\Base\Form\EnsalementSectionSearchType;
use App\Bundle\Base\Form\EnsalementSectionType;
use App\Bundle\Base\Form\SystemEnsalementRoomsType as RoomType;
use App\Bundle\Base\Form\SystemEnsalementSessionsType as SessionType;
use App\Bundle\Base\Form\SystemEnsalementSlotsType as SlotType;
use App\Bundle\Base\Repository\SystemEnsalementRoomsRepository as RoomRepository;
use App\Bundle\Base\Repository\SystemEnsalementSchedulingRepository as SchedulingRepository;
use App\Bundle\Base\Repository\SystemEnsalementSessionsRepository as SessionRepository;
use App\Bundle\Base\Repository\SystemEnsalementSlotsRepository as SlotRepository;
use App\Bundle\Base\Services\Edition as EditionService;
use App\Bundle\Base\Services\SystemEvaluationConfig;
use App\Bundle\Base\Services\User as UserService;
use App\Bundle\Base\Traits\AccessControl;
use Knp\Component\Pager\PaginatorInterface;
use setasign\Fpdi\Fpdi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;
use Yectep\PhpSpreadsheetBundle\Factory as PhpSpreadsheet;

/**
 * @Route("/ensalement")
 *
 * Class EnsalementController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class EnsalementController extends AbstractController
{
    use AccessControl;

    /**
     * @var RoomRepository
     */
    private $roomRepository;

    /**
     * @var SessionRepository
     */
    private $sessionRepository;

    /**
     * @var SlotRepository
     */
    private $slotRepository;

    /**
     * @var SchedulingRepository
     */
    private $schedulingRepository;

    /**
     * @var Breadcrumbs
     */
    private $breadcrumbsService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var SystemEvaluationConfig
     */
    private $systemEvaluationConfigService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string|string[]
     */
    private $uploadPath = Scheduling::UPLOAD_PATH;

    /**
     * @var EditionService
     */
    private $editionService;

    /**
     * EnsalementController constructor.
     *
     * @param RoomRepository $roomRepository
     * @param SessionRepository $sessionRepository
     * @param SlotRepository $slotRepository
     * @param SchedulingRepository $schedulingRepository
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        RoomRepository $roomRepository,
        SessionRepository $sessionRepository,
        SlotRepository $slotRepository,
        SchedulingRepository $schedulingRepository,
        Breadcrumbs $breadcrumbs,
        UrlGeneratorInterface $urlGenerator,
        SystemEvaluationConfig $systemEvaluationConfig,
        UserService $userService,
        EditionService $editionService,
        ParameterBagInterface $parameterBag
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));

        $this->roomRepository = $roomRepository;
        $this->sessionRepository = $sessionRepository;
        $this->slotRepository = $slotRepository;
        $this->schedulingRepository = $schedulingRepository;
        $this->breadcrumbsService = $breadcrumbs;
        $this->urlGenerator = $urlGenerator;
        $this->systemEvaluationConfigService = $systemEvaluationConfig;
        $this->userService = $userService;
        $this->editionService = $editionService;
        $this->filesystem = new Filesystem();

        $this->uploadPath = str_replace('#KERNEL#', $parameterBag->get('kernel.project_dir'), $this->uploadPath);
    }

    /**
     * @Route("/{edition}/rooms", name="ensalement_rooms_index", methods={"GET"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Edition $edition
     *
     * @return Response
     */
    public function roomsIndex(Request $request, PaginatorInterface $paginator, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $edition || null !== $edition->getDeletedAt()) {
            return new Response('', 404);
        }

        // $this->breadcrumbsService->addItem('ENSALEMENT_INDEX', $this->urlGenerator->generate('ensalement_index'));
        $this->breadcrumbsService->addItem('ENSALEMENT');
        $this->breadcrumbsService->addItem('ENSALEMENT_ROOMS');
        $this->get('twig')->addGlobal('pageTitle', sprintf('Ensalamento - %s', $edition->getNamePortuguese())); // não haverá tradução

        $room = new Room();
        $room->setEdition($edition);
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        $results = $paginator->paginate($this->roomRepository->list($edition->getId()), $request->query->get('page', 1), 20);

        return $this->render('@Base/ensalamento_classroom_registration/tabs/rooms/index.html.twig', [
            'edition' => $edition,
            'form' => $form->createView(),
            // 'entity' => $room,
            'results' => $results,
            'ROOM_TYPES' => array_flip(Room::ROOM_TYPES),
            'secionsEnable' => $this->systemEvaluationConfigService->freeSections($edition),
            'ensalementGeneralEnable' => $this->systemEvaluationConfigService->ensalementGeneralFlag($edition),
            'ensalementPriorityEnable' => $this->systemEvaluationConfigService->ensalementPriorityFlag($edition),
        ]);
    }

    /**
     * @Route("/{edition}/rooms/new", name="ensalement_rooms_new", methods={"POST"})
     *
     * @param Request $request
     * @param Edition $edition
     *
     * @return JsonResponse|Response
     */
    public function roomsNew(Request $request, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $edition || null !== $edition->getDeletedAt()) {
            return new Response('', 404);
        }

        $room = new Room();
        $room->setEdition($edition);
        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if (! $form->isSubmitted() || ! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/ensalamento_classroom_registration/tabs/rooms/partials/_form.html.twig', [
                'edition' => $edition,
                'form' => $form->createView(),
                'entity' => $room,
            ]), 400);
        }

        $room->setCreatedAt(new \DateTime('now'));

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($room);
            $entityManager->flush();
            $this->addFlash('success', 'Room created');
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }

    /**
     * @Route("/rooms/{id}", name="ensalement_rooms_remove", methods={"DELETE"})
     *
     * @param Request $request
     * @param Room $room
     *
     * @return Response
     */
    public function roomsRemove(Request $request, Room $room)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $room || null !== $room->getDeletedAt()) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $room->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $room->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($room);
        $entityManager->flush();
        // $this->addFlash('success', 'Room removed');

        return new Response('', 200);
    }

    /**
     * @Route("/{edition}/sessions", name="ensalement_sessions_index", methods={"GET"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Edition $edition
     *
     * @return Response
     */
    public function sessionsIndex(Request $request, PaginatorInterface $paginator, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $edition || null !== $edition->getDeletedAt()) {
            return new Response('', 404);
        }

        // $this->breadcrumbsService->addItem('ENSALEMENT_INDEX', $this->urlGenerator->generate('ensalement_index'));
        $this->breadcrumbsService->addItem('ENSALEMENT');
        $this->breadcrumbsService->addItem('ENSALEMENT_SESSIONS');
        $this->get('twig')->addGlobal('pageTitle', sprintf('Ensalamento - %s', $edition->getNamePortuguese())); // não haverá tradução

        $session = new Session();
        $session->setEdition($edition);
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        $results = $paginator->paginate($this->sessionRepository->list($edition->getId()), $request->query->get('page', 1), 20);

        return $this->render('@Base/ensalamento_classroom_registration/tabs/sessions/index.html.twig', [
            'edition' => $edition,
            'form' => $form->createView(),
            // 'entity' => $session,
            'results' => $results,
            'SESSION_TYPES' => array_flip(Session::SESSION_TYPES),
            'secionsEnable' => $this->systemEvaluationConfigService->freeSections($edition),
            'ensalementGeneralEnable' => $this->systemEvaluationConfigService->ensalementGeneralFlag($edition),
            'ensalementPriorityEnable' => $this->systemEvaluationConfigService->ensalementPriorityFlag($edition),
        ]);
    }

    /**
     * @Route("/{edition}/sessions/new", name="ensalement_sessions_new", methods={"POST"})
     *
     * @param Request $request
     * @param Edition $edition
     *
     * @return JsonResponse|Response
     */
    public function sessionsNew(Request $request, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $edition || null !== $edition->getDeletedAt()) {
            return new Response('', 404);
        }

        $session = new Session();
        $session->setEdition($edition);
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if (! $form->isSubmitted() || ! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/ensalamento_classroom_registration/tabs/sessions/partials/_form.html.twig', [
                'edition' => $edition,
                'form' => $form->createView(),
                'entity' => $session,
            ]), 400);
        }

        $session->setCreatedAt(new \DateTime('now'));

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($session);
            $entityManager->flush();
            $this->addFlash('success', 'Session created');
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }

    /**
     * @Route("/sessions/{id}", name="ensalement_sessions_remove", methods={"DELETE"})
     *
     * @param Request $request
     * @param Session $session
     *
     * @return Response
     */
    public function sessionsRemove(Request $request, Session $session)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $session || null !== $session->getDeletedAt()) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $session->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $session->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($session);
        $entityManager->flush();
        // $this->addFlash('success', 'Session removed');

        return new Response('', 200);
    }

    /**
     * @Route("/{edition}/slots", name="ensalement_slots_index", methods={"GET"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Edition $edition
     *
     * @return Response
     */
    public function slotsIndex(Request $request, PaginatorInterface $paginator, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $edition || null !== $edition->getDeletedAt()) {
            return new Response('', 404);
        }

        // $this->breadcrumbsService->addItem('ENSALEMENT_INDEX', $this->urlGenerator->generate('ensalement_index'));
        $this->breadcrumbsService->addItem('ENSALEMENT');
        $this->breadcrumbsService->addItem('ENSALEMENT_SLOTS');
        $this->get('twig')->addGlobal('pageTitle', sprintf('Ensalamento - %s', $edition->getNamePortuguese())); // não haverá tradução

        $slot = new Slot();
        $slot->setEdition($edition);
        $form = $this->createForm(SlotType::class, $slot);
        $form->handleRequest($request);

        $results = $paginator->paginate($this->slotRepository->list($edition->getId()), $request->query->get('page', 1), 20);

        return $this->render('@Base/ensalamento_classroom_registration/tabs/slots/index.html.twig', [
            'edition' => $edition,
            'form' => $form->createView(),
            // 'entity' => $slot,
            'results' => $results,
            'SESSION_TYPES' => array_flip(Session::SESSION_TYPES),
            'secionsEnable' => $this->systemEvaluationConfigService->freeSections($edition),
            'ensalementGeneralEnable' => $this->systemEvaluationConfigService->ensalementGeneralFlag($edition),
            'ensalementPriorityEnable' => $this->systemEvaluationConfigService->ensalementPriorityFlag($edition),
        ]);
    }

    /**
     * @Route("/{edition}/slots/load_sessions", name="ensalement_slots_load_sessions", methods={"POST"})
     *
     * @param Request $request
     * @param Edition $edition
     *
     * @return JsonResponse|Response
     */
    public function slotsLoadSessions(Request $request, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $edition || null !== $edition->getDeletedAt()) {
            return new Response('', 404);
        }

        $slot = new Slot();
        $slot->setEdition($edition);
        $form = $this->createForm(SlotType::class, $slot);
        $form->handleRequest($request);

        return new Response($this->renderView('@Base/ensalamento_classroom_registration/tabs/slots/partials/_form.html.twig', [
            'edition' => $edition,
            'form' => $form->createView(),
            'entity' => $slot,
        ]), 200);

    }

    /**
     * @Route("/{edition}/slots/new", name="ensalement_slots_new", methods={"POST"})
     *
     * @param Request $request
     * @param Edition $edition
     *
     * @return JsonResponse|Response
     */
    public function slotsNew(Request $request, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $edition || null !== $edition->getDeletedAt()) {
            return new Response('', 404);
        }

        $slot = new Slot();
        $slot->setEdition($edition);
        $form = $this->createForm(SlotType::class, $slot);
        $form->handleRequest($request);

        if (! $form->isSubmitted() || ! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/ensalamento_classroom_registration/tabs/slots/partials/_form.html.twig', [
                'edition' => $edition,
                'form' => $form->createView(),
                'entity' => $slot,
            ]), 400);
        }

        $slot->setCreatedAt(new \DateTime('now'));

        try {
            $sessions = $form->get('systemEnsalementSessions')->getData();

            foreach ($sessions as $session) {
                $newSlot = clone $slot;
                $newSlot->setSystemEnsalementSessions($session);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($newSlot);
                $entityManager->flush();
            }
            $this->addFlash('success', 'Slot created');
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }

    /**
     * @Route("/slots/{id}", name="ensalement_slots_remove", methods={"DELETE"})
     *
     * @param Request $request
     * @param Slot $slot
     *
     * @return Response
     */
    public function slotsRemove(Request $request, Slot $slot)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $slot || null !== $slot->getDeletedAt()) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $slot->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $slot->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($slot);
        $entityManager->flush();
        // $this->addFlash('success', 'Slot removed');

        return new Response('', 200);
    }

    /**
     * @Route("/{edition}/priority", name="ensalement_priority_index", methods={"GET"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Edition $edition
     *
     * @return Response
     */
    public function priorityIndex(Request $request, PaginatorInterface $paginator, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (
            ! $edition
            || null !== $edition->getDeletedAt()
            || ! $this->systemEvaluationConfigService->get($edition)
            || ! $this->systemEvaluationConfigService->get($edition)->getEnsalementPriority()
        ) {
            return new Response('', 404);
        }

        // $this->breadcrumbsService->addItem('ENSALEMENT_INDEX', $this->urlGenerator->generate('ensalement_index'));
        $this->breadcrumbsService->addItem('ENSALEMENT');
        $this->breadcrumbsService->addItem('ENSALEMENT_PRIORITY');
        $this->get('twig')->addGlobal('pageTitle', sprintf('Ensalamento - %s', $edition->getNamePortuguese())); // não haverá tradução

        $scheduling = new Scheduling();
        $scheduling->setEdition($edition);
        $form = $this->createForm(EnsalementPriorityType::class, $scheduling);
        $form->handleRequest($request);

        $results = $paginator->paginate($this->schedulingRepository->list($edition->getId(), true), $request->query->get('page', 1), 20);

        return $this->render('@Base/ensalamento_classroom_registration/tabs/priority/index.html.twig', [
            'edition' => $edition,
            'form' => $form->createView(),
            //'entity' => $scheduling,
            'results' => $results,
            'SESSION_TYPES' => array_flip(Session::SESSION_TYPES),
            'ACTIVITY_TYPES' => array_flip(Activity::ACTIVITY_TYPES),
            'secionsEnable' => $this->systemEvaluationConfigService->freeSections($edition),
            'ensalementGeneralEnable' => $this->systemEvaluationConfigService->ensalementGeneralFlag($edition),
            'ensalementPriorityEnable' => $this->systemEvaluationConfigService->ensalementPriorityFlag($edition),
        ]);
    }

    /**
     * @Route("/{edition}/priority/filter", name="ensalement_priority_filter", methods={"POST"})
     *
     * @param Request $request
     * @param Edition $edition
     *
     * @return JsonResponse|Response
     */
    public function priorityFilter(Request $request, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $edition || null !== $edition->getDeletedAt()) {
            return new Response('', 404);
        }

        $scheduling = new Scheduling();
        $scheduling->setEdition($edition);

        $form = $this->createForm(EnsalementPriorityType::class, $scheduling);
        $form->handleRequest($request);

        return new Response($this->renderView('@Base/ensalamento_classroom_registration/tabs/priority/partials/_form.html.twig', [
            'edition' => $edition,
            'form' => $form->createView(),
            'entity' => $scheduling,
        ]), 200);
    }

    /**
     * @Route("/{edition}/priority/new", name="ensalement_priority_new", methods={"POST"})
     *
     * @param Request $request
     * @param Edition $edition
     *
     * @return JsonResponse|Response
     */
    public function priorityNew(Request $request, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $edition || null !== $edition->getDeletedAt()) {
            return new Response('', 404);
        }

        $scheduling = new Scheduling();
        $scheduling->setEdition($edition);
        $form = $this->createForm(EnsalementPriorityType::class, $scheduling);
        $form->handleRequest($request);

        if (! $form->isSubmitted() || ! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/ensalamento_classroom_registration/tabs/priority/partials/_form.html.twig', [
                'edition' => $edition,
                'form' => $form->createView(),
                //'entity' => $scheduling,
            ]), 400);
        }

        $scheduling->setCreatedAt(new \DateTime('now'));
        $scheduling->setPriority(true);
        $scheduling->setContentType($form->getData()->getPanel() ? 2 : 1);

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($scheduling);
            $entityManager->flush();
            $this->addFlash('success', 'Schedule created');
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }

    /**
     * @Route("/priority/{id}", name="ensalement_priority_remove", methods={"DELETE"})
     *
     * @param Request $request
     * @param Scheduling $scheduling
     *
     * @return Response
     */
    public function priorityRemove(Request $request, Scheduling $scheduling)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $scheduling || null !== $scheduling->getDeletedAt()) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $scheduling->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $scheduling->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($scheduling);
        $entityManager->flush();
        // $this->addFlash('success', 'Scheduling removed');

        return new Response('', 200);
    }

    /**
     * @Route("/{edition}/sections", name="ensalement_sections_index", methods={"GET"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Edition $edition
     *
     * @return Response
     */
    public function sectionsIndex(Request $request, PaginatorInterface $paginator, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (
            ! $edition
            || null !== $edition->getDeletedAt()
            || ! $this->systemEvaluationConfigService->freeSections($edition)
        ) {
            return new Response('', 404);
        }

        // $this->breadcrumbsService->addItem('ENSALEMENT_INDEX', $this->urlGenerator->generate('ensalement_index'));
        $this->breadcrumbsService->addItem('ENSALEMENT');
        $this->breadcrumbsService->addItem('ENSALEMENT_SECTIONS');
        $this->get('twig')->addGlobal('pageTitle', sprintf('Ensalamento - %s', $edition->getNamePortuguese())); // não haverá tradução

        $scheduling = new Scheduling();
        $scheduling->setEdition($edition);
        $form = $this->createForm(EnsalementSectionSearchType::class, $scheduling, ['edition' => $edition, 'csrf_protection' => false, 'method' => 'GET']);
        $form->handleRequest($request);

        $criteria = $request->query->get('search', []);

        $results = $paginator->paginate($this->schedulingRepository->list($edition->getId(), false, $criteria), $request->query->get('page', 1), 20);

        return $this->render('@Base/ensalamento_classroom_registration/tabs/sections/index.html.twig', [
            'edition' => $edition,
            'form' => $form->createView(),
            // 'entity' => $scheduling,
            'results' => $results,
            'SESSION_TYPES' => array_flip(Session::SESSION_TYPES),
            'ACTIVITY_TYPES' => array_flip(Activity::ACTIVITY_TYPES),
            'COORDINATOR_DEBATER_TYPES' => array_flip(Scheduling::COORDINATOR_DEBATER_TYPES),
            'SECTION_FORMATS' => array_flip(Scheduling::SECTION_FORMATS),
            'LANGUAGES' => array_flip(Scheduling::LANGUAGES),
            'secionsEnable' => $this->systemEvaluationConfigService->freeSections($edition),
            'ensalementGeneralEnable' => $this->systemEvaluationConfigService->ensalementGeneralFlag($edition),
            'ensalementPriorityEnable' => $this->systemEvaluationConfigService->ensalementPriorityFlag($edition),
        ]);
    }

    /**
     * @Route("/{edition}/sections/search_filter", name="ensalement_sections_search_filter", methods={"POST"})
     *
     * @param Request $request
     * @param Edition $edition
     *
     * @return Response
     */
    public function sectionsSearchFilter(Request $request, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $edition || null !== $edition->getDeletedAt()) {
            return new Response('', 404);
        }

        $scheduling = new Scheduling();
        $scheduling->setEdition($edition);
        $form = $this->createForm(EnsalementSectionSearchType::class, $scheduling, ['edition' => $edition, 'csrf_protection' => false]);
        $form->handleRequest($request);

        return new Response($this->renderView('@Base/ensalamento_classroom_registration/tabs/sections/partials/_search_form.html.twig', [
            'edition' => $edition,
            'form' => $form->createView(),
            'entity' => $scheduling,
        ]), 200);
    }

    /**
     * @Route("/{edition}/sections/filter/{id}", name="ensalement_sections_filter", methods={"POST"}, defaults={"id" = null})
     *
     * @param Request $request
     * @param Edition $edition
     * @param Scheduling|null $scheduling
     *
     * @return Response
     */
    public function sectionsFilter(Request $request, Edition $edition, ?Scheduling $scheduling)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $edition || null !== $edition->getDeletedAt()) {
            return new Response('', 404);
        }

        if ($scheduling && null !== $scheduling->getDeletedAt()) {
            return new Response('', 404);
        }

        if (! $scheduling) {
            $scheduling = new Scheduling();
        }

        $scheduling->setEdition($edition);
        $form = $this->createForm(EnsalementSectionType::class, $scheduling, ['edition' => $edition]);
        $form->handleRequest($request);

        return new Response($this->renderView('@Base/ensalamento_classroom_registration/tabs/sections/partials/_form.html.twig', [
            'edition' => $edition,
            'form' => $form->createView(),
            'entity' => $scheduling,
        ]), 200);
    }

    /**
     * @Route("/{edition}/sections/new", name="ensalement_sections_new", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Edition $edition
     *
     * @return JsonResponse|Response
     */
    public function sectionsNew(Request $request, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (
            ! $edition
            || null !== $edition->getDeletedAt()
            || ! $this->systemEvaluationConfigService->freeSections($edition)
        ) {
            return new Response('', 404);
        }

        // $this->breadcrumbsService->addItem('ENSALEMENT_INDEX', $this->urlGenerator->generate('ensalement_index'));
        $this->breadcrumbsService->addItem('ENSALEMENT');
        $this->breadcrumbsService->addItem('ENSALEMENT_SECTIONS', $this->urlGenerator->generate('ensalement_sections_index', ['edition' => $edition->getId()]));
        $this->breadcrumbsService->addItem('ENSALEMENT_SECTIONS_NEW');
        $this->get('twig')->addGlobal('pageTitle', sprintf('Ensalamento - %s', $edition->getNamePortuguese())); // não haverá tradução

        $scheduling = new Scheduling();

        $scheduling->setEdition($edition);
        $form = $this->createForm(EnsalementSectionType::class, $scheduling, ['edition' => $edition]);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/ensalamento_classroom_registration/tabs/sections/form.html.twig', [
                'edition' => $edition,
                'form' => $form->createView(),
                'entity' => $scheduling,
                'secionsEnable' => $this->systemEvaluationConfigService->freeSections($edition),
                'ensalementGeneralEnable' => $this->systemEvaluationConfigService->ensalementGeneralFlag($edition),
                'ensalementPriorityEnable' => $this->systemEvaluationConfigService->ensalementPriorityFlag($edition),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        $em = $this->getDoctrine()->getManager();
        /** @var SchedulingRepository $er */
        $er = $em->getRepository(Scheduling::class);

        if (
            $form->get('systemEnsalementSlots')
            && $form->get('systemEnsalementSlots')->getData()
            && $form->get('systemEnsalementSlots')->getData()->getSystemEnsalementSessions()
        ) {
            /** @var SystemEnsalementSlots $slot */
            $slot = $form->get('systemEnsalementSlots')->getData();

            if (
                $form->get('coordinatorDebater1')
                && $form->get('coordinatorDebater1')->getData()
            ) {
                $coordinatorDebater1 = $form->get('coordinatorDebater1')->getData();
                $count = $er->countByAuthorAndSession(
                    $edition,
                    $coordinatorDebater1,
                    $slot->getSystemEnsalementSessions(),
                    $slot->getSystemEnsalementRooms(),
                    null
                );

                if ($count > 0) {
                    $form->get('coordinatorDebater1')->addError(
                        new FormError('Este coordenador/debatedor já está vinculado a outra sessão no mesmo dia e horário.')
                    );
                }
            }

            if (
                $form->get('coordinatorDebater2')
                && $form->get('coordinatorDebater2')->getData()
            ) {
                $coordinatorDebater2 = $form->get('coordinatorDebater2')->getData();
                $count = $er->countByAuthorAndSession(
                    $edition,
                    $coordinatorDebater2,
                    $slot->getSystemEnsalementSessions(),
                    $slot->getSystemEnsalementRooms(),
                    null
                );

                if ($count > 0) {
                    $form->get('coordinatorDebater2')->addError(
                        new FormError('Este coordenador/debatedor já está vinculado a outra sessão no mesmo dia e horário.')
                    );
                }
            }

            if (count($form->get('articles')) > 0) {
                foreach ($form->get('articles') as $article) {
                    /** @var UserArticles $userArticle */
                    $userArticle = $article->get('userArticles')->getData();
                    if (! $userArticle || 0 === $userArticle->getUserArticlesAuthors()->count()) {
                        continue;
                    }

                    foreach ($userArticle->getUserArticlesAuthors() as $author) {
                        $count = $er->countByAuthorAndSession(
                            $edition,
                            $author->getUserAuthor(),
                            $slot->getSystemEnsalementSessions(),
                            $slot->getSystemEnsalementRooms(),
                            null
                        );

                        if ($count > 0) {
                            $article->get('userArticles')->addError(
                                new FormError('Um ou mais autores deste artigo já estão vinculados a outra sessão no mesmo dia e horário.')
                            );
                        }
                    }
                }
            }
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/ensalamento_classroom_registration/tabs/sections/partials/_form.html.twig', [
                'edition' => $edition,
                'form' => $form->createView(),
                'entity' => $scheduling,
            ]), 400);
        }

        $scheduling->setCreatedAt(new \DateTime('now'));

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($scheduling);
            $entityManager->flush();
            $this->addFlash('success', 'Section created');
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }

    /**
     * @Route("/sections/{id}/edit", name="ensalement_sections_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Scheduling $scheduling
     *
     * @return Response
     */
    public function sectionsEdit(Request $request, Scheduling $scheduling)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (
            ! $scheduling
            || null !== $scheduling->getDeletedAt()
            || ! $this->systemEvaluationConfigService->freeSections($scheduling->getEdition())
        ) {
            return new Response('', 404);
        }

        // $this->breadcrumbsService->addItem('ENSALEMENT_INDEX', $this->urlGenerator->generate('ensalement_index'));
        $this->breadcrumbsService->addItem('ENSALEMENT');
        $this->breadcrumbsService->addItem('ENSALEMENT_SECTIONS', $this->urlGenerator->generate('ensalement_sections_index', ['edition' => $scheduling->getEdition()->getId()]));
        $this->breadcrumbsService->addItem('ENSALEMENT_SECTIONS_EDIT');
        $this->get('twig')->addGlobal('pageTitle', sprintf('Ensalamento - %s', $scheduling->getEdition()->getNamePortuguese())); // não haverá tradução

        $form = $this->createForm(EnsalementSectionType::class, $scheduling, ['edition' => $scheduling->getEdition()]);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/ensalamento_classroom_registration/tabs/sections/form.html.twig', [
                'edition' => $scheduling->getEdition(),
                'form' => $form->createView(),
                'entity' => $scheduling,
                'secionsEnable' => $this->systemEvaluationConfigService->freeSections($scheduling->getEdition()),
                'ensalementGeneralEnable' => $this->systemEvaluationConfigService->ensalementGeneralFlag($scheduling->getEdition()),
                'ensalementPriorityEnable' => $this->systemEvaluationConfigService->ensalementPriorityFlag($scheduling->getEdition()),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        $em = $this->getDoctrine()->getManager();
        /** @var SchedulingRepository $er */
        $er = $em->getRepository(Scheduling::class);

        if (
            $form->get('systemEnsalementSlots')
            && $form->get('systemEnsalementSlots')->getData()
            && $form->get('systemEnsalementSlots')->getData()->getSystemEnsalementSessions()
        ) {
            /** @var SystemEnsalementSlots $slot */
            $slot = $form->get('systemEnsalementSlots')->getData();

            if (
                $form->get('coordinatorDebater1')
                && $form->get('coordinatorDebater1')->getData()
            ) {
                $coordinatorDebater1 = $form->get('coordinatorDebater1')->getData();
                $count = $er->countByAuthorAndSession(
                    $scheduling->getEdition(),
                    $coordinatorDebater1,
                    $slot->getSystemEnsalementSessions(),
                    $slot->getSystemEnsalementRooms(),
                    $scheduling
                );

                if ($count > 0) {
                    $form->get('coordinatorDebater1')->addError(
                        new FormError('Este coordenador/debatedor já está vinculado a outra sessão no mesmo dia e horário.')
                    );
                }
            }

            if (
                $form->get('coordinatorDebater2')
                && $form->get('coordinatorDebater2')->getData()
            ) {
                $coordinatorDebater2 = $form->get('coordinatorDebater2')->getData();
                $count = $er->countByAuthorAndSession(
                    $scheduling->getEdition(),
                    $coordinatorDebater2,
                    $slot->getSystemEnsalementSessions(),
                    $slot->getSystemEnsalementRooms(),
                    $scheduling
                );

                if ($count > 0) {
                    $form->get('coordinatorDebater2')->addError(
                        new FormError('Este coordenador/debatedor já está vinculado a outra sessão no mesmo dia e horário.')
                    );
                }
            }

            if (count($form->get('articles')) > 0) {
                foreach ($form->get('articles') as $article) {
                    /** @var UserArticles $userArticle */
                    $userArticle = $article->get('userArticles')->getData();
                    if (! $userArticle || 0 === $userArticle->getUserArticlesAuthors()->count()) {
                        continue;
                    }

                    foreach ($userArticle->getUserArticlesAuthors() as $author) {
                        $count = $er->countByAuthorAndSession(
                            $scheduling->getEdition(),
                            $author->getUserAuthor(),
                            $slot->getSystemEnsalementSessions(),
                            $slot->getSystemEnsalementRooms(),
                            $scheduling
                        );

                        if ($count > 0) {
                            $article->get('userArticles')->addError(
                                new FormError('Um ou mais autores deste artigo já estão vinculados a outra sessão no mesmo dia e horário.')
                            );
                        }
                    }
                }
            }
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/ensalamento_classroom_registration/tabs/sections/partials/_form.html.twig', [
                'edition' => $scheduling->getEdition(),
                'form' => $form->createView(),
                'entity' => $scheduling,
            ]), 400);
        }

        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Section changed');

        return new Response('', 200);
    }

    /**
     * @Route("/sections/{id}", name="ensalement_sections_remove", methods={"DELETE"})
     *
     * @param Request $request
     * @param Scheduling $scheduling
     *
     * @return Response
     */
    public function sectionsRemove(Request $request, Scheduling $scheduling)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $scheduling || null !== $scheduling->getDeletedAt()) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $scheduling->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $scheduling->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        // $entityManager->persist($scheduling);
        $entityManager->remove($scheduling);
        $entityManager->flush();
        // $this->addFlash('success', 'Scheduling removed');

        return new Response('', 200);
    }

    /**
     * @Route("/{edition}/general", name="ensalement_general_index", methods={"GET"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param Edition $edition
     *
     * @return Response
     */
    public function generalIndex(Request $request, PaginatorInterface $paginator, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (
            ! $edition
            || null !== $edition->getDeletedAt()
            || ! $this->systemEvaluationConfigService->get($edition)
            || ! $this->systemEvaluationConfigService->get($edition)->getEnsalementGeneral()
        ) {
            return new Response('', 404);
        }

        // $this->breadcrumbsService->addItem('ENSALEMENT_INDEX', $this->urlGenerator->generate('ensalement_index'));
        $this->breadcrumbsService->addItem('ENSALEMENT');
        $this->breadcrumbsService->addItem('ENSALEMENT_GENERAL');
        $this->get('twig')->addGlobal('pageTitle', sprintf('Ensalamento - %s', $edition->getNamePortuguese())); // não haverá tradução

        $scheduling = new Scheduling();
        $scheduling->setEdition($edition);
        $form = $this->createForm(EnsalementGeneralSearchType::class, $scheduling, ['edition' => $edition, 'csrf_protection' => false, 'method' => 'GET']);
        $form->handleRequest($request);

        $formSlots = $this->createForm(EnsalementGeneralType::class, $scheduling, ['csrf_protection' => false]);

        $criteria = $request->query->get('search', []);

        $slots = $this->slotRepository->list($edition->getId())->getQuery()->getResult();
        $results = $paginator->paginate($this->schedulingRepository->list($edition->getId(), false, $criteria), $request->query->get('page', 1), 20);

        return $this->render('@Base/ensalamento_classroom_registration/tabs/general/index.html.twig', [
            'edition' => $edition,
            'form' => $form->createView(),
            // 'entity' => $scheduling,
            'results' => $results,
            'SESSION_TYPES' => array_flip(Session::SESSION_TYPES),
            'ACTIVITY_TYPES' => array_flip(Activity::ACTIVITY_TYPES),
            'COORDINATOR_DEBATER_TYPES' => array_flip(Scheduling::COORDINATOR_DEBATER_TYPES),
            'SECTION_FORMATS' => array_flip(Scheduling::SECTION_FORMATS),
            'LANGUAGES' => array_flip(Scheduling::LANGUAGES),
            'formSlots' => $formSlots->createView(),
            'slots' => $slots,
            'secionsEnable' => $this->systemEvaluationConfigService->freeSections($edition),
            'ensalementGeneralEnable' => $this->systemEvaluationConfigService->ensalementGeneralFlag($edition),
            'ensalementPriorityEnable' => $this->systemEvaluationConfigService->ensalementPriorityFlag($edition),
        ]);
    }

    /**
     * @Route("/{edition}/generate_coordinator_debater_excel", name="ensalement_generate_coordinator_debater_excel", methods={"GET"})
     *
     * @param Edition $edition
     * @param PhpSpreadsheet $phpSpreadsheet
     * @param TranslatorInterface $translator
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function generateCoordinatorDebaterExcel(Edition $edition, PhpSpreadsheet $phpSpreadsheet, TranslatorInterface $translator)
    {
        $spreadsheet = $phpSpreadsheet->createSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr(sprintf('Coordenadores %s', $edition->getName()), 0, 31));
        $lineIndex = 1;

        $column = 1;
        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Divisão'));
        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Sala e horário'));
        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Sessão'));
        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Nome'));
        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Tipo'));
        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('E-mail'));

        $lineIndex++;

        $qb = $this->schedulingRepository->list($edition->getId());
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->isNotNull('sesss.coordinatorDebater1'),
            $qb->expr()->isNotNull('sesss.coordinatorDebater2')
        ));
        $qb->distinct(true);

        /** @var Scheduling[] $results */
        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            $day = $result->getSystemEnsalementSlots()->getSystemEnsalementSessions()->getDate()->format('d/m');
            $start = $result->getSystemEnsalementSlots()->getSystemEnsalementSessions()->getStart()->format('H:i');
            $end = $result->getSystemEnsalementSlots()->getSystemEnsalementSessions()->getEnd()->format('H:i');
            $room = $result->getSystemEnsalementSlots()->getSystemEnsalementRooms()->getName();

            if ($result->getCoordinatorDebater1()) {
                $column = 1;
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getDivision()->getName());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue('Dia ' . $day . ' - ' . $start . '-' . $end . ' - ' . $room);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getTitle());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getCoordinatorDebater1()->getName());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(array_search($result->getCoordinatorDebater1Type(), Scheduling::COORDINATOR_DEBATER_TYPES));
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getCoordinatorDebater1()->getEmail());

                $lineIndex++;
            }

            if ($result->getCoordinatorDebater2()) {
                $column = 1;
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getDivision()->getName());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue('Dia ' . $day . ' - ' . $start . '-' . $end . ' - ' . $room);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getTitle());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getCoordinatorDebater2()->getName());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(array_search($result->getCoordinatorDebater2Type(), Scheduling::COORDINATOR_DEBATER_TYPES));
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getCoordinatorDebater2()->getEmail());

                $lineIndex++;
            }
        }

        // Gera arquivo
        $response = $phpSpreadsheet->createStreamedResponse($spreadsheet, 'Xls');

        // Redirect output to a client’s web browser (Xls)
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', sprintf('attachment;filename="CoordDebat %s.xls"', $edition->getName()));
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * @Route("/{edition}/generate_detailed_schedule_pdf", name="ensalement_generate_detailed_schedule_pdf", methods={"GET"})
     *
     * @param Edition $edition
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     *
     * @return Response
     * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function generateDetailedSchedulePdf(Edition $edition, Request $request, ParameterBagInterface $parameterBag)
    {
        $editionDetailedScheduling = $edition->getSchedulings();

        if (0 === $editionDetailedScheduling->count()) {
            return new Response('Ok');
        }
        $basePath = $parameterBag->get('kernel.project_dir') . '/public';

        $pdfPath = $this->uploadPath . $edition->getId();

        if (! $this->filesystem->exists($pdfPath)) {
            $this->filesystem->mkdir($pdfPath, 0755);
        }

        $pdfFullPath = $pdfPath . '/' . md5($edition->getId()) . '.pdf';

        $date = $this->editionService->dateIntervalFormat($edition->getDateStart(), $edition->getDateEnd(), $request->getLocale());

        $color = $this->editionService->getColor($edition->getColor());

        [$r, $g, $b] = sscanf($color, "#%02x%02x%02x");

        $pdf = new Fpdi();

        foreach ($editionDetailedScheduling as $scheduling) {
            $pdf->AddPage();

            $pdf->SetFillColor(255, 255, 255);
            $pdf->Rect(0, 0, 300, 25, 'F');

            $pdf->SetFillColor($r, $g, $b);
            $pdf->Rect(0, 25, 300, 1, 'F');

            $pdf->Image($basePath . '/build/images/logo/anpad.png', 6, 6, 45);

            $pdf->SetY(3);
            $pdf->SetX(56);
            $pdf->SetTextColor($r, $g, $b);
            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(150, 10, iconv('utf-8', 'windows-1252//IGNORE', $edition->getLongname() . ' - ' . $edition->getName()), 0, 0, 'R');
            $pdf->Ln();

            $pdf->SetY(9);
            $pdf->SetX(56);
            $pdf->SetTextColor($r, $g, $b);
            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(150, 10, iconv('utf-8', 'windows-1252//IGNORE', $edition->getPlace() . ' - ' . $date), 0, 0, 'R');
            $pdf->Ln();

            $pdf->SetY(14);
            $pdf->SetX(56);
            $pdf->SetTextColor($r, $g, $b);
            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(150, 10, iconv('utf-8', 'windows-1252//IGNORE', $edition->getEvent()->getIssn()), 0, 0, 'R');
            $pdf->Ln();

            $pdf->SetY(30);
            $pdf->SetFont('Helvetica', 'B', 10);
            $pdf->SetFillColor(60, 80, 128);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 6, iconv('utf-8', 'windows-1252//IGNORE', 'Dia ' . $scheduling->getSystemEnsalementSlots()->getSystemEnsalementSessions()->getDate()->format('d/m') .
                ' - ' . $scheduling->getSystemEnsalementSlots()->getSystemEnsalementSessions()->getStart()->format('H:i') .
                ' - ' . $scheduling->getSystemEnsalementSlots()->getSystemEnsalementSessions()->getEnd()->format('H:i') .
                ' Sala: ' . $scheduling->getSystemEnsalementSlots()->getSystemEnsalementRooms()->getName()), 0, 1, 'L', true);

            $pdf->SetTextColor(33, 37, 41);

            $pdf->SetFont('Helvetica', 'B', 8);
            $pdf->SetFillColor(213, 223, 230);
            $pdf->Cell(0, 6, iconv('utf-8', 'windows-1252//IGNORE', array_search(
                    $scheduling->getSystemEnsalementSlots()->getSystemEnsalementSessions()->getType(),
                    SystemEnsalementSessions::SESSION_TYPES
                ) . ' - ' . array_search(
                    $scheduling->getFormat(),
                    Scheduling::SECTION_FORMATS
                )), 0, 1, 'L', true);

            $pdf->Cell(0, 6, iconv('utf-8', 'windows-1252//IGNORE', $scheduling->getTitle()), 0, 1, 'L', true, $scheduling->getSystemEnsalementSlots()->getLink());

            if ($scheduling->getUserThemes()) {
                $pdf->Cell(0, 6, iconv('utf-8', 'windows-1252//IGNORE', 'Tema' . $scheduling->getUserThemes()->getPosition() .
                    ' - ' . $scheduling->getUserThemes()->getDetails()->getTitle()), 0, 1, 'L', true);
            }

            if ($scheduling->getCoordinatorDebater1()) {
                $pdf->Cell(0, 6, iconv('utf-8', 'windows-1252//IGNORE', array_search($scheduling->getCoordinatorDebater1Type(), Scheduling::COORDINATOR_DEBATER_TYPES) . ':'), 0, 1, 'L', true);

                $coordinatorDebater = $scheduling->getCoordinatorDebater1()->getName() .
                    ' ' . $scheduling->getCoordinatorDebater1()->getInstitutionsPrograms();

                $pdf->SetFont('Helvetica', '', 8);
                $pdf->MultiCell(0, 6, iconv('utf-8', 'windows-1252//IGNORE', $coordinatorDebater), 0, 'L', true);
            }

            if ($scheduling->getCoordinatorDebater2()) {
                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->Cell(0, 6, iconv('utf-8', 'windows-1252//IGNORE', array_search($scheduling->getCoordinatorDebater2Type(), Scheduling::COORDINATOR_DEBATER_TYPES) . ':'), 0, 1, 'L', true);

                $coordinatorDebater2 = $scheduling->getCoordinatorDebater2()->getName() .
                    ' ' . $scheduling->getCoordinatorDebater2()->getInstitutionsPrograms();

                $pdf->SetFont('Helvetica', '', 8);
                $pdf->MultiCell(0, 6, iconv('utf-8', 'windows-1252//IGNORE', $coordinatorDebater2), 0, 'L', true);
            }

            if ($scheduling->getArticles()->count() > 0) {
                foreach ($scheduling->getArticles() as $article) {
                    $pdf->SetFont('Helvetica', 'B', 9);
                    $pdf->Cell(0, 6, iconv('utf-8', 'windows-1252//IGNORE', $edition->getEvent()->getName() .
                        ' ' . $article->getUserArticles()->getId() .
                        ' Tema ' . $article->getUserArticles()->getUserThemes()->getPosition()), 0, 1, 'L', true);

                    $pdf->SetFont('Helvetica', 'B', 10);
                    $pdf->Cell(0, 6, iconv('utf-8', 'windows-1252//IGNORE', $article->getUserArticles()->getTitle()), 0, 1, 'L', true);

                    if ($article->getUserArticles()->getUserArticlesAuthors()->count() > 0) {
                        foreach ($article->getUserArticles()->getUserArticlesAuthors() as $author) {
                            $line = $author->getUserAuthor()->getName() . ' ' . $author->getUserAuthor()->getInstitutionsPrograms();

                            $pdf->SetFont('Helvetica', '', 8);
                            $pdf->MultiCell(0, 6, iconv('utf-8', 'windows-1252//IGNORE', $line), 0, 'L', true);
                        }
                    }
                    $pdf->Ln();
                }
                $pdf->Ln();
            }

            if ($scheduling->getActivity()) {
                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->MultiCell(0, 6, iconv('utf-8', 'windows-1252//IGNORE', $scheduling->getActivity()->getTitle()), 0, 'L', true);

                $pdf->SetFont('Helvetica', '', 8);
                $pdf->MultiCell(0, 6, iconv('utf-8', 'windows-1252//IGNORE', $scheduling->getActivity()->getDescription()), 0, 'L', true);
            }
        }
        $pdf->Output($pdfFullPath, 'F');

        return new BinaryFileResponse($pdfFullPath);
    }

    /**
     * @Route("/{edition}/general/search_filter", name="ensalement_general_search_filter", methods={"POST"})
     *
     * @param Request $request
     * @param Edition $edition
     *
     * @return Response
     */
    public function generalSearchFilter(Request $request, Edition $edition)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $edition || null !== $edition->getDeletedAt()) {
            return new Response('', 404);
        }

        $scheduling = new Scheduling();
        $scheduling->setEdition($edition);
        $form = $this->createForm(EnsalementGeneralSearchType::class, $scheduling, ['edition' => $edition, 'csrf_protection' => false]);
        $form->handleRequest($request);

        return new Response($this->renderView('@Base/ensalamento_classroom_registration/tabs/general/partials/_search_form.html.twig', [
            'edition' => $edition,
            'form' => $form->createView(),
            'entity' => $scheduling,
        ]), 200);
    }

    /**
     * @Route("/general/{id}/edit", name="ensalement_general_edit", methods={"POST"})
     *
     * @param Request $request
     * @param Scheduling $scheduling
     *
     * @return Response
     */
    public function generalEdit(Request $request, Scheduling $scheduling)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $scheduling || null !== $scheduling->getDeletedAt()) {
            return new Response('', 404);
        }

        $form = $this->createForm(EnsalementGeneralType::class, $scheduling, ['csrf_protection' => false]);
        $form->handleRequest($request);

        if (! $form->isSubmitted() || ! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        $em = $this->getDoctrine()->getManager();
        /** @var SchedulingRepository $er */
        $er = $em->getRepository(Scheduling::class);

        if (
            $form->get('systemEnsalementSlots')
            && $form->get('systemEnsalementSlots')->getData()
            && $form->get('systemEnsalementSlots')->getData()->getSystemEnsalementSessions()
        ) {
            /** @var SystemEnsalementSlots $slot */
            $slot = $form->get('systemEnsalementSlots')->getData();

            if ($scheduling->getArticles()->count() > 0) {
                foreach ($scheduling->getArticles() as $article) {
                    $userArticle = $article->getUserArticles();
                    if (! $userArticle || 0 === $userArticle->getUserArticlesAuthors()->count()) {
                        continue;
                    }

                    foreach ($userArticle->getUserArticlesAuthors() as $author) {
                        $count = $er->countByAuthorAndSession(
                            $scheduling->getEdition(),
                            $author->getUserAuthor(),
                            $slot->getSystemEnsalementSessions(),
                            $slot->getSystemEnsalementRooms(),
                            $scheduling
                        );

                        if ($count > 0) {
                            $form->get('systemEnsalementSlots')->addError(
                                new FormError('Um ou mais autores deste artigo já estão vinculados a outra sessão no mesmo dia e horário.')
                            );
                        }
                    }
                }
            }
        }

        if (! $form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                foreach ($error->getOrigin()->getPropertyPath()->getElements() as $element) {
                    if (! empty($errors[$element])) {
                        continue;
                    }

                    $errors[$element] = $error->getMessage();
                }
            }

            return new JsonResponse($errors, 400);
        }

        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/slot/edit/{id}", name="ensalement_slots_edit", methods={"POST"})
     *
     * @param Request $request
     * @param Slot $slot
     *
     * @return Response
     */
    public function slotsEdit(Request $request, Slot $slot)
    {
        $user = $this->getUser();
        $this->isLogged($user);

        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $slot || null !== $slot->getDeletedAt()) {
            return new Response('', 404);
        }

        $data = $request->request->all();

        $slot->setLink($data['link']);

        $em = $this->getDoctrine()->getManager();
        $em->persist($slot);
        $em->flush();

        return new Response(true, 200);
    }
}
