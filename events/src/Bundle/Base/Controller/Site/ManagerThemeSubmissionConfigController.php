<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\ThemeSubmissionConfig;
use App\Bundle\Base\Form\ThemeSubmissionConfigType;
use App\Bundle\Base\Repository\ThemeSubmissionConfigRepository;
use App\Bundle\Base\Traits\AccessControl;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("manager/theme_submission_config")
 */
class ManagerThemeSubmissionConfigController extends AbstractController
{
    use AccessControl;

    protected ThemeSubmissionConfigRepository $themeSubmissionConfigRepository;

    public function __construct(Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator, ThemeSubmissionConfigRepository $themeSubmissionConfigRepository)
    {
        $this->themeSubmissionConfigRepository = $themeSubmissionConfigRepository;

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
     * @Route("/", name="manager_theme_submission_config", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $this->checkUser();

        $this->get('twig')->addGlobal('pageTitle', 'Configurações de Submissão de Temas');

        $query = $this->themeSubmissionConfigRepository->queryAll();
        $items = $paginator->paginate($query, $request->query->get('page', 1), 20);

        return $this->render('@Base/gestor/theme_submission_config/index.html.twig', [
            'items' => $items,
        ]);
    }

    /**
     * @Route("/new", name="manager_theme_submission_config_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $this->checkUser();

        $this->get('twig')->addGlobal('pageTitle', 'Configurações de Submissão de Temas');

        $entity = new ThemeSubmissionConfig();

        $form = $this->createForm(ThemeSubmissionConfigType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $this->checkSettings($entity);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($entity);
                $entityManager->flush();

                $this->addFlash('success', 'Configuração criada.');

                return $this->redirectToRoute('manager_theme_submission_config');
            } else {
                $this->addFlash('error', 'Não foi possível validar os dados.');
            }
        }

        return $this->render('@Base/gestor/theme_submission_config/new.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="manager_theme_submission_config_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, ThemeSubmissionConfig $entity): Response
    {
        $this->checkUser();

        $this->get('twig')->addGlobal('pageTitle', 'Configurações de Submissão de Temas');

        $form = $this->createForm(ThemeSubmissionConfigType::class, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $this->checkSettings($entity);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($entity);
                $entityManager->flush();

                $this->addFlash('success', 'Configuração alterada.');

                return $this->redirectToRoute('manager_theme_submission_config');
            } else {
                $this->addFlash('error', 'Não foi possível validar os dados.');
            }
        }

        return $this->render('@Base/gestor/theme_submission_config/edit.html.twig', [
            'form' => $form->createView(),
            'entity' => $entity,
        ]);
    }

    /**
     * @Route("/{id}", name="manager_theme_submission_config_remove", methods={"DELETE"})
     */
    public function remove(Request $request, ThemeSubmissionConfig $entity): Response
    {
        $this->checkUser();

        if (! $this->isCsrfTokenValid('delete' . $entity->getId(), $request->request->get('_token'))) {
            return new Response('', 401);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($entity);
        $entityManager->flush();

        return new Response('', 204);
    }

    protected function checkSettings(ThemeSubmissionConfig $themeSubmissionConfig): void
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $table = ThemeSubmissionConfig::class;
        $id = $themeSubmissionConfig->getId() ?? 0;

        if ($themeSubmissionConfig->getIsCurrent()) {
            $query = $em->createQuery('UPDATE ' . $table . ' AS T 
                SET T.isCurrent =:isCurrent, T.updatedAt =CURRENT_TIMESTAMP() 
                WHERE T.deletedAt IS NULL AND T.id <> :id');
            $query->setParameter('isCurrent', ThemeSubmissionConfig::IS_CURRENT_FALSE);
            $query->setParameter('id', $id);
            $query->execute();
        }

        if ($themeSubmissionConfig->getIsAvailable()) {
            $query = $em->createQuery('UPDATE ' . $table . ' AS T 
                SET T.isAvailable =:isAvailable, T.updatedAt =CURRENT_TIMESTAMP() 
                WHERE T.deletedAt IS NULL AND T.id <> :id');
            $query->setParameter('isAvailable', ThemeSubmissionConfig::IS_AVAILABLE_FALSE);
            $query->setParameter('id', $id);
            $query->execute();
        }

        if ($themeSubmissionConfig->getIsEvaluationAvailable()) {
            $query = $em->createQuery('UPDATE ' . $table . ' AS T 
                SET T.isEvaluationAvailable =:isEvaluationAvailable, T.updatedAt =CURRENT_TIMESTAMP() 
                WHERE T.deletedAt IS NULL AND T.id <> :id');
            $query->setParameter('isEvaluationAvailable', ThemeSubmissionConfig::IS_AVAILABLE_FALSE);
            $query->setParameter('id', $id);
            $query->execute();
        }

        if ($themeSubmissionConfig->getIsResultAvailable()) {
            $query = $em->createQuery('UPDATE ' . $table . ' AS T 
                SET T.isResultAvailable =:isResultAvailable, T.updatedAt =CURRENT_TIMESTAMP() 
                WHERE T.deletedAt IS NULL AND T.id <> :id');
            $query->setParameter('isResultAvailable', ThemeSubmissionConfig::IS_AVAILABLE_FALSE);
            $query->setParameter('id', $id);
            $query->execute();
        }
    }
}
