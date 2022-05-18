<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\EditionPaymentMode;
use App\Bundle\Base\Form\EditionPaymentModeType;
use App\Bundle\Base\Repository\EditionFileRepository;
use App\Bundle\Base\Repository\EditionPaymentModeRepository;
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
 * Class ManagerEditionPaymentsController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class ManagerEditionPaymentsController extends AbstractController
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
     * @var EditionPaymentModeRepository
     */
    private $editionPaymentModeRepository;

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
     * @param EditionPaymentModeRepository $editionPaymentModeRepository
     * @param SpeakerRepository $speakerRepository
     * @param EditionFileRepository $fileRepository
     * @param ParameterBagInterface $parameterBag
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        EventRepository $eventRepository,
        EditionRepository $editionRepository,
        EditionPaymentModeRepository $editionPaymentModeRepository,
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
        $this->editionPaymentModeRepository = $editionPaymentModeRepository;
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
     * @Route("/{editionId}/payments", name="manager_edition_payments_index", methods={"GET"})
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     *
     * @return Response
     */
    public function paymentsIndex(PaginatorInterface $paginator, Request $request)
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
        $this->breadcrumbsService->addItem('MANAGER_PAYMENTS');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $results = $paginator->paginate($this->editionPaymentModeRepository->list($edition->getId()), $request->query->get('page', 1), 20);

        return $this->render('@Base/gestor/tabs/payments/list.html.twig', [
            'event' => $edition->getEvent(),
            'edition' => $edition,
            'editionPaymentModes' => $results,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
            'TYPES' => array_flip(EditionPaymentMode::TYPES),
        ]);
    }

    /**
     * @Route("/{editionId}/payments/new", name="manager_edition_payments_new", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function paymentsNew(Request $request)
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
        $this->breadcrumbsService->addItem('MANAGER_PAYMENTS', $this->urlGenerator->generate('manager_edition_payments_index', ['editionId' => $edition->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_PAYMENTS_NEW');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $editionPaymentMode = new EditionPaymentMode();

        $editionPaymentMode->setEdition($edition);

        $form = $this->createForm(EditionPaymentModeType::class, $editionPaymentMode);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/gestor/tabs/payments/form.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'editionPaymentMode' => $editionPaymentMode,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/gestor/tabs/payments/partials/_form.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'editionPaymentMode' => $editionPaymentMode,
            ]), 400);
        }

        $editionPaymentMode->setCreatedAt(new \DateTime('now'));

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($editionPaymentMode);
            $entityManager->flush();
            $this->addFlash('success', 'Forma de pagamento criada');
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }

    /**
     * @Route("/payments/{id}/edit", name="manager_edition_payments_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param EditionPaymentMode $editionPaymentMode
     *
     * @return Response
     */
    public function paymentsEdit(Request $request, EditionPaymentMode $editionPaymentMode)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (null !== $editionPaymentMode->getDeletedAt()) {
            return new Response('', 500);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS', $this->urlGenerator->generate('manager_editions_index', ['eventId' => $editionPaymentMode->getEdition()->getEvent()->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_PAYMENTS', $this->urlGenerator->generate('manager_edition_payments_index', ['editionId' => $editionPaymentMode->getEdition()->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_PAYMENTS_EDIT');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $form = $this->createForm(EditionPaymentModeType::class, $editionPaymentMode);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/gestor/tabs/payments/form.html.twig', [
                'event' => $editionPaymentMode->getedition()->getEvent(),
                'edition' => $editionPaymentMode->getedition(),
                'form' => $form->createView(),
                'editionPaymentMode' => $editionPaymentMode,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/gestor/tabs/payments/partials/_form.html.twig', [
                'event' => $editionPaymentMode->getedition()->getEvent(),
                'edition' => $editionPaymentMode->getedition(),
                'form' => $form->createView(),
                'editionPaymentMode' => $editionPaymentMode,
            ]), 400);
        }

        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Forma de pagamento atualizada');

        return new Response('', 200);
    }

    /**
     * @Route("/payments/{id}", name="manager_edition_payments_remove", methods={"DELETE"})
     *
     * @param Request $request
     * @param EditionPaymentMode $editionPaymentMode
     *
     * @return Response
     */
    public function paymentsRemove(Request $request, EditionPaymentMode $editionPaymentMode)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $editionPaymentMode->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $editionPaymentMode->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($editionPaymentMode);
        $entityManager->flush();
        $this->addFlash('success', 'Forma de pagamento excluída');

        return new Response('', 200);
    }
}
