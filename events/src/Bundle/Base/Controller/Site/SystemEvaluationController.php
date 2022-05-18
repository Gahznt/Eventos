<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Activity;
use App\Bundle\Base\Entity\ArticleIndicationSearch;
use App\Bundle\Base\Entity\CoordinatorsSearch;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\EditionSignup;
use App\Bundle\Base\Entity\EvaluatorsSearch;
use App\Bundle\Base\Entity\Panel;
use App\Bundle\Base\Entity\SystemEvaluation;
use App\Bundle\Base\Entity\SystemEvaluationAveragesSearch;
use App\Bundle\Base\Entity\SystemEvaluationConfig;
use App\Bundle\Base\Entity\SystemEvaluationIndicationsSearch;
use App\Bundle\Base\Entity\SystemEvaluationSubmissionsSearch;
use App\Bundle\Base\Entity\Thesis;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Form\ActivityBaseType;
use App\Bundle\Base\Form\ActivitySearchType;
use App\Bundle\Base\Form\ArticleIndicationType;
use App\Bundle\Base\Form\CoordinatorsSearchType;
use App\Bundle\Base\Form\EditionSignupSearchType;
use App\Bundle\Base\Form\EvaluatorsSearchType;
use App\Bundle\Base\Form\PanelSearchType;
use App\Bundle\Base\Form\SystemEvaluationAuthorRateType;
use App\Bundle\Base\Form\SystemEvaluationAveragesType;
use App\Bundle\Base\Form\SystemEvaluationIndicationsSearchType;
use App\Bundle\Base\Form\SystemEvaluationSubmissionsSearchType;
use App\Bundle\Base\Form\ThesisSearchType;
use App\Bundle\Base\Repository\ActivityRepository;
use App\Bundle\Base\Repository\DivisionRepository;
use App\Bundle\Base\Repository\EditionSignupRepository;
use App\Bundle\Base\Repository\PanelRepository;
use App\Bundle\Base\Repository\SystemEvaluationConfigRepository;
use App\Bundle\Base\Repository\SystemEvaluationIndicationsRepository;
use App\Bundle\Base\Repository\SystemEvaluationRepository;
use App\Bundle\Base\Repository\ThesisRepository;
use App\Bundle\Base\Repository\UserArticlesRepository;
use App\Bundle\Base\Repository\UserRepository;
use App\Bundle\Base\Repository\UserThemesRepository;
use App\Bundle\Base\Services\SystemEvaluation as SystemEvaluationService;
use App\Bundle\Base\Services\SystemEvaluationAverages as SystemEvaluationAveragesService;
use App\Bundle\Base\Services\SystemEvaluationConfig as SystemEvaluationConfigService;
use App\Bundle\Base\Services\SystemEvaluationIndication as SystemEvaluationIndicationService;
use App\Bundle\Base\Traits\AccessControl;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;
use Yectep\PhpSpreadsheetBundle\Factory as PhpSpreadsheet;

/**
 * @Route("system_evaluation")
 * Class SystemEvaluationController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class SystemEvaluationController extends AbstractController
{
    use AccessControl;

    const PAGE_LIMIT = 20;
    const PAGE_NUM_DEFAULT = 1;

    /**
     * @var SystemEvaluationIndicationService
     */
    private $systemEvaluationIndicationService;

    /**
     * @var SystemEvaluationIndicationsRepository
     */
    private $systemEvaluationIndicationsRepository;

    /**
     * @var SystemEvaluationConfigService
     */
    private $systemEvaluationConfigService;

    /**
     * @var SystemEvaluationService
     */
    private $systemEvaluationService;

    /**
     * @var SystemEvaluationAveragesService
     */
    private $systemEvaluationAveragesService;

    /**
     * @var SystemEvaluationRepository
     */
    private $systemEvaluationRepository;

    /**
     * @var SystemEvaluationConfigRepository
     */
    private $systemEvaluationConfigRepository;

    /**
     * @var DivisionRepository
     */
    private $divisonRepository;

    /**
     * @var UserThemesRepository
     */
    private $userThemesRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserArticlesRepository
     */
    private $userArticlesRepository;

    /**
     * @var string|string[]
     */
    private $linkPath = UserArticles::PUBLIC_PATH;

    public function __construct(
        Breadcrumbs                           $breadcrumbs,
        UrlGeneratorInterface                 $urlGenerator,
        UserArticlesRepository                $userArticlesRepository,
        SystemEvaluationConfigRepository      $systemEvaluationConfigRepository,
        SystemEvaluationConfigService         $systemEvaluationConfig,
        SystemEvaluationIndicationService     $systemEvaluationIndicationService,
        DivisionRepository                    $divisionRepository,
        UserThemesRepository                  $userThemesRepository,
        UserRepository                        $userRepository,
        SystemEvaluationIndicationsRepository $systemEvaluationIndicationsRepository,
        SystemEvaluationService               $systemEvaluationService,
        SystemEvaluationAveragesService       $systemEvaluationAveragesService,
        SystemEvaluationRepository            $systemEvaluationRepository
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Administrative', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('System Evaluation');
        $this->userArticlesRepository = $userArticlesRepository;
        $this->systemEvaluationConfigRepository = $systemEvaluationConfigRepository;
        $this->systemEvaluationConfigService = $systemEvaluationConfig;
        $this->systemEvaluationIndicationService = $systemEvaluationIndicationService;
        $this->userThemesRepository = $userThemesRepository;
        $this->divisonRepository = $divisionRepository;
        $this->userRepository = $userRepository;
        $this->systemEvaluationIndicationsRepository = $systemEvaluationIndicationsRepository;
        $this->systemEvaluationService = $systemEvaluationService;
        $this->systemEvaluationAveragesService = $systemEvaluationAveragesService;
        $this->systemEvaluationRepository = $systemEvaluationRepository;
    }

    /**
     * @Route("/{edition}/indications", name="system_evaluation_indications", methods={"GET", "POST"})
     *
     * @param PaginatorInterface $paginator
     * @param Edition $edition
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indications(Edition $edition, Request $request, PaginatorInterface $paginator)
    {
        $this->isLogged($this->getUser());
        $menuBreadcumb = $this->evaluationSubmenu('INDICAÇÕES', $edition->getId());

        $eventName = $edition->getEvent()->getNamePortuguese();
        $eventDate = $edition->getEvent()->getCreatedAt()->format('Y');
        $this->get('twig')->addGlobal('pageTitle', "$eventName $eventDate");

        $articleIndicationSearch = new ArticleIndicationSearch();
        $form = $this->createForm(ArticleIndicationType::class, $articleIndicationSearch, ['edition' => $edition]);
        $form->handleRequest($request);
        $user = $this->getUser();
        $division = null;

        if ($user) {
            foreach ($user->getUserDivisionCoordinator() as $coordinator) {
                $division = $coordinator->getDivision();
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $userThemes = $form->getData()->getThemes();
            $search = $form->getData()->getSearch();
            $articles = $this->userArticlesRepository->findByTeste(['edition' => $edition, 'division' => $division, 'theme' => $userThemes, 'search' => $search]);
        } else {
            $articles = $this->userArticlesRepository->findByTeste(['edition' => $edition, 'division' => $division]);
        }

        $page = $request->query->get('page', self::PAGE_NUM_DEFAULT);

        if (! empty($articles)) {
            $articles = $paginator->paginate($articles, $page, self::PAGE_LIMIT);
        }

        return $this->render('@Base/system_evaluation/indications/index.html.twig', [
            'form' => $form->createView(),
            'data' => $articles,
            'edition' => $edition,
            'menuBreadcumb' => $menuBreadcumb,
        ]);
    }


    /**
     * @Route("/{edition}/averages", name="system_evaluation_averages", methods={"GET", "POST"})
     *
     * @param Edition $edition
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function averages(Edition $edition, Request $request, PaginatorInterface $paginator)
    {
        $this->isLogged($this->getUser());
        $menuBreadcumb = $this->evaluationSubmenu('MÉDIAS', $edition->getId());

        $eventName = $edition->getEvent()->getNamePortuguese();
        $eventDate = $edition->getEvent()->getCreatedAt()->format('Y');

        $this->get('twig')->addGlobal('pageTitle', "$eventName $eventDate");

        $systemEvaluationAveragesSearch = new SystemEvaluationAveragesSearch();
        $form = $this->createForm(SystemEvaluationAveragesType::class, $systemEvaluationAveragesSearch);
        $form->handleRequest($request);
        $primary = 0;
        $secondary = 0;
        $submited = false;
        $user = $this->getUser();
        $division = null;

        if ($user) {
            foreach ($user->getUserDivisionCoordinator() as $coordinator) {
                $division = $coordinator->getDivision();
            }
        }

        $articles = $this->getArticlesWaiting($division);
        $score = [];

        if (! empty($articles)) {
            foreach ($articles as $key => $article) {
                $_score = $this->systemEvaluationService->calculateCriterias($article);
                if (! is_null($_score['primary']) && ! is_null($_score['secondary'])) {
                    $score[$article->getId()] = $_score;
                } else {
                    unset($articles[$key]);
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $submited = true;
            $primary = $systemEvaluationAveragesSearch->getPrimary();
            $secondary = $systemEvaluationAveragesSearch->getSecondary();
            $saved = $systemEvaluationAveragesSearch->getSaved();

            if ($saved == 1) {

                $status = $this->systemEvaluationAveragesService->registerWithArticles(
                    $division,
                    $edition,
                    $user,
                    $primary,
                    $secondary,
                    $articles,
                    $score
                );

                if ($status) {
                    $articles = $this->getArticlesWaiting($division);
                    $this->addFlash('success', 'Articles status updated!');
                } else {
                    $this->addFlash('error', 'Error, imposible set status');
                }

                $submited = false;
            }
        }

        $page = $request->query->get('page', self::PAGE_NUM_DEFAULT);
        $articles = $paginator->paginate($articles, $page, self::PAGE_LIMIT);

        return $this->render('@Base/system_evaluation/averages/index.html.twig', [
            'data' => $articles,
            'form' => $form->createView(),
            'edition' => $edition,
            'score' => $score,
            'primary' => $primary,
            'secondary' => $secondary,
            'submited' => $submited,
            'menuBreadcumb' => $menuBreadcumb,
        ]);
    }

    /**
     * @Route("/{edition}/set_indications/{article}", name="system_evaluation_set_indications", methods={"GET", "POST"})
     *
     * @param Edition $edition
     * @param UserArticles $article
     * @param PaginatorInterface $paginator
     * @param Request $request
     *
     * @return Response
     */
    public function setIndications(Edition $edition, UserArticles $article, PaginatorInterface $paginator, Request $request)
    {
        $this->isLogged($this->getUser());
        $this->get('twig')->addGlobal('pageTitle', $edition->getName());

        $realRequestMethod = $request->getMethod();

        $systemIndicationSearch = new SystemEvaluationIndicationsSearch();
        // força o método GET para popular o formulário de busca
        $request->setMethod('GET');
        $form = $this->createForm(SystemEvaluationIndicationsSearchType::class, $systemIndicationSearch, ['edition' => $edition]);
        $form->handleRequest($request);

        $request->setMethod($realRequestMethod);

        // $user = $this->getUser();

        $division = $request->get('division', null);
        $theme = $request->get('theme', null);
        $level = $request->get('level', null);
        $search = $request->get('search', '');

        if ($request->isMethod('POST')) {
            $indications = $request->get('indications', []);

            if (! empty($indications)) {
                $indications = array_slice($indications, 0, 2);
            }

            $save = $this->systemEvaluationIndicationService->register($article, $indications);
            $messageSuccess = 'Indication with success';

            if (empty($indications)) {
                $messageSuccess = 'Indications deleted';
            }

            if ($save) {
                $this->addFlash('success', $messageSuccess);
            } else {
                $this->addFlash('error', 'Error, imposible indication');
            }
        }

        $users = $this->systemEvaluationIndicationService->calculateIndications(
            $edition,
            $article,
            $division,
            $theme,
            $level,
            $search
        );

        $page = $request->query->get('page', self::PAGE_NUM_DEFAULT);

        if (! empty($users)) {
            $users = $paginator->paginate($users, $page, self::PAGE_LIMIT);
        }

        $savedList = [];
        $indications = $this->systemEvaluationIndicationsRepository->findBy(['userArticles' => $article]);
        if (! empty($indications)) {
            foreach ($indications as $indication) {
                $savedList[] = $indication->getUserEvaluator()->getId();
            }
        }

        $menuBreadcumb = $this->evaluationSubmenu('INDICAÇÕES', $edition->getId());

        return $this->render('@Base/system_evaluation/indications/indication.html.twig', [
            'article' => $article,
            'data' => $users,
            'savedList' => $savedList,
            'form' => $form->createView(),
            'menuBreadcumb' => $menuBreadcumb,
        ]);
    }

    /**
     * @Route("/{edition}/config", name="system_evaluation_config", methods={"GET", "POST"})
     *
     * @param Edition $edition
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function config(Edition $edition, Request $request)
    {
        $this->isLogged($this->getUser());
        $currentConfig = $this->systemEvaluationConfigService->get($edition);
        $originalArticleFree = SystemEvaluationConfig::DISABLE;
        $articleSubmissionAvaliable = SystemEvaluationConfig::DISABLE;
        $evaluationArticleAvaliable = SystemEvaluationConfig::DISABLE;
        $panelSubmissionAvailable = SystemEvaluationConfig::DISABLE;
        $results = SystemEvaluationConfig::DISABLE;
        $autoCertificates = SystemEvaluationConfig::DISABLE;
        $freeCertificates = SystemEvaluationConfig::DISABLE;
        $ensalementGeneral = SystemEvaluationConfig::DISABLE;
        $ensalementPriority = SystemEvaluationConfig::DISABLE;
        $freeSections = SystemEvaluationConfig::DISABLE;
        $freeSignup = SystemEvaluationConfig::DISABLE;
        $thesisSubmissionAvailable = SystemEvaluationConfig::DISABLE;
        $detailedSchedulingAvailable = SystemEvaluationConfig::DISABLE;


        if (! empty($currentConfig)) {
            $originalArticleFree = $currentConfig->getArticeFree();
            $articleSubmissionAvaliable = $currentConfig->getArticeSubmissionAvaliable();
            $evaluationArticleAvaliable = $currentConfig->getEvaluateArticleAvaliable();
            $panelSubmissionAvailable = $currentConfig->getPanelSubmissionAvailable();
            $results = $currentConfig->getResultsAvaliable();
            $autoCertificates = $currentConfig->getAutomaticCertiticates();
            $freeCertificates = $currentConfig->getFreeCertiticates();
            $ensalementGeneral = $currentConfig->getEnsalementGeneral();
            $ensalementPriority = $currentConfig->getEnsalementPriority();
            $freeSections = $currentConfig->getFreeSections();
            $freeSignup = $currentConfig->getFreeSignup();
            $thesisSubmissionAvailable = $currentConfig->getThesisSubmissionAvailable();
            $detailedSchedulingAvailable = $currentConfig->getDetailedSchedulingAvailable();
        }

        $this->get('twig')->addGlobal('pageTitle', $edition->getName());
        $menuBreadcumb = $this->evaluationSubmenu('CONFIGURAÇÕES', $edition->getId());

        if ($request->isMethod("POST")) {
            $articleSubmissionAvaliable = (int)$request->get('article_submission_avaliable', SystemEvaluationConfig::DISABLE);
            $evaluationArticleAvaliable = (int)$request->get('evaluation_article_avaliable', SystemEvaluationConfig::DISABLE);
            $panelSubmissionAvailable = (int)$request->get('panel_submission_available', SystemEvaluationConfig::DISABLE);
            $results = (int)$request->get('results', SystemEvaluationConfig::DISABLE);
            $articleFree = (int)$request->get('article_free', $originalArticleFree);
            $autoCertificates = (int)$request->get('auto_certificates', SystemEvaluationConfig::DISABLE);
            $freeCertificates = (int)$request->get('free_certificates', SystemEvaluationConfig::DISABLE);
            $ensalementGeneral = (int)$request->get('ensalement_general', SystemEvaluationConfig::DISABLE);
            $ensalementPriority = (int)$request->get('ensalement_priority', SystemEvaluationConfig::DISABLE);
            $freeSections = (int)$request->get('free_sections', SystemEvaluationConfig::DISABLE);
            $freeSignup = (int)$request->get('free_signup', SystemEvaluationConfig::DISABLE);
            $thesisSubmissionAvailable = (int)$request->get('thesis_submission_available', SystemEvaluationConfig::DISABLE);
            $detailedSchedulingAvailable = (int)$request->get('detailed_scheduling_available', SystemEvaluationConfig::DISABLE);

            $messageSuccess = 'Configuration saved!';

            if ($articleFree) {
                $messageSuccess .= ' - Article released';
            }

            try {

                $user = $this->getUser();
                $ip = $request->getClientIp();
                $flag = $this->systemEvaluationConfigService->register(
                    $articleFree,
                    $results,
                    $evaluationArticleAvaliable,
                    $articleSubmissionAvaliable,
                    $ip,
                    $edition,
                    $user,
                    $autoCertificates,
                    $freeCertificates,
                    $ensalementGeneral,
                    $ensalementPriority,
                    $freeSections,
                    $freeSignup,
                    $panelSubmissionAvailable,
                    $thesisSubmissionAvailable,
                    $detailedSchedulingAvailable
                );

            } catch (\Exception $e) {
                $flag = false;
            }

            if ($flag) {
                $this->addFlash('success', $messageSuccess);
            } else {
                $this->addFlash('error', 'Couldn\'t save configuration');
            }
        }

        return $this->render('@Base/system_evaluation/config/index.html.twig', [
            'menuBreadcumb' => $menuBreadcumb,
            'articleFree' => $originalArticleFree,
            'articleSubmissionAvaliable' => $articleSubmissionAvaliable,
            'evaluateArticleAvaliable' => $evaluationArticleAvaliable,
            'panelSubmissionAvailable' => $panelSubmissionAvailable,
            'autoCertificates' => $autoCertificates,
            'freeCertificates' => $freeCertificates,
            'ensalementGeneral' => $ensalementGeneral,
            'ensalementPriority' => $ensalementPriority,
            'freeSections' => $freeSections,
            'freeSignup' => $freeSignup,
            'results' => $results,
            'thesisSubmissionAvailable' => $thesisSubmissionAvailable,
            'detailedSchedulingAvailable' => $detailedSchedulingAvailable,
        ]);
    }

    /**
     * @Route("/", name="system_evaluation_index", methods={"GET"})
     */
    public function index()
    {
        $this->isLogged($this->getUser());
        return $this->redirect('/');
    }

    /**
     * @Route("/{edition}/submissions", name="system_evaluation_submissions", methods={"GET"})
     *
     * @param Edition $edition
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function submissions(Edition $edition, Request $request, PaginatorInterface $paginator, PhpSpreadsheet $phpSpreadsheet, TranslatorInterface $translator)
    {
        $this->isLogged($this->getUser());
        $systemEvaluationSubmissionsSearch = new SystemEvaluationSubmissionsSearch();
        $form = $this->createForm(SystemEvaluationSubmissionsSearchType::class, $systemEvaluationSubmissionsSearch, ['edition' => $edition]);
        $form->handleRequest($request);

        $this->get('twig')->addGlobal('pageTitle', 'System Evaluation');
        $division = null;
        $user = $this->getUser();

        foreach ($user->getUserDivisionCoordinator() as $coordinator) {
            $division = $coordinator->getDivision();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $status = $form->getData()->getStatus();
            $theme = $form->getData()->getUserThemes();
            $type = $form->getData()->getType();
            $search = $form->getData()->getSearch();
            $articles = $this->userArticlesRepository->findByTesteQb(compact('edition', 'division', 'status', 'theme', 'type', 'search'));
        } else {
            $articles = $this->userArticlesRepository->findByTesteQb(compact('edition', 'division'));
        }

        $page = $request->query->get('page', self::PAGE_NUM_DEFAULT);
        $data = $paginator->paginate($articles, $page, self::PAGE_LIMIT);

        $indicationsCount = $this->systemEvaluationIndicationService->getCountByArticle($articles);
        $indicationsValidCount = $this->systemEvaluationIndicationService->getCountValidByArticle($articles);

        $pageTitle = $edition->getEvent()->getNamePortuguese() . ' ' . $edition->getDateStart()->format('Y');
        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_SUBMISSIONS', $edition->getId());

        if ('excel' === $request->get('export')) {
            $spreadsheet = $phpSpreadsheet->createSpreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(mb_substr(sprintf('Listagem de Submissões %s', $edition->getName()), 0, 31));
            $lineIndex = 1;

            $column = 1;
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Id'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Divisão'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Título'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Status'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Designações'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Avaliações'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Tema'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Enquadramento'));

            $lineIndex++;

            $data = $articles->getQuery()->getResult();

            foreach ($data as $article) {
                $ARTICLE_EVALUATION_STATUS = array_flip(UserArticles::ARTICLE_EVALUATION_STATUS);
                $status = $ARTICLE_EVALUATION_STATUS[$article->getStatus()];

                $indications = $indicationsCount[$article->getId()] ?? 0;
                $evaluations = $indicationsValidCount[$article->getId()] ?? 0;
                $FRAMES = array_flip(UserArticles::FRAMES);

                $column = 1;
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($article->getId());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($article->getDivisionId()->getInitials());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($article->getTitle());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans($status));
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($indications);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($evaluations);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($article->getUserThemes()->getPosition() . '-' . $article->getUserThemes()->getDetails()->getPortugueseTitle());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans($FRAMES[$article->getFrame()]));

                $lineIndex++;
            }

            // Gera arquivo
            $response = $phpSpreadsheet->createStreamedResponse($spreadsheet, 'Xls');

            // Redirect output to a client’s web browser (Xls)
            $response->headers->set('Content-Type', 'application/vnd.ms-excel');
            $response->headers->set('Content-Disposition', sprintf('attachment;filename="Listagem de Submissoes %s.xls"', $edition->getName()));
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;
        }

        return $this->render('@Base/system_evaluation/submissions/index.html.twig', [
            'form' => $form->createView(),
            'data' => $data,
            'pageTitle' => $pageTitle,
            'menuBreadcumb' => $menuBreadcumb,
            'edition' => $edition,
            'indicationsCount' => $indicationsCount,
            'indicationsValidCount' => $indicationsValidCount,
            'FRAMES' => array_flip(UserArticles::FRAMES),
        ]);
    }

    /**
     * @Route("/{article}/submissions-details", name="system_evaluation_submissions_details", methods={"GET"})
     *
     * @param UserArticles $article
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function submissionsDetails(UserArticles $article)
    {
        $this->isLogged($this->getUser());
        $eventName = $article->getEditionId()->getEvent()->getNamePortuguese();
        $eventDate = $article->getEditionId()->getEvent()->getCreatedAt()->format('Y');
        $this->get('twig')->addGlobal('pageTitle', "$eventName $eventDate");
        //$division = null;
        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_SUBMISSIONS', $article->getEditionId()->getId());

        return $this->render('@Base/system_evaluation/submissions/details.html.twig', [
            'data' => $article,
            'edition' => $article->getEditionId(),
            'menuBreadcumb' => $menuBreadcumb,
            'FRAMES' => array_flip(UserArticles::FRAMES),
            'ARTICLE_RESULTING_FROM' => array_flip(UserArticles::ARTICLE_RESULTING_FROM),
            'linkPath' => $this->linkPath,
            'LANGUAGES' => array_flip(UserArticles::LANGUAGES),
        ]);
    }

    /**
     * @Route("/{edition}/coordinators", name="submissions_coordinators", methods={"GET"})
     *
     * @param PaginatorInterface $paginator
     * @param Edition $edition
     * @param Request $request
     * @param PhpSpreadsheet $phpSpreadsheet
     * @param TranslatorInterface $translator
     *
     * @return Response|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function coordinators(PaginatorInterface $paginator, Edition $edition, Request $request, PhpSpreadsheet $phpSpreadsheet, TranslatorInterface $translator)
    {
        $this->isLogged($this->getUser());
        $coordinatorsSearch = new CoordinatorsSearch();
        $form = $this->createForm(CoordinatorsSearchType::class, $coordinatorsSearch, ['edition' => $edition]);
        $form->handleRequest($request);

        //$eventName = $edition->getEvent()->getNamePortuguese();
        //$eventDate = $edition->getEvent()->getCreatedAt()->format('Y');
        $this->get('twig')->addGlobal('pageTitle', $edition->getName());
        $menuBreadcumb = $this->evaluationSubmenu('COORDENADORES', $edition->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            $theme = $form->getData()->getUserThemes();
            $type = $form->getData()->getType();
            $search = $form->getData()->getSearch();
            $division = $form->getData()->getDivision();

            $total = false;

            if (empty($theme) && empty($type) && empty($search) && empty($division)) {
                $total = true;
            }

            $coordinators = $this->userRepository->findCoordinators([
                'theme' => $theme,
                'type' => $type,
                'search' => $search,
                'total' => $total,
                'division' => $division,
                'edition' => $edition,
            ]);
        } else {
            $coordinators = $this->userRepository->findCoordinators(['total' => true, 'edition' => $edition]);
        }

        if ('excel' === $request->get('export')) {
            $spreadsheet = $phpSpreadsheet->createSpreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(mb_substr(sprintf('Coordenadores %s', $edition->getName()), 0, 31));
            $lineIndex = 1;

            $column = 1;
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Nome'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Usuário'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Tipo de Usuário'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(sprintf('%s / %s', $translator->trans('Theme'), $translator->trans('Division')));

            $lineIndex++;

            $results = $coordinators->getQuery()->getResult();
            foreach ($results as $result) {
                /** @var User $result */

                $userType = '';
                $divisionOrTheme = [];
                // $isDivision = false;

                if ($result->getUserThemesResearchers()->count() > 0) {
                    foreach ($result->getUserThemesResearchers() as $leader) {
                        if (
                            ! $leader->getUserThemes()
                            || ! $leader->getUserThemes()->getDetails()
                        ) {
                            continue;
                        }

                        $divisionOrTheme[$leader->getUserThemes()->getDetails()->getId()] = $leader->getUserThemes()->getDetails()->getPortugueseTitle();
                        $userType = $translator->trans('ROLE_LEADER');
                    }

                    // $isDivision = false;
                }

                if ($result->getUserDivisionCoordinator()->count() > 0) {
                    foreach ($result->getUserDivisionCoordinator() as $coord) {
                        if (
                            ! $coord->getDivision()
                        ) {
                            continue;
                        }

                        $divisionOrTheme[] = $coord->getDivision()->getName();
                        $userType = $translator->trans('ROLE_DIVISION_COORDINATOR');
                    }

                    // $isDivision = true;
                }

                $column = 1;
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getName());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getIdentifier());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($userType);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(implode(', ', $divisionOrTheme));

                $lineIndex++;
            }

            // Gera arquivo
            $response = $phpSpreadsheet->createStreamedResponse($spreadsheet, 'Xls');

            // Redirect output to a client’s web browser (Xls)
            $response->headers->set('Content-Type', 'application/vnd.ms-excel');
            $response->headers->set('Content-Disposition', sprintf('attachment;filename="Coordenadores %s.xls"', $edition->getName()));
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;
        } else {
            $page = $request->query->get('page', self::PAGE_NUM_DEFAULT);
            $coordinators = $paginator->paginate($coordinators, $page, self::PAGE_LIMIT);

            return $this->render('@Base/system_evaluation/coordinators/index.html.twig', [
                'coordinators' => $coordinators,
                'menuBreadcumb' => $menuBreadcumb,
                'edition' => $edition,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{edition}/coordinators/{id}/show", name="submissions_coordinators_show", methods={"GET"})
     *
     * @param Edition $edition
     * @param User $id
     * @param PaginatorInterface $paginator
     * @param Request $request
     *
     * @return Response
     */
    public function coordinatorShow(Edition $edition, User $id, PaginatorInterface $paginator, Request $request)
    {
        $this->isLogged($this->getUser());
        $this->get('twig')->addGlobal('pageTitle', $edition->getName());
        $menuBreadcumb = $this->evaluationSubmenu('COORDENADORES', $edition->getId());

        return $this->render('@Base/system_evaluation/coordinators/details.html.twig', [
            'user' => $id,
            'edition' => $edition,
            'menuBreadcumb' => $menuBreadcumb,
        ]);
    }

    /**
     * @Route("/{edition}/approved_articles", name="approved_articles", methods={"GET"})
     *
     * @param PaginatorInterface $paginator
     * @param Edition $edition
     * @param Request $request
     * @param PhpSpreadsheet $phpSpreadsheet
     * @param TranslatorInterface $translator
     *
     * @return Response|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function approvedArticles(PaginatorInterface $paginator, Edition $edition, Request $request, PhpSpreadsheet $phpSpreadsheet, TranslatorInterface $translator)
    {
        $this->isLogged($this->getUser());

        $this->get('twig')->addGlobal('pageTitle', $edition->getName());
        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_DASHBOARD', $edition->getId());

        $approvedArticles = $this->userArticlesRepository->getReportApprovedArticles($edition->getId());

        $EDITION_SIGNUP_STATUS_PAY = array_flip(EditionSignup::EDITION_SIGNUP_STATUS_PAY);
        $EDITION_SIGNUP_STATUS_PAY[''] = 'EDITION_SIGNUP_STATUS_NOT_SUBSCRIBED';

        if ('excel' === $request->get('export')) {
            $spreadsheet = $phpSpreadsheet->createSpreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(mb_substr(sprintf('Inscrições - Trabalhos aprovados %s', $edition->getName()), 0, 31));
            $lineIndex = 1;

            $column = 1;
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Id'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Divisão'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Tema'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Autor'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Documento'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Email'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Categoria'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Status'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Observação'));

            $lineIndex++;

            foreach ($approvedArticles as $result) {
                $status = $EDITION_SIGNUP_STATUS_PAY[$result['status_pay']];

                $note = '';
                foreach ($approvedArticles as $approvedArticle) {
                    $statusAuthor = $EDITION_SIGNUP_STATUS_PAY[$approvedArticle['status_pay']];

                    if ($result['id'] === $approvedArticle['id']) {
                        $note .= "[{$translator->trans($statusAuthor)}] {$approvedArticle['author']}  - ";
                    }
                }

                $note = rtrim($note, ' - ');

                $column = 1;
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result['id']);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result['divisao']);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result['ordem'] . '-' . $result['theme_title']);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result['author']);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result['document']);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result['email']);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result['tipo_inscricao']);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans($status));
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($note);

                $lineIndex++;
            }

            // Gera arquivo
            $response = $phpSpreadsheet->createStreamedResponse($spreadsheet, 'Xls');

            // Redirect output to a client’s web browser (Xls)
            $response->headers->set('Content-Type', 'application/vnd.ms-excel');
            $response->headers->set('Content-Disposition', sprintf('attachment;filename="Inscrições - Trabalhos Aprovados %s.xls"', $edition->getName()));
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;
        } else {
            $page = $request->query->get('page', self::PAGE_NUM_DEFAULT);
            $approvedArticles = $paginator->paginate($approvedArticles, $page, self::PAGE_LIMIT);

            return $this->render('@Base/system_evaluation/reports/approved_articles.html.twig', [
                'approved_articles' => $approvedArticles,
                'menuBreadcumb' => $menuBreadcumb,
                'edition' => $edition,
                'EDITION_SIGNUP_STATUS_PAY' => $EDITION_SIGNUP_STATUS_PAY,
            ]);
        }
    }

    /**
     * @Route("/{edition}/dashboard", name="system_evaluation_dashboard", methods={"GET"})
     *
     * @param Edition $edition
     * @param UserArticlesRepository $ua
     *
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function dashboard(Edition $edition, UserArticlesRepository $ua)
    {
        $this->isLogged($this->getUser());

        if (! $edition) {
            return new Response('', 404);
        }

        $eventName = $edition->getEvent()->getNamePortuguese();
        $eventDate = $edition->getEvent()->getCreatedAt()->format('Y');
        $this->get('twig')->addGlobal('pageTitle', "$eventName $eventDate");

        // Artigos para Avaliação (Por designações)
        $indicationQuantity = $ua->getDashEvaluationIndicationQuantity($edition->getId());
        $indicationQuantityLabels = [];
        $indicationQuantityValues = [];
        if (count($indicationQuantity) > 0) {
            foreach ($indicationQuantity as $item) {
                $indicationQuantityLabels[] = sprintf('%d Desig.', $item['indication_count']);
                $indicationQuantityValues[] = $item['count'];
            }
        }

        // Designações Efetuadas
        $indicationProgress = $ua->getDashEvaluationByIndicationProgress($edition->getId());
        $indicationProgressLabels = [];
        $indicationProgressValues = [];
        if (count($indicationProgress) > 0) {
            foreach ($indicationProgress as $item) {
                $indicationProgressLabels[] = $item['valid'] ? 'Completas' : 'Incompletas';
                $indicationProgressValues[] = $item['count'];
            }
        }

        // Artigos com 3 Designações
        $indicationByThree = $ua->getDashEvaluationIndicationsByQuantity($edition->getId(), 3);
        $indicationByThreeLabels = ['Completas', 'Incompletas',];
        $indicationByThreeValues = [0, 0,];
        if (count($indicationByThree) > 0) {
            $indicationByThreeValues[0] = $indicationByThree[0]['valid_count'];
            $indicationByThreeValues[1] = $indicationByThree[0]['invalid_count'];
        }

        // Artigos com 2 Designações
        $indicationByTwo = $ua->getDashEvaluationIndicationsByQuantity($edition->getId(), 2);
        $indicationByTwoLabels = ['Completas', 'Incompletas',];
        $indicationByTwoValues = [0, 0,];
        if (count($indicationByTwo) > 0) {
            $indicationByTwoValues[0] = $indicationByTwo[0]['valid_count'];
            $indicationByTwoValues[1] = $indicationByTwo[0]['invalid_count'];
        }

        // Artigos com 1 Designação
        $indicationByOne = $ua->getDashEvaluationIndicationsByQuantity($edition->getId(), 1);
        $indicationByOneLabels = ['Completas', 'Incompletas',];
        $indicationByOneValues = [0, 0,];
        if (count($indicationByOne) > 0) {
            $indicationByOneValues[0] = $indicationByOne[0]['valid_count'];
            $indicationByOneValues[1] = $indicationByOne[0]['invalid_count'];
        }

        // Conclusão por Divisões
        $indicationProgressByDivision = $ua->getDashEvaluationByIndicationProgressByDivision($edition->getId());
        $indicationProgressByDivisionLabels = [];
        $indicationProgressByDivisionValidValues = [];
        $indicationProgressByDivisionInvalidValues = [];
        if (count($indicationProgressByDivision) > 0) {
            foreach ($indicationProgressByDivision as $item) {
                $indicationProgressByDivisionLabels[] = $item['initials'];
                $indicationProgressByDivisionValidValues[] = $item['valid'];
                $indicationProgressByDivisionInvalidValues[] = $item['invalid'];
            }
        }

        // Temas Por Divisão
        $indicationThemesByDivision = $ua->getDashEvaluationByIndicationThemesByDivision($edition->getId());
        $indicationThemesByDivisionData = [];
        if (count($indicationThemesByDivision) > 0) {
            foreach ($indicationThemesByDivision as $item) {
                if (empty($indicationThemesByDivisionData['d_' . $item['division']])) {
                    $indicationThemesByDivisionData['d_' . $item['division']] = [
                        'id' => $item['division'],
                        'initials' => $item['initials'],
                        'label' => $item['portuguese'],
                        'themes' => [],

                        'labels' => [],
                        'valid_values' => [],
                        'invalid_values' => [],
                    ];
                }

                $indicationThemesByDivisionData['d_' . $item['division']]['labels'][] = $item['portugueseTitle'];
                $indicationThemesByDivisionData['d_' . $item['division']]['valid_values'][] = $item['valid'];
                $indicationThemesByDivisionData['d_' . $item['division']]['invalid_values'][] = $item['invalid'];
            }
        }

        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_DASHBOARD', $edition->getId());

        return $this->render('@Base/system_evaluation/dashboard/index.html.twig',
            compact(
                'menuBreadcumb',
                'indicationQuantityLabels', 'indicationQuantityValues',

                'indicationProgressLabels', 'indicationProgressValues',

                'indicationByThreeLabels', 'indicationByThreeValues',
                'indicationByTwoLabels', 'indicationByTwoValues',
                'indicationByOneLabels', 'indicationByOneValues',

                'indicationProgressByDivisionLabels', 'indicationProgressByDivisionValidValues', 'indicationProgressByDivisionInvalidValues',

                'indicationThemesByDivisionData'
            ));
    }

    /**
     * @Route("/{edition}/reports/{page}", name="system_evaluation_reports", methods={"GET"})
     *
     * @param Edition $edition
     * @param UserArticlesRepository $ua
     * @param int $page
     * @param Request $request
     * @param PhpSpreadsheet $phpSpreadsheet
     * @param TranslatorInterface $translator
     *
     * @return Response|StreamedResponse
     */
    public function reports(Edition $edition, UserArticlesRepository $ua, $page, Request $request, PhpSpreadsheet $phpSpreadsheet, TranslatorInterface $translator)
    {
        $this->isLogged($this->getUser());

        if (! $edition) {
            return new Response('', 404);
        }

        $eventName = $edition->getEvent()->getNamePortuguese();
        $eventDate = $edition->getEvent()->getCreatedAt()->format('Y');
        $this->get('twig')->addGlobal('pageTitle', "$eventName $eventDate");

        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_DASHBOARD', $edition->getId());

        $FRAMES = array_flip(UserArticles::FRAMES);
        $ARTICLE_EVALUATION_STATUS = array_flip(UserArticles::ARTICLE_EVALUATION_STATUS);
        $AUTHOR_RATE_ONE_OPTIONS = array_flip(SystemEvaluation::AUTHOR_RATE_ONE_OPTIONS);
        $AUTHOR_RATE_TWO_OPTIONS = array_flip(SystemEvaluation::AUTHOR_RATE_TWO_OPTIONS);

        switch ($page) {
            case 'statistics':
                $tableData = $ua->getReportStatistics($edition->getId()); //$this->reportTableData($page);
                for ($i = 0; $i < count($tableData); $i++) {
                    $tableData[$i]['themes'] = explode(',', $tableData[$i]['themes']);
                    $tableData[$i]['submitted'] = (int)$tableData[$i]['submitted'];
                    $tableData[$i]['rp_pre'] = (int)$tableData[$i]['rp_pre'];
                    $tableData[$i]['available'] = (int)$tableData[$i]['available'];
                    $tableData[$i]['discrepancies'] = (int)$tableData[$i]['discrepancies'];
                }
                break;
            case 'sys_statistics':
                $tableData = $ua->getReportSysStatistics($edition->getId());
                for ($i = 0; $i < count($tableData); $i++) {
                    $tableData[$i]['themes'] = explode(',', $tableData[$i]['themes']);
                    $tableData[$i]['submitted_orig'] = (int)$tableData[$i]['submitted_orig'];
                    $tableData[$i]['submitted_final'] = (int)$tableData[$i]['submitted_final'];
                    $tableData[$i]['available'] = (int)$tableData[$i]['available'];
                    $tableData[$i]['rp'] = (int)$tableData[$i]['rp'];
                    $tableData[$i]['evaluators'] = (int)$tableData[$i]['evaluators'];
                    $tableData[$i]['evaluator_by1'] = (int)$tableData[$i]['evaluator_by1'];
                    $tableData[$i]['evaluator_by2'] = (int)$tableData[$i]['evaluator_by2'];
                    $tableData[$i]['evaluator_by3'] = (int)$tableData[$i]['evaluator_by3'];
                    $tableData[$i]['discrepancies'] = (int)$tableData[$i]['discrepancies'];
                    $tableData[$i]['invited'] = (int)$tableData[$i]['invited'];
                    $tableData[$i]['selected'] = (int)$tableData[$i]['selected'];
                }
                break;
            case 'pending_action':
                $tableData = $ua->getReportPendingAction($edition->getId());
                for ($i = 0; $i < count($tableData); $i++) {
                    if (! empty($tableData[$i]['format_error_at'])) {
                        $tableData[$i]['situation'] = 'RECUSADO POR FORMATO';
                        $tableData[$i]['reason'] = $tableData[$i]['format_error_justification'];
                    } else {
                        $tableData[$i]['situation'] = 'RECUSADO NA PRÉ SELEÇÃO';
                        $tableData[$i]['reason'] = $tableData[$i]['reject_justification'];
                    }
                }
                break;
            case 'refused_format':
                $tableData = $ua->getReportRefusedFormat($edition->getId());
                break;
            case 'refused_pre':
                $tableData = $ua->getReportRefusedPre($edition->getId());
                break;
            case 'coord_division':
                $tableData = $ua->getReportCoordDivision($edition->getId());
                break;
            case 'articles_theme':
                $tableData = $ua->getReportArticlesTheme($edition->getId());
                break;
            case 'discrepancies':
                $tableData = $ua->getReportDiscrepancies($edition->getId());
                break;
            case 'inprogress':
                $tableData = $ua->getReportInprogress($edition->getId());
                for ($i = 0; $i < count($tableData); $i++) {
                    $tableData[$i]['designations'] = (int)$tableData[$i]['designations'];
                    $tableData[$i]['evaluations'] = (int)$tableData[$i]['evaluations'];
                }
                break;
            case 'inprogress_evaluators':
                $tableData = $ua->getReportInprogressEvaluators($edition->getId());
                for ($i = 0; $i < count($tableData); $i++) {
                    $tableData[$i]['designations'] = (int)$tableData[$i]['designations'];
                    $tableData[$i]['evaluations'] = (int)$tableData[$i]['evaluations'];
                }
                break;
            case 'inprogress_evaluations':
                $tableData = $ua->getReportInprogressEvaluations($edition->getId());


                if ('excel' === $request->get('export')) {
                    $map = [
                        'weak' => 1,
                        'regular' => 2,
                        'good' => 3,
                        'very_good' => 4,
                    ];

                    $spreadsheet = $phpSpreadsheet->createSpreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setTitle(mb_substr('Avaliação dos Avaliadores', 0, 31));
                    $lineIndex = 1;

                    $column = 1;
                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Id'));
                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('SYS_EV_REPORT_TH_FIT'));
                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('SYS_EV_REPORT_TH_THEME'));
                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('SYS_EV_REPORT_TH_DATE'));
                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('SYS_EV_REPORT_TH_EVALUATOR_ID'));
                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('SYS_EV_REPORT_TH_CHAR_COUNT'));
                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('SYS_EV_REPORT_TH_SEEM'));
                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('SYS_EV_REPORT_TH_UTILITY'));
                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('SYS_EV_REPORT_TH_VALUE'));
                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('SYS_EV_REPORT_TH_CONSTRUCTIVE'));
                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('SYS_EV_REPORT_TH_VALUE'));

                    $lineIndex++;

                    if (count($tableData) > 0) {
                        foreach ($tableData as $item) {
                            $column = 1;
                            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($item['id']);
                            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans($FRAMES[$item['fit']]));
                            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($item['theme']);
                            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue((new \DateTime($item['date']))->format('d/m/Y H:i'));
                            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($item['evaluator_id']);
                            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($item['char_count']);
                            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($item['justification']);
                            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(isset($AUTHOR_RATE_ONE_OPTIONS[$item['author_rate_one']])
                                ? $translator->trans($AUTHOR_RATE_ONE_OPTIONS[$item['author_rate_one']])
                                : '');
                            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(isset($map[$item['author_rate_one']])
                                ? $map[$item['author_rate_one']]
                                : '');

                            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(isset($AUTHOR_RATE_TWO_OPTIONS[$item['author_rate_two']])
                                ? $translator->trans($AUTHOR_RATE_TWO_OPTIONS[$item['author_rate_two']])
                                : '');

                            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(isset($map[$item['author_rate_two']])
                                ? $map[$item['author_rate_two']]
                                : '');

                            $lineIndex++;
                        }
                    }

                    // Gera arquivo
                    $response = $phpSpreadsheet->createStreamedResponse($spreadsheet, 'Xls');

                    // Redirect output to a client’s web browser (Xls)
                    $response->headers->set('Content-Type', 'application/vnd.ms-excel');
                    $response->headers->set('Content-Disposition', sprintf('attachment;filename="Avaliação dos Avaliadores %s.xls"', $edition->getName()));
                    $response->headers->set('Cache-Control', 'max-age=0');

                    return $response;
                }
                break;
            case 'institutions':
                $tableData = $ua->getReportInstitutions($edition->getId());
                for ($i = 0; $i < count($tableData); $i++) {
                    $tableData[$i]['submitted'] = (int)$tableData[$i]['submitted'];
                    $tableData[$i]['selected'] = (int)$tableData[$i]['selected'];
                    $tableData[$i]['authors'] = (int)$tableData[$i]['authors'];
                }

                if ('excel' === $request->get('export')) {
                    $spreadsheet = $phpSpreadsheet->createSpreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setTitle(mb_substr(sprintf('Planilha - Instituições e Programas %s', $edition->getName()), 0, 31));
                    $lineIndex = 1;
                    $column = 1;
                    $sheet->getCellByColumnAndRow($column, $lineIndex++)->setValue($edition->getName());
                    $sheet->getCellByColumnAndRow($column, $lineIndex++)->setValue($translator->trans('Planilha - Instituições e Programas'));

                    $today = new \DateTime('now');

                    $sheet->getCellByColumnAndRow($column, $lineIndex++)->setValue($translator->trans('Gerada em: ') . $today->format('d/m/Y H:i:s'));
                    $sheet->getCellByColumnAndRow($column, $lineIndex++)->setValue('www.anpad.org.br');

                    $lineIndex++;

                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Instituição'));

                    $sheet->getStyle('A6:E6')
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('ffc5c5c5');

                    $sheet->getStyle('A6:E6')
                        ->getAlignment()
                        ->setHorizontal('center');

                    $sheet->getStyle('A6:E6')
                        ->getFont()
                        ->setBold(true);

                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Programa'));
                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Artigos Submetidos'));
                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Artigos Selecionados'));
                    $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Autores'));

                    for ($i = 0; $i < count($tableData); $i++) {
                        $lineIndex++;
                        $column = 1;
                        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($tableData[$i]['institution']);
                        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($tableData[$i]['program']);
                        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($tableData[$i]['submitted']);
                        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($tableData[$i]['selected']);
                        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($tableData[$i]['authors']);
                    }

                    $response = $phpSpreadsheet->createStreamedResponse($spreadsheet, 'Xls');

                    $response->headers->set('Content-Type', 'application/vnd.ms-excel');
                    $response->headers->set('Content-Disposition', sprintf('attachment;filename="Planilha - Instituições e Programas %s.xls"', $edition->getName()));
                    $response->headers->set('Cache-Control', 'max-age=0');

                    return $response;
                }

                break;
            case 'articles_qtd':
                $tableData = $ua->getReportArticlesQtd($edition->getId());
                for ($i = 0; $i < count($tableData); $i++) {
                    $tableData[$i]['articles'] = explode(',', $tableData[$i]['articles']);
                }
                break;
            default:
                $tableData = [];
                break;
        }

        return $this->render("@Base/system_evaluation/reports/$page.html.twig", compact(
            'edition',
            'menuBreadcumb',
            'tableData',
            'FRAMES',
            'ARTICLE_EVALUATION_STATUS',
            'AUTHOR_RATE_ONE_OPTIONS',
            'AUTHOR_RATE_TWO_OPTIONS'
        ));
    }

    /**
     * @param $label
     * @param null $edition
     *
     * @return array
     */
    public function evaluationSubmenu($label, $edition = null)
    {
        return [
            ['label' => 'DASHBOARD', 'href' => "/dashboard/user", 'active' => $label == 'SYS_EV_MENU_DASHBOARD'],
            ['label' => 'SUBMISSÕES', 'href' => [
                ['label' => 'ARTIGOS', 'href' => "/system_evaluation/$edition/submissions"],
                ['label' => 'PAINÉIS', 'href' => "/system_evaluation/$edition/panels"],
                ['label' => 'ATIVIDADES', 'href' => "/system_evaluation/$edition/activities"],
                ['label' => 'TESES', 'href' => "/system_evaluation/$edition/thesis"],
            ], 'active' => $label == 'SYS_EV_MENU_SUBMISSIONS'],
            ['label' => 'MÉDIAS', 'href' => "/system_evaluation/$edition/averages", 'active' => $label == 'MÉDIAS'],
            ['label' => 'INSCRIÇÕES', 'href' => "/system_evaluation/$edition/sign_ups", 'active' => $label == 'INSCRIÇÕES'],
            ['label' => 'INDICAÇÕES', 'href' => "/system_evaluation/$edition/indications", 'active' => $label == 'INDICAÇÕES'],
            ['label' => 'AVALIADORES', 'href' => "/system_evaluation/$edition/evaluators", 'active' => $label == 'AVALIADORES'],
            [
                'label' => 'RELATÓRIOS',
                'href' => [
                    /*['label' => 'Estatísticas', 'href' => '/'],
                    ['label' => 'Estatísticas do Sistema', 'href' => '/'],
                    ['label' => 'Ação Pendente', 'href' => '/'],
                    ['label' => 'Recusados por Formato (avaliadores)', 'href' => '/'],
                    ['label' => 'Recusados na pré seleção', 'href' => '/'],
                    ['label' => 'Trabalhos Dir. ao Coordenador', 'href' => '/'],
                    ['label' => 'Artigos com alteração de tema', 'href' => '/'],
                    ['label' => 'Discrepâncias', 'href' => '/'],
                    ['label' => 'Andamento das Avaliações', 'href' => '/'],
                    ['label' => 'Avaliação dos Avaliadores', 'href' => '/'],
                    ['label' => 'Aprovados (Listagem Simples)', 'href' => '/'],
                    ['label' => 'Autoria', 'href' => '/'],
                    ['label' => 'Instituições e Programas', 'href' => '/'],
                    ['label' => 'Quantidade de Artigos por Autores', 'href' => '/'],
                    ['label' => 'Tabela de Avaliação', 'href' => '/'],
                    ['label' => 'Gráfico de Submissões', 'href' => '/'],
                    ['label' => 'Gráfico de Submissões - 30 dias', 'href' => '/'],*/

                    ['label' => 'SYS_EV_MENU_STATISTICS', 'href' => "/system_evaluation/$edition/reports/statistics"],
                    ['label' => 'SYS_EV_MENU_SYS_STATISTICS', 'href' => "/system_evaluation/$edition/reports/sys_statistics"],
                    ['label' => 'SYS_EV_MENU_ACTION', 'href' => "/system_evaluation/$edition/reports/pending_action"],
                    ['label' => 'SYS_EV_MENU_REFUSED', 'href' => "/system_evaluation/$edition/reports/refused_format"],
                    ['label' => 'SYS_EV_MENU_REFUSED_PRE', 'href' => "/system_evaluation/$edition/reports/refused_pre"],
                    ['label' => 'SYS_EV_MENU_WORKS', 'href' => "/system_evaluation/$edition/reports/coord_division"],
                    ['label' => 'SYS_EV_MENU_ARTICLES', 'href' => "/system_evaluation/$edition/reports/articles_theme"],
                    ['label' => 'SYS_EV_MENU_DISCREPENCIES', 'href' => "/system_evaluation/$edition/reports/discrepancies"],
                    ['label' => 'SYS_EV_MENU_INPROGRESS', 'href' => "/system_evaluation/$edition/reports/inprogress"],
                    [
                        'label' => 'SYS_EV_MENU_INPROGRESS_EVALUATORS',
                        'href' => "/system_evaluation/$edition/reports/inprogress_evaluators",
                    ],
                    [
                        'label' => 'SYS_EV_MENU_INPROGRESS_EVALUATIONS',
                        'href' => "/system_evaluation/$edition/reports/inprogress_evaluations",
                    ],
                    ['label' => 'SYS_EV_MENU_APPROVED', 'href' => "/system_evaluation/$edition/reports/approved"],
                    ['label' => 'SYS_EV_MENU_APPROVED_ARTICLES', 'href' => "/system_evaluation/$edition/approved_articles"],
                    ['label' => 'SYS_EV_MENU_AUTHOR', 'href' => "/system_evaluation/$edition/reports/author"],
                    ['label' => 'SYS_EV_MENU_INSTITUTIONS', 'href' => "/system_evaluation/$edition/reports/institutions"],
                    ['label' => 'SYS_EV_MENU_ARTICLES_QTD', 'href' => "/system_evaluation/$edition/reports/articles_qtd"],
                    ['label' => 'SYS_EV_MENU_TABLE', 'href' => "/system_evaluation/$edition/reports/table"],
                    ['label' => 'SYS_EV_MENU_CHART', 'href' => "/system_evaluation/$edition/reports/chart/total"],
                    ['label' => 'SYS_EV_MENU_CHART_LAST', 'href' => "/system_evaluation/$edition/reports/chart/last"],
                ],
                'active' => $label == 'SYS_EV_MENU_DASHBOARD',
            ],
            ['label' => 'COORDENADORES', 'href' => "/system_evaluation/$edition/coordinators", 'active' => $label == 'COORDENADORES'],
            ['label' => 'CONFIGURAÇÕES', 'href' => "/system_evaluation/$edition/config", 'active' => $label == 'CONFIGURAÇÕES'],
        ];
    }

    /**
     * @Route("/{edition}/evaluators", name="system_evaluation_evaluators", methods={"GET"})
     *
     * @param Edition $edition
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param PhpSpreadsheet $phpSpreadsheet
     * @param TranslatorInterface $translator
     *
     * @return Response|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function evaluators(Edition $edition, Request $request, PaginatorInterface $paginator, PhpSpreadsheet $phpSpreadsheet, TranslatorInterface $translator)
    {
        $this->isLogged($this->getUser());

        $this->get('twig')->addGlobal('pageTitle', $edition->getName());

        $menuBreadcumb = $this->evaluationSubmenu('AVALIADORES', $edition->getId());

        $evaluatorsSearch = new EvaluatorsSearch();
        $form = $this->createForm(EvaluatorsSearchType::class, $evaluatorsSearch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $users = $form->getData()->getUsers();
            $search = $form->getData()->getSearch();
            $evaluators = $this->userRepository->getEvaluators(compact('edition', 'search'));
        } else {
            $evaluators = $this->userRepository->getEvaluators(compact('edition'));
        }

        if ('excel' === $request->get('export')) {
            $spreadsheet = $phpSpreadsheet->createSpreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(mb_substr(sprintf('Avaliadores %s', $edition->getName()), 0, 31));
            $lineIndex = 1;

            $column = 1;
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Nome'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Formação'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Temas Cad.'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Temas Desig.'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('E-mail'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Telefone'));

            $lineIndex++;

            $results = $evaluators->getQuery()->getResult();
            foreach ($results as $result) {
                /** @var User $result */
                $levels = [];
                foreach ($result->getAcademics() as $academic) {
                    $levels[] = array_search($academic->getLevel(), User::USER_LEVELS);
                }

                $themes = [];
                if (
                    $result->getUserEvaluationArticles()
                    && $result->getUserEvaluationArticles()->getThemeFirstId()
                    && $result->getUserEvaluationArticles()->getThemeFirstId()->getDetails()
                ) {
                    $themes[] = $result->getUserEvaluationArticles()->getThemeFirstId()->getDetails()->getPortugueseTitle();
                }
                if (
                    $result->getUserEvaluationArticles()
                    && $result->getUserEvaluationArticles()->getThemeSecondId()
                    && $result->getUserEvaluationArticles()->getThemeSecondId()->getDetails()
                ) {
                    $themes[] = $result->getUserEvaluationArticles()->getThemeSecondId()->getDetails()->getPortugueseTitle();
                }

                $indicationThemes = [];
                if ($result->getIndications()->count() > 0) {
                    foreach ($result->getIndications() as $indication) {
                        if (
                            ! $indication->getUserArticles()
                            || ! $indication->getUserArticles()->getUserThemes()
                            || ! $indication->getUserArticles()->getUserThemes()->getDetails()
                        ) {
                            continue;
                        }

                        $indicationThemes[] = $indication->getUserArticles()->getUserThemes()->getDetails()->getPortugueseTitle();
                    }
                }

                $column = 1;
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getName());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(implode(', ', $levels));
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(implode(', ', $themes));
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(implode(', ', $indicationThemes));
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getEmail());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getPhone());

                $lineIndex++;
            }

            // Gera arquivo
            $response = $phpSpreadsheet->createStreamedResponse($spreadsheet, 'Xls');

            // Redirect output to a client’s web browser (Xls)
            $response->headers->set('Content-Type', 'application/vnd.ms-excel');
            $response->headers->set('Content-Disposition', sprintf('attachment;filename="Avaliadores %s.xls"', $edition->getName()));
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;
        } else {
            $page = $request->query->get('page', self::PAGE_NUM_DEFAULT);
            $evaluators = $paginator->paginate($evaluators, $page, self::PAGE_LIMIT);

            return $this->render('@Base/system_evaluation/evaluators/index.html.twig', [
                'form' => $form->createView(),
                'evaluators' => $evaluators,
                'edition' => $edition,
                'menuBreadcumb' => $menuBreadcumb,
                'USER_LEVELS' => array_flip(User::USER_LEVELS),
            ]);
        }
    }

    private function getArticlesWaiting($division)
    {
        return $this->userArticlesRepository->findBy([
            'divisionId' => $division,
            'status' => UserArticles::ARTICLE_EVALUATION_STATUS_WAITING,
            'deletedAt' => null,
        ]);
    }

    /**
     * @Route("/{evaluation}/author_rate", name="system_evaluation_author_rate", methods={"POST"})
     *
     * @param SystemEvaluation $evaluation
     * @param Request $request
     *
     * @return Response
     */
    public function authorRate(SystemEvaluation $evaluation, Request $request)
    {
        $this->isAuthorizedUser($evaluation->getUserArticles()->getUserArticlesAuthors());

        SystemEvaluationAuthorRateType::$blockPrefixIndex = $evaluation->getId();
        $form = $this->createForm(SystemEvaluationAuthorRateType::class, $evaluation);
        $form->handleRequest($request);

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $evaluation->setIsAuthorRated(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evaluation);
            $entityManager->flush();

            return new Response('', 200);
        } else {
            return new Response($this->renderView('@Base/user_articles/tabs/partials/author_rate_form.html.twig', [
                'evaluation' => $evaluation,
                'form' => $form->createView(),
            ]), 400);
        }
    }

    /**
     * @Route("/{edition}/sign_ups", name="system_evaluation_sign_ups", methods={"GET"})
     *
     * @param Edition $edition
     * @param Request $request
     * @param EditionSignupRepository $er
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function signUps(Edition $edition, Request $request, EditionSignupRepository $er, PaginatorInterface $paginator, PhpSpreadsheet $phpSpreadsheet, TranslatorInterface $translator)
    {
        $this->isLogged($this->getUser());

        $this->get('twig')->addGlobal('pageTitle', 'Inscrições');

        $menuBreadcumb = $this->evaluationSubmenu('INSCRIÇÕES', $edition->getId());

        $form = $this->createForm(EditionSignupSearchType::class, null, ['csrf_protection' => false, 'method' => 'GET']);
        $form->handleRequest($request);

        $criteria = $request->query->get('search', []);

        $queryBuilder = $er->findByEdition($edition->getId(), $criteria);

        if ('excel' === $request->get('export')) {

            $results = $queryBuilder->getQuery()->getResult();

            $spreadsheet = $phpSpreadsheet->createSpreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(mb_substr(sprintf('Listagem de Inscrições %s', $edition->getName()), 0, 31));
            $lineIndex = 1;

            $column = 1;
            //$sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Inscrição'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Nome'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('E-mail'));
            //$sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Id'));
            //$sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Identificador'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Participação'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Status'));

            $lineIndex++;

            /** @var EditionSignup $signup */
            foreach ($results as $signup) {

                $column = 1;
                //$sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($signup->getEdition()->getId() . $signup->getId());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($signup->getJoined()->getName());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($signup->getJoined()->getEmail());
                //$sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($signup->getJoined()->getId());
                //$sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($signup->getJoined()->getIdentifier());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($signup->getPaymentMode()->getName());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($signup->getStatusPay() ? 'Pago' : 'Pendente');

                $lineIndex++;
            }

            // Gera arquivo
            $response = $phpSpreadsheet->createStreamedResponse($spreadsheet, 'Xls');

            // Redirect output to a client’s web browser (Xls)
            $response->headers->set('Content-Type', 'application/vnd.ms-excel');
            $response->headers->set('Content-Disposition', sprintf('attachment;filename="Listagem de Inscrições %s.xls"', $edition->getName()));
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;
        }

        $results = $paginator->paginate($queryBuilder, $request->query->get('page', 1), 20);

        return $this->render('@Base/system_evaluation/sign_ups/index.html.twig', [
            'signUps' => $results,
            'edition' => $edition,
            'menuBreadcumb' => $menuBreadcumb,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{edition}/panels", name="system_evaluation_panels", methods={"GET"})
     *
     * @param PaginatorInterface $paginator
     * @param Edition $edition
     * @param Request $request
     * @param PhpSpreadsheet $phpSpreadsheet
     * @param TranslatorInterface $translator
     *
     * @return Response|StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function panels(PaginatorInterface $paginator, Edition $edition, Request $request, PanelRepository $repository, PhpSpreadsheet $phpSpreadsheet, TranslatorInterface $translator)
    {
        $this->isLogged($this->getUser());

        $this->get('twig')->addGlobal('pageTitle', $edition->getName());
        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_SUBMISSIONS', $edition->getId());

        $form = $this->createForm(PanelSearchType::class, null, ['csrf_protection' => false, 'method' => 'GET', 'edition' => $edition]);
        $form->handleRequest($request);

        $criteria = $request->query->get('search', []);

        $list = $repository->list($criteria);

        if ('excel' === $request->get('export')) {
            $spreadsheet = $phpSpreadsheet->createSpreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(mb_substr(sprintf('Paineis %s', $edition->getName()), 0, 31));
            $lineIndex = 1;

            $column = 1;
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Título'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Divisão'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Idioma'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Justificativa'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Sugestão'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Proponente'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Painelistas'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Status'));

            $lineIndex++;

            $results = $list->getQuery()->getResult();
            /** @var Panel $result */
            foreach ($results as $result) {
                $column = 1;
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getTitle());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getDivisionId()->getPortuguese());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(array_search($result->getLanguage(), Panel::LANGUAGE));
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getJustification());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getSuggestion());

                $proponent = '';
                if ($result->getProponentId()) {
                    $proponent = $result->getProponentId()->getName();
                }
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($proponent);

                $panelists = '';
                if ($result->getPanelsPanelists()->count() > 0) {
                    foreach ($result->getPanelsPanelists() as $i => $panelist) {
                        if (! $panelist->getPanelistId()) {
                            continue;
                        }
                        $panelists .= sprintf("%d - %s%s", $i + 1, $panelist->getPanelistId()->getName(), PHP_EOL);
                    }
                }
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($panelists);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(array_search($result->getStatusEvaluation(), Panel::PANEL_EVALUATION_STATUS));

                $lineIndex++;
            }

            // Gera arquivo
            $response = $phpSpreadsheet->createStreamedResponse($spreadsheet, 'Xls');

            // Redirect output to a client’s web browser (Xls)
            $response->headers->set('Content-Type', 'application/vnd.ms-excel');
            $response->headers->set('Content-Disposition', sprintf('attachment;filename="Paineis %s.xls"', $edition->getName()));
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;
        } else {
            $results = $paginator->paginate($list, $request->query->get('page', 1), 20);

            return $this->render('@Base/system_evaluation/panels/list.html.twig', [
                'panels' => $results,
                'menuBreadcumb' => $menuBreadcumb,
                'edition' => $edition,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{edition}/panels/{id}/show", name="system_evaluation_panels_show", methods={"GET"})
     *
     * @param Edition $edition
     * @param Panel $panel
     * @param PaginatorInterface $paginator
     * @param Request $request
     *
     * @return Response
     */
    public function panelShow(Edition $edition, Panel $panel, PaginatorInterface $paginator, Request $request)
    {
        $this->isLogged($this->getUser());
        $this->get('twig')->addGlobal('pageTitle', $edition->getName());
        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_SUBMISSIONS', $edition->getId());

        return $this->render('@Base/system_evaluation/panels/show.html.twig', [
            'panel' => $panel,
            'edition' => $edition,
            'menuBreadcumb' => $menuBreadcumb,
        ]);
    }

    /**
     * @Route("/{edition}/activities", name="system_evaluation_activities", methods={"GET"})
     *
     * @param PaginatorInterface $paginator
     * @param Edition $edition
     * @param Request $request
     * @param PhpSpreadsheet $phpSpreadsheet
     * @param TranslatorInterface $translator
     *
     * @return Response|StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function activities(PaginatorInterface $paginator, Edition $edition, Request $request, ActivityRepository $repository, PhpSpreadsheet $phpSpreadsheet, TranslatorInterface $translator)
    {
        $this->isLogged($this->getUser());

        $this->get('twig')->addGlobal('pageTitle', $edition->getName());
        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_SUBMISSIONS', $edition->getId());

        $form = $this->createForm(ActivitySearchType::class, null, ['csrf_protection' => false, 'method' => 'GET', 'edition' => $edition]);
        $form->handleRequest($request);

        $criteria = $request->query->get('search', []);

        $list = $repository->list($criteria);

        if ('excel' === $request->get('export')) {
            $spreadsheet = $phpSpreadsheet->createSpreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(mb_substr(sprintf('Atividades %s', $edition->getName()), 0, 31));
            $lineIndex = 1;

            $column = 1;
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Tipo'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Divisão'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Idioma'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Título'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Descrição'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Painelistas'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Convidados'));

            $lineIndex++;

            $results = $list->getQuery()->getResult();
            /** @var Activity $result */
            foreach ($results as $result) {
                $column = 1;
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(array_search($result->getActivityType(), Activity::ACTIVITY_TYPES));
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getDivision()->getName());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(array_search($result->getLanguage(), Activity::ACTIVITY_LANGUAGES));
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getTitlePortuguese());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getDescriptionPortuguese());

                $panelists = '';
                if ($result->getPanelists()->count() > 0) {
                    foreach ($result->getPanelists() as $i => $panelist) {
                        if (! $panelist->getPanelist()) {
                            continue;
                        }
                        $panelists .= sprintf("%d - %s%s", $i + 1, $panelist->getPanelist()->getName(), PHP_EOL);
                    }
                }
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($panelists);

                $guests = '';
                if ($result->getGuests()->count() > 0) {
                    foreach ($result->getGuests() as $i => $guest) {
                        if (! $guest->getGuest()) {
                            continue;
                        }
                        $name = $guest->getName();
                        if (empty($name) && ! empty($guest->getGuest())) {
                            $name = $guest->getGuest()->getName();
                        }

                        $guests .= sprintf("%d - %s%s", $i + 1, $name, PHP_EOL);
                    }
                }
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($guests);

                $lineIndex++;
            }

            // Gera arquivo
            $response = $phpSpreadsheet->createStreamedResponse($spreadsheet, 'Xls');

            // Redirect output to a client’s web browser (Xls)
            $response->headers->set('Content-Type', 'application/vnd.ms-excel');
            $response->headers->set('Content-Disposition', sprintf('attachment;filename="Atividades %s.xls"', $edition->getName()));
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;
        } else {
            $results = $paginator->paginate($list, $request->query->get('page', 1), 20);

            return $this->render('@Base/system_evaluation/activities/list.html.twig', [
                'activities' => $results,
                'menuBreadcumb' => $menuBreadcumb,
                'edition' => $edition,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{edition}/activities/{id}/show", name="system_evaluation_activities_show", methods={"GET"})
     *
     * @param Edition $edition
     * @param Activity $activity
     * @param PaginatorInterface $paginator
     * @param Request $request
     *
     * @return Response
     */
    public function activityShow(Edition $edition, Activity $activity, PaginatorInterface $paginator, Request $request)
    {
        $this->isLogged($this->getUser());
        $this->get('twig')->addGlobal('pageTitle', $edition->getName());
        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_SUBMISSIONS', $edition->getId());

        return $this->render('@Base/system_evaluation/activities/show.html.twig', [
            'activity' => $activity,
            'edition' => $edition,
            'menuBreadcumb' => $menuBreadcumb,
        ]);
    }

    /**
     * @Route("/{edition}/activities/{id}/edit", name="system_evaluation_activities_edit", methods={"GET", "POST"})
     *
     * @param Edition $edition
     * @param Activity $activity
     * @param Activity $activity
     *
     * @return Response
     */
    public function activityEdit(Request $request, Edition $edition, Activity $activity)
    {
        $this->isLogged($this->getUser());
        $this->get('twig')->addGlobal('pageTitle', $edition->getName());
        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_SUBMISSIONS', $edition->getId());

        /*if (null !== $activity->getDeletedAt()) {
            return new Response('', 500);
        }*/

        $form = $this->createForm(ActivityBaseType::class, $activity);
        $form->handleRequest($request);

        if (! $form->isSubmitted() || ! $form->isValid()) {
            if ($form->isSubmitted() && ! $form->isValid()) {
                $this->addFlash('error', 'Ocorreu um erro ao validar os dados.');
            }

            return $this->render('@Base/system_evaluation/activities/form.html.twig', [
                'form' => $form->createView(),
                'activity' => $activity,
                'edition' => $edition,
                'menuBreadcumb' => $menuBreadcumb,
                'submitted' => $form->isSubmitted(),
            ]);
        }

        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Atividade alterada com sucesso!');

        return $this->redirectToRoute('system_evaluation_activities', ['edition' => $edition->getId()]);
    }

    /**
     * @Route("/{edition}/thesis", name="system_evaluation_thesis", methods={"GET"})
     *
     * @param PaginatorInterface $paginator
     * @param Edition $edition
     * @param Request $request
     * @param ThesisRepository $repository
     * @param PhpSpreadsheet $phpSpreadsheet
     * @param TranslatorInterface $translator
     *
     * @return Response|StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function thesis(PaginatorInterface $paginator, Edition $edition, Request $request, ThesisRepository $repository, PhpSpreadsheet $phpSpreadsheet, TranslatorInterface $translator)
    {
        $this->isLogged($this->getUser());

        $this->get('twig')->addGlobal('pageTitle', $edition->getName());
        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_SUBMISSIONS', $edition->getId());

        $form = $this->createForm(ThesisSearchType::class, null, ['csrf_protection' => false, 'method' => 'GET', 'edition' => $edition]);
        $form->handleRequest($request);

        /** @var array $criteria */
        $criteria = $request->query->get('search', []);

        $list = $repository->list($criteria);

        if ('excel' === $request->get('export')) {
            $spreadsheet = $phpSpreadsheet->createSpreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(mb_substr(sprintf('Teses %s', $edition->getName()), 0, 31));

            $lineIndex = 1;
            $column = 1;

            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('ID'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Título'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Modalidade'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Status'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Confirmada?'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Nome'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('CPF'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('E-mail'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Celular'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Estado'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Orientador'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Divisão'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Tema'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('1ª Instituição'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('1ª Programa'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('2ª Instituição'));
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('2ª Programa'));

            $lineIndex++;

            $results = $list->getQuery()->getResult();
            /** @var Thesis $result */
            foreach ($results as $result) {
                $column = 1;

                $institutionFirst = $result->getUser()->getInstitutionsPrograms()->getInstitutionsFirst();
                $programFirst = $result->getUser()->getInstitutionsPrograms()->getProgramsFirst();

                $institutionSecond = $result->getUser()->getInstitutionsPrograms()->getInstitutionsSecond();
                $programSecond = $result->getUser()->getInstitutionsPrograms()->getProgramsSecond();

                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getId());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getTitle());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(array_search($result->getModality(), Thesis::MODALITIES));
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans(array_search($result->getStatus(), Thesis::EVALUATION_STATUS)));
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue(($result->getConfirmed() == true) ? 'Sim' : 'Não');
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getUser()->getName());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getUser()->getIdentifier());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getUser()->getEmail());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getUser()->getCellphone());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getUser()->getCity()->getState()->getIso2());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getAdvisorName());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getDivision()->getInitials());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($result->getUserThemes()->getPosition() . '-' . $result->getUserThemes()->getDetails()->getTitle());
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($institutionFirst);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($programFirst);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($institutionSecond);
                $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($programSecond);

                unset($column);

                $lineIndex++;
            }

            // Gera arquivo
            $response = $phpSpreadsheet->createStreamedResponse($spreadsheet, 'Xls');

            // Redirect output to a client’s web browser (Xls)
            $response->headers->set('Content-Type', 'application/vnd.ms-excel');
            $response->headers->set('Content-Disposition', sprintf('attachment;filename="Teses %s.xls"', $edition->getName()));
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;
        } else {
            $results = $paginator->paginate($list, $request->query->get('page', 1), 20);

            return $this->render('@Base/system_evaluation/thesis/list.html.twig', [
                'thesis' => $results,
                'menuBreadcumb' => $menuBreadcumb,
                'edition' => $edition,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/{edition}/thesis/{id}/show", name="system_evaluation_thesis_show", methods={"GET"})
     *
     * @param Edition $edition
     * @param Thesis $thesis
     * @param PaginatorInterface $paginator
     * @param Request $request
     *
     * @return Response
     */
    public function thesisShow(Edition $edition, Thesis $thesis, PaginatorInterface $paginator, Request $request)
    {
        $this->isLogged($this->getUser());
        $this->get('twig')->addGlobal('pageTitle', $edition->getName());
        $menuBreadcumb = $this->evaluationSubmenu('SYS_EV_MENU_SUBMISSIONS', $edition->getId());

        return $this->render('@Base/system_evaluation/thesis/show.html.twig', [
            'entity' => $thesis,
            'edition' => $edition,
            'menuBreadcumb' => $menuBreadcumb,
        ]);
    }

    /**
     * @Route("/{edition}/thesis/{id}/update_status/{status}", name="system_evaluation_thesis_update_status", methods={"GET"}, requirements={"status"="approved|reproved"})
     *
     * @param Edition $edition
     * @param Thesis $thesis
     * @param string $status
     * @param PaginatorInterface $paginator
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function thesisUpdateStatus(Edition $edition, Thesis $thesis, string $status, Request $request)
    {
        $statusMap = [
            'approved' => Thesis::EVALUATION_STATUS_APPROVED,
            'reproved' => Thesis::EVALUATION_STATUS_REPROVED,
        ];

        try {
            $thesis->setStatus($statusMap[$status]);

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Thesis status updated!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error, imposible set status');
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
