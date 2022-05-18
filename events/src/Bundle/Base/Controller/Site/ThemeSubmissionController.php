<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\ThemeSubmissionConfig;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesEvaluationLog;
use App\Bundle\Base\Form\UserThemesType;
use App\Bundle\Base\Repository\ThemeSubmissionConfigRepository;
use App\Bundle\Base\Services\User as UserService;
use App\Bundle\Base\Services\UserThemes as UserThemesService;
use App\Bundle\Base\Services\UserThemesEvaluationLog as UserThemesEvaluationLogService;
use App\Bundle\Base\Traits\AccessControl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("theme_submission")
 */
class ThemeSubmissionController extends AbstractController
{
    use AccessControl;

    const INIT_STEP = 1;
    const LAST_STEP = 3;

    private ThemeSubmissionConfigRepository $submissionConfigRepository;

    private UserService $userService;

    private UserThemesService $userThemesService;

    private UserThemesEvaluationLogService $userThemesEvaluationLogService;

    private ?ThemeSubmissionConfig $submissionConfig;

    public function __construct(Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator, ThemeSubmissionConfigRepository $submissionConfigRepository, UserService $userService, UserThemesService $userThemesService, UserThemesEvaluationLogService $userThemesEvaluationLogService)
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Events', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Theme Submission');

        $this->submissionConfigRepository = $submissionConfigRepository;
        $this->userService = $userService;
        $this->userThemesService = $userThemesService;
        $this->userThemesEvaluationLogService = $userThemesEvaluationLogService;
    }

    protected function checkUser(?UserThemes $userThemes = null): void
    {
        if (! $this->getUser()) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        if (null === $userThemes) {
            // não exite submissão ainda
            return;
        }

        if (! $this->isAuthorizedThemeResearcher($userThemes->getUserThemesResearchers())) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }
    }

    protected function getSubmissionConfig()
    {
        if (isset($this->submissionConfig)) {
            return $this->submissionConfig;
        }

        $this->submissionConfig = $this->submissionConfigRepository->findOneBy([
            'isAvailable' => ThemeSubmissionConfig::IS_AVAILABLE_TRUE,
        ]);

        // Quando não existir configuração disponível,
        // Se o usuário logado for Coordenador ou Comitê
        // Procura uma configuração com avaliação disponível
        if (! $this->submissionConfig) {
            $user = $this->getUser();
            assert($user instanceof User);

            if (
                $this->userService->isAdmin($user)
                || $this->userService->isAdminOperational($user)
                || $this->userService->isDivisionCoordinator($user)
                || $this->userService->isDivisionCommittee($user)
            ) {
                $this->submissionConfig = $this->submissionConfigRepository->findOneBy([
                    'isEvaluationAvailable' => ThemeSubmissionConfig::IS_AVAILABLE_TRUE,
                ]);
            }
        }

        return $this->submissionConfig;
    }

    /**
     * @Route("/", name="theme_submission_index", methods={"GET", "POST"})
     */
    public function index(Request $request, TranslatorInterface $translator): Response
    {
        $this->checkUser();

        if (! $this->getSubmissionConfig()) {
            return new Response('', 404);
        }

        $this->get('twig')->addGlobal('pageTitle', 'THEME_TITLE');

        $params = $request->get('user_themes', []);
        $researchers = $params['userThemesResearchers'] ?? [];
        //$users = array_map(fn($item) => (int)$item['researcher'], $researchers);

        $userThemes = new UserThemes();
        $step = (int)$request->get('step', self::INIT_STEP);

        $form = $this->createForm(UserThemesType::class, $userThemes, ['step' => $step, 'researchers' => $researchers]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $user = $this->getUser();
            assert($user instanceof User);

            if ($step >= 1) {
                $this->userThemesService->validateStep1($form, $translator);
                if ($step >= 2) {
                    $this->userThemesService->validateStep2($form, $translator, $user, $this->submissionConfig);
                }
            }

            if ($form->isValid()) {
                $step++;

                if ($step >= self::LAST_STEP) {
                    $userThemes->setUser($user);
                    $userThemes->setThemeSubmissionConfig($this->submissionConfig);
                    $userThemes->setStatus(UserThemes::THEME_EVALUATION_STATUS_WAITING);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($userThemes);
                    $entityManager->flush();

                    $this->userThemesEvaluationLogService->register(
                        $userThemes,
                        $this->getUser(),
                        $request->getClientIp(),
                        UserThemesEvaluationLog::ACTION_SUBMISSION,
                        'Submissão enviada pelo proponente "' . $user->getName() . '"',
                        false
                    );
                }
            }
        }

        return $this->render('@Base/theme/user/index.html.twig', [
            'form' => $form->createView(),
            'userThemes' => $userThemes,
            'step' => $step,
            'submmited' => $form->isSubmitted(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="theme_submission_delete", methods={"GET"})
     */
    public function delete(UserThemes $entity, Request $request): Response
    {
        $this->checkUser($entity);

        // só deixa cancelar se as submissões ainda estiverem liberadas
        // e a submissão ainda estiver aguardando avaliação
        if (
            false === $entity->getThemeSubmissionConfig()->getIsAvailable()
            || (
                $entity->getStatus() !== UserThemes::THEME_EVALUATION_STATUS_WAITING
                && $entity->getStatus() !== UserThemes::THEME_EVALUATION_STATUS_NOT_SELECTED
                && $entity->getStatus() !== UserThemes::THEME_EVALUATION_STATUS_SELECTED
            )
        ) {
            $this->addFlash('error.dashboard', 'Não foi possível cancelar a Submissão.');
        } else {
            $entity->setStatus(UserThemes::THEME_EVALUATION_STATUS_CANCELED);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success.dashboard', 'Submissão cancelada com sucesso.');

            $user = $this->getUser();
            assert($user instanceof User);

            $this->userThemesEvaluationLogService->register(
                $entity,
                $this->getUser(),
                $request->getClientIp(),
                UserThemesEvaluationLog::ACTION_CANCEL_SUBMISSION,
                'Submissão cancelada pelo proponente "' . $user->getName() . '"',
                false
            );
        }

        return $this->redirectToRoute('dashboard_user_index');
    }


    /**
     * @Route("/show/{id}", name="theme_submission_show", methods={"GET"})
     */
    public function show(UserThemes $entity): Response
    {
        $this->checkUser($entity);

        $this->get('twig')->addGlobal('pageTitle', 'Ver Tema');

        return $this->render('@Base/theme/user/show.html.twig', $this->getPermissionsArray() + [
                'userTheme' => $entity,
            ]);
    }
}
