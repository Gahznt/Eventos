<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Institution;
use App\Bundle\Base\Entity\Permission;
use App\Bundle\Base\Entity\Program;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserConsents;
use App\Bundle\Base\Entity\UserInstitutionsPrograms;
use App\Bundle\Base\Form\ProgramType;
use App\Bundle\Base\Repository\CityRepository;
use App\Bundle\Base\Repository\InstitutionRepository;
use App\Bundle\Base\Repository\ProgramRepository;
use App\Bundle\Base\Repository\UserRepository;
use App\Bundle\Base\Services\User as UserService;
use App\Bundle\Base\Services\UserConsents as UserConsentsService;
use App\Bundle\Base\Traits\AccessControl;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/manager")
 *
 * Class ManagerProgramsController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class ManagerProgramsController extends AbstractController
{
    use AccessControl;

    /**
     * @var InstitutionRepository
     */
    private $institutionRepository;

    /**
     * @var ProgramRepository
     */
    private $programRepository;

    /**
     * @var Breadcrumbs
     */
    private $breadcrumbsService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var UserService
     */
    private $userService;

    private UserRepository $userRepository;

    private CityRepository $cityRepository;

    private UserPasswordEncoderInterface $passwordEncoder;

    private UserConsentsService $userConsentsService;

    public function __construct(
        InstitutionRepository        $institutionRepository,
        Breadcrumbs                  $breadcrumbs,
        UrlGeneratorInterface        $urlGenerator,
        UserService                  $userService,
        ProgramRepository            $programRepository,
        UserRepository               $userRepository,
        CityRepository               $cityRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        UserConsentsService          $userConsentsService
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $this->institutionRepository = $institutionRepository;
        $this->programRepository = $programRepository;
        $this->breadcrumbsService = $breadcrumbs;
        $this->urlGenerator = $urlGenerator;
        $this->userService = $userService;
        $this->userRepository = $userRepository;
        $this->cityRepository = $cityRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->userConsentsService = $userConsentsService;
    }

    /**
     * @return array
     */
    protected function getMenuBreadcumb()
    {
        return [
            ['label' => 'MANAGER_MB_DASHBOARD', 'href' => '/'],
            ['label' => 'MANAGER_MB_INSTITUTIONS', 'href' => '/gestor', 'active' => true],
        ];
    }

    /**
     * @Route("/{institution}/programs", name="manager_programs_index", methods={"GET"})
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param Institution $institution
     *
     * @return Response
     */
    public function programsIndex(PaginatorInterface $paginator, Request $request, Institution $institution)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('MANAGER_INSTITUTIONS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_PROGRAMS');
        $this->get('twig')->addGlobal('pageTitle', 'INSTITUTION_MANAGER');

        if (! $institution) {
            return new Response('', 404);
        }

        $results = $paginator->paginate($this->programRepository->list($institution->getId()), $request->query->get('page', 1), 20);

        return $this->render('@Base/gestor/tabs/programs/list.html.twig', [
            'institution' => $institution,
            'programs' => $results,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
        ]);
    }

    /**
     * @Route("/{institution}/programs/new", name="manager_programs_new", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function programsNew(Request $request, Institution $institution)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $institution) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('MANAGER_INSTITUTIONS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_PROGRAMS', $this->urlGenerator->generate('manager_programs_index', ['institution' => $institution->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_PROGRAMS_NEW');
        $this->get('twig')->addGlobal('pageTitle', 'INSTITUTIONS_MANAGER');

        $program = new Program();
        $program->setInstitution($institution);

        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $request->isXmlHttpRequest()) {

            $email = $form->get('email')->getData();
            $user = $this->userRepository->findByIdentifier('email', $email);

            if ($user instanceof User) {
                $form->get('email')->addError(new FormError('E-mail já cadastrado'));
            }

            if ($form->isValid()) {
                try {
                    $program->setCreatedAt(new \DateTime('now'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($program);

                    $this->createUser($program, $request);

                    $entityManager->flush();

                    $this->addFlash('success', 'Program created');

                    return new JsonResponse([], 201);

                } catch (\Exception $e) {
                    return new Response($e->getMessage(), 500);
                }
            } else {
                return new Response($this->renderView('@Base/gestor/tabs/programs/partials/_form.html.twig', [
                    'institution' => $institution,
                    'form' => $form->createView(),
                    'program' => $program,
                ]), 400);
            }
        }

        return $this->render('@Base/gestor/tabs/programs/form.html.twig', [
            'institution' => $institution,
            'form' => $form->createView(),
            'program' => $program,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
        ]);
    }

    /**
     * @Route("/programs/{program}/edit", name="manager_programs_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Program $program
     *
     * @return Response
     */
    public function programsEdit(Request $request, Program $program)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (null !== $program->getDeletedAt()) {
            return new Response('', 500);
        }

        $this->breadcrumbsService->addItem('MANAGER_INSTITUTIONS', $this->urlGenerator->generate('manager_institutions_index'));
        $this->breadcrumbsService->addItem('MANAGER_PROGRAMS', $this->urlGenerator->generate('manager_programs_index', ['institution' => $program->getInstitution()->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_PROGRAMS_EDIT');
        $this->get('twig')->addGlobal('pageTitle', 'INSTITUTION_MANAGER');

        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $request->isXmlHttpRequest()) {

            $email = $form->get('email')->getData();
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (
                $user instanceof User
                && (
                    ! $program->getUser()
                    || $user->getId() !== $program->getUser()->getId()
                )
            ) {
                $form->get('email')->addError(new FormError('E-mail já cadastrado'));
            }

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($program);

                $this->createUser($program, $request);

                $entityManager->flush();
                $this->addFlash('success', 'Program updated');

                return new Response('', 200);

            } else {
                return new Response($this->renderView('@Base/gestor/tabs/programs/partials/_form.html.twig', [
                    'institution' => $program->getInstitution(),
                    'form' => $form->createView(),
                    'program' => $program,
                ]), 400);
            }
        }

        return $this->render('@Base/gestor/tabs/programs/form.html.twig', [
            'institution' => $program->getInstitution(),
            'form' => $form->createView(),
            'program' => $program,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
        ]);
    }

    /**
     * @Route("/programs/{program}", name="manager_programs_remove", methods={"DELETE"})
     *
     * @param Request $request
     * @param Program $program
     *
     * @return Response
     */
    public function programsRemove(Request $request, Program $program)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $program->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $program->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($program);
        $entityManager->flush();
        $this->addFlash('success', 'Program removed');

        return new Response('', 200);
    }

    private function createUser(Program $program, Request $request): void
    {
        if ($program->getUser()) {
            return;
        }

        $entityManager = $this->getDoctrine()->getManager();

        // User
        $user = new User();

        $user->setLocale(User::USER_LOCALE_PT);
        $user->setName($program->getName());
        $user->setEmail($program->getEmail());
        $user->setIdentifier('PG' . time());
        $user->setRecordType(User::USER_RECORD_TYPE_BRAZILIAN);
        $user->setIsForeignUseCpf(User::USER_FOREIGN_USE_CPF_NO);
        $user->setIsForeignUsePassport(User::USER_FOREIGN_USE_PASSPORT_AUTOMATIC);

        $user->setBirthday(new \DateTime());
        $user->setPhone($program->getPhone());
        $user->setCellphone($program->getCellphone());
        $user->setStreet($program->getStreet());
        $user->setNumber($program->getNumber());
        $user->setComplement($program->getComplement());
        $user->setZipcode($program->getZipcode());
        $user->setNeighborhood($program->getNeighborhood());

        $user->setCity($program->getCity());

        $user->setPassword($this->passwordEncoder->encodePassword($user, $program->getEmail()));
        $user->setRoles([Permission::ROLE_USER_GUEST]);

        $institutionsPrograms = new UserInstitutionsPrograms();
        $institutionsPrograms->setInstitutionFirstId($program->getInstitution());
        $institutionsPrograms->setProgramFirstId($program);

        $user->setInstitutionsPrograms($institutionsPrograms);

        $entityManager->persist($user);

        // UserConsents
        $consents = new UserConsents();
        $consents->setCreatedAt(new \DateTime());
        $consents->setIp($request->getClientIp());
        $consents->setStatus(UserConsents::USER_CONSENTS_STATUS_ACCEPT);
        $consents->setType(UserConsents::USER_CONSENTS_TYPE_REGISTER);
        $consents->setUser($user);

        $entityManager->persist($consents);

        // Program
        $program->setUser($user);

        $entityManager->persist($program);

        $entityManager->flush();
    }
}
