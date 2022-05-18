<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Panel;
use App\Bundle\Base\Entity\PanelEvaluationAction;
use App\Bundle\Base\Entity\PanelEvaluationList;
use App\Bundle\Base\Entity\PanelEvaluationLog;
use App\Bundle\Base\Form\PanelEvaluationActionType;
use App\Bundle\Base\Form\PanelEvaluationListType;
use App\Bundle\Base\Form\PanelType;
use App\Bundle\Base\Repository\DivisionRepository;
use App\Bundle\Base\Repository\PanelEvaluationLogRepository;
use App\Bundle\Base\Repository\PanelRepository;
use App\Bundle\Base\Repository\UserInstitutionsProgramsRepository;
use App\Bundle\Base\Services\Helper\PanelEvaluation;
use App\Bundle\Base\Services\PanelEvaluationLog as PanelEvaluationLogService;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;
use Yectep\PhpSpreadsheetBundle\Factory as PhpSpreadsheet;


/**
 *
 * @Route("/panel_evaluation")
 *
 * Class PanelEvaluationController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class PanelEvaluationController extends AbstractController
{

    const INIT_STEP = 1;

    /**
     * @var PanelRepository
     */
    private $panelRepository;

    /**
     * @var PanelEvaluationLogRepository
     */
    private $PanelEvaluationLogRepository;

    /**
     * @var DivisionRepository
     */
    private $divisionRepository;

    /**
     * @var UserInstitutionsProgramsRepository
     */
    private $userInstitutionsProgramsRepository;

    /**
     * @var PanelEvaluationLogService
     */
    private $panelEvaluationLogService;

    /**
     * @var string|string[]
     */
    private $uploadPath = Panel::UPLOAD_PATH;


    /**
     * PanelEvaluationController constructor.
     *
     * @param PanelRepository $panelRepository
     * @param PanelEvaluationLogRepository $PanelEvaluationLogRepository
     * @param DivisionRepository $divisionRepository
     * @param UserInstitutionsProgramsRepository $userInstitutionsProgramsRepository
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     * @param PanelEvaluationLogService $panelEvaluationLogService
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        PanelRepository $panelRepository,
        PanelEvaluationLogRepository $PanelEvaluationLogRepository,
        DivisionRepository $divisionRepository,
        UserInstitutionsProgramsRepository $userInstitutionsProgramsRepository,
        Breadcrumbs $breadcrumbs,
        UrlGeneratorInterface $urlGenerator,
        PanelEvaluationLogService $panelEvaluationLogService,
        ParameterBagInterface $parameterBag
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Administrative', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('PANEL_TITLE');

        $this->panelRepository = $panelRepository;
        $this->PanelEvaluationLogRepository = $PanelEvaluationLogRepository;
        $this->divisionRepository = $divisionRepository;
        $this->userInstitutionsProgramsRepository = $userInstitutionsProgramsRepository;
        $this->panelEvaluationLogService = $panelEvaluationLogService;
        $this->uploadPath = str_replace('#KERNEL#', $parameterBag->get('kernel.project_dir'), $this->uploadPath);
    }


    /**
     * @Route("/", name="panel_evaluation_index", methods={"GET"})
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $this->get('twig')->addGlobal('pageTitle', 'PEVAL_TITLE');

        $panelEvaluationList = new PanelEvaluationList();

        $step = (int)$request->get('step', self::INIT_STEP);
        $stepDet = (int)$request->get('stepDet', self::INIT_STEP);
        $filters = [];
        $panelId = null;

        switch ($step) {
            case 2:
                // Ação de busca na listagem de temas
                $panelView = new Panel;

                $filters = $request->get('panel_evaluation_list') ?? [];
                if (isset($filters['id']) && $filters['id']) {
                    $panelEvaluationList->setId($this->panelRepository->find($filters['id']));
                }
                if (isset($filters['divisionId']) && $filters['divisionId']) {
                    $panelEvaluationList->setDivisionId($this->divisionRepository->find($filters['divisionId']));
                }
                if (isset($filters['statusEvaluation']) && $filters['statusEvaluation']) {
                    $panelEvaluationList->setStatusEvaluation($filters['statusEvaluation']);
                }
                if (isset($filters['search']) && $filters['search']) {
                    $panelEvaluationList->setSearch($filters['search']);
                }

                $panels = $this->panelRepository->findByFilters($filters);
                break;

            case 3:
                // Ação de click para visualizar tema na listagem de temas
                $instProponent = null;
                $panelId = $request->get('panelId');
                $panelView = $this->panelRepository->find($panelId);
                $panelLog = $this->PanelEvaluationLogRepository->findBy(['panel' => $panelId], ['createdAt' => 'DESC']);
                $panelLogSub = $this->PanelEvaluationLogRepository->findOneBy(['panel' => $panelId, 'action' => PanelEvaluationLog::ACTION_SUBMISSION]);

                $panels = $this->panelRepository->findBy([], ['id' => 'asc']);
                $filters = ['id' => $panelId];
                $panelDet = $this->panelRepository->findOneBy($filters, ['id' => 'asc']);
                $panelDet->setStatusEvaluationText();
                $instPanelists = [];
                foreach ($panelDet->getPanelsPanelists() as $panelist) {
                    $instPanelists[$panelist->getPanelistId()->getId()] =
                        $this->userInstitutionsProgramsRepository->findOneBy(['user' => $panelist->getPanelistId()->getId()]);
                }
                if ($panelDet->getProponentId() !== null) {
                    $instProponent = $this->userInstitutionsProgramsRepository->findOneBy(['user' => $panelDet->getProponentId()->getId()]);
                }


                break;

            default:
                $panelView = new Panel;
                $panels = $this->panelRepository->findBy([], ['id' => 'asc']);
                break;
        }

        $dashboard = $this->panelRepository->sumDashboard();

        // Cria forms
        $formList =
            $this->createForm(PanelEvaluationListType::class, $panelEvaluationList,
                ['allow_extra_fields' => true, 'method' => 'GET']);
        $formView =
            $this->createForm(PanelType::class, $panelView,
                ['allow_extra_fields' => true, 'method' => 'GET']);

        return $this->render('@Base/panel/evaluation/index.html.twig', [
            'formList' => $formList->createView(),
            'formView' => $formView->createView(),
            'panels' => $panels,
            'panel' => $panelDet ?? null,
            'dashboard' => $dashboard,
            'step' => $step,
            'stepDet' => $stepDet,
            'panelId' => $panelId,
            'panelLog' => $panelLog ?? null,
            'panelLogSub' => $panelLogSub ?? null,
            'instPanelists' => $instPanelists ?? null,
            'instProponent' => $instProponent ?? null,
        ]);
    }

    /**
     * @Route("/consideration", name="panel_evaluation_consideration", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function consideration(Request $request): Response
    {

        try {
            $panelEdit = $this->panelRepository->find($request->get('panelId'));

            $this->panelEvaluationLogService->register(
                $panelEdit,
                $this->getUser(),
                $request->getClientIp(),
                PanelEvaluationLog::ACTION_CONSIDERATION,
                $request->get('panelConsideration'),
                $request->get('panelConsiderationAuthor')
            );

            return new JsonResponse(['saved' => true], 200);

        } catch (\Exception $e) {
            return new JsonResponse(['saved' => false], 500, ['x-error' => $e->getMessage()]);
        }
    }

    /**
     * @Route("/cancel", name="panel_evaluation_cancel", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function cancel(Request $request): Response
    {

        try {
            $newStatus = Panel::PANEL_EVALUATION_STATUS['PANEL_EVALUATION_STATUS_CANCELED'];

            $panelEdit = $this->panelRepository->find($request->get('panelId'));
            $panelEdit->setStatusEvaluation($newStatus);

            $this->panelEvaluationLogService->register(
                $panelEdit,
                $this->getUser(),
                $request->getClientIp(),
                PanelEvaluationLog::ACTION_CANCEL_SUBMISSION,
                'Submissão cancelada a pedido do usuário',
                true
            );

            return new JsonResponse(['saved' => true], 200);

        } catch (\Exception $e) {
            return new JsonResponse(['saved' => false], 500, ['x-error' => preg_replace('/\n/', ' ', $e->getMessage())]);
        }
    }


    /**
     * @Route("/spreadsheet", name="panel_evaluation_spreadsheet", methods={"POST"})
     *
     * @param Request $request
     * @param PhpSpreadsheet $excel
     *
     * @return JsonResponse|StreamedResponse
     */
    public function spreadsheet(Request $request, PhpSpreadsheet $excel)
    {

        try {
            $panelsheet = $excel->createSpreadsheet();
            $panelsheet->getProperties()
                ->setCreator('ANPAD')
                ->setTitle('Planilha de Submissão de Painéis');

            $sheet = $panelsheet->getActiveSheet();
            $sheet->setTitle('Painéis');

            // Header
            $line = 2;
            $col = 'A';
            $sheet
                ->setCellValue($col++ . $line, 'Id')
                ->setCellValue($col++ . $line, 'Divisão')
                ->setCellValue($col++ . $line, 'Status')
                ->setCellValue($col++ . $line, 'Título')
                ->setCellValue($col++ . $line, 'Justificativa')
                ->setCellValue($col++ . $line, 'Sugestão');

            $qtdPanelistas = 5;
            $panCol = [];
            for ($k = 1; $k <= $qtdPanelistas; $k++) {
                $panCol[$k][] = $col;
                $sheet
                    ->setCellValue($col++ . $line, 'Nome')
                    ->setCellValue($col++ . $line, 'CPF')
                    ->setCellValue($col++ . $line, 'E-mail')
                    ->setCellValue($col . $line, 'Sigla');
                $panCol[$k][] = $col;
                $col++;
            }

            $sheet
                ->setCellValue($col++ . $line, 'Proponente')
                ->setCellValue($col++ . $line, 'CPF/Passaporte')
                ->setCellValue($col++ . $line, 'E-mail')
                ->setCellValue($col++ . $line, 'Titulação');

            $qtdInst = 2;
            $instCol = [];
            for ($k = 1; $k <= $qtdInst; $k++) {
                $instCol[$k][] = $col;
                $sheet
                    ->setCellValue($col++ . $line, 'Nome')
                    ->setCellValue($col++ . $line, 'Programa')
                    ->setCellValue($col . $line, 'Estado');
                $instCol[$k][] = $col;
                if ($k < $qtdInst) {
                    $col++;
                }
            }
            $lastCol = $col;

            // Merges
            for ($k = 1; $k <= $qtdPanelistas; $k++) {
                $sheet->mergeCells($panCol[$k][0] . '1:' . $panCol[$k][1] . '1');
                $sheet->setCellValue($panCol[$k][0] . '1', 'Panelista ' . $k);
                $sheet->getStyle($panCol[$k][0] . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($panCol[$k][0] . '1')->getFont()->setBold(true);
            }
            for ($k = 1; $k <= $qtdInst; $k++) {
                $sheet->mergeCells($instCol[$k][0] . '1:' . $instCol[$k][1] . '1');
                $sheet->setCellValue($instCol[$k][0] . '1', 'Instituição ' . $k);
                $sheet->getStyle($instCol[$k][0] . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($instCol[$k][0] . '1')->getFont()->setBold(true);
            }

            // Header format
            $sheet->getStyle('A1:' . $lastCol . $line)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:' . $lastCol . $line)->getFont()->setBold(true);
            $sheet->getStyle('A1:' . $lastCol . $line)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()
                ->setARGB('FFCCCCCC');

            // Loop painéis
            $panels = $this->panelRepository->findBy([], ['id' => 'DESC']);
            foreach ($panels as $panel) {
                $line++;
                $col = 'A';

                // Painel
                $sheet
                    ->setCellValue($col++ . $line, $panel->getId())
                    ->setCellValue($col++ . $line, $panel->getDivisionId()->getInitials())
                    ->setCellValue($col++ . $line, PanelEvaluation::getStatusText($panel->getStatusEvaluation()))
                    ->setCellValue($col++ . $line, $panel->getTitle())
                    ->setCellValue($col++ . $line, $panel->getJustification())
                    ->setCellValue($col++ . $line, $panel->getSuggestion());

                // Painelistas
                for ($k = 1; $k <= $qtdPanelistas; $k++) {
                    $panelist = $panel->getPanelsPanelists()[($k - 1)] ?? null;
                    if ($panelist) {
                        $sheet
                            ->setCellValue($col++ . $line, $panelist->getPanelistId()->getName() ?? '')
                            ->setCellValue($col++ . $line, $panelist->getPanelistId()->getIdentifier() ?? '')
                            ->setCellValue($col++ . $line, $panelist->getPanelistId()->getEmail() ?? '')
                            ->setCellValue($col++ . $line, $panelist->getPanelistId()->getCity()->getCodeState() ?? '');
                    } else {
                        for ($w = 0; $w < 4; $w++) {
                            $sheet->setCellValue($col++ . $line, '');
                        }
                    }
                }

                // Proponente
                $sheet
                    ->setCellValue($col++ . $line, $panel->getProponentId()->getName() ?? '')
                    ->setCellValue($col++ . $line, $panel->getProponentId()->getIdentifier() ?? '')
                    ->setCellValue($col++ . $line, $panel->getProponentId()->getEmail() ?? '')
                    ->setCellValue($col++ . $line, $panel->getProponentId()->getNickname());

                // Instituição
                $institution = $this->userInstitutionsProgramsRepository->findOneBy(['user' => $panel->getProponentId()->getId()]);
                if ($institution) {
                    $sheet
                        ->setCellValue($col++ . $line, $institution->getInstitutionFirstId()->getName())
                        ->setCellValue($col++ . $line, $institution->getProgramFirstId()->getName())
                        ->setCellValue($col++ . $line, $institution->getInstitutionFirstId()->getCity()->getCodeState());

                    if ($institution->getInstitutionSecondId()) {
                        $sheet
                            ->setCellValue($col++ . $line, $institution->getInstitutionSecondId()->getName())
                            ->setCellValue($col++ . $line, $institution->getProgramSecondId()->getName())
                            ->setCellValue($col++ . $line, $institution->getInstitutionSecondId()->getCity()->getCodeState());
                    }
                }
            }

            // Gera arquivo
            $response = $excel->createStreamedResponse($panelsheet, 'Xls');

            // Redirect output to a client’s web browser (Xls)
            $response->headers->set('Content-Type', 'application/vnd.ms-excel');
            $response->headers->set('Content-Disposition', 'attachment;filename="PanelSubmission.xls"');
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * @Route("/action", name="panel_evaluation_action", methods={"GET"})
     *
     * @return Response
     */
    public function action(Request $request): Response
    {
        $this->get('twig')->addGlobal('pageTitle', 'PEVAL_TITLE');

        $panelEvaluationList = new PanelEvaluationList();
        $panelEvaluationAction = new PanelEvaluationAction();

        $step = (int)$request->get('step', self::INIT_STEP);
        $flashSuccess = (int)$request->get('flashSuccess');
        $filters = [];
        $panelId = null;

        switch ($step) {
            case 1:
                // Ação de busca na listagem de temas
                $filters = $request->get('panel_evaluation_list') ?? [];
                if (isset($filters['id']) && $filters['id']) {
                    $panelEvaluationList->setId($this->panelRepository->find($filters['id']));
                }
                if (isset($filters['divisionId']) && $filters['divisionId']) {
                    $panelEvaluationList->setDivisionId($this->divisionRepository->find($filters['divisionId']));
                }
                if (isset($filters['search']) && $filters['search']) {
                    $panelEvaluationList->setSearch($filters['search']);
                }

                // Fixa status aguardando
                $filters['statusEvaluation'] = Panel::PANEL_EVALUATION_STATUS['PANEL_EVALUATION_STATUS_WAITING'];
                $panelEvaluationList->setStatusEvaluation($filters['statusEvaluation']);
                $panels = $this->panelRepository->findByFilters($filters);
                $panelLog = new PanelEvaluationLog;
                break;

            case 2:
                // Ação de mostrar tela de avaliação
                $panelId = $request->get('id');
                $panelLog = $this->PanelEvaluationLogRepository->findBy(['panel' => $panelId], ['createdAt' => 'DESC']);

                $panelEdit = $this->panelRepository->find($panelId);

                $panelEvaluationAction
                    ->setId($panelEdit)
                    ->setDivisionId($panelEdit->getDivisionId())
                    ->setTitle($panelEdit->getTitle())
                    ->setStatusEvaluation($panelEdit->getStatusEvaluation());

                $filters = ['id' => $panelId];
                $panels = $this->panelRepository->findByFilters($filters);
                break;

        }

        // Cria forms
        $formList =
            $this->createForm(PanelEvaluationListType::class, $panelEvaluationList,
                ['allow_extra_fields' => true, 'method' => 'GET']);

        $formAction =
            $this->createForm(PanelEvaluationActionType::class, $panelEvaluationAction,
                ['allow_extra_fields' => true, 'method' => 'GET']);

        if ($flashSuccess) {
            $this->addFlash('success', 'Painel avaliado com sucesso');
        }

        return $this->render('@Base/panel/evaluation/action/index.html.twig', [
            'formList' => $formList->createView(),
            'formAction' => $formAction->createView(),
            'panels' => $panels,
            'panelLog' => $panelLog,
            'step' => $step,
            'panelId' => $panelId,
        ]);
    }


    /**
     * @Route("/action/evaluate", name="panel_evaluation_action_evaluate", methods={"POST"})
     *
     * @return responseJson
     */
    public function evaluate(Request $request): Response
    {

        try {
            $newStatus = $request->get('panel_evaluation_action')['statusEvaluation'];

            $panelEdit = $this->panelRepository->find($request->get('panelId'));
            $panelEdit->setStatusEvaluation($newStatus);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($panelEdit);
            $entityManager->flush();

            $this->panelEvaluationLogService->register(
                $panelEdit,
                $this->getUser(),
                $request->getClientIp(),
                PanelEvaluationLog::ACTION_UPDATE_STATUS,
                'Painel #' . $panelEdit->getId() . ' status=' . $newStatus .
                ' (' . PanelEvaluation::getStatusText($newStatus) . ')',
                true
            );

            return new JsonResponse(['saved' => true], 200);

        } catch (\Exception $e) {
            return new JsonResponse(['saved' => false], 500, ['x-error' => preg_replace('/\n/', ' ', $e->getMessage())]);
        }
    }


    /**
     * @Route("/download", name="panel_evaluation_download", methods={"POST"})
     *
     * @return Response
     */
    public function download(Request $request)
    {
        try {
            $parameters = json_decode($request->getContent());
            if (! is_object($parameters) && ! $$parameters->file) {
                throw new \Exception("Missing download file");
            }
            $response = new BinaryFileResponse($this->uploadPath . $parameters->file);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

            return $response;

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
