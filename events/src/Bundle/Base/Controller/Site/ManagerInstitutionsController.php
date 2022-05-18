<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Institution;
use App\Bundle\Base\Form\InstitutionSearchType;
use App\Bundle\Base\Form\InstitutionType;
use App\Bundle\Base\Repository\InstitutionRepository;
use App\Bundle\Base\Services\User as UserService;
use App\Bundle\Base\Traits\AccessControl;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/manager/institutions")
 *
 * Class ManagerInstitutionsController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class ManagerInstitutionsController extends AbstractController
{
    use AccessControl;

    /**
     * @var InstitutionRepository
     */
    private $institutionRepository;

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

    /**
     * ManagerController constructor.
     *
     * @param InstitutionRepository $institutionRepository
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        InstitutionRepository $institutionRepository,
        Breadcrumbs $breadcrumbs,
        UrlGeneratorInterface $urlGenerator,
        UserService $userService
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $this->institutionRepository = $institutionRepository;
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
            ['label' => 'MANAGER_MB_INSTITUTIONS', 'href' => '/gestor/institutions', 'active' => true],
        ];
    }

    /**
     * @Route("/", name="manager_institutions_index", methods={"GET", "POST"})
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

        $this->breadcrumbsService->addItem('MANAGER_INSTITUTIONS');
        $this->get('twig')->addGlobal('pageTitle', 'INSTITUTION_MANAGER');

        $form = $this->createForm(InstitutionSearchType::class, null, ['csrf_protection' => false, 'method' => 'GET']);
        $form->handleRequest($request);

        $criteria = $request->query->get('search', []);

        $results = $paginator->paginate($this->institutionRepository->list($criteria), $request->query->get('page', 1), 20);

        return $this->render('@Base/gestor/tabs/institutions/list.html.twig', [
            'institutions' => $results,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="manager_institutions_new", methods={"GET", "POST"})
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

        $this->breadcrumbsService->addItem('MANAGER_INSTITUTIONS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_INSTITUTIONS_NEW');
        $this->get('twig')->addGlobal('pageTitle', 'INSTITUTION_MANAGER');

        $institution = new Institution();

        $form = $this->createForm(InstitutionType::class, $institution);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->render('@Base/gestor/tabs/institutions/form.html.twig', [
                'form' => $form->createView(),
                'institution' => $institution,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (!$request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (!$form->isValid()) {
            return new Response($this->renderView('@Base/gestor/tabs/institutions/partials/_form.html.twig', [
                'form' => $form->createView(),
                'institution' => $institution,
            ]), 400);
        }

        $institution->setCreatedAt(new \DateTime('now'));

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($institution);
            $entityManager->flush();
            $this->addFlash('success', 'Institution created');
        } catch (\Exception $e) {
            return new Response('', 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }

    /**
     * @Route("/{id}/edit", name="manager_institutions_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Institution $institution
     *
     * @return Response
     */
    public function edit(Request $request, Institution $institution)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('MANAGER_INSTITUTIONS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_INSTITUTIONS_EDIT');
        $this->get('twig')->addGlobal('pageTitle', 'INSTITUTION_MANAGER');

        if (null !== $institution->getDeletedAt()) {
            return new Response('', 500);
        }

        $form = $this->createForm(InstitutionType::class, $institution);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/gestor/tabs/institutions/form.html.twig', [
                'form' => $form->createView(),
                'institution' => $institution,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/gestor/tabs/institutions/partials/_form.html.twig', [
                'form' => $form->createView(),
                'institution' => $institution,
            ]), 400);
        }

        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Institution updated');

        return new Response('', 200);
    }

    /**
     * @Route("/{id}", name="manager_institutions_remove", methods={"DELETE"})
     *
     * @param Request $request
     * @param Institution $institution
     *
     * @return Response
     */
    public function remove(Request $request, Institution $institution)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $institution->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $institution->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($institution);
        $entityManager->flush();
        $this->addFlash('success', 'Institution removed');

        return new Response('', 200);
    }
}
