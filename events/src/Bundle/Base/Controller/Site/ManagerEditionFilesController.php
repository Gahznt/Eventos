<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\EditionFile;
use App\Bundle\Base\Entity\Speaker;
use App\Bundle\Base\Form\EditionFileType;
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
 * Class ManagerEditionFilesController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class ManagerEditionFilesController extends AbstractController
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
    private $uploadPath = EditionFile::UPLOAD_PATH;

    /**
     * ManagerEditionFilesController constructor.
     *
     * @param EventRepository $eventRepository
     * @param EditionRepository $editionRepository
     * @param SubsectionRepository $subsectionRepository
     * @param SpeakerRepository $speakerRepository
     * @param EditionFileRepository $fileRepository
     * @param ParameterBagInterface $parameterBag
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     * @param UserService $userService
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
     * @Route("/{editionId}/files", name="manager_files_index", methods={"GET"})
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     *
     * @return Response
     */
    public function filesIndex(PaginatorInterface $paginator, Request $request)
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
        $this->breadcrumbsService->addItem('MANAGER_FILES');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $results = $paginator->paginate($this->fileRepository->list($edition->getId()), $request->query->get('page', 1), 20);

        return $this->render('@Base/gestor/tabs/files/list.html.twig', [
            'event' => $edition->getEvent(),
            'edition' => $edition,
            'files' => $results,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
        ]);
    }

    /**
     * @param FormInterface $form
     * @param EditionFile $file
     * @param SluggerInterface $slugger
     * @param ParameterBagInterface $parameterBag
     */
    protected function saveEditionFile(FormInterface $form, EditionFile $file, SluggerInterface $slugger, ParameterBagInterface $parameterBag)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $fileFile = $form->get('file')->getData();

        if ($fileFile) {
            $originalFilename = pathinfo($fileFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $ext = $fileFile->guessExtension();
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $ext;
            $newFilename = ltrim(mb_substr($newFilename, -254), '-');
            try {
                $fileFile->move(
                    $this->uploadPath,
                    $newFilename
                );

                $file->setFileName(mb_substr($originalFilename . '.' . $ext, -254));
                $file->setFilePath($newFilename);
            } catch (\Exception $e) {

            }
        }
    }

    /**
     * @Route("/{editionId}/files/new", name="manager_files_new", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param SluggerInterface $slugger
     * @param ParameterBagInterface $parameterBag
     *
     * @return Response
     */
    public function filesNew(Request $request, SluggerInterface $slugger, ParameterBagInterface $parameterBag)
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
        $this->breadcrumbsService->addItem('MANAGER_FILES', $this->urlGenerator->generate('manager_files_index', ['editionId' => $edition->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_FILES_NEW');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $file = new EditionFile();

        $file->setEdition($edition);

        $form = $this->createForm(EditionFileType::class, $file);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/gestor/tabs/files/form.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'file' => $file,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/gestor/tabs/files/partials/_form.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'file' => $file,
            ]), 400);
        }

        $file->setCreatedAt(new \DateTime('now'));

        $this->saveEditionFile($form, $file, $slugger, $parameterBag);

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($file);
            $entityManager->flush();
            $this->addFlash('success', 'File uploaded');
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }

    /**
     * @Route("/files/{id}/edit", name="manager_files_edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param EditionFile $file
     * @param SluggerInterface $slugger
     * @param ParameterBagInterface $parameterBag
     *
     * @return Response
     */
    public function filesEdit(Request $request, EditionFile $file, SluggerInterface $slugger, ParameterBagInterface $parameterBag)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (null !== $file->getDeletedAt()) {
            return new Response('', 500);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS', $this->urlGenerator->generate('manager_editions_index', ['eventId' => $file->getEdition()->getEvent()->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_FILES', $this->urlGenerator->generate('manager_files_index', ['editionId' => $file->getEdition()->getId()]));
        $this->breadcrumbsService->addItem('MANAGER_FILES_EDIT');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $form = $this->createForm(EditionFileType::class, $file);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/gestor/tabs/files/form.html.twig', [
                'event' => $file->getedition()->getEvent(),
                'edition' => $file->getedition(),
                'form' => $form->createView(),
                'file' => $file,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/gestor/tabs/files/partials/_form.html.twig', [
                'event' => $file->getedition()->getEvent(),
                'edition' => $file->getedition(),
                'form' => $form->createView(),
                'file' => $file,
            ]), 400);
        }

        $this->saveEditionFile($form, $file, $slugger, $parameterBag);

        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'File updated');

        return new Response('', 200);
    }

    /**
     * @Route("/files/{id}", name="manager_files_remove", methods={"DELETE"})
     *
     * @param Request $request
     * @param EditionFile $file
     *
     * @return Response
     */
    public function filesRemove(Request $request, EditionFile $file)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $file->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $file->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($file);
        $entityManager->flush();
        $this->addFlash('success', 'File removed');

        return new Response('', 200);
    }
}
