<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Subsection;
use App\Bundle\Base\Form\SubsectionType;
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
 * @Route("/manager/edition")
 *
 * Class ManagerEditionSubsectionsController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class ManagerEditionSubsectionsController extends AbstractController
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
     * @Route("/{editionId}/subsections", name="manager_subsections_index", methods={"GET"})
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     *
     * @return Response
     */
    public function subsectionsIndex(PaginatorInterface $paginator, Request $request)
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
        $this->breadcrumbsService->addItem('MANAGER_SUBSECTIONS');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $results = $paginator->paginate($this->subsectionRepository->list($edition->getId()), $request->query->get('page', 1), 20);

        return $this->render('@Base/gestor/tabs/subsections/list.html.twig', [
            'event' => $edition->getEvent(),
            'edition' => $edition,
            'subsections' => $results,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
        ]);
    }

    /**
     * @Route("/{editionId}/subsections/new", name="manager_subsections_new", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function subsectionsNew(Request $request)
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
        $this->breadcrumbsService->addItem('MANAGER_SUBSECTIONS', $this->urlGenerator->generate('manager_subsections_index', ['editionId' => $edition->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_SUBSECTIONS_NEW');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $subsection = new Subsection();

        $subsection->setEdition($edition);

        $form = $this->createForm(SubsectionType::class, $subsection);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/gestor/tabs/subsections/form.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'subsection' => $subsection,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/gestor/tabs/subsections/partials/_form.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'subsection' => $subsection,
            ]), 400);
        }

        $subsection->setCreatedAt(new \DateTime('now'));

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($subsection);
            $entityManager->flush();
            $this->addFlash('success', 'Subsection created');
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }

    /**
     * @Route("/subsections/{id}/edit", name="manager_subsections_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Subsection $subsection
     *
     * @return Response
     */
    public function subsectionsEdit(Request $request, Subsection $subsection)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (null !== $subsection->getDeletedAt()) {
            return new Response('', 500);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS', $this->urlGenerator->generate('manager_editions_index', ['eventId' => $subsection->getEdition()->getEvent()->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_SUBSECTIONS', $this->urlGenerator->generate('manager_subsections_index', ['editionId' => $subsection->getEdition()->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_SUBSECTIONS_EDIT');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $form = $this->createForm(SubsectionType::class, $subsection);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/gestor/tabs/subsections/form.html.twig', [
                'event' => $subsection->getedition()->getEvent(),
                'edition' => $subsection->getedition(),
                'form' => $form->createView(),
                'subsection' => $subsection,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/gestor/tabs/subsections/partials/_form.html.twig', [
                'event' => $subsection->getedition()->getEvent(),
                'edition' => $subsection->getedition(),
                'form' => $form->createView(),
                'subsection' => $subsection,
            ]), 400);
        }

        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Subsection updated');

        return new Response('', 200);
    }

    /**
     * @Route("/subsections/{id}", name="manager_subsections_remove", methods={"DELETE"})
     *
     * @param Request $request
     * @param Subsection $subsection
     *
     * @return Response
     */
    public function subsectionsRemove(Request $request, Subsection $subsection)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $subsection->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $subsection->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($subsection);
        $entityManager->flush();
        $this->addFlash('success', 'Subsection removed');

        return new Response('', 200);
    }
}
