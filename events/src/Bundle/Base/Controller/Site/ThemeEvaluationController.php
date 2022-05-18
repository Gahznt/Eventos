<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\ThemeSubmissionConfig;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesEvaluationLog;
use App\Bundle\Base\Form\UserThemesEvaluationCancellationType;
use App\Bundle\Base\Form\UserThemesEvaluationConsiderationType;
use App\Bundle\Base\Form\UserThemesSearchType;
use App\Bundle\Base\Form\UserThemesType;
use App\Bundle\Base\Repository\ThemeSubmissionConfigRepository;
use App\Bundle\Base\Repository\UserThemesEvaluationLogRepository;
use App\Bundle\Base\Repository\UserThemesRepository;
use App\Bundle\Base\Services\User as UserService;
use App\Bundle\Base\Services\UserThemes as UserThemesService;
use App\Bundle\Base\Services\UserThemesEvaluationLog as UserThemesEvaluationLogService;
use App\Bundle\Base\Traits\AccessControl;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/theme_evaluation")
 */
class ThemeEvaluationController extends AbstractController
{
    use AccessControl;

    private UserThemesRepository $userThemesRepository;

    private ThemeSubmissionConfigRepository $submissionConfigRepository;

    private UserThemesEvaluationLogRepository $evaluationLogRepository;

    private UserService $userService;

    private UserThemesService $userThemesService;

    private UserThemesEvaluationLogService $userThemesEvaluationLogService;

    private ?ThemeSubmissionConfig $submissionConfig;

    public function __construct(UserThemesRepository $userThemesRepository, ThemeSubmissionConfigRepository $submissionConfigRepository, UserThemesEvaluationLogRepository $evaluationLogRepository, UserService $userService, UserThemesService $userThemesService, UserThemesEvaluationLogService $userThemesEvaluationLogService, Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator)
    {
        $this->userThemesRepository = $userThemesRepository;
        $this->submissionConfigRepository = $submissionConfigRepository;
        $this->evaluationLogRepository = $evaluationLogRepository;
        $this->userService = $userService;
        $this->userThemesService = $userThemesService;
        $this->userThemesEvaluationLogService = $userThemesEvaluationLogService;

        $this->submissionConfig = $this->submissionConfigRepository->findOneBy([
            'isEvaluationAvailable' => ThemeSubmissionConfig::IS_AVAILABLE_TRUE,
        ]);

        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Administrative', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Themes');
    }

    protected function checkUser(): void
    {
        if (! $this->getUser()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $user = $this->getUser();
        assert($user instanceof User);

        if (
            ! $this->userService->isAdmin($user)
            && ! $this->userService->isAdminOperational($user)
            && ! $this->userService->isDivisionCoordinator($user)
            && ! $this->userService->isDivisionCommittee($user)
        ) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }
    }

    protected function getSubmissionConfig()
    {
        if (isset($this->submissionConfig)) {
            return $this->submissionConfig;
        }

        $this->submissionConfig = $this->submissionConfigRepository->findOneBy([
            'isEvaluationAvailable' => ThemeSubmissionConfig::IS_AVAILABLE_TRUE,
        ]);

        return $this->submissionConfig;
    }

    protected function checkThemeStatus(UserThemes $entity): bool
    {
        return (
                $entity->getStatus() === UserThemes::THEME_EVALUATION_STATUS_WAITING
                || $entity->getStatus() === UserThemes::THEME_EVALUATION_STATUS_NOT_SELECTED
                || $entity->getStatus() === UserThemes::THEME_EVALUATION_STATUS_SELECTED
            )
            && $entity->getThemeSubmissionConfig()->getIsEvaluationAvailable();
    }

    /**
     * @Route("/", name="theme_evaluation_index", methods={"GET"})
     */
    public function index(): Response
    {
        $this->checkUser();

        if (! $this->getSubmissionConfig()) {
            return new Response('', 404);
        }

        $user = $this->getUser();
        assert($user instanceof User);

        if (! $this->userService->isAdmin($user) && ! $this->userService->isAdminOperational($user)) {
            return $this->redirectToRoute('theme_evaluation_list');
        }

        $this->get('twig')->addGlobal('pageTitle', 'TEVAL_TITLE');

        $dashboard = $this->userThemesRepository->sumDashboard($this->submissionConfig);

        return $this->render('@Base/theme/user/evaluation/tabs/dashboard/index.html.twig', $this->getPermissionsArray() + [
                'dashboard' => $dashboard,
            ]);
    }

    /**
     * @Route("/list", name="theme_evaluation_list", methods={"GET"})
     */
    public function list(Request $request): Response
    {
        $this->checkUser();

        if (! $this->getSubmissionConfig()) {
            return new Response('', 404);
        }

        $user = $this->getUser();
        assert($user instanceof User);

        $this->get('twig')->addGlobal('pageTitle', 'TEVAL_TITLE');

        $form = $this->createForm(UserThemesSearchType::class);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isAdminOperational($user)) {
            $form->remove('division');
        }

        $form->handleRequest($request);
        $criteria = $request->query->get('search', []);
        assert(is_array($criteria));

        if (! $this->userService->isAdmin($user) && ! $this->userService->isAdminOperational($user)) {
            if ($this->userService->isDivisionCoordinator($user)) {
                $criteria['division'] = $user->getUserDivisionCoordinator()->get(0)->getDivision()->getId();
            }

            if ($this->userService->isDivisionCommittee($user)) {
                $criteria['division'] = $user->getUserCommittees()->get(0)->getDivision()->getId();
            }
        }

        $query = $this->userThemesRepository->queryAllByConfig($this->submissionConfig, $criteria);
        $themes = $query->getQuery()->getResult();

        return $this->render('@Base/theme/user/evaluation/tabs/themes/index.html.twig', $this->getPermissionsArray() + [
                'form' => $form->createView(),
                'themes' => $themes,
            ]);
    }

    /**
     * @Route("/show/{id}", name="theme_evaluation_show", methods={"GET"})
     */
    public function show(UserThemes $entity): Response
    {
        $this->checkUser();

        if (! $this->checkThemeStatus($entity)) {
            // return new Response('', 404);
        }

        $this->get('twig')->addGlobal('pageTitle', 'TEVAL_TITLE');

        return $this->render('@Base/theme/user/evaluation/tabs/themes/show.html.twig', $this->getPermissionsArray() + [
                'userTheme' => $entity,
                'userThemes' => $entity,
            ]);
    }

    /**
     * @Route("/evaluation/{id}", name="theme_evaluation_evaluation", methods={"GET", "POST"})
     */
    public function evaluation(UserThemes $entity, Request $request, TranslatorInterface $translator): Response
    {
        $this->checkUser();

        if (! $this->checkThemeStatus($entity)) {
            return new Response('', 404);
        }

        $user = $this->getUser();
        assert($user instanceof User);

        $this->get('twig')->addGlobal('pageTitle', 'TEVAL_TITLE');

        $params = $request->get('user_themes', []);
        $researchers = $params['userThemesResearchers'] ?? [];

        $form = $this->createForm(UserThemesType::class, $entity, ['step' => 1, 'researchers' => $researchers]);
        $form->remove('userThemesResearchers');
        if (! $this->userService->isAdmin($user) && ! $this->userService->isAdminOperational($user)) {
            $form->remove('division');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->userThemesService->validateStep1($form, $translator);

            if ($form->isValid()) {

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                $this->addFlash('success', 'Submissão alterada com sucesso.');

                $this->userThemesEvaluationLogService->register(
                    $entity,
                    $this->getUser(),
                    $request->getClientIp(),
                    UserThemesEvaluationLog::ACTION_UPDATE_DATA,
                    'Dados alterados por "' . $user->getName() . '"',
                    false
                );
            } else {
                $this->addFlash('error', 'Não foi possível validar os dados.');
            }
        }

        return $this->render('@Base/theme/user/evaluation/tabs/themes/evaluation.html.twig', $this->getPermissionsArray() + [
                'form' => $form->createView(),
                'userThemes' => $entity,
                'step' => 1,
                'submmited' => true,
                'formCancellation' => $this->createForm(UserThemesEvaluationCancellationType::class)->createView(),
            ]);
    }

    /**
     * @Route("/evaluation/{id}/proponents", name="theme_evaluation_evaluation_proponents", methods={"GET"})
     */
    public function evaluationProponents(UserThemes $entity, Request $request): Response
    {
        $this->checkUser();

        if (! $this->checkThemeStatus($entity)) {
            return new Response('', 404);
        }

        $user = $this->getUser();
        assert($user instanceof User);

        $this->get('twig')->addGlobal('pageTitle', 'TEVAL_TITLE');

        $params = $request->get('user_themes', []);
        $researchers = $params['userThemesResearchers'] ?? [];

        $step = (int)$request->get('step', ThemeSubmissionController::INIT_STEP);

        $form = $this->createForm(UserThemesType::class, $entity, ['step' => $step, 'researchers' => $researchers]);
        if (! $this->userService->isAdmin($user) && ! $this->userService->isAdminOperational($user)) {
            $form->remove('division');
        }

        return $this->render('@Base/theme/user/evaluation/tabs/themes/proponents.html.twig', $this->getPermissionsArray() + [
                'form' => $form->createView(),
                'userThemes' => $entity,
                'step' => $step,
                'submmited' => true,
                'formCancellation' => $this->createForm(UserThemesEvaluationCancellationType::class)->createView(),
            ]);
    }

    /**
     * @Route("/evaluation/{id}/considerations", name="theme_evaluation_evaluation_considerations", methods={"GET", "POST"})
     */
    public function evaluationConsiderations(UserThemes $entity, Request $request, TranslatorInterface $translator): Response
    {
        $this->checkUser();

        if (! $this->checkThemeStatus($entity)) {
            return new Response('', 404);
        }

        $user = $this->getUser();
        assert($user instanceof User);

        $this->get('twig')->addGlobal('pageTitle', 'TEVAL_TITLE');

        $evaluationLog = new UserThemesEvaluationLog();

        $form = $this->createForm(UserThemesEvaluationConsiderationType::class, $evaluationLog);
        $form->get('status')->setData($entity->getStatus());
        $form->get('position')->setData($entity->getPosition());
        $form->handleRequest($request);


        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $evaluationLog->setUserThemes($entity);
                $evaluationLog->setUser($user);
                $evaluationLog->setAction(UserThemesEvaluationLog::ACTION_CONSIDERATION);
                $evaluationLog->setIp($request->getClientIp());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($evaluationLog);

                $this->addFlash('success', 'Consideração feita com sucesso.');

                // verifica se a ordenação foi alterada e atualiza a entity UserThemes
                if ($form->get('position')->getData() != $entity->getPosition()) {
                    $entity->setPosition((int)$form->get('position')->getData());

                    // grava log de alteração de dados
                    $this->userThemesEvaluationLogService->register(
                        $entity,
                        $this->getUser(),
                        $request->getClientIp(),
                        UserThemesEvaluationLog::ACTION_UPDATE_DATA,
                        'Ordenação alterada para "' . $entity->getPosition() . '" por "' . $user->getName() . '"',
                        false
                    );
                }

                // verifica se o status foi alterado e atualiza a entity UserThemes
                if ($form->get('status')->getData() != $entity->getStatus()) {
                    $entity->setStatus((int)$form->get('status')->getData());

                    $statusOptions = array_flip(UserThemes::THEME_EVALUATION_STATUS);
                    $statusString = $translator->trans($statusOptions[$entity->getStatus()]);

                    // grava log de alteração de status
                    $this->userThemesEvaluationLogService->register(
                        $entity,
                        $this->getUser(),
                        $request->getClientIp(),
                        UserThemesEvaluationLog::ACTION_UPDATE_STATUS,
                        'Status alterado para "' . $statusString . '" por "' . $user->getName() . '"',
                        false
                    );
                }

                $entityManager->flush();

                return $this->redirectToRoute('theme_evaluation_evaluation_considerations', ['id' => $entity->getId()]);
            } else {
                $this->addFlash('error', 'Não foi possível validar os dados.');
            }
        }

        // lista o histórico
        $logs = $this->evaluationLogRepository->findBy([
            'userThemes' => $entity->getId(),
            'action' => UserThemesEvaluationLog::ACTION_CONSIDERATION,
        ], ['id' => 'DESC']);

        return $this->render('@Base/theme/user/evaluation/tabs/themes/considerations.html.twig', $this->getPermissionsArray() + [
                'form' => $form->createView(),
                'userThemes' => $entity,
                'logs' => $logs,
                'formCancellation' => $this->createForm(UserThemesEvaluationCancellationType::class)->createView(),
            ]);
    }

    /**
     * @Route("/evaluation/{id}/logs", name="theme_evaluation_evaluation_logs", methods={"GET"})
     */
    public function evaluationLogs(UserThemes $entity, Request $request): Response
    {
        $this->checkUser();

        if (! $this->checkThemeStatus($entity)) {
            return new Response('', 404);
        }

        $user = $this->getUser();
        assert($user instanceof User);

        $this->get('twig')->addGlobal('pageTitle', 'TEVAL_TITLE');

        $criteria = Criteria::create();
        $criteria->andWhere(
            Criteria::expr()->andX(
                Criteria::expr()->eq('userThemes', $entity),
                Criteria::expr()->neq('action', UserThemesEvaluationLog::ACTION_CONSIDERATION)
            )
        );
        $criteria->orderBy(['id' => 'DESC']);

        $logs = $this->evaluationLogRepository->matching($criteria)->toArray();

        return $this->render('@Base/theme/user/evaluation/tabs/themes/logs.html.twig', $this->getPermissionsArray() + [
                'userThemes' => $entity,
                'logs' => $logs,
                'formCancellation' => $this->createForm(UserThemesEvaluationCancellationType::class)->createView(),
            ]);
    }

    /**
     * @Route("/evaluation/{id}/cancellation", name="theme_evaluation_evaluation_cancellation", methods={"POST"})
     */
    public function evaluationCancellation(UserThemes $entity, Request $request, TranslatorInterface $translator): Response
    {
        $this->checkUser();

        if (! $this->checkThemeStatus($entity)) {
            return new Response('', 404);
        }

        $user = $this->getUser();
        assert($user instanceof User);

        $evaluationLog = new UserThemesEvaluationLog();

        $form = $this->createForm(UserThemesEvaluationCancellationType::class, $evaluationLog);
        $form->handleRequest($request);

        if (! $form->isSubmitted() || ! $form->isValid()) {
            return new JsonResponse([
                'message' => $translator->trans('Não foi possível validar os dados.'),
            ], 400);
        }

        // grava a consideração
        $evaluationLog->setUserThemes($entity);
        $evaluationLog->setUser($user);
        $evaluationLog->setAction(UserThemesEvaluationLog::ACTION_CANCEL_SUBMISSION);
        $evaluationLog->setIp($request->getClientIp());
        $evaluationLog->setVisibleAuthor(false);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($evaluationLog);

        // cancela o tema
        $entity->setStatus(UserThemes::THEME_EVALUATION_STATUS_CANCELED);

        $entityManager->flush();

        return new JsonResponse([], 201);
    }
}
