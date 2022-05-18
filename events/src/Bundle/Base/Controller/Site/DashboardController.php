<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Panel;
use App\Bundle\Base\Entity\ThemeSubmissionConfig;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Repository\CertificateRepository;
use App\Bundle\Base\Repository\EditionRepository;
use App\Bundle\Base\Repository\EditionSignupRepository;
use App\Bundle\Base\Repository\EventRepository;
use App\Bundle\Base\Repository\PanelRepository;
use App\Bundle\Base\Repository\SystemEnsalementSchedulingRepository;
use App\Bundle\Base\Repository\ThemeSubmissionConfigRepository;
use App\Bundle\Base\Repository\ThesisRepository;
use App\Bundle\Base\Repository\UserArticlesRepository;
use App\Bundle\Base\Repository\UserThemesRepository;
use App\Bundle\Base\Services\Edition as EditionService;
use App\Bundle\Base\Services\EditionSignup as EditionSignupService;
use App\Bundle\Base\Services\SystemEvaluation as SystemEvaluationService;
use App\Bundle\Base\Services\SystemEvaluationConfig;
use App\Bundle\Base\Services\SystemEvaluationIndication as SystemEvaluationIndicationService;
use App\Bundle\Base\Services\User as UserService;
use App\Bundle\Base\Services\UserArticles as UserArticlesService;
use App\Bundle\Base\Services\UserAssociation;
use App\Bundle\Base\Services\UserThemes as UserThemesService;
use App\Bundle\Base\Traits\AccessControl;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("dashboard")
 * Class ArticleEvaluationController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class DashboardController extends AbstractController
{
    use AccessControl;

    /**
     * @var UserAssociation
     */
    private $userAssociationService;
    /**
     * @var SystemEvaluationIndicationService
     */
    private $systemEvaluationIndicationService;
    /**
     * @var EventRepository
     */
    private $eventRepository;
    /**
     * @var UserArticlesRepository
     */
    private $userArticlesRepository;
    /**
     * @var EditionRepository
     */
    private $editionRepository;
    /**
     * @var EditionSignupRepository
     */
    private $editionSignupRepository;

    private ThemeSubmissionConfigRepository $submissionConfigRepository;

    /**
     * @var UserArticlesService
     */
    private $userArticlesService;
    /**
     * @var EditionSignupService
     */
    private $editionSignupService;
    /**
     * @var UserThemesService
     */
    private $userThemesService;
    /**
     * @var UserThemesRepository
     */
    private $userThemesRepository;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var SystemEvaluationConfig
     */
    private $systemEvaluationConfigService;

    /**
     * @var CertificateRepository
     */
    private $certificateRepository;

    /**
     * @var EditionService
     */
    private $editionService;

    /**
     * @var SystemEvaluationService
     */
    private $systemEvaluationService;

    /**
     * @var PanelRepository
     */
    private $panelRepository;

    /**
     * @var ThesisRepository
     */
    private $thesisRepository;

    /**
     * @var SystemEnsalementSchedulingRepository
     */
    private $systemEnsalementSchedulingRepository;

    public function __construct(
        Breadcrumbs                          $breadcrumbs,
        UrlGeneratorInterface                $urlGenerator,
        UserAssociation                      $userAssociation,
        SystemEvaluationIndicationService    $systemEvaluationIndication,
        EventRepository                      $eventRepository,
        EditionRepository                    $editionRepository,
        UserArticlesService                  $userArticlesService,
        EditionSignupService                 $editionSignupService,
        UserThemesService                    $userThemesService,
        UserArticlesRepository               $userArticlesRepository,
        UserThemesRepository                 $userThemesRepository,
        UserService                          $userService,
        SystemEvaluationConfig               $systemEvaluationConfig,
        CertificateRepository                $certificateRepository,
        EditionService                       $editionService,
        SystemEvaluationService              $systemEvaluationService,
        EditionSignupRepository              $editionSignupRepository,
        ThemeSubmissionConfigRepository      $submissionConfigRepository,
        PanelRepository                      $panelRepository,
        ThesisRepository                     $thesisRepository,
        SystemEnsalementSchedulingRepository $systemEnsalementSchedulingRepository
    )
    {

        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('DASHBOARD', $urlGenerator->generate('index'));
        $this->userAssociationService = $userAssociation;
        $this->systemEvaluationIndicationService = $systemEvaluationIndication;
        $this->eventRepository = $eventRepository;
        $this->editionRepository = $editionRepository;
        $this->userArticlesService = $userArticlesService;
        $this->editionSignupService = $editionSignupService;
        $this->userThemesService = $userThemesService;
        $this->userArticlesRepository = $userArticlesRepository;
        $this->userThemesRepository = $userThemesRepository;
        $this->userService = $userService;
        $this->systemEvaluationConfigService = $systemEvaluationConfig;
        $this->certificateRepository = $certificateRepository;
        $this->editionService = $editionService;
        $this->systemEvaluationService = $systemEvaluationService;
        $this->editionSignupRepository = $editionSignupRepository;
        $this->submissionConfigRepository = $submissionConfigRepository;
        $this->panelRepository = $panelRepository;
        $this->thesisRepository = $thesisRepository;
        $this->systemEnsalementSchedulingRepository = $systemEnsalementSchedulingRepository;
    }

    /**
     * @Route("/admin", name="dashboard_admin_index", methods={"GET"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function admin(Request $request)
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_ADMIN_OPERATIONAL');
        if (! $hasAccess) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $user = $this->getUser();

        $this->isLogged($user);
        $this->get('twig')->addGlobal('pageTitle', 'Dashboard');


        $events = $this->eventRepository->withEditionArticles();
        $articlesByEdition = [];
        $signUpByEdition = [];
        $indicationsValidCount = [];
        $articlesApprovedByEdition = [];
        $userThemesApproved = 0;
        $userThemesCount = 0;

        $coordinatorView = "@Base/dashboardCoordinator/index.html.twig";
        $viewPath = $userView = "@Base/dashboardAdmin/index.html.twig";

        if (! empty($events)) {
            $userThemesApproved = $this->userThemesService->getApproved();
            $userThemesCount = $this->userThemesService->getCount();
            foreach ($events as $event) {
                if (empty($event->getEditions())) {
                    continue;
                }

                foreach ($event->getEditions() as $edition) {
                    $_id = $edition->getId();
                    $_articles = $edition->getUserArticles();
                    $articlesByEdition[$_id] = $this->userArticlesService->getCountByEdition($edition);
                    $signUpByEdition[$_id] = $this->editionSignupService->getCountByEdition($edition);
                    $indicationsValidCount[$_id] = $this->systemEvaluationIndicationService->getCountValidByArticle($_articles,
                        false);
                    $articlesApprovedByEdition[$_id] = $this->userArticlesService->getApproved($edition);
                }
            }
        }

        return $this->render($viewPath, [
            'events' => $events,
            'indicationsValidCount' => $indicationsValidCount,
            'signUpByEdition' => $signUpByEdition,
            'articlesByEdition' => $articlesByEdition,
            'articlesApprovedByEdition' => $articlesApprovedByEdition,
            'userThemesApproved' => $userThemesApproved,
            'userThemesCount' => $userThemesCount,
        ]);
    }

    /**
     * @Route("/user", name="dashboard_user_index", methods={"GET"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function user(Request $request)
    {
        $this->get('twig')->addGlobal('pageTitle', 'Dashboard');
        $user = $this->getUser();

        if (! $user) {
            return new Response('', 404);
        }

        assert($user instanceof User);

        $coordinatorView = "@Base/dashboardCoordinator/index.html.twig";
        $leadView = "@Base/dashboardLeader/index.html.twig";
        $viewPath = $userView = "@Base/dashboardUser/index.html.twig";
        $isAdmin = $this->userService->isAdmin($user);
        $isAdminOperational = $this->userService->isUser($user);

        if ($isAdmin || $isAdminOperational) {
            //return $this->redirectToRoute('dashboard_admin_index');
        }

        $isDivisionCoordinator = $this->userService->isDivisionCoordinator($user);
        $isDivisionCommittee = $this->userService->isDivisionCommittee($user);
        $isThemeLead = $this->userService->isThemeLead($user);
        $isEvaluator = $this->userService->isEvaluator($user);

        if ($isDivisionCoordinator || $isDivisionCommittee || $isThemeLead || $isEvaluator) {
            $text = $isDivisionCoordinator
                ? 'ROLE_DIVISION_COORDINATOR'
                : ($isDivisionCommittee
                    ? 'ROLE_COMMITTEE'
                    : ($isThemeLead
                        ? 'ROLE_LEADER'
                        : ($isEvaluator
                            ? 'ROLE_EVALUATOR'
                            : '')));

            $userSubRole = [
                'text' => $text,
                'color' => '#d0305d',
                'textColor' => 'white',
            ];

            $this->get('twig')->addGlobal('userSubRole', $userSubRole);
        }

        $events = [];
        $currentEditions = [];
        $nextEditions = $this->editionRepository->findNext();
        $association = $this->userAssociationService->getByUser($user);
        $certificates = $this->certificateRepository->list(null, [
            'user' => $user,
            'isActive' => true,
        ])->getQuery()->getResult();

        if (count($nextEditions) > 0) {
            $now = (new \DateTime())->getTimestamp();
            foreach ($nextEditions as $edition) {

                $currentEditions[] = [
                    'id' => $edition->getId(),
                    'name' => $edition->getNamePortuguese(),
                    'name_i18n' => [
                        'pt_br' => $edition->getNamePortuguese(),
                        'en' => $edition->getNameEnglish(),
                        'es' => $edition->getNameSpanish(),
                    ],
                    'event_name' => $edition->getEvent()->getNamePortuguese(),
                    'event_name_i18n' => [
                        'pt_br' => $edition->getEvent()->getNamePortuguese(),
                        'en' => $edition->getEvent()->getNameEnglish(),
                        'es' => $edition->getEvent()->getNameSpanish(),
                    ],
                    'place' => $edition->getPlace(),
                    'date_start' => $edition->getDateStart(),
                    'date_end' => $edition->getDateEnd(),
                    'color' => $edition->getColor(),
                    'is_active' => $edition->getDateStart()->getTimestamp() <= $now && $edition->getDateEnd()->getTimestamp() >= $now,
                    'approved_articles_count' => $this->userArticlesRepository->getNumberOfApprovedArticlesByEditionAndAuthor($edition->getId(), $user->getId()),
                    'approved_themes_count' => $this->userThemesService->getApprovedByUser($user),
                    'is_user_signed_up' => $this->editionSignupRepository->isUserSignedUp($edition, $user),
                    'my_assignments_count' => $this->systemEvaluationIndicationService->getCountByUser($edition, $user),
                    'config' => $this->systemEvaluationConfigService->getArray($edition),
                ];

                if (! isset($events[$edition->getEvent()->getId()])) {
                    $events[$edition->getEvent()->getId()] = [
                        'id' => $edition->getEvent()->getId(),
                        'name' => $edition->getEvent()->getNamePortuguese(),
                        'name_i18n' => [
                            'pt_br' => $edition->getEvent()->getNamePortuguese(),
                            'en' => $edition->getEvent()->getNameEnglish(),
                            'es' => $edition->getEvent()->getNameSpanish(),
                        ],
                        'editions' => [],
                    ];
                }

                $config = $this->systemEvaluationConfigService->getArray($edition);

                $events[$edition->getEvent()->getId()]['editions'][] = [
                    'id' => $edition->getId(),
                    'name' => $edition->getNamePortuguese(),
                    'name_i18n' => [
                        'pt_br' => $edition->getNamePortuguese(),
                        'en' => $edition->getNameEnglish(),
                        'es' => $edition->getNameSpanish(),
                    ],
                    'config' => $config,
                ];
            }
        }

        $articles = [];
        $articlesList = $this->userArticlesRepository->getUserSubmissions($this->getUser()->getId());
        if (count($articlesList) > 0) {
            foreach ($articlesList as $article) {
                $config = $this->systemEvaluationConfigService->getArray($article->getEditionId());

                $articles[] = [
                    'id' => $article->getId(),
                    'title' => $article->getTitle(),
                    'title_i18n' => [
                        'pt_br' => $article->getPortuguese(),
                        'en' => $article->getEnglish(),
                        'es' => $article->getSpanish(),
                    ],
                    'event_name' => $article->getEditionId()->getEvent()->getNamePortuguese(),
                    'event_name_i18n' => [
                        'pt_br' => $article->getEditionId()->getEvent()->getNamePortuguese(),
                        'en' => $article->getEditionId()->getEvent()->getNameEnglish(),
                        'es' => $article->getEditionId()->getEvent()->getNameSpanish(),
                    ],
                    'edition_name' => $article->getEditionId()->getNamePortuguese(),
                    'edition_name_i18n' => [
                        'pt_br' => $article->getEditionId()->getNamePortuguese(),
                        'en' => $article->getEditionId()->getNameEnglish(),
                        'es' => $article->getEditionId()->getNameSpanish(),
                    ],
                    'edition_config' => $config,
                    'theme_name' => $article->getUserThemes()->getDetails()->getPortugueseTitle(),
                    'theme_name_i18n' => [
                        'pt_br' => $article->getUserThemes()->getDetails()->getPortugueseTitle(),
                        'en' => $article->getUserThemes()->getDetails()->getEnglishTitle(),
                        'es' => $article->getUserThemes()->getDetails()->getSpanishTitle(),
                    ],
                    'theme_i18n' => [
                        'pt_br' => $article->getUserThemes()->getDetails()->getPortugueseTitle(),
                        'en' => $article->getUserThemes()->getDetails()->getEnglishTitle(),
                        'es' => $article->getUserThemes()->getDetails()->getSpanishTitle(),
                    ],
                    'status' => $article->getStatus(),
                ];
            }
        }

        $editionsSignUp = [];
        $editionsSignUpList = $this->editionSignupRepository->getUserEditions($this->getUser()->getId());
        if (count($editionsSignUpList) > 0) {
            foreach ($editionsSignUpList as $editionSignup) {
                $config = $this->systemEvaluationConfigService->getArray($editionSignup->getEdition());

                $editionsSignUp[] = [
                    'id' => $editionSignup->getId(),
                    'event_name' => $editionSignup->getEdition()->getEvent()->getNamePortuguese(),
                    'event_name_i18n' => [
                        'pt_br' => $editionSignup->getEdition()->getEvent()->getNamePortuguese(),
                        'en' => $editionSignup->getEdition()->getEvent()->getNameEnglish(),
                        'es' => $editionSignup->getEdition()->getEvent()->getNameSpanish(),
                    ],
                    'edition_id' => $editionSignup->getEdition()->getId(),
                    'edition_name' => $editionSignup->getEdition()->getNamePortuguese(),
                    'edition_name_i18n' => [
                        'pt_br' => $editionSignup->getEdition()->getNamePortuguese(),
                        'en' => $editionSignup->getEdition()->getNameEnglish(),
                        'es' => $editionSignup->getEdition()->getNameSpanish(),
                    ],
                    'edition_config' => $config,
                    'edition_place' => $editionSignup->getEdition()->getPlace(),
                    'edition_date_start' => $editionSignup->getEdition()->getDateStart(),
                    'edition_date_end' => $editionSignup->getEdition()->getDateEnd(),
                    'initial_institute' => $editionSignup->getInitialInstitute(),
                    'badge' => $editionSignup->getBadge(),
                    'status_pay' => $editionSignup->getStatusPay(),
                    'edition_payment_mode_name' => $editionSignup->getPaymentMode()->getName(),
                ];
            }
        }

        $panels = [];
        $panelsList = $this->panelRepository->getUserPanels($this->getUser()->getId());
        if (count($panelsList) > 0) {
            foreach ($panelsList as $panel) {
                $config = $this->systemEvaluationConfigService->getArray($panel->getEditionId());

                $panels[] = [
                    'id' => $panel->getId(),
                    'proponent_id' => $panel->getProponentId()->getId(),
                    'event_name' => $panel->getEditionId()->getEvent()->getNamePortuguese(),
                    'event_name_i18n' => [
                        'pt_br' => $panel->getEditionId()->getEvent()->getNamePortuguese(),
                        'en' => $panel->getEditionId()->getEvent()->getNameEnglish(),
                        'es' => $panel->getEditionId()->getEvent()->getNameSpanish(),
                    ],
                    'edition_id' => $panel->getEditionId()->getId(),
                    'edition_name' => $panel->getEditionId()->getNamePortuguese(),
                    'edition_name_i18n' => [
                        'pt_br' => $panel->getEditionId()->getNamePortuguese(),
                        'en' => $panel->getEditionId()->getNameEnglish(),
                        'es' => $panel->getEditionId()->getNameSpanish(),
                    ],
                    'edition_config' => $config,
                    'title' => $panel->getTitle(),
                    'status_evaluation' => $panel->getStatusEvaluation(),
                    'language' => array_flip(Panel::LANGUAGE)[$panel->getLanguage()],
                ];
            }
        }

        $themes = $this->userThemesRepository->getUserSubmissions($this->getUser()->getId());

        $thesis = $this->thesisRepository->getUserSubmissions($user);

        $schedulings = $this->systemEnsalementSchedulingRepository->findByUser($user);

        $isThemeSubmissionAvailable = $this->submissionConfigRepository->count([
                'isAvailable' => ThemeSubmissionConfig::IS_AVAILABLE_TRUE,
            ]) > 0;
        $isThemeSubmissionEvaluationAvailable = $this->submissionConfigRepository->count([
                'isEvaluationAvailable' => ThemeSubmissionConfig::IS_AVAILABLE_TRUE,
            ]) > 0;

        return $this->render($viewPath, [
            'events' => $events,
            'current_editions' => $currentEditions,
            'articles' => $articles,
            'themes' => $themes,
            'association' => $association,
            'associationNow' => new \DateTime(),
            'user' => $user,
            'isEvaluator' => $isEvaluator,
            'isAdmin' => $isAdmin,
            'isAdminOperational' => $isAdminOperational,
            'isDivisionCoordinator' => $isDivisionCoordinator,
            'isDivisionCommittee' => $isDivisionCommittee,
            'isThemeLead' => $isThemeLead,
            'isThemeSubmissionAvailable' => $isThemeSubmissionAvailable,
            'isThemeSubmissionEvaluationAvailable' => $isThemeSubmissionEvaluationAvailable,
            'certificates' => $certificates,
            'editionsSignUp' => $editionsSignUp,
            'panels' => $panels,
            'thesis' => $thesis,
            'schedulings' => $schedulings,
        ]);
    }

    /**
     * @Route("/{id}/article_submission_stats", name="article_submission_stats")
     *
     * @param UserArticles $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function article_submission_stats(UserArticles $id)
    {
        $user = $this->getUser();
        $this->isLogged($user);

        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $this->get('twig')->addGlobal('pageTitle', 'ARTICLE_STATS');

        $data = [];
        $data['languages'] = array_flip(UserArticles::LANGUAGES);
        $data['frames'] = array_flip(UserArticles::FRAMES);
        $data['result_from'] = array_flip(UserArticles::ARTICLE_RESULTING_FROM);
        $data['premium'] = [1 => 'YES', 0 => 'NO'];

        return $this->render('@Base/article_submission/stats/index.html.twig',
            [
                'label' => $id->getEditionId()->getNamePortuguese(),
                'submission' => $id,
                'data' => $data,
            ]
        );
    }

    /**
     * @Route("/{id}/article_submission_stats_download", name="article_submission_stats_download")
     *
     * @param UserArticles $id
     * @param Pdf $snappy
     *
     * @return \Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse|\Symfony\Component\HttpFoundation\JsonResponse|Response
     */
    public function article_submission_stats_download(UserArticles $id, Pdf $snappy)
    {
        $user = $this->getUser();

        $this->isLogged($user);

        if (
            ! $this->userService->isAdmin($user)
            && ! $this->userService->isDivisionCoordinator($user)
            && ! $this->userService->isDivisionCommittee($user)
            && ! $this->userService->isThemeLead($user)
            && ! $this->userService->isEvaluator($user)
            && ! $this->isOwnerUser($id->getUserId())
        ) {
            return new Response('', 404);
        }

        return $this->systemEvaluationService->generateArticleStatusPDF($id, $snappy);
    }
}
