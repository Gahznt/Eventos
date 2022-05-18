<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\ThemeSubmissionConfig;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Repository\ThemeSubmissionConfigRepository;
use App\Bundle\Base\Repository\EditionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("theme")
 */
class ThemeRenderController extends AbstractController
{
   const CURRENT_THEME_EDITION = 116;
   
   private ThemeSubmissionConfigRepository $submissionConfigRepository;
   private ThemeSubmissionConfig $submissionConfig;
   private EditionRepository $editionRepository;
   private Edition $edition;

   public function __construct(Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator, ThemeSubmissionConfigRepository $submissionConfigRepository,
      EditionRepository $editionRepository)
   {
      $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
      $breadcrumbs->addItem('Events', $urlGenerator->generate('index'));
      $breadcrumbs->addItem('DIVISION_THEMES', $urlGenerator->generate('index'));
      
      $this->submissionConfigRepository = $submissionConfigRepository;
      $this->submissionConfig = $this->submissionConfigRepository->findOneBy(['isCurrent' => ThemeSubmissionConfig::IS_CURRENT_TRUE]);

      $this->editionRepository = $editionRepository;
      $this->edition = $this->editionRepository->findOneBy(['id' => self::CURRENT_THEME_EDITION]);
   }      

   /**
    * @Route("/list", name="theme_list", methods={"GET"})
    */
   public function index(Request $request, TranslatorInterface $translator): Response
   {
      if (! $this->edition) {
         return new Response('', 404);
      }

      $this->get('twig')->addGlobal('pageTitle', 'Divisions and Themes');

      return $this->render('/theme/show/index.html.twig', ['edition' => $this->edition]);
   }   
}