<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\ThemeSubmissionConfig;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesEvaluationLog;
use App\Bundle\Base\Form\UserThemesSearchType;
use App\Bundle\Base\Repository\UserThemesRepository;
use App\Bundle\Base\Services\UserThemesEvaluationLog as UserThemesEvaluationLogService;
use App\Bundle\Base\Traits\AccessControl;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("manager/themes")
 */
class ManagerThemesController extends AbstractController
{
    use AccessControl;

    private UserThemesRepository $themeRepository;
    private UserThemesEvaluationLogService $userThemesEvaluationLogService;

    public function __construct(Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator, UserThemesRepository $themeRepository, UserThemesEvaluationLogService $userThemesEvaluationLogService)
    {
        $this->themeRepository = $themeRepository;
        $this->userThemesEvaluationLogService = $userThemesEvaluationLogService;

        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Events');
    }

    protected function checkUser(): void
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_ADMIN_OPERATIONAL');

        if (! $hasAccess) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }
    }

    /**
     * @Route("/{themeSubmissionConfig}", name="manager_themes", methods={"GET"})
     */
    public function index(ThemeSubmissionConfig $themeSubmissionConfig, Request $request, PaginatorInterface $paginator): Response
    {
        $this->checkUser();

        $this->get('twig')->addGlobal('pageTitle', 'Temas Submetidos');

        $form = $this->createForm(UserThemesSearchType::class);
        $form->handleRequest($request);
        /** @var array $criteria */
        $criteria = $request->query->get('search', []);

        $query = $this->themeRepository->queryAllByConfig($themeSubmissionConfig, $criteria);
        $items = $paginator->paginate($query, $request->query->get('page', 1), 20);

        return $this->render('@Base/gestor/themes/index.html.twig', [
            'items' => $items,
            'themeSubmissionConfig' => $themeSubmissionConfig,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{themeSubmissionConfig}/selected", name="manager_themes_selected", methods={"GET"})
     */
    public function selected(ThemeSubmissionConfig $themeSubmissionConfig, Request $request, PaginatorInterface $paginator): Response
    {
        $this->checkUser();

        $this->get('twig')->addGlobal('pageTitle', 'Temas Selecionados');

        $form = $this->createForm(UserThemesSearchType::class);
        // status vai ser sempre "Selecionado"
        $form->remove('status');
        $form->handleRequest($request);

        $criteria = $request->query->get('search', []);
        assert(is_array($criteria));

        $criteria['status'] = UserThemes::THEME_EVALUATION_STATUS_SELECTED;

        $query = $this->themeRepository->queryAllByConfig($themeSubmissionConfig, $criteria);
        $items = $paginator->paginate($query, $request->query->get('page', 1), 99999);

        return $this->render('@Base/gestor/themes/selected.html.twig', [
            'items' => $items,
            'themeSubmissionConfig' => $themeSubmissionConfig,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{themeSubmissionConfig}/approve_selected", name="manager_themes_approve_selected", methods={"GET"})
     */
    public function approveSelected(ThemeSubmissionConfig $themeSubmissionConfig, Request $request): Response
    {
        $this->checkUser();

        $this->disableOthers($themeSubmissionConfig);

        $user = $this->getUser();
        assert($user instanceof User);

        $criteria = [
            'status' => UserThemes::THEME_EVALUATION_STATUS_SELECTED,
        ];

        $query = $this->themeRepository->queryAllByConfig($themeSubmissionConfig, $criteria);
        $items = $query->getQuery()->getResult();

        $entityManager = $this->getDoctrine()->getManager();
        foreach ($items as $entity) {
            assert($entity instanceof UserThemes);

            $entity->setStatus(UserThemes::THEME_EVALUATION_APPROVED);

            $this->userThemesEvaluationLogService->register(
                $entity,
                $this->getUser(),
                $request->getClientIp(),
                UserThemesEvaluationLog::ACTION_UPDATE_STATUS,
                'Submissão aprovada pelo administrador "' . $user->getName() . '"',
                false
            );
        }
        $entityManager->flush();

        $this->addFlash('success', 'Aprovação realizada com sucesso.');

        return $this->redirectToRoute('manager_theme_submission_config');
    }

    private function disableOthers(ThemeSubmissionConfig $themeSubmissionConfig): void
    {
        $table = UserThemes::class;
        $id = $themeSubmissionConfig->getId();

        $map = [
            UserThemes::THEME_EVALUATION_STATUS_WAITING => UserThemes::THEME_EVALUATION_STATUS_WAITING_ARCHIVE,
            UserThemes::THEME_EVALUATION_STATUS_NOT_SELECTED => UserThemes::THEME_EVALUATION_STATUS_NOT_SELECTED_ARCHIVE,
            UserThemes::THEME_EVALUATION_STATUS_SELECTED => UserThemes::THEME_EVALUATION_STATUS_SELECTED_ARCHIVE,
            UserThemes::THEME_EVALUATION_APPROVED => UserThemes::THEME_EVALUATION_APPROVED_ARCHIVE,
            UserThemes::THEME_EVALUATION_STATUS_CANCELED => UserThemes::THEME_EVALUATION_STATUS_CANCELED_ARCHIVE,
        ];

        $entityManager = $this->getDoctrine()->getManager();

        foreach ($map as $old => $new) {
            $query = $entityManager->createQuery('UPDATE ' . $table . ' AS T 
                SET T.status =:new 
                WHERE T.status =:old AND T.themeSubmissionConfig <> :id');

            $query->setParameter('new', $new);
            $query->setParameter('old', $old);
            $query->setParameter('id', $id);
            $query->execute();
        }

        $entityManager->flush();
    }

    /**
     * @Route("/show/{id}", name="manager_themes_show", methods={"GET"})
     */
    public function show(UserThemes $entity): Response
    {
        $this->checkUser();

        $this->get('twig')->addGlobal('pageTitle', 'Ver Tema');

        return $this->render('@Base/gestor/themes/show.html.twig', $this->getPermissionsArray() + [
                'userTheme' => $entity,
                'themeSubmissionConfig' => $entity->getThemeSubmissionConfig(),
            ]);
    }

    /**
     * @Route("/remove/{id}", name="manager_themes_remove", methods={"GET"})
     */
    public function remove(UserThemes $entity, Request $request): Response
    {
        $this->checkUser();

        if (
            $entity->getStatus() !== UserThemes::THEME_EVALUATION_STATUS_WAITING
            && $entity->getStatus() !== UserThemes::THEME_EVALUATION_STATUS_NOT_SELECTED
            && $entity->getStatus() !== UserThemes::THEME_EVALUATION_STATUS_SELECTED
        ) {
            $this->addFlash('error', 'Não foi possível cancelar a Submissão.');
        } else {
            $entity->setStatus(UserThemes::THEME_EVALUATION_STATUS_CANCELED);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash('success', 'Submissão cancelada com sucesso.');

            $user = $this->getUser();
            assert($user instanceof User);

            $this->userThemesEvaluationLogService->register(
                $entity,
                $this->getUser(),
                $request->getClientIp(),
                UserThemesEvaluationLog::ACTION_CANCEL_SUBMISSION,
                'Submissão cancelada pelo administrador "' . $user->getName() . '"',
                false
            );
        }

        return $this->redirectToRoute('manager_themes', ['themeSubmissionConfig' => $entity->getThemeSubmissionConfig()->getId()]);
    }
}
