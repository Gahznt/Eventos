<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Contracts\ApiControllerBase as Controller;
use App\Bundle\Base\Entity\Permission;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserAcademics;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Entity\UserAssociation;
use App\Bundle\Base\Entity\UserInstitutionsPrograms;
use App\Bundle\Base\Entity\UserSearch;
use App\Bundle\Base\Entity\UserThemeKeyword;
use App\Bundle\Base\Form\UserAssociationType;
use App\Bundle\Base\Form\UserEditType;
use App\Bundle\Base\Form\UserInstitutionsProgramsType;
use App\Bundle\Base\Form\UserPasswordChangeType;
use App\Bundle\Base\Form\UserSearchType;
use App\Bundle\Base\Form\UserSimplifiedType;
use App\Bundle\Base\Repository\UserAssociationRepository;
use App\Bundle\Base\Repository\UserRepository;
use App\Bundle\Base\Traits\AccessControl;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;


/**
 * @Route("user")
 * Class UserController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class UserController extends Controller
{
    use AccessControl;

    const PAGE_LIMIT = 10;
    const PAGE_NUM_DEFAULT = 1;
    const INIT_STEP = 1;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserAssociationRepository
     */
    private $userAssociationRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepository
     * @param UserAssociationRepository $associations
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        UserRepository $userRepository,
        UserAssociationRepository $associations,
        Breadcrumbs $breadcrumbs,
        UrlGeneratorInterface $urlGenerator,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Administrative', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Registration Listing');
        $this->userRepository = $userRepository;
        $this->userAssociationRepository = $associations;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/", name="user_default", methods={"GET"})
     */
    public function default()
    {
        $this->isLogged($this->getUser());

        return $this->redirectToRoute('user_show', ['id' => $this->getUser()->getId()], 301);
    }

    /**
     * @Route("/list", name="user_list", methods={"GET"})
     * @param PaginatorInterface $paginator
     * @param Request $request
     *
     * @return Response
     */
    public function list(PaginatorInterface $paginator, Request $request): Response
    {
        $this->isLogged($this->getUser());

        $hasAccess = $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_ADMIN_OPERATIONAL');
        if (! $hasAccess) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $this->get('twig')->addGlobal('pageTitle', 'Users');

        $userSearch = new UserSearch();
        $form = $this->createForm(UserSearchType::class, $userSearch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $since = $form->getData()->getSince();
            $thru = $form->getData()->getThru();
            $levels = $form->getData()->getlevels();
            $payment = $form->getData()->getPayment();
            $paymentDays = $form->getData()->getPaymentDays();
            $search = $form->getData()->getSearch();
            $users = $this->userRepository->list(compact('since', 'thru', 'levels', 'paymentDays', 'payment', 'search'));
        } else {
            $users = [];
        }

        $page = $request->query->get('page', self::PAGE_NUM_DEFAULT);

        if (! empty($users)) {
            $users = $paginator->paginate($users, $page, self::PAGE_LIMIT);
        }

        return $this->render('@Base/users/index.html.twig', [
            'levels' => array_flip(UserAssociation::USER_ASSOCIATIONS_LEVEL),
            'users' => $users,
            'form' => $form->createView(),
            'isFormSubmitted' => $form->isSubmitted(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="user_show", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param User $user
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return Response
     */
    public function show(Request $request, ?User $user, Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator)
    {
        $this->isLogged($this->getUser());

        if (! $user) {
            throw $this->createNotFoundException('User not found');
        }

        //@TODO melhorar helper twig
        $isOwnerUser = $this->isOwnerOrAdmin($user);

        $this->get('twig')->addGlobal('pageTitle', 'USER_TITLE');

        $association = new UserAssociation();
        $form = $this->createForm(UserAssociationType::class, $association);
        $form->handleRequest($request);

        $tab = '';
        if ($form->isSubmitted() && $form->isValid()) {
            $association->setUser($user);
            $association->setLevel(1);

            $value = $association::USER_ASSOCIATIONS_VALUES[$association->getType()] +
                ($association::USER_ASSOCIATIONS_DIVISION_ADITIONAL_VALUE[$association->getType()] * count($association->getAditionals()));

            $association->setValue($value);
            $association->setStatusPay($association::USER_ASSOCIATIONS_STATUS_NOT_PAY);

            if (is_null($association->getId())) {
                $association->setCreatedAt(new DateTime());
            }

            $association->setUpdatedAt(new DateTime());
            $association->setExpiredAt(new DateTime('now +1 year'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($association);
            $entityManager->flush();
        } elseif ($form->isSubmitted() && ! $form->isValid()) {
            $tab = 'new';
        }

        $user = $this->userRepository->find($user);
        $associationsSearch = $this->userAssociationRepository->findBy(['user' => $user->getId()], ['createdAt' => 'DESC']);
        if (! empty($associationsSearch)) {
            $associations['latest'] = array_shift($associationsSearch);
            $associations['history'] = $associationsSearch;
        } else {
            $associations = [];
        }

        return $this->render('@Base/users/show.html.twig', [
            'user' => $user,
            'associations' => $associations,
            'tab' => $tab,
            'form' => $form->createView(),
            'isOwnerUser' => $isOwnerUser,
        ]);
    }

    /**
     * @Route("/{id}/password_change", name="user_password_change", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param User $user
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     *
     * @return Response
     */
    public function password_change(Request $request, ?User $user, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->isLogged($this->getUser());

        if (! $user) {
            throw $this->createNotFoundException('User not found');
        }

        $isOwnerUser = $this->isOwnerOrAdmin($user);

        if (! $isOwnerUser) {
            $user = $this->getUser();
        }

        $this->get('twig')->addGlobal('pageTitle', 'USER_TITLE');

        $form = $this->createForm(UserPasswordChangeType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $data['password']));
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Password updated');
        }
        return $this->render('@Base/users/password_change.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'isOwnerUser' => $isOwnerUser,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET", "POST"})
     *
     * @param User $user
     * @param Request $request
     *
     * @return Response
     */
    public function edit(User $user, Request $request)
    {
        $isOwnerUser = $this->isOwnerOrAdmin($user);

        $this->get('twig')->addGlobal('pageTitle', 'Alteração de dados');
        UserEditType::$step = 999;
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $user->setUpdatedAt(new \DateTime());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'User updated');

                return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
            } else {
                $this->addFlash('warn', 'Check form errors');
            }
        }

        $association = new UserAssociation();
        $formAssociation = $this->createForm(UserAssociationType::class, $association);

        $associationsSearch = $this->userAssociationRepository->findBy(['user' => $user->getId()], ['createdAt' => 'DESC']);

        if (! empty($associationsSearch)) {
            $associations['latest'] = array_shift($associationsSearch);
            $associations['history'] = $associationsSearch;
        } else {
            $associations = [];
        }

        return $this->render('@Base/users/edit/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'submmited' => $form->isSubmitted(),
            'errors' => $form->getErrors(true),
            'associations' => $associations,
            'isOwnerUser' => $isOwnerUser,
            'formAssociation' => $formAssociation->createView(),
            'tab' => '',
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function newUser(Request $request): Response
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN');
        if (! $hasAccess) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $this->get('twig')->addGlobal('pageTitle', 'Cadastro Simplificado');

        $user = new User();
        $form = $this->createForm(UserSimplifiedType::class, $user);
        $form->handleRequest($request);
        $errors = [];
        $submmited = false;

        if ($form->isSubmitted()) {
            $submmited = true;

            UserInstitutionsProgramsType::$validationEnabled = false;
            if ($form->isValid()) {
                $user->setCreatedAt(new \DateTime());
                $user->setRoles([Permission::ROLE_USER_GUEST]);
                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'User created');
            } else {
                $this->addFlash('warn', 'Check form errors');
                $errors = $form->getErrors(true);
            }
        }

        return $this->render('@Base/gestor/tabs/users/form.html.twig', [
            'form' => $form->createView(),
            'submmited' => $submmited,
            'errors' => $errors,
            'tab' => '',
        ]);
    }

    /**
     * @Route("/{id}/confirm_pay", name="user_confirm_pay", methods={"POST"})
     *
     * @param UserAssociation $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function confirmPay(UserAssociation $id)
    {
        $id->setLastPay(new \DateTime());
        $id->setStatusPay(UserAssociation::USER_ASSOCIATIONS_STATUS_PAY);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($id);
        $entityManager->flush();

        $this->addFlash('success', 'Status pay ok');

        return $this->redirectToRoute('user_edit', ['id' => $id->getUser()->getId()], 301);
    }

    //@TODO MELHORAR ESSE METODO

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param User $user
     *
     * @return Response
     */
    public function delete(Request $request, User $user): Response
    {
        $isOwnerUser = $this->isOwnerUser($user);

        if ($isOwnerUser === false) {

            $userId = $user->getId();
            if ($this->isCsrfTokenValid('delete' . $userId, $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $userArticles = $entityManager->getRepository(UserArticles::class)->findBy(['userId' => $userId]);

                if (! empty($userArticles)) {
                    $this->addFlash('error', "User Can't be deleted!");
                    return $this->redirectToRoute('user_show', ['id' => $userId]);
                }

                $userPrograms =
                    $entityManager->getRepository(UserInstitutionsPrograms::class)->findBy(['user' => $userId]);
                $userKeywords =
                    $entityManager->getRepository(UserThemeKeyword::class)->findBy(['user' => $userId]);
                $userAcademics =
                    $entityManager->getRepository(UserAcademics::class)->findBy(['user' => $userId]);

                $userRelations = array_merge($userAcademics, $userKeywords, $userPrograms);
                foreach ($userRelations as $userData) {
                    $entityManager->remove($userData);
                }
                $entityManager->remove($user);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('user_list');
    }
}
