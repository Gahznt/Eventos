<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\ArticleEvaluationSearch;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\SystemEvaluation;
use App\Bundle\Base\Entity\SystemEvaluationIndications;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Form\ArticleEvaluationSearchType;
use App\Bundle\Base\Form\ArticleEvaluationType;
use App\Bundle\Base\Repository\SystemEvaluationIndicationsRepository;
use App\Bundle\Base\Repository\UserArticlesRepository;
use App\Bundle\Base\Services\SystemEvaluationConfig;
use App\Bundle\Base\Services\SystemEvaluationLog as SystemEvaluationLogService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("article_evaluation")
 * Class ArticleEvaluationController
 * @package App\Bundle\Base\Controller\Site
 */
class ArticleEvaluationController extends AbstractController
{

    /**
     * @var SystemEvaluationLogService
     */
    private $systemEvaluationLogService;
    /**
     * @var UserArticlesRepository
     */
    private $userArticlesRepository;
    /**
     * @var SystemEvaluationIndicationsRepository
     */
    private $systemEvaluationIndicationsRepository;
    /**
     * @var SystemEvaluationConfig
     */
    private $systemEvaluationConfigService;

    /**
     *
     */
    const PAGE_LIMIT = 10;
    /**
     *
     */
    const PAGE_NUM_DEFAULT = 1;

    /**
     * ArticleEvaluationController constructor.
     *
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     * @param SystemEvaluationLogService $evaluationLogService
     * @param SystemEvaluationConfig $systemEvaluationConfig
     * @param UserArticlesRepository $userArticlesRepository
     * @param SystemEvaluationIndicationsRepository $systemEvaluationIndicationsRepository
     */
    public function __construct(
        Breadcrumbs $breadcrumbs,
        UrlGeneratorInterface $urlGenerator,
        SystemEvaluationLogService $evaluationLogService,
        SystemEvaluationConfig $systemEvaluationConfig,
        UserArticlesRepository $userArticlesRepository,
        SystemEvaluationIndicationsRepository $systemEvaluationIndicationsRepository
    ) {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Administrative', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('System Evaluation');
        $this->systemEvaluationLogService = $evaluationLogService;
        $this->userArticlesRepository = $userArticlesRepository;
        $this->systemEvaluationIndicationsRepository = $systemEvaluationIndicationsRepository;
        $this->systemEvaluationConfigService = $systemEvaluationConfig;
    }

    /**
     * @Route("/{edition}/index", name="article_evaluation_index", methods={"GET"})
     *
     * @param Edition $edition
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Edition $edition, PaginatorInterface $paginator, Request $request)
    {
        if (
            ! $edition
            || null !== $edition->getDeletedAt()
            || ! $this->systemEvaluationConfigService->get($edition)
            || ! $this->systemEvaluationConfigService->get($edition)->getEvaluateArticleAvaliable()
        ) {
            return new Response('', 404);
        }

        $this->get('twig')->addGlobal('pageTitle', 'Avaliação');
        $articleEvaluationSearch = new ArticleEvaluationSearch();
        $form = $this->createForm(ArticleEvaluationSearchType::class, $articleEvaluationSearch, ['edition' => $edition]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $division = $form->getData()->getDivision();
            $userThemes = $form->getData()->getUserThemes();
            $search = $form->getData()->getSearch();

            $articles = $this->systemEvaluationIndicationsRepository->findByFilters(['userEvaluator' => $this->getUser(), 'division' => $division, 'userThemes' => $userThemes, 'search' => $search]);
        } else {
            $articles = $this->systemEvaluationIndicationsRepository->findByFilters(['userEvaluator' => $this->getUser()]);
        }

        $page = $request->query->get('page', self::PAGE_NUM_DEFAULT);

        $articles = $paginator->paginate($articles, $page, self::PAGE_LIMIT);

        return $this->render('@Base/article_evaluation/index.html.twig', [
            'form'     => $form->createView(),
            'articles' => $articles,
            'edition' => $edition
        ]);
    }

    /**
     * @Route("/{systemEvaluationIndications}/{edition}/show", name="article_evaluation_show", methods={"GET","POST"})
     *
     * @param SystemEvaluationIndications $systemEvaluationIndications
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function show(SystemEvaluationIndications $systemEvaluationIndications, Edition $edition, Request $request)
    {

        if ($systemEvaluationIndications->getValid()) {
            return $this->redirect($this->generateUrl('article_evaluation_index'));
        }

        $this->get('twig')->addGlobal('pageTitle', 'Avaliação');
        $systemEvaluation = new SystemEvaluation();
        $form = $this->createForm(ArticleEvaluationType::class, $systemEvaluation);
        $form->handleRequest($request);


        if ($request->isMethod('POST')) {

            $systemEvaluation->setUserArticles($systemEvaluationIndications->getUserArticles());
            $systemEvaluation->setUserOwner($this->getUser());
            $systemEvaluation->setCreatedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();

            $systemEvaluationIndications->setValid(true);
            $entityManager->persist($systemEvaluationIndications);
            $entityManager->flush();

            $entityManager->persist($systemEvaluation);
            $entityManager->flush();

            $this->systemEvaluationLogService->register(
                $systemEvaluation,
                'Modificação - Avaliação',
                UserArticles::ARTICLE_EVALUATION_STATUS_WAITING,
                $request->getClientIp(),
                $this->getUser()
            );

            $this->addFlash('success', 'Criteria created!');

            return $this->redirect($this->generateUrl('article_evaluation_index', ['edition' => $edition->getId()]));
        }

        return $this->render('@Base/article_evaluation/show.html.twig', [
            'form'    => $form->createView(),
            'article' => $systemEvaluationIndications,
        ]);
    }

    /**
     * @Route("/{systemEvaluationIndications}/saveInfo", name="article_evaluation_save_info", methods={"POST"})
     *
     * @param SystemEvaluationIndications $systemEvaluationIndications
     * @param Request $request
     * @return mixed
     */
    public function saveInfoEvaluation(SystemEvaluationIndications $systemEvaluationIndications, Request $request)
    {

        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {

            $url = $this->generateUrl('article_evaluation_index');
            $content = $request->get('justificationInfoAval', null);
            try {

                $entityManager = $this->getDoctrine()->getManager();

                $systemEvaluationIndications->setValid(true);
                $entityManager->persist($systemEvaluationIndications);
                $entityManager->flush();

                $systemEvaluation = new SystemEvaluation();
                $systemEvaluation->setUserArticles($systemEvaluationIndications->getUserArticles());
                $systemEvaluation->setUserOwner($this->getUser());
                $systemEvaluation->setRejectJustification($content);
                $systemEvaluation->setRejectAt(new \DateTime());

                $entityManager->persist($systemEvaluation);
                $entityManager->flush();

                $this->addFlash('success', 'Criteria created!');

                return new JsonResponse(['data' => ['url' => $url], 'status' => 'ok'], 201);

            } catch (\Exception $e) {

                return new JsonResponse(['data' => ['url' => $url], 'status' => 'error'], 500);
            }

        }

        return $this->redirectToRoute('article_evaluation_show',
            ['systemEvaluationIndications' => $systemEvaluationIndications]);
    }

    /**
     * @Route("/{systemEvaluationIndications}/saveError", name="article_evaluation_save_error", methods={"POST"})
     *
     * @param SystemEvaluationIndications $systemEvaluationIndications
     * @param Request $request
     * @return mixed
     */
    public function saveErrorEvaluation(SystemEvaluationIndications $systemEvaluationIndications, Request $request)
    {
        if ($request->isXmlHttpRequest() && $request->isMethod('POST')) {
            $url = $this->generateUrl('article_evaluation_index');
            $content = $request->get('justificationInfoAval', null);
            try {
                $entityManager = $this->getDoctrine()->getManager();

                $systemEvaluationIndications->setValid(true);
                $entityManager->persist($systemEvaluationIndications);
                $entityManager->flush();

                $systemEvaluation = new SystemEvaluation();
                $systemEvaluation->setUserArticles($systemEvaluationIndications->getUserArticles());
                $systemEvaluation->setUserOwner($this->getUser());
                $systemEvaluation->setFormatErrorJustification($content);
                $systemEvaluation->setFormatErrorAt(new \DateTime());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($systemEvaluation);
                $entityManager->flush();

                $this->addFlash('success', 'Criteria created!');

                return new JsonResponse(['data' => ['url' => $url], 'status' => 'ok'], 201);

            } catch (\Exception $e) {

                return new JsonResponse(['data' => ['url' => $url], 'status' => 'error'], 500);
            }
        }

        return $this->redirectToRoute('article_evaluation_show',
            ['systemEvaluationIndications' => $systemEvaluationIndications]);
    }

}
