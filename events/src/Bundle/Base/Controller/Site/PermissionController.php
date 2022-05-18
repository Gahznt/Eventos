<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\DivisionCoordinator;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\Permission;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserCommittee;
use App\Bundle\Base\Form\PermissionCommitteeType;
use App\Bundle\Base\Form\PermissionCoordinatorType;
use App\Bundle\Base\Form\PermissionType;
use App\Bundle\Base\Repository\ActivityRepository;
use App\Bundle\Base\Repository\DivisionCoordinatorRepository;
use App\Bundle\Base\Repository\UserCommitteeRepository;
use App\Bundle\Base\Repository\UserRepository;
use App\Bundle\Base\Repository\EventRepository;
use App\Bundle\Base\Traits\AccessControl;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;
use App\Bundle\Base\Services\Helper\Permission as PermissionService;

/**
 * @Route("/permission")
 *
 * Class ActivityController
 * @package App\Bundle\Base\Controller
 */
class PermissionController extends AbstractController
{
    use AccessControl;

    const PAGE_LIMIT = 10;
    const PAGE_NUM_DEFAULT = 1;

    private $userRepository;
    private $permissionService;
    private $divisionCoordinatorRepository;
    private $userCommitteeRepository;
    private $eventRepository;

    public function __construct(
        ActivityRepository $activityRepository,
        Breadcrumbs $breadcrumbs,
        UrlGeneratorInterface $urlGenerator,
        UserRepository $userRepository,
        PermissionService $permissionService,
        DivisionCoordinatorRepository $divisionCoordinatorRepository,
        UserCommitteeRepository $userCommitteeRepository,
        EventRepository $eventRepository
    ) {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Administrative', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('PERMISSION', $urlGenerator->generate('permission_index'));
        $this->userRepository = $userRepository;
        $this->permissionService = $permissionService;
        $this->divisionCoordinatorRepository = $divisionCoordinatorRepository;
        $this->userCommitteeRepository = $userCommitteeRepository;
        $this->eventRepository = $eventRepository;
    }

    /**
     * @Route("/", name="permission_index", methods={"GET"})
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $this->isLogged($this->getUser());
        $this->denyAccessUnlessGranted(Permission::ROLE_ADMIN);

        $this->get('twig')->addGlobal('pageTitle', 'PERMISSION');
        $permission = new Permission();
        $form = $this->createForm(PermissionType::class, $permission);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $levels = $form->getData()->getlevels();
            $search = $form->getData()->getSearch();
            $permission = $form->getData()->getPermissions();

            $users = $this->userRepository->findByFilters(compact('levels', 'search','permission'));
        }else{
            $users = $this->userRepository->findBy([
                'deletedAt' => null,
            ]);
        }

        $permissions = $permissionsSelect = PermissionService::getPermissions();
        $permissions = PermissionService::removeExtraPermission($permissions);

        $levels = PermissionService::getLevels();
        $page = $request->query->get('page', self::PAGE_NUM_DEFAULT);
        $users = $paginator->paginate($users, $page, self::PAGE_LIMIT);

        return $this->render('@Base/permission/index.html.twig', [
            'form' => $form->createView(),
            'levels' => $levels,
            'users' => $users,
            'permissions' => $permissions,
            'permissionsSelect' => $permissionsSelect
        ]);
    }

    /**
     * @Route("/coordinator", name="permission_index_coordinator", methods={"GET", "POST"})
     *
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function indexCoordinator(Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator,  PaginatorInterface $paginator, Request $request)
    {
        $breadcrumbs->addItem('COORDINATOR', $urlGenerator->generate('index'));
        $this->isLogged($this->getUser());
        $this->denyAccessUnlessGranted(Permission::ROLE_ADMIN);

        $this->get('twig')->addGlobal('pageTitle', 'PERMISSIONS_COORD_TITLE');
        $divisionCoordinator = new DivisionCoordinator();
        $form = $this->createForm(PermissionCoordinatorType::class, $divisionCoordinator);
        $form->handleRequest($request);

        $events = $this->eventRepository->withEditionArticles();

        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $divisionCoordinator->setCreatedAt(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($divisionCoordinator);
            $entityManager->flush();
            $this->addFlash('success', 'Permissão salva');
        }

        $users = $this->divisionCoordinatorRepository->findBy(['deletedAt' => null]);
        $page = $request->query->get('page', self::PAGE_NUM_DEFAULT);
        $users = $paginator->paginate($users, $page, self::PAGE_LIMIT);

        return $this->render('@Base/permission/coordinator/index.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
            'events' => $events
        ]);
    }

    /**
     * @Route("/committe", name="permission_index_committe", methods={"GET", "POST"})
     *
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function indexCommitte(Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator,  PaginatorInterface $paginator, Request $request)
    {
        $breadcrumbs->addItem('COORDINATOR', $urlGenerator->generate('index'));
        $this->isLogged($this->getUser());
        $this->denyAccessUnlessGranted(Permission::ROLE_ADMIN);
        $this->get('twig')->addGlobal('pageTitle', 'PERMISSIONS_COMMITTEE_TITLE');

        $committee = new UserCommittee();
        $form = $this->createForm(PermissionCommitteeType::class, $committee);
        $form->handleRequest($request);

        $events = $this->eventRepository->withEditionArticles();

        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $committee->setCreatedAt(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($committee);
            $entityManager->flush();
            $this->addFlash('success', 'Permissão salva');
        }

        $users = $this->userCommitteeRepository->findBy(['deletedAt' => null]);
        $page = $request->query->get('page', self::PAGE_NUM_DEFAULT);
        $users = $paginator->paginate($users, $page, self::PAGE_LIMIT);

        return $this->render('@Base/permission/committee/index.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
            'events' => $events
        ]);
    }

    /**
     * @Route("/{id}/coordinator-delete", name="permission_coordinator_delete", methods={"POST"})
     *
     * @param DivisionCoordinator $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteCoordinator(DivisionCoordinator $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $id->setDeletedAt(new \DateTime());
        $entityManager->persist($id);
        $entityManager->flush();

        $this->addFlash('success', 'Permissão deletada');

        return $this->redirectToRoute('permission_index_coordinator');
    }

    /**
     * @Route("/{id}/save", name="permission_set", methods={"POST"})
     * @param null $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function save($id = null, Request $request)
    {
        $this->isLogged($this->getUser());
        $this->denyAccessUnlessGranted(Permission::ROLE_ADMIN);

        $user = $this->userRepository->find($id);
        $permissions = $request->get('PERMISSIONS', [Permission::ROLE_USER_GUEST]);

        $entityManager = $this->getDoctrine()->getManager();
        $user->setRoles($permissions);
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', 'Permissão salva');


        return $this->redirectToRoute('permission_index');
    }
}
