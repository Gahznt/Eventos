<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\EditionDiscount;
use App\Bundle\Base\Form\EditionDiscountType;
use App\Bundle\Base\Repository\EditionDiscountRepository;
use App\Bundle\Base\Repository\EditionFileRepository;
use App\Bundle\Base\Repository\EditionRepository;
use App\Bundle\Base\Repository\EventRepository;
use App\Bundle\Base\Repository\SpeakerRepository;
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
 * @Route("/manager/edition")
 *
 * Class ManagerEditionDiscountsController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class ManagerEditionDiscountsController extends AbstractController
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
     * @var EditionDiscountRepository
     */
    private $editionDiscountRepository;

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
     * @param EditionDiscountRepository $editionDiscountRepository
     * @param SpeakerRepository $speakerRepository
     * @param EditionFileRepository $fileRepository
     * @param ParameterBagInterface $parameterBag
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        EventRepository $eventRepository,
        EditionRepository $editionRepository,
        EditionDiscountRepository $editionDiscountRepository,
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
        $this->editionDiscountRepository = $editionDiscountRepository;
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
     * @Route("/{editionId}/discounts", name="manager_edition_discounts_index", methods={"GET"})
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
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

        $edition = $this->editionRepository->findOneBy([
            'id' => $request->get('editionId'),
            'deletedAt' => null,
        ]);

        if (! $edition) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS', $this->urlGenerator->generate('manager_editions_index', ['eventId' => $edition->getEvent()->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_DISCOUNTS');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $results = $paginator->paginate($this->editionDiscountRepository->list($edition->getId()), $request->query->get('page', 1), 20);

        return $this->render('@Base/gestor/tabs/discounts/list.html.twig', [
            'event' => $edition->getEvent(),
            'edition' => $edition,
            'editionDiscounts' => $results,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
            'TYPES' => array_flip(EditionDiscount::TYPES),
        ]);
    }

    /**
     * @Route("/{editionId}/discounts/new", name="manager_edition_discounts_new", methods={"GET", "POST"})
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

        $edition = $this->editionRepository->findOneBy([
            'id' => $request->get('editionId'),
            'deletedAt' => null,
        ]);

        if (! $edition) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS', $this->urlGenerator->generate('manager_editions_index', ['eventId' => $edition->getEvent()->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_DISCOUNTS', $this->urlGenerator->generate('manager_edition_discounts_index', ['editionId' => $edition->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_DISCOUNTS_NEW');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $editionDiscount = new EditionDiscount();

        $editionDiscount->setEdition($edition);

        $form = $this->createForm(EditionDiscountType::class, $editionDiscount);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/gestor/tabs/discounts/form.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'editionDiscount' => $editionDiscount,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            //return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/gestor/tabs/discounts/partials/_form.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'editionDiscount' => $editionDiscount,
            ]), 400);
        }

        $editionDiscount->setCreatedAt(new \DateTime('now'));

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($editionDiscount);
            $entityManager->flush();
            $this->addFlash('success', 'Desconto criado');
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }

    /**
     * @Route("/discounts/{id}/edit", name="manager_edition_discounts_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param EditionDiscount $editionDiscount
     *
     * @return Response
     */
    public function edit(Request $request, EditionDiscount $editionDiscount)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (null !== $editionDiscount->getDeletedAt()) {
            return new Response('', 500);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS', $this->urlGenerator->generate('manager_editions_index', ['eventId' => $editionDiscount->getEdition()->getEvent()->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_DISCOUNTS', $this->urlGenerator->generate('manager_edition_discounts_index', ['editionId' => $editionDiscount->getEdition()->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_DISCOUNTS_EDIT');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $form = $this->createForm(EditionDiscountType::class, $editionDiscount);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/gestor/tabs/discounts/form.html.twig', [
                'event' => $editionDiscount->getedition()->getEvent(),
                'edition' => $editionDiscount->getedition(),
                'form' => $form->createView(),
                'editionDiscount' => $editionDiscount,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/gestor/tabs/discounts/partials/_form.html.twig', [
                'event' => $editionDiscount->getedition()->getEvent(),
                'edition' => $editionDiscount->getedition(),
                'form' => $form->createView(),
                'editionDiscount' => $editionDiscount,
            ]), 400);
        }

        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Desconto atualizado');

        return new Response('', 200);
    }

    /**
     * @Route("/discounts/{id}", name="manager_edition_discounts_remove", methods={"DELETE"})
     *
     * @param Request $request
     * @param EditionDiscount $editionDiscount
     *
     * @return Response
     */
    public function remove(Request $request, EditionDiscount $editionDiscount)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $editionDiscount->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $editionDiscount->setIsActive(false);
        $editionDiscount->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($editionDiscount);
        $entityManager->flush();
        $this->addFlash('success', 'Desconto excluído');

        return new Response('', 200);
    }
}
