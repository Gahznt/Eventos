<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Certificate;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\EditionSignup;
use App\Bundle\Base\Entity\SystemEnsalementScheduling;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Form\CertificateAwardsType;
use App\Bundle\Base\Form\CertificateBatchActivateType;
use App\Bundle\Base\Form\CertificateManualType;
use App\Bundle\Base\Form\CertificateSearchType;
use App\Bundle\Base\Repository\CertificateRepository;
use App\Bundle\Base\Repository\DivisionCoordinatorRepository;
use App\Bundle\Base\Repository\EditionRepository;
use App\Bundle\Base\Repository\EditionSignupRepository;
use App\Bundle\Base\Repository\SystemEnsalementSchedulingRepository;
use App\Bundle\Base\Repository\SystemEvaluationRepository;
use App\Bundle\Base\Repository\UserCommitteeRepository;
use App\Bundle\Base\Repository\UserRepository;
use App\Bundle\Base\Repository\UserThemesResearchersRepository;
use App\Bundle\Base\Services\SystemEvaluationConfig;
use App\Bundle\Base\Services\User as UserService;
use App\Bundle\Base\Traits\AccessControl;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Endroid\QrCode\QrCode;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;


/**
 * @Route("certificate")
 */
class CertificateController extends AbstractController
{
    use AccessControl;

    const PAGE_LIMIT = 200;
    const PAGE_NUM_DEFAULT = 1;

    private UserRepository $userRepository;

    private EditionRepository $editionRepository;

    private EditionSignupRepository $editionSignupRepository;

    private CertificateRepository $certificateRepository;

    private SystemEvaluationRepository $systemEvaluationRepository;

    private SystemEnsalementSchedulingRepository $systemEnsalementSchedulingRepository;

    private DivisionCoordinatorRepository $divisionCoordinatorRepository;

    private UserCommitteeRepository $userCommitteeRepository;

    private UserThemesResearchersRepository $userThemesResearchersRepository;

    private SystemEvaluationConfig $systemEvaluationConfigService;

    private Breadcrumbs $breadcrumbsService;

    private UrlGeneratorInterface $urlGenerator;

    private UserService $userService;

    private Filesystem $filesystem;

    private string $uploadPath = Certificate::GENERATE_PATH;

    private string $layoutPath = Certificate::LAYOUT_PATH;

    private string $htmlPath = Certificate::HTML_PATH;

    private string $qrCodePath = Certificate::QRCODE_PATH;

    public function __construct(
        UserRepository                       $userRepository,
        EditionRepository                    $editionRepository,
        EditionSignupRepository              $editionSignupRepository,
        CertificateRepository                $certificateRepository,
        SystemEvaluationRepository           $systemEvaluationRepository,
        SystemEnsalementSchedulingRepository $systemEnsalementSchedulingRepository,
        DivisionCoordinatorRepository        $divisionCoordinatorRepository,
        UserCommitteeRepository              $userCommitteeRepository,
        UserThemesResearchersRepository      $userThemesResearchersRepository,
        Breadcrumbs                          $breadcrumbs,
        ParameterBagInterface                $parameterBag,
        SystemEvaluationConfig               $systemEvaluationConfig,
        UrlGeneratorInterface                $urlGenerator,
        UserService                          $userService
    )
    {
        $this->userRepository = $userRepository;
        $this->editionRepository = $editionRepository;
        $this->editionSignupRepository = $editionSignupRepository;
        $this->certificateRepository = $certificateRepository;
        $this->systemEvaluationRepository = $systemEvaluationRepository;
        $this->systemEnsalementSchedulingRepository = $systemEnsalementSchedulingRepository;
        $this->divisionCoordinatorRepository = $divisionCoordinatorRepository;
        $this->userCommitteeRepository = $userCommitteeRepository;
        $this->userThemesResearchersRepository = $userThemesResearchersRepository;
        $this->breadcrumbsService = $breadcrumbs;
        $this->systemEvaluationConfigService = $systemEvaluationConfig;
        $this->urlGenerator = $urlGenerator;
        $this->userService = $userService;

        $this->filesystem = new Filesystem();

        $this->uploadPath = str_replace('#KERNEL#', $parameterBag->get('kernel.project_dir'), $this->uploadPath);
        $this->layoutPath = str_replace('#KERNEL#', $parameterBag->get('kernel.project_dir'), $this->layoutPath);
        $this->htmlPath = str_replace('#KERNEL#', $parameterBag->get('kernel.project_dir'), $this->htmlPath);
        $this->qrCodePath = str_replace('#KERNEL#', $parameterBag->get('kernel.project_dir'), $this->qrCodePath);
    }

    /**
     * @Route("/{edition}/index", name="certificate_list", methods={"GET"})
     */
    public function index(Edition $edition, Request $request, PaginatorInterface $paginator)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS', $this->urlGenerator->generate('manager_editions_index', ['eventId' => $edition->getEvent()->getId()]));
        $this->breadcrumbsService->addItem('CERTIFICATE');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $form = $this->createForm(CertificateSearchType::class, null, ['csrf_protection' => false, 'method' => 'GET', 'edition' => $edition]);
        $form->handleRequest($request);

        /** @var array $criteria */
        $criteria = $request->query->get('search', []);

        $certificates = $this->certificateRepository->list($edition, $criteria);

        $page = $request->query->get('page', self::PAGE_NUM_DEFAULT);
        $certificates = $paginator->paginate($certificates, $page, self::PAGE_LIMIT);

        return $this->render('@Base/certificate/index.html.twig', [
            'certificates' => $certificates,
            'edition' => $edition,
            'form' => $form->createView(),
        ]);
    }

    protected function addCertificate(
        Edition     $edition,
        User        $user,
        int         $type,
        ?Collection $userArticles = null,
        ?Collection $userThemes = null,
        ?Collection $activities = null,
        ?Collection $panels = null,
        ?Collection $theses = null,
        ?Collection $divisions = null,
        ?array      $variables = null
    )
    {
        $certificate = null;

        $criteria = [
            'edition' => $edition,
            'user' => $user,
            'type' => $type,
        ];

        if ($type === Certificate::CERTIFICATE_TYPE_APRESENTACAO) {
            $criteria['userArticles'] = $userArticles;
        }

        if ($type !== Certificate::CERTIFICATE_TYPE_MANUAL) {
            /** @var Certificate $certificate */
            $certificate = $this->certificateRepository->findOneByCriteria($criteria);
        }

        if (! $certificate) {
            $certificate = new Certificate();
            $certificate->setEdition($edition);
            $certificate->setUser($user);
            $certificate->setType($type);
            $certificate->setIsActive(false);
            $certificate->setCreatedAt(new \DateTime());
        }

        if ($certificate->getUserArticles()->count() > 0) {
            foreach ($certificate->getUserArticles() as $userArticle) {
                $certificate->removeUserArticle($userArticle);
            }
        }

        if ($certificate->getUserThemes()->count() > 0) {
            foreach ($certificate->getUserThemes() as $userTheme) {
                $certificate->removeUserTheme($userTheme);
            }
        }

        if ($certificate->getActivities()->count() > 0) {
            foreach ($certificate->getActivities() as $activity) {
                $certificate->removeActivity($activity);
            }
        }

        if ($certificate->getPanels()->count() > 0) {
            foreach ($certificate->getPanels() as $panel) {
                $certificate->removePanel($panel);
            }
        }

        if ($certificate->getTheses()->count() > 0) {
            foreach ($certificate->getTheses() as $thesis) {
                $certificate->removeThesis($thesis);
            }
        }

        if ($certificate->getDivisions()->count() > 0) {
            foreach ($certificate->getDivisions() as $division) {
                $certificate->removeDivision($division);
            }
        }

        $certificate->setUpdatedAt(new \DateTime());

        if (! empty($userArticles)) {
            foreach ($userArticles as $userArticle) {
                $certificate->addUserArticle($userArticle);
            }
        }

        if (! empty($userThemes)) {
            foreach ($userThemes as $userTheme) {
                $certificate->addUserTheme($userTheme);
            }
        }

        if (! empty($activities)) {
            foreach ($activities as $activity) {
                $certificate->addActivity($activity);
            }
        }

        if (! empty($panels)) {
            foreach ($panels as $panel) {
                $certificate->addPanel($panel);
            }
        }

        if (! empty($theses)) {
            foreach ($theses as $thesis) {
                $certificate->addThesis($thesis);
            }
        }

        if (! empty($divisions)) {
            foreach ($divisions as $division) {
                $certificate->addDivision($division);
            }
        }

        if (! empty($variables)) {
            $certificate->setVariables($variables);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($certificate);
        $entityManager->flush();
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return bool
     */
    protected function generateParticipacao(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $signups = $this->editionSignupRepository->findUserSignedUpAndNotListener($edition, $firstResult, $maxResults)
            ->groupBy('esup.joined')
            ->getQuery()->getResult();

        if (count($signups) > 0) {
            /** @var EditionSignup $signup */
            foreach ($signups as $signup) {
                $this->addCertificate($edition, $signup->getJoined(), Certificate::CERTIFICATE_TYPE_PARTICIPANTE);
            }

            return true;
        }

        return false;
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return bool
     */
    protected function generateApresentacao(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $articles = $edition->getUserArticles([
            'status' => UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED,
            'firstResult' => $firstResult,
            'maxResults' => $maxResults,
        ]);

        if ($articles->count() > 0) {
            foreach ($articles as $article) {
                $authors = $article->getUserArticlesAuthors();

                if ($authors->count() > 0) {
                    foreach ($authors as $author) {
                        $userArticles = new ArrayCollection();
                        $userArticles[] = $article;
                        $this->addCertificate($edition, $author->getUserAuthor(), Certificate::CERTIFICATE_TYPE_APRESENTACAO, $userArticles);
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return bool
     */
    protected function generateAvaliacao(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $systemEvaluations = $this->systemEvaluationRepository->findUniqueEvaluatorsByEdition($edition, $firstResult, $maxResults);

        if (count($systemEvaluations) > 0) {
            foreach ($systemEvaluations as $systemEvaluation) {
                $userEvaluations = $this->systemEvaluationRepository->findByEditionAndEvaluator($edition, $systemEvaluation->getUserOwner());

                $userArticles = new ArrayCollection();

                foreach ($userEvaluations as $userEvaluation) {
                    $userArticles[] = $userEvaluation->getUserArticles();
                }

                $this->addCertificate($edition, $systemEvaluation->getUserOwner(), Certificate::CERTIFICATE_TYPE_AVALIACAO, $userArticles);
            }

            return true;
        }

        return false;
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return bool
     */
    protected function generateCoordenadorSessao(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $schedulings = $this->systemEnsalementSchedulingRepository->findCoordinatorsByEdition($edition, $firstResult, $maxResults);

        if (count($schedulings) > 0) {

            $coordinators = [];

            foreach ($schedulings as $scheduling) {
                if (SystemEnsalementScheduling::TYPE_COORDINATOR == $scheduling->getCoordinatorDebater1Type()) {
                    $coordinators[$scheduling->getCoordinatorDebater1()->getId()] = $scheduling->getCoordinatorDebater1();
                }

                if (SystemEnsalementScheduling::TYPE_COORDINATOR == $scheduling->getCoordinatorDebater2Type()) {
                    $coordinators[$scheduling->getCoordinatorDebater2()->getId()] = $scheduling->getCoordinatorDebater2();
                }
            }

            foreach ($coordinators as $coordinator) {
                $userSchedulings = $this->systemEnsalementSchedulingRepository->findByEditionAndCoordinator($edition, $coordinator);

                $userThemes = new ArrayCollection();

                foreach ($userSchedulings as $userScheduling) {
                    $userThemes[] = $userScheduling->getUserThemes();
                }

                $this->addCertificate($edition, $coordinator, Certificate::CERTIFICATE_TYPE_COORDENADOR_SESSAO, null, $userThemes);
            }

            return true;
        }

        return false;
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return bool
     */
    protected function generateDebatedorSessao(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $schedulings = $this->systemEnsalementSchedulingRepository->findDebatersByEdition($edition, $firstResult, $maxResults);

        if (count($schedulings) > 0) {

            $debaters = [];

            foreach ($schedulings as $scheduling) {
                if (SystemEnsalementScheduling::TYPE_DEBATER == $scheduling->getCoordinatorDebater1Type()) {
                    $debaters[$scheduling->getCoordinatorDebater1()->getId()] = $scheduling->getCoordinatorDebater1();
                }

                if (SystemEnsalementScheduling::TYPE_DEBATER == $scheduling->getCoordinatorDebater2Type()) {
                    $debaters[$scheduling->getCoordinatorDebater2()->getId()] = $scheduling->getCoordinatorDebater2();
                }
            }

            foreach ($debaters as $debater) {
                $userSchedulings = $this->systemEnsalementSchedulingRepository->findByEditionAndDebater($edition, $debater);

                $userThemes = new ArrayCollection();

                foreach ($userSchedulings as $userScheduling) {
                    $userThemes[] = $userScheduling->getUserThemes();
                }

                $this->addCertificate($edition, $debater, Certificate::CERTIFICATE_TYPE_DEBATEDOR_SESSAO, null, $userThemes);
            }

            return true;
        }

        return false;
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return bool
     */
    protected function generateCoordenadorDebatedorSessao(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $schedulings = $this->systemEnsalementSchedulingRepository->findCoordinatorDebatersByEdition($edition, $firstResult, $maxResults);

        if (count($schedulings) > 0) {

            $coordinatorDebaters = [];

            foreach ($schedulings as $scheduling) {
                if (SystemEnsalementScheduling::TYPE_COORDINATOR_DEBATER == $scheduling->getCoordinatorDebater1Type()) {
                    $coordinatorDebaters[$scheduling->getCoordinatorDebater1()->getId()] = $scheduling->getCoordinatorDebater1();
                }

                if (SystemEnsalementScheduling::TYPE_COORDINATOR_DEBATER == $scheduling->getCoordinatorDebater2Type()) {
                    $coordinatorDebaters[$scheduling->getCoordinatorDebater2()->getId()] = $scheduling->getCoordinatorDebater2();
                }
            }

            foreach ($coordinatorDebaters as $coordinatorDebater) {
                $userSchedulings = $this->systemEnsalementSchedulingRepository->findByEditionAndCoordinatorDebater($edition, $coordinatorDebater);

                $userThemes = new ArrayCollection();

                foreach ($userSchedulings as $userScheduling) {
                    $userThemes[] = $userScheduling->getUserThemes();
                }

                $this->addCertificate($edition, $coordinatorDebater, Certificate::CERTIFICATE_TYPE_COORDENADOR_DEBATEDOR_SESSAO, null, $userThemes);
            }

            return true;
        }

        return false;
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return bool
     */
    protected function generateCoordenadorDivisao(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $divisionCoordinators = $this->divisionCoordinatorRepository->findByEdition($edition, $firstResult, $maxResults);

        if (count($divisionCoordinators) > 0) {
            foreach ($divisionCoordinators as $divisionCoordinator) {
                $divisions = new ArrayCollection();
                $divisions[] = $divisionCoordinator->getDivision();

                $this->addCertificate($edition, $divisionCoordinator->getCoordinator(), Certificate::CERTIFICATE_TYPE_COORDENADOR_DIVISAO, null, null, null, null, null, $divisions);
            }

            return true;
        }

        return false;
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return bool
     */
    protected function generateComiteCientifico(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $userCommittees = $this->userCommitteeRepository->findByEdition($edition, $firstResult, $maxResults);

        if (count($userCommittees) > 0) {
            foreach ($userCommittees as $userCommittee) {
                $divisions = new ArrayCollection();
                $divisions[] = $userCommittee->getDivision();

                $this->addCertificate($edition, $userCommittee->getUser(), Certificate::CERTIFICATE_TYPE_COMITE_CIENTIFICO, null, null, null, null, null, $divisions);
            }

            return true;
        }

        return false;
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return bool
     */
    protected function generateLiderTema(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $userThemesResearchers = $this->userThemesResearchersRepository->findUniqueResearchersByEdition($edition, $firstResult, $maxResults);

        if (count($userThemesResearchers) > 0) {
            foreach ($userThemesResearchers as $userThemesResearcher) {
                $userResearchers = $this->userThemesResearchersRepository->findByEditionAndResearcher($edition, $userThemesResearcher->getResearcher());

                $userThemes = new ArrayCollection();

                foreach ($userResearchers as $userResearcher) {
                    $userThemes[] = $userResearcher->getUserThemes();
                }

                $this->addCertificate($edition, $userThemesResearcher->getResearcher(), Certificate::CERTIFICATE_TYPE_LIDER_TEMA, null, $userThemes);
            }

            return true;
        }

        return false;
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return bool
     */
    protected function generateVoluntario(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $signups = $this->editionSignupRepository->findUniqueUserSignedUpAndIsVoluntary($edition, $firstResult, $maxResults);

        if (count($signups) > 0) {
            /** @var EditionSignup $signup */
            foreach ($signups as $signup) {
                $this->addCertificate($edition, $signup->getJoined(), Certificate::CERTIFICATE_TYPE_VOLUNTARIO);
            }

            return true;
        }

        return false;
    }

    /**
     * @Route("/{edition}/generate/{page}", name="certificate_generate", methods={"GET"})
     *
     * @param Edition $edition
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function generate(Edition $edition, Request $request, PaginatorInterface $paginator, int $page = 0)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $maxResults = 20;
        $firstResult = $page * $maxResults;

        $continue = false;
        $continue = $this->generateParticipacao($edition, $firstResult, $maxResults) || $continue;
        echo $continue ? 1 : 0;
        $continue = $this->generateApresentacao($edition, $firstResult, $maxResults) || $continue;
        echo $continue ? 1 : 0;
        $continue = $this->generateAvaliacao($edition, $firstResult, $maxResults) || $continue;
        echo $continue ? 1 : 0;

        $continue = $this->generateCoordenadorSessao($edition, $firstResult, $maxResults) || $continue;
        echo $continue ? 1 : 0;
        $continue = $this->generateDebatedorSessao($edition, $firstResult, $maxResults) || $continue;
        echo $continue ? 1 : 0;
        $continue = $this->generateCoordenadorDebatedorSessao($edition, $firstResult, $maxResults) || $continue;
        echo $continue ? 1 : 0;

        $continue = $this->generateCoordenadorDivisao($edition, $firstResult, $maxResults) || $continue;
        echo $continue ? 1 : 0;
        $continue = $this->generateComiteCientifico($edition, $firstResult, $maxResults) || $continue;
        echo $continue ? 1 : 0;
        $continue = $this->generateLiderTema($edition, $firstResult, $maxResults) || $continue;
        echo $continue ? 1 : 0;

        $continue = $this->generateVoluntario($edition, $firstResult, $maxResults) || $continue;
        echo $continue ? 1 : 0;

        if ($continue) {
            ?>
            Página <?= $page ?>
            <script type="text/javascript">
                setTimeout(function () {
                    window.location = '/pt_br/certificate/<?= $edition->getId() ?>/generate/<?= $page + 1 ?>';
                }, 10);
            </script>
            <?php
            exit;
        }

        echo 'generate done.';
        exit();
    }

    /**
     * @param Certificate $certificate
     */
    protected function createQrCode(Certificate $certificate)
    {
        $qrCodePath = $this->qrCodePath . $certificate->getEdition()->getId();
        if (! $this->filesystem->exists($qrCodePath)) {
            $this->filesystem->mkdir($qrCodePath, 0755);
        }

        $qrCodeFullPath = $qrCodePath . '/' . md5($certificate->getId()) . '.png';

        $url = $this->generateUrl('certificate_show', ['certificate' => $certificate->getId(), 'hash' => $certificate->getHash()], UrlGenerator::ABSOLUTE_URL);

        $size = $certificate->getEdition()->getCertificateQrcodeSize();
        if (! $size) {
            $size = 200;
        }

        $qrCode = new QrCode($url);
        $qrCode->setSize($size);
        $qrCode->setMargin(10);

        $qrCode->writeFile($qrCodeFullPath);
    }

    /**
     * @param Certificate $certificate
     * @param string $htmlFullPath
     */
    private function generateCertificateHtml(Certificate $certificate, $htmlFullPath = '')
    {
        $layoutFile = $this->layoutPath . $certificate->getEdition()->getId() . '/' . $certificate->getEdition()->getCertificateLayoutPath();
        $layout = '';
        if (file_exists($layoutFile)) {
            $layout = 'data:' . mime_content_type($layoutFile) . ';base64,' . base64_encode(file_get_contents($layoutFile));
        }

        $qrCodeFile = $this->qrCodePath . $certificate->getEdition()->getId() . '/' . md5($certificate->getId()) . '.png';
        if (! file_exists($qrCodeFile)) {
            $this->createQrCode($certificate);
        }
        $qrCode = '';
        if (file_exists($qrCodeFile)) {
            $qrCode = 'data:' . mime_content_type($qrCodeFile) . ';base64,' . base64_encode(file_get_contents($qrCodeFile));
        }

        $qrCodeSize = $certificate->getEdition()->getCertificateQrcodeSize();
        if (! $qrCodeSize) {
            $qrCodeSize = 200;
        }

        $qrCodeRight = $certificate->getEdition()->getCertificateQrcodePositionRight();
        if (! $qrCodeRight) {
            $qrCodeRight = 100;
        }

        $qrCodeBottom = $certificate->getEdition()->getCertificateQrcodePositionBottom();
        if (! $qrCodeBottom) {
            $qrCodeBottom = 350;
        }

        $templatePath = '@Base/certificate/templates/' . Certificate::CERTIFICATE_TEMPLATE_MAP[$certificate->getType()] . '.html.twig';

        $html = $this->renderView($templatePath, compact('certificate', 'layout', 'qrCode', 'qrCodeSize', 'qrCodeRight', 'qrCodeBottom'));

        file_put_contents($htmlFullPath, trim($html));

        $certificate->setUpdatedAt(new \DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($certificate);
        $entityManager->flush();
    }

    /**
     * @Route("/{certificate}/download", name="certificate_download", methods={"GET"})
     *
     * @param Certificate $certificate
     * @param Pdf $snappy
     *
     * @return PdfResponse|Response
     */
    public function download(Certificate $certificate, Pdf $snappy)
    {
        if (! $certificate
            || ! $certificate->getIsActive()
            || null !== $certificate->getDeletedAt()
            || ! $certificate->getEdition()->getSystemEvaluationConfigs()
            || ! $certificate->getEdition()->getSystemEvaluationConfigs()[0]
            || ! $certificate->getEdition()->getSystemEvaluationConfigs()[0]->getFreeCertiticates()
        ) {
            return new Response('', 404);
        }

        $this->isOwnerUser($certificate->getUser());

        $pdfPath = $this->uploadPath . $certificate->getEdition()->getId();
        if (! $this->filesystem->exists($pdfPath)) {
            $this->filesystem->mkdir($pdfPath, 0755);
        }
        $htmlPath = $this->htmlPath . $certificate->getEdition()->getId();
        if (! $this->filesystem->exists($htmlPath)) {
            $this->filesystem->mkdir($htmlPath, 0755);
        }

        $pdfFullPath = $pdfPath . '/' . md5($certificate->getId()) . '.pdf';
        $htmlFullPath = $htmlPath . '/' . md5($certificate->getId()) . '.html';

        if (! file_exists($pdfFullPath)) {
            if (! file_exists($htmlFullPath)) {
                $this->generateCertificateHtml($certificate, $htmlFullPath);
            }

            $snappy->generateFromHtml(iconv('utf-8', 'windows-1252//IGNORE', file_get_contents($htmlFullPath)), $pdfFullPath, [
                'orientation' => PageSetup::ORIENTATION_LANDSCAPE,
                'image-dpi' => 100,

                /*'margin-top' => 0,
                'margin-right' => 0,
                'margin-bottom' => 0,
                'margin-left' => 0,*/
            ], true);
        }

        return new PdfResponse(
            file_get_contents($pdfFullPath),
            'Certificado.pdf'
        );
    }

    /**
     * @Route("/{certificate}/show/{hash}", name="certificate_show", methods={"GET"})
     */
    public function show(Certificate $certificate, string $hash, TranslatorInterface $translator): Response
    {
        if (
            ! $certificate
            || ! password_verify($certificate->getId(), base64_decode($hash))
        ) {
            return new Response('', 404);
        }

        if (
            ! ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_ADMIN_OPERATIONAL'))
            && (
                ! $certificate->getIsActive()
                || null !== $certificate->getDeletedAt()
                || ! $certificate->getEdition()->getSystemEvaluationConfigs()
                || ! $certificate->getEdition()->getSystemEvaluationConfigs()[0]
                || ! $certificate->getEdition()->getSystemEvaluationConfigs()[0]->getFreeCertiticates()
            )
        ) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('ANPAD', $this->urlGenerator->generate('index'));
        $this->breadcrumbsService->addItem('CERTIFICATES');

        $this->get('twig')->addGlobal('pageTitle', mb_strtoupper(sprintf(
            '%s - %s',
            $translator->trans('Certificate'),
            $certificate->getEdition()->getName()
        )));

        $htmlPath = $this->htmlPath . $certificate->getEdition()->getId();
        if (! $this->filesystem->exists($htmlPath)) {
            $this->filesystem->mkdir($htmlPath, 0755);
        }

        $htmlFullPath = $htmlPath . '/' . md5($certificate->getId()) . '.html';

        if (! file_exists($htmlFullPath)) {
            $this->generateCertificateHtml($certificate, $htmlFullPath);
        }

        return $this->render('@Base/certificate/show.html.twig', [
            'html' => file_get_contents($htmlFullPath),
        ]);
    }

    protected function clearCertificateCacheFiles(Certificate $certificate): void
    {
        $pdfPath = $this->uploadPath . $certificate->getEdition()->getId();
        $pdfFullPath = $pdfPath . '/' . md5($certificate->getId()) . '.pdf';
        if ($this->filesystem->exists($pdfFullPath)) {
            $this->filesystem->remove($pdfFullPath);
        }

        $htmlPath = $this->htmlPath . $certificate->getEdition()->getId();
        $htmlFullPath = $htmlPath . '/' . md5($certificate->getId()) . '.html';
        if ($this->filesystem->exists($htmlFullPath)) {
            $this->filesystem->remove($htmlFullPath);
        }
    }

    /**
     * @param Certificate $certificate
     */
    protected function activateCertificate(Certificate $certificate)
    {
        $certificate->setIsActive(true);
        $certificate->setUpdatedAt(new \DateTime());

        if (Certificate::CERTIFICATE_TYPE_APRESENTACAO == $certificate->getType()) {
            foreach ($certificate->getUserArticles() as $userArticle) {
                foreach ($userArticle->getUserArticlesAuthors() as $userArticlesAuthor) {
                    if ($userArticlesAuthor->getUserAuthor()->getId() == $certificate->getUser()->getId()) {
                        $userArticlesAuthor->setIsPresented(true);
                    }
                }
            }
        }

        $this->clearCertificateCacheFiles($certificate);
    }

    /**
     * @Route("/{certificate}/activate", name="certificate_activate", methods={"GET"})
     *
     * @param Certificate $certificate
     * @param Request $request
     *
     * @return Response
     */
    public function activate(Certificate $certificate, Request $request)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        try {
            $this->activateCertificate($certificate);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Certificate status updated!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error, imposible set status');
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param Certificate $certificate
     */
    protected function disableCertificate(Certificate $certificate)
    {
        $certificate->setIsActive(false);
        $certificate->setUpdatedAt(new \DateTime());

        if (Certificate::CERTIFICATE_TYPE_APRESENTACAO == $certificate->getType()) {
            foreach ($certificate->getUserArticles() as $userArticle) {
                foreach ($userArticle->getUserArticlesAuthors() as $userArticlesAuthor) {
                    if ($userArticlesAuthor->getUserAuthor()->getId() == $certificate->getUser()->getId()) {
                        $userArticlesAuthor->setIsPresented(false);
                    }
                }
            }
        }

        $this->clearCertificateCacheFiles($certificate);
    }

    /**
     * @Route("/{certificate}/disable", name="certificate_disable", methods={"GET"})
     *
     * @param Certificate $certificate
     * @param Request $request
     *
     * @return Response
     */
    public function disable(Certificate $certificate, Request $request)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        try {
            $this->disableCertificate($certificate);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Certificate status updated!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error, imposible set status');
        }

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/{edition}/batch_activate", name="certificate_batch_activate", methods={"GET", "POST"})
     *
     * @param Edition $edition
     * @param Request $request
     *
     * @return Response
     */
    public function batchActivate(Edition $edition, Request $request)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS', $this->urlGenerator->generate('manager_editions_index', ['eventId' => $edition->getEvent()->getId()]));
        $this->breadcrumbsService->addItem('CERTIFICATE');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $form = $this->createForm(CertificateBatchActivateType::class, null, ['csrf_protection' => false, 'method' => 'POST', 'edition' => $edition]);
        $form->handleRequest($request);

        if (! $form->isSubmitted()
            || $form->isSubmitted() && ! $form->isValid()
        ) {
            if ($form->isSubmitted() && ! $form->isValid()) {
                $this->addFlash('error', 'Não foi possível realizar a operação.');
            }

            return $this->render('@Base/certificate/batch_activate.html.twig', [
                'edition' => $edition,
                'form' => $form->createView(),
            ]);
        }

        /** @var array $criteria */
        $criteria = $request->get('batch', []);

        /** @var Certificate[] $certificates */
        $certificates = $this->certificateRepository->list($edition, $criteria)->getQuery()->getResult();

        foreach ($certificates as $certificate) {
            if (Certificate::ACTIVE_ON == $criteria['operation']) {
                $this->activateCertificate($certificate);
            } else {
                $this->disableCertificate($certificate);
            }
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        $this->addFlash('success', 'Operação realizada com sucesso!');

        return $this->redirect($this->generateUrl('certificate_batch_activate', ['edition' => $edition->getId()]));
    }

    /**
     * @Route("/{edition}/new_awards", name="certificate_new_awards", methods={"GET", "POST"})
     *
     * @param Edition $edition
     * @param Request $request
     *
     * @return Response
     */
    public function newAwards(Edition $edition, Request $request)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS', $this->urlGenerator->generate('manager_editions_index', ['eventId' => $edition->getEvent()->getId()]));
        $this->breadcrumbsService->addItem('CERTIFICATE', $this->urlGenerator->generate('certificate_list', ['edition' => $edition->getId()]));
        //$this->breadcrumbsService->addItem('NEW');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $certificate = new Certificate();

        $form = $this->createForm(CertificateAwardsType::class, $certificate, ['edition' => $edition]);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/certificate/new_awards_form.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'entity' => $certificate,
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/certificate/partials/_new_awards_form.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'entity' => $certificate,
            ]), 400);
        }

        try {

            /** @var UserArticles $article */
            $article = $form->get('userArticles')->getData();
            $authors = $article->getUserArticlesAuthors();

            /** @var int $type */
            $type = $form->get('type')->getData();

            if ($authors->count() > 0) {
                foreach ($authors as $author) {
                    $userArticles = new ArrayCollection();
                    $userArticles[] = $article;
                    $this->addCertificate($edition, $author->getUserAuthor(), $type, $userArticles);
                }
            }

            $this->addFlash('success', 'Certificado criado');
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }

    /**
     * @Route("/{edition}/new_manual", name="certificate_new_manual", methods={"GET", "POST"})
     *
     * @param Edition $edition
     * @param Request $request
     *
     * @return Response
     */
    public function newManual(Edition $edition, Request $request)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isUser($user)) {
            return new Response('', 404);
        }

        $this->breadcrumbsService->addItem('MANAGER_EVENTS', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('MANAGER_EDITIONS', $this->urlGenerator->generate('manager_editions_index', ['eventId' => $edition->getEvent()->getId()]));
        $this->breadcrumbsService->addItem('CERTIFICATE', $this->urlGenerator->generate('certificate_list', ['edition' => $edition->getId()]));
        //$this->breadcrumbsService->addItem('NEW');
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_MANAGER');

        $certificate = new Certificate();

        $formData = $request->get('certificate_manual', []);

        $form = $this->createForm(CertificateManualType::class, $certificate, ['edition' => $edition, 'user' => $formData['user'] ?? []]);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/certificate/new_manual_form.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'entity' => $certificate,
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/certificate/partials/_new_manual_form.html.twig', [
                'event' => $edition->getEvent(),
                'edition' => $edition,
                'form' => $form->createView(),
                'entity' => $certificate,
            ]), 400);
        }
        try {
            /** @var User $userOwner */
            foreach ($form->get('user')->getData() as $userOwner) {
                $this->addCertificate($edition, $userOwner, Certificate::CERTIFICATE_TYPE_MANUAL, null, null, null, null, null, null, [
                    'title' => $form->get('title')->getData(),
                    'description' => $form->get('description')->getData(),
                    'content' => $form->get('content')->getData(),
                ]);
            }

            $this->addFlash('success', 'Certificado criado');
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }

    /**
     * @Route("/remove_manual/{id}", name="certificate_remove_manual", methods={"DELETE"})
     *
     * @param Request $request
     * @param Certificate $certificate
     *
     * @return Response
     */
    public function removeManual(Request $request, Certificate $certificate)
    {
        $user = $this->getUser();
        $this->isLogged($user);
        if (
            ! $this->userService->isAdmin($user) &&
            ! $this->userService->isUser($user)
            || Certificate::CERTIFICATE_TYPE_MANUAL !== $certificate->getType()
        ) {
            return new Response('', 404);
        }

        if (! $this->isCsrfTokenValid('delete' . $certificate->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $certificate->setDeletedAt(new \DateTime('now'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($certificate);
        $entityManager->flush();
        $this->addFlash('success', 'Cerificate removed');

        return new Response('', 200);
    }
}
