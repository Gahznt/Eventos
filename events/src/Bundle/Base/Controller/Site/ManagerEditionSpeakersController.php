<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Certificate;
use App\Bundle\Base\Entity\Speaker;
use App\Bundle\Base\Form\SpeakerType;
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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/manager/edition")
 *
 * Class ManagerEditionSpeakersController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class ManagerEditionSpeakersController extends AbstractController
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
     * @var string
     */
    private $uploadPath = Speaker::UPLOAD_PATH;

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

        $this->filesystem = new Filesystem();

        $this->uploadPath = str_replace('#KERNEL#', $parameterBag->get('kernel.project_dir'), $this->uploadPath);
        if (! $this->filesystem->exists($this->uploadPath)) {
            $this->filesystem->mkdir($this->uploadPath, 0755);
        }
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
     * @Route("/{editionId}/speakers", name="manager_speakers_index", methods={"GET"})
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     *
     * @return Response
     */
    public function speakersIndex(PaginatorInterface $paginator, Request $request)
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
        $this->breadcrumbsService->addItem('MANAGER_SPEAKERS');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $results = $paginator->paginate($this->speakerRepository->list($edition->getId()), $request->query->get('page', 1), 20);

        return $this->render('@Base/gestor/tabs/speakers/list.html.twig', [
            'event' => $edition->getEvent(),
            'edition' => $edition,
            'speakers' => $results,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
        ]);
    }

    /**
     * @param FormInterface $form
     * @param Speaker $speaker
     * @param SluggerInterface $slugger
     * @param ParameterBagInterface $parameterBag
     *
     * @throws \Exception
     */
    protected function saveSpeakerPicture(FormInterface $form, Speaker $speaker, SluggerInterface $slugger, ParameterBagInterface $parameterBag)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $pictureFile = $form->get('picture')->getData();

        if ($pictureFile) {
            $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();
            $newFilename = ltrim(mb_substr($newFilename, -254), '-');
            try {
                $pictureFile->move(
                    $this->uploadPath,
                    $newFilename
                );

                $speaker->setPicturePath($newFilename);
            } catch (FileException $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }

    /**
     * @Route("/{editionId}/speakers/new", name="manager_speakers_new", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param ParameterBagInterface $parameterBag
     *
     * @return JsonResponse|Response
     * @throws \Exception
     */
    public function speakersNew(Request $request, SluggerInterface $slugger, ParameterBagInterface $parameterBag)
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
        $this->breadcrumbsService->addItem('MANAGER_SPEAKERS', $this->urlGenerator->generate('manager_speakers_index', ['editionId' => $edition->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_SPEAKERS_NEW');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $speaker = new Speaker();

        $speaker->setEdition($edition);

        $form = $this->createForm(SpeakerType::class, $speaker);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/gestor/tabs/speakers/form.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'speaker' => $speaker,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/gestor/tabs/speakers/partials/_form.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'speaker' => $speaker,
            ]), 400);
        }

        $speaker->setCreatedAt(new \DateTime('now'));

        $this->saveSpeakerPicture($form, $speaker, $slugger, $parameterBag);

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($speaker);
            $entityManager->flush();
            $this->addFlash('success', 'Speaker created');
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }

    /**
     * @Route("/speakers/{id}/edit", name="manager_speakers_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Speaker $speaker
     * @param SluggerInterface $slugger
     * @param ParameterBagInterface $parameterBag
     *
     * @return Response
     * @throws \Exception
     */
    public function speakersEdit(Request $request, Speaker $speaker, SluggerInterface $slugger, ParameterBagInterface $parameterBag)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (null !== $speaker->getDeletedAt()) {
            return new Response('', 500);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS', $this->urlGenerator->generate('manager_editions_index', ['eventId' => $speaker->getEdition()->getEvent()->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_SPEAKERS', $this->urlGenerator->generate('manager_speakers_index', ['editionId' => $speaker->getEdition()->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_SPEAKERS_EDIT');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $form = $this->createForm(SpeakerType::class, $speaker);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/gestor/tabs/speakers/form.html.twig', [
                'event' => $speaker->getedition()->getEvent(),
                'edition' => $speaker->getedition(),
                'form' => $form->createView(),
                'speaker' => $speaker,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/gestor/tabs/speakers/partials/_form.html.twig', [
                'event' => $speaker->getedition()->getEvent(),
                'edition' => $speaker->getedition(),
                'form' => $form->createView(),
                'speaker' => $speaker,
            ]), 400);
        }

        $this->saveSpeakerPicture($form, $speaker, $slugger, $parameterBag);

        $this->getDoctrine()->getManager()->flush();

        return new Response('', 200);
    }

    /**
     * @Route("/speakers/{id}", name="manager_speakers_remove", methods={"DELETE"})
     *
     * @param Request $request
     * @param Speaker $speaker
     *
     * @return Response
     */
    public function speakersRemove(Request $request, Speaker $speaker)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $speaker->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $speaker->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($speaker);
        $entityManager->flush();
        $this->addFlash('success', 'Speaker updated');

        return new Response('', 200);
    }
}
