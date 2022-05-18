<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Form\SystemEvaluationAuthorRateType;
use App\Bundle\Base\Form\UserArticlesAuthorsType;
use App\Bundle\Base\Form\UserArticlesType;
use App\Bundle\Base\Repository\EditionRepository;
use App\Bundle\Base\Services\Edition as EditionService;
use App\Bundle\Base\Services\PageMap;
use App\Bundle\Base\Services\SystemEvaluationConfig;
use App\Bundle\Base\Traits\AccessControl;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;
use Yectep\PhpSpreadsheetBundle\Factory as PhpSpreadsheet;

/**
 *
 * @Route("article_submission")
 *
 * Class ArticleSubmissionController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class ArticleSubmissionController extends AbstractController
{
    use AccessControl;

    /**
     *
     */
    const INIT_STEP = 1;
    /**
     *
     */
    const PREFIX_ARTICLE_UPLOAD_FILE_NAME = 'article';

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string|string[]
     */
    private $uploadPath = UserArticles::UPLOAD_PATH;

    /**
     * @var string
     */
    private $linkPath = UserArticles::PUBLIC_PATH;

    /**
     * @var EditionRepository
     */
    private $editionRepository;

    /**
     * @var SystemEvaluationConfig
     */
    private $systemEvaluationConfigService;

    /**
     * @var EditionService
     */
    private EditionService $editionService;

    /**
     * ArticleSubmissionController constructor.
     *
     * @param EditionRepository $editionRepository
     * @param ParameterBagInterface $parameterBag
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     * @param PageMap $pageMap
     * @param SystemEvaluationConfig $systemEvaluationConfig
     * @param EditionService $editionService
     */
    public function __construct(
        EditionRepository      $editionRepository,
        ParameterBagInterface  $parameterBag,
        Breadcrumbs            $breadcrumbs,
        UrlGeneratorInterface  $urlGenerator,
        PageMap                $pageMap,
        SystemEvaluationConfig $systemEvaluationConfig,
        EditionService         $editionService
    )
    {
        $pageMap->setTitle('ARTICLE_SUBMISSION_TITLE');
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Events', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('ARTICLE_SUBMISSION_TITLE');
        $this->editionRepository = $editionRepository;
        $this->uploadPath = str_replace('#KERNEL#', $parameterBag->get('kernel.project_dir'), $this->uploadPath);
        $this->filesystem = new Filesystem();
        $this->systemEvaluationConfigService = $systemEvaluationConfig;
        $this->editionService = $editionService;
    }

    /**
     * @Route("/{edition}/index", name="article_submission_index", methods={"GET", "POST"})
     *
     * @param Edition $edition
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param MailerInterface $mailer
     *
     * @return Response
     */
    public function index(Edition $edition, Request $request, TranslatorInterface $translator, MailerInterface $mailer): Response
    {
        if (
            ! $edition
            || null !== $edition->getDeletedAt()
            || ! $this->systemEvaluationConfigService->get($edition)
            || ! $this->systemEvaluationConfigService->get($edition)->getArticeSubmissionAvaliable()
            || ! $this->getUser()
        ) {
            return new Response('', 404);
        }

        $userRepository = $this->getDoctrine()->getRepository(UserArticles::class);
        if ($userRepository->getNumberOfNotCanceledArticlesByEditionAndAuthor($edition->getId(), $this->getUser()->getId()) >= 3) {
            $this->addFlash('error.dashboard', 'ARTICLE_QUANTITY_EXCEEDED');
            return $this->redirectToRoute('dashboard_user_index');
        }

        $this->get('twig')->addGlobal('pageTitle', 'ARTICLE_SUBMISSION_TITLE');
        $userArticles = new UserArticles();
        $userArticles->setUserId($this->getUser());
        $userArticles->setEditionId($edition);

        $step = (int)$request->get('step', self::INIT_STEP);

        UserArticlesType::$step = $step;

        if ($step < 3) {
            UserArticlesAuthorsType::$validationEnabled = false;
        }

        $form = $this->createForm(UserArticlesType::class, $userArticles, ['edition' => $edition]);
        $form->handleRequest($request);


        // inicialmente eram dois checkboxes. depois, os dois juntos se tornaram "um" radio
        // quando o form não foi submetido ainda, marca o job complete como a opção "default"
        if (! $form->isSubmitted()) {
            if (
                ! $form->get('jobComplete')->getData()
                && ! $form->get('resumeFlag')->getData()
            ) {
                $form->get('jobComplete')->setData(true);
            }
        }

        if ($form->isSubmitted() && $request->isXmlHttpRequest()) {
            // valida o enquadramento
            // A opção "Casos para Ensino" do campo "Como o trabalho se enquadra" deve estar disponível
            // somente de a divisão escolhida for EPQ e tema "Casos para Ensino".
            if ($form->get('userThemes')->getData() && $form->get('frame')->getData()) {
                if (
                    'Casos para Ensino' === $form->get('userThemes')->getData()->getDetails()->getTitle()
                    && 5 !== (int)$form->get('frame')->getData()
                ) {
                    $form->get('frame')->addError(new FormError($translator->trans('ARTICLE_CASES_FOR_TEACHING_MESSAGE2')));
                } elseif (
                    5 === (int)$form->get('frame')->getData()
                    && 'Casos para Ensino' !== $form->get('userThemes')->getData()->getDetails()->getTitle()
                ) {
                    $form->get('frame')->addError(new FormError($translator->trans('ARTICLE_CASES_FOR_TEACHING_MESSAGE')));
                }
            }

            // validação do idioma
            if (
                ! $form->get('portuguese')->getData()
                && ! $form->get('english')->getData()
                && ! $form->get('spanish')->getData()
            ) {
                $form->get('portuguese')->addError(new FormError('Este campo deve ser preenchido'));
            }

            if (
                ! $form->get('jobComplete')->getData()
                && ! $form->get('resumeFlag')->getData()
            ) {
                $form->get('jobComplete')->addError(new FormError('Este campo deve ser preenchido'));
            }

            // tratamento da tela 2 (resumo)
            if ($step > 1) {
                $userArticles->setResume(
                    implode(' ',
                        array_slice(
                            explode(' ',
                                preg_replace('/\s+/', ' ', $userArticles->getResume())
                            ), 0, $userArticles->getEditionId()->getEvent()->getNumberWords() ?: 200
                        )
                    )
                );
            }

            // validação da tela 3 (autores)
            if ($step > 2) {
                $isLoggedUserExists = false;
                $mapUsers = [];
                if (count($form->get('userArticlesAuthors')) > 0) {
                    foreach ($form->get('userArticlesAuthors') as $key => $userArticlesAuthor) {
                        /** @var User $userAuthor */
                        $userAuthor = $userArticlesAuthor->get('userAuthorId')->getData();
                        if (! $userAuthor) {
                            continue;
                        }

                        if (in_array($userAuthor->getId(), $mapUsers)) {
                            $userArticlesAuthor->get('userAuthorId')->addError(new FormError('Já cadastrado'));
                        }

                        $mapUsers[] = $userAuthor->getId();

                        // valida se o usuário logado está na lista
                        if ((int)$userAuthor->getId() === (int)$this->getUser()->getId()) {
                            $isLoggedUserExists = true;
                        }

                        $userRepository = $this->getDoctrine()->getRepository(UserArticles::class);
                        if ($userRepository->getNumberOfNotCanceledArticlesByEditionAndAuthor($edition->getId(), $userAuthor->getId()) >= 3) {
                            $userArticlesAuthor->get('userAuthorId')->addError(new FormError(sprintf('O autor %s atingiu o limite máximo de %d trabalhos submetidos.<br /> A contagem do número de trabalhos não distingue autoria de coautoria.', $userAuthor->getName(), 3)));
                        }
                    }
                }
                if (! $isLoggedUserExists) {
                    $form->get('userArticlesAuthors')->addError(new FormError('O usuário logado deve estar na lista'));
                }
            }

            // validação da tela 4 (arquivos)
            if ($step > 3) {
                $fileTest = false;
                if (isset($request->files->get('user_articles')['userArticlesFiles'])) {
                    $fileTest = array_filter($request->files->get('user_articles')['userArticlesFiles']);
                    $fileTest = end($fileTest);
                }

                if ($fileTest && empty($fileTest['path'])) {
                    $fileError = new FormError('File not found');
                    $form->get('userArticlesFiles')->addError($fileError);
                } else {

                    $size = 0;
                    foreach ($form->get('userArticlesFiles') as $key => $file) {
                        $size = $size + $file->get('path')->getData()->getSize();
                    }

                    if (number_format($size / 1048576, 2) > 2) {
                        $fileError = new FormError('File over size');
                        $form->get('userArticlesFiles')->addError($fileError);
                    }
                }
            }

            if ($form->isSubmitted() && $form->isValid()) {

                if ($step > 3) {

                    // faz uma cópia dos dados de UserInstitutionsPrograms
                    if ($userArticles->getUserArticlesAuthors()->count() > 0) {
                        foreach ($userArticles->getUserArticlesAuthors() as $userArticleAuthor) {
                            if (
                                ! $userArticleAuthor->getUserAuthor()
                                || ! $userArticleAuthor->getUserAuthor()->getInstitutionsPrograms()
                            ) {
                                continue;
                            }

                            $institutionsPrograms = clone $userArticleAuthor->getUserAuthor()->getInstitutionsPrograms();

                            $userArticleAuthor->setStateFirst($institutionsPrograms->getStateFirstId());
                            $userArticleAuthor->setStateSecond($institutionsPrograms->getStateSecondId());

                            $userArticleAuthor->setInstitutionFirst($institutionsPrograms->getInstitutionFirstId());
                            $userArticleAuthor->setOtherInstitutionFirst($institutionsPrograms->getOtherInstitutionFirst());

                            $userArticleAuthor->setInstitutionSecond($institutionsPrograms->getInstitutionSecondId());
                            $userArticleAuthor->setOtherInstitutionSecond($institutionsPrograms->getOtherInstitutionSecond());

                            $userArticleAuthor->setProgramFirst($institutionsPrograms->getProgramFirstId());
                            $userArticleAuthor->setOtherProgramFirst($institutionsPrograms->getOtherProgramFirst());

                            $userArticleAuthor->setProgramSecond($institutionsPrograms->getProgramSecondId());
                            $userArticleAuthor->setOtherProgramSecond($institutionsPrograms->getOtherProgramSecond());

                        }
                    }

                    if (
                        $request->files->get('user_articles')
                        && isset($request->files->get('user_articles')['userArticlesFiles'])
                        && ! empty($request->files->get('user_articles')['userArticlesFiles'])
                    ) {

                        if (! $this->filesystem->exists($this->uploadPath)) {
                            $this->filesystem->mkdir($this->uploadPath, 0755);
                        }

                        foreach ($form->get('userArticlesFiles') as $key => $fileField) {
                            $file = $fileField->get('path')->getData();

                            if (! $file) {
                                continue;
                            }

                            $modifyArticle = $userArticles->getUserArticlesFiles()[$key];
                            $newFilename = self::PREFIX_ARTICLE_UPLOAD_FILE_NAME . '_' . uniqid() . "_{$key}_." . $file->guessExtension();
                            $modifyArticle->setPath($newFilename);
                            $modifyArticle->setCreatedAt(new \DateTime());
                            $file->move($this->uploadPath, $newFilename);
                        }
                    }

                    $entityManager = $this->getDoctrine()->getManager();
                    $userArticles->setCreatedAt(new \DateTime());
                    $userArticles->setStatus(1);
                    $userArticles->setIp($request->getClientIp());
                    $entityManager->persist($userArticles);
                    $entityManager->flush();

                    // faz o envio de email
                    if ($userArticles->getUserArticlesAuthors()->count() > 0) {
                        $authorsDetails = "";
                        foreach ($userArticles->getUserArticlesAuthors() as $userArticleAuthor) {
                            if (! $userArticleAuthor->getUserAuthor()) {
                                continue;
                            }

                            $authorsDetails .= sprintf("# %d  - %s - %s\n", $userArticleAuthor->getOrder(), $userArticleAuthor->getUserAuthor()->getName(), $userArticleAuthor->getUserAuthor()->getEmail());
                        }

                        foreach ($userArticles->getUserArticlesAuthors() as $userArticleAuthor) {
                            if (! $userArticleAuthor->getUserAuthor()) {
                                continue;
                            }

                            $email = new Email();
                            $email
                                ->from('ANPAD <noreply@anpad.org.br>')
                                ->to($userArticleAuthor->getUserAuthor()->getEmail())
                                //->cc('cc@example.com')
                                //->bcc('bcc@example.com')
                                //->replyTo('replyto@example.com')
                                //->priority(Email::PRIORITY_HIGH)
                                ->subject($translator->trans('ARTICLE_SUBMISSION_EMAIL_TITLE', [
                                    '%edition%' => $userArticles->getEditionId()->getName(),
                                ]))
                                ->text($translator->trans('ARTICLE_SUBMISSION_EMAIL_BODY_TEXT', [
                                    '%edition%' => $userArticles->getEditionId()->getName(),
                                    '%title%' => $userArticles->getTitle(),
                                    '%theme%' => $userArticles->getUserThemes()->getDetails()->getTitle(),
                                    '%resume%' => $userArticles->getResume(),
                                    '%authors%' => $authorsDetails,
                                ]))
                                ->html($translator->trans('ARTICLE_SUBMISSION_EMAIL_BODY_HTML', [
                                    '%edition%' => $userArticles->getEditionId()->getName(),
                                    '%title%' => $userArticles->getTitle(),
                                    '%theme%' => $userArticles->getUserThemes()->getDetails()->getTitle(),
                                    '%resume%' => $userArticles->getResume(),
                                    '%authors%' => nl2br($authorsDetails),
                                ]));

                            try {
                                $mailer->send($email);
                            } catch (TransportExceptionInterface $e) {

                            }
                        }
                    }

                    return new JsonResponse(['saved' => true, 'pass' => true], 200, ['x-step' => $step]);
                } else {
                    return new JsonResponse(['saved' => false, 'pass' => true], 200, ['x-step' => $step + 1]);
                }

            } else {
                $response = new Response($this->renderView('@Base/user_articles/partials/_index.html.twig', [
                    'form' => $form->createView(),
                    'userArticles' => $userArticles,
                    'step' => $step,
                ]));

                $response->setStatusCode(500)
                    ->headers->set('x-step', $step);

                return $response;
            }
        }

        return $this->render('@Base/user_articles/index.html.twig', [
            'form' => $form->createView(),
            'userArticles' => $userArticles,
            'step' => $step,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="article_submission_delete", methods={"GET", "POST"})
     *
     * @param UserArticles $id
     * @param Request $request
     *
     * @return Response
     */
    public function delete(UserArticles $id, Request $request): Response
    {
        $this->isAuthorizedUser($id->getUserArticlesAuthors());

        $config = $this->systemEvaluationConfigService->getArray($id->getEditionId());

        if (
            1 !== (int)$config['articeSubmissionAvaliable']
            || UserArticles::ARTICLE_EVALUATION_STATUS_WAITING !== $id->getStatus()
        ) {
            return new Response('', 404);
        }

        $id->setUserDeletedId($this->getUser());
        $id->setStatus(UserArticles::ARTICLE_EVALUATION_STATUS_CANCELED);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($id);
        $entityManager->flush();

        $this->addFlash('success.dashboard', 'ARTICLE_DELETE_MSG');

        return $this->redirect('/');
    }

    /**
     * @Route("/{article}/show", name="article_submission_show", methods={"GET"})
     *
     * @param UserArticles $article
     * @param Request $request
     *
     * @return Response
     */
    public function show(UserArticles $article, Request $request): Response
    {
        $this->isAuthorizedUser($article->getUserArticlesAuthors());

        $forms = [];

        if ($article->getEvaluations()->count() > 0) {
            foreach ($article->getEvaluations() as $evaluation) {
                SystemEvaluationAuthorRateType::$blockPrefixIndex = $evaluation->getId();
                $form = $this->createForm(SystemEvaluationAuthorRateType::class, $evaluation);
                $forms[] = $form->createView();
            }
        }

        $this->get('twig')->addGlobal('pageTitle', 'Ver Artigo');

        return $this->render('@Base/user_articles/show.html.twig', [
            'userArticle' => $article,
            'LANGUAGES' => array_flip(UserArticles::LANGUAGES),
            'FRAMES' => array_flip(UserArticles::FRAMES),
            'ARTICLE_RESULTING_FROM' => array_flip(UserArticles::ARTICLE_RESULTING_FROM),
            'data' => $article,
            'linkPath' => $this->linkPath,
            'forms' => $forms,
        ]);
    }

    /**
     * @Route("/{edition}/generate_approved_pdf/{page}", name="article_submission_generate_approved_pdf", methods={"GET"})
     *
     * @param Edition $edition
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @param int $page
     *
     * @return Response
     * @throws CrossReferenceException
     * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
     * @throws \setasign\Fpdi\PdfParser\PdfParserException
     * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
     * @throws \setasign\Fpdi\PdfReader\PdfReaderException
     */
    public function generateApprovedPdf(Edition $edition, Request $request, ParameterBagInterface $parameterBag, int $page = 0)
    {
        $maxResults = 20;
        $firstResult = $page * $maxResults;

        if (0 === $edition->getUserArticles([
                'status' => UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED,
                'firstResult' => $firstResult,
                'maxResults' => $maxResults,
            ])->count()) {

            $pdfPath = $this->uploadPath . $edition->getId() . '/approved';
            if (! $this->filesystem->exists($pdfPath)) {
                $this->filesystem->mkdir($pdfPath, 0755);
            }

            $zipPath = $this->uploadPath . $edition->getApprovedArticlesFile();

            if (file_exists($zipPath)) {
                unlink($zipPath);
            }

            $this->zipDirectory($pdfPath, $zipPath);

            return new Response('Ok');
        }

        $basePath = $parameterBag->get('kernel.project_dir') . '/public';

        foreach ($edition->getUserArticles([
            'status' => UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED,
            'firstResult' => $firstResult,
            'maxResults' => $maxResults,
        ]) as $article) {

            $pdfFullPath = $this->uploadPath . $article->getApprovedFile();
            $pdfPath = dirname($pdfFullPath);
            if (! $this->filesystem->exists($pdfPath)) {
                $this->filesystem->mkdir($pdfPath, 0755);
            }

            $date = $this->editionService->dateIntervalFormat($edition->getDateStart(), $edition->getDateEnd(), $request->getLocale());

            $color = $this->editionService->getColor($edition->getColor());

            [$r, $g, $b] = sscanf($color, "#%02x%02x%02x");

            $pdf = new Fpdi();

            $pdf->AddPage();

            // layout da primeira pagina
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Rect(0, 0, 300, 25, 'F');

            $pdf->SetFillColor($r, $g, $b);
            $pdf->Rect(0, 25, 300, 1, 'F');

            $pdf->Image($basePath . '/build/images/logo/anpad_002.png', -5, 6, 45);

            $pdf->SetY(3);
            $pdf->SetX(56);
            $pdf->SetTextColor($r, $g, $b);
            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(150, 10, iconv('utf-8', 'windows-1252//IGNORE', $edition->getLongname() . ' - ' . $edition->getName()), 0, 0, 'R');
            $pdf->Ln();

            $pdf->SetY(9);
            $pdf->SetX(56);
            $pdf->SetTextColor($r, $g, $b);
            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(150, 10, iconv('utf-8', 'windows-1252//IGNORE', $edition->getPlace() . ' - ' . $date), 0, 0, 'R');
            $pdf->Ln();

            $pdf->SetY(14);
            $pdf->SetX(56);
            $pdf->SetTextColor($r, $g, $b);
            $pdf->SetFont('Times', '', 10);
            $pdf->Cell(150, 10, iconv('utf-8', 'windows-1252//IGNORE', $edition->getEvent()->getIssn()), 0, 0, 'R');
            $pdf->Ln();

            $pdf->SetTextColor(0, 0, 0);
            // fim do layout da primeira pagina

            // begin Título
            $pdf->SetY(30);
            $pdf->SetX(29);
            $pdf->SetFont('Times', 'B', 12);
            $pdf->MultiCell(160, 6, iconv('utf-8', 'windows-1252//IGNORE', $article->getTitle()), 0, 'C');
            $pdf->Ln();
            $pdf->Ln();
            // end Título

            // begin Autoria
            $pdf->SetX(29);
            $pdf->SetFont('Times', 'B', 12);
            $pdf->MultiCell(160, 2, 'Autoria', 0, 'C');
            $pdf->Ln();

            if ($article->getUserArticlesAuthors()->count() > 0) {
                foreach ($article->getUserArticlesAuthors() as $articlesAuthor) {
                    $pdf->SetX(29);
                    $pdf->SetFont('Times', '', 12);
                    $pdf->MultiCell(160, 5, iconv('utf-8', 'windows-1252//IGNORE', $articlesAuthor->getUserAuthor()->getName()) . ' - ' . $articlesAuthor->getUserAuthor()->getEmail(), 0, 'C');

                    $line1 = $articlesAuthor->getInstitutionsProgramsFirst();

                    if (! empty($line1)) {
                        $pdf->SetX(29);
                        $pdf->SetFont('Times', '', 9);
                        $pdf->MultiCell(160, 5, iconv('utf-8', 'windows-1252//IGNORE', $line1), 0, 'C');
                    }

                    $line2 = $articlesAuthor->getInstitutionsProgramsSecond();

                    if (! empty($line2)) {
                        $pdf->SetX(29);
                        $pdf->SetFont('Times', '', 9);
                        $pdf->MultiCell(160, 5, iconv('utf-8', 'windows-1252//IGNORE', $line2), 0, 'C');
                        $pdf->Ln();
                    } else {
                        $pdf->Ln();
                    }
                }
            }
            $pdf->Ln();
            // end Autoria

            // begin Agradecimentos
            if (! empty($article->getAcknowledgment())) {
                $pdf->SetX(29);
                $pdf->SetFont('Times', 'B', 12);
                $pdf->MultiCell(160, 2, 'Agradecimentos', 0, 'C');
                $pdf->Ln();

                $pdf->SetX(29);
                $pdf->SetFont('Times', '', 12);
                $pdf->MultiCell(160, 5, iconv('utf-8', 'windows-1252//IGNORE', $article->getAcknowledgment()), 0, 'C');
                $pdf->Ln();
                $pdf->Ln();
            }
            // end Agradecimentos

            // begin Resumo
            $pdf->SetX(29);
            $pdf->SetFont('Times', 'B', 12);
            $pdf->MultiCell(160, 2, 'Resumo', 0, 'C');
            $pdf->Ln();

            $pdf->SetX(29);
            $pdf->SetFont('Times', '', 12);
            $pdf->MultiCell(160, 5, iconv('utf-8', 'windows-1252//IGNORE', $article->getResume()), 0, 'J');
            $pdf->Ln();
            // end Resumo

            if (
                ! $article->getResumeFlag()
                && $article->getJobComplete()
                && $article->getUserArticlesFiles()->count() > 0
            ) {
                foreach ($article->getUserArticlesFiles() as $articlesFile) {
                    if (empty($articlesFile->getPath())) {
                        continue;
                    }

                    $file = $this->uploadPath . $articlesFile->getPath();

                    if (! file_exists($file)) {
                        continue;
                    }

                    $pageCount = 0;

                    try {
                        $pageCount = $pdf->setSourceFile($file);
                    } catch (CrossReferenceException $e) {
                        $filepdf = fopen($file, "r");

                        if ($filepdf) {
                            $lineFirst = fgets($filepdf);
                            fclose($filepdf);

                            preg_match_all('/(\d+)(\.\d+)/', $lineFirst, $matches);

                            if (! empty($matches[0]) && ! empty($matches[0][0])) {
                                if (floatval($matches[0][0]) > 1.4) {
                                    $newFile = str_replace('.pdf', '.1.4.pdf', $file);
                                    shell_exec('gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile="' . $newFile . '" "' . $file . '"');

                                    try {
                                        $pageCount = $pdf->setSourceFile($newFile);
                                    } catch (CrossReferenceException $e) {
                                        echo 'Erro no arquivo ' . $file . '<br>';
                                        echo $e->getMessage();
                                    }
                                } else {
                                    echo 'Erro no arquivo ' . $file . '<br>';
                                }
                            } else {
                                echo 'Erro no arquivo ' . $file . '<br>';
                            }
                        } else {
                            echo 'Erro no arquivo ' . $file . '<br>';
                        }
                    }

                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $importedPage = $pdf->ImportPage($pageNo);
                        $templatesize = $pdf->getTemplatesize($importedPage);

                        $pdf->AddPage(
                            $templatesize[0] > $templatesize[1] ? 'L' : 'P',
                            [
                                $templatesize[0],
                                $templatesize[1],
                            ]
                        );

                        $pdf->useTemplate($importedPage);

                        // begin header
                        $pdf->Image($basePath . '/build/images/logo/anpad_002.png', -1, 3, 30);

                        $pdf->SetY(0);
                        $pdf->SetX(56);
                        $pdf->SetTextColor($r, $g, $b);
                        $pdf->SetFont('Times', '', 10);
                        $pdf->Cell(150, 10, iconv('utf-8', 'windows-1252//IGNORE', $edition->getLongname() . ' - ' . $edition->getName()), 0, 0, 'R');
                        $pdf->Ln();

                        $pdf->SetY(5);
                        $pdf->SetX(56);
                        $pdf->SetTextColor($r, $g, $b);
                        $pdf->SetFont('Times', '', 9);
                        $pdf->Cell(150, 10, iconv('utf-8', 'windows-1252//IGNORE', $edition->getPlace() . ' - ' . $date) . ' - ' . iconv('utf-8', 'windows-1252//IGNORE', $edition->getEvent()->getIssn()), 0, 0, 'R');
                        $pdf->Ln();
                        // end header
                    }
                }
            }

            $pdf->Output($pdfFullPath, 'F');
        }
        ?>
        Página <?= $page ?>
        <script type="text/javascript">
            setTimeout(function () {
                window.location = '/pt_br/article_submission/<?= $edition->getId() ?>/generate_approved_pdf/<?= $page + 1 ?>';
            }, 10);
        </script>
        <?php
        exit;
    }

    /**
     * @Route("/{edition}/generate_ensalement_report", name="article_submission_generate_ensalement_report", methods={"GET"})
     *
     * @param Edition $edition
     * @param PhpSpreadsheet $phpSpreadsheet
     * @param TranslatorInterface $translator
     *
     * @return Response|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function generateEnsalementReport(Edition $edition, PhpSpreadsheet $phpSpreadsheet, TranslatorInterface $translator)
    {
        if (0 === $edition->getUserArticles([
                'status' => UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED,
            ])->count()) {
            return new Response('Ok');
        }

        //if ('excel' === $request->get('export')) {
        $spreadsheet = $phpSpreadsheet->createSpreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(mb_substr(sprintf('Listagem de Artigos %s', $edition->getName()), 0, 31));
        $lineIndex = 1;

        $column = 1;
        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Id'));
        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Link'));
        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Arquivo'));
        $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($translator->trans('Path'));


        $lineIndex++;

        foreach ($edition->getUserArticles([
            'status' => UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED,
        ]) as $article) {

            if (1 == $article->getEditionId()->getEvent()->getId()) {
                $id = $article->getDivisionId()->getInitials();
            } else {
                $id = $article->getEditionId()->getEvent()->getName();
            }

            $info = pathinfo($article->getApprovedFile());

            $column = 1;
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($id . $article->getId());
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue('http://anpad.com.br' . UserArticles::PUBLIC_PATH . $article->getApprovedFile());
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($info['filename'] . '.' . $info['extension']);
            $sheet->getCellByColumnAndRow($column++, $lineIndex)->setValue($article->getApprovedFile());

            $lineIndex++;
        }

        // Gera arquivo
        $response = $phpSpreadsheet->createStreamedResponse($spreadsheet, 'Xls');

        // Redirect output to a client’s web browser (Xls)
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', sprintf('attachment;filename="Listagem de Artigos Aprovados %s.xls"', $edition->getName()));
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
        //}
    }

    /**
     * @Route("/{article}/declaration", name="article_submission_declaration", methods={"GET"})
     *
     * @param UserArticles $article
     * @param Request $request
     * @param ParameterBagInterface $parameterBag
     * @param Environment $templating
     * @param Pdf $snappy
     *
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function declaration(UserArticles $article, Request $request, ParameterBagInterface $parameterBag, Environment $templating, Pdf $snappy): Response
    {
        $pdfPath = $this->uploadPath . $article->getEditionId()->getId() . '/declaration/';

        if (! $this->filesystem->exists($pdfPath)) {
            $this->filesystem->mkdir($pdfPath, 0755);
        }

        $pdfFullPath = $pdfPath . md5($article->getId()) . '.pdf';

        $html = $this->renderView('@Base/user_articles/declaration.html.twig', [
            'userArticle' => $article,
            'signatureText' => nl2br("Claudia Bitencourt
            Diretora Científica da ANPAD
            Triênio 2021-2023"),
            'basePath' => $parameterBag->get('kernel.project_dir') . '/public',
            'showStyles' => false,
        ]);

        $snappy->generateFromHtml(iconv('utf-8', 'windows-1252//IGNORE', $html), $pdfFullPath, [
            'orientation' => PageSetup::ORIENTATION_PORTRAIT,
            'image-dpi' => 72,

            'margin-top' => 0,
            'margin-right' => 0,
            'margin-bottom' => 0,
            'margin-left' => 0,
        ], true);

        return new PdfResponse(file_get_contents($pdfFullPath), 'Declaracao-de-Aceite.pdf');

        /**
         * Para o caso de visualização no browser em 'debug'
         *
         * return $this->render('@Base/user_articles/declaration.html.twig', [
         * 'userArticle' => $article,
         * 'signatureText' => nl2br("Claudia Bitencourt
         * Diretora Científica da ANPAD
         * Triênio 2021-2023"),
         * 'basePath' => $parameterBag->get('kernel.project_dir') . '/public',
         * 'showStyles' => true,
         * ]);
         */
    }

    public static function zipDirectory($directory, $zipName)
    {
        // new zip
        $zip = new \ZipArchive();

        // get files
        $finder = new Finder();
        $finder->files()->in($directory);

        // loop files
        foreach ($finder as $file) {

            // open zip
            if ($zip->open($zipName, \ZipArchive::CREATE) !== true) {
                throw new FileException('Zip file could not be created/opened.');
            }

            // add to zip
            $zip->addFile($file->getRealpath(), basename($file->getRealpath()));

            // close zip
            if (! $zip->close()) {
                throw new FileException('Zip file could not be closed.');
            }
        }

        return $zip;
    }
}
