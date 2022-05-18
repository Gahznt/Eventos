<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Services\PageMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/import")
 * Class HomeController
 * @package App\Bundle\Base\Controller\Site
 */
class ImportController extends AbstractController
{

    public function __construct(Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator)
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Administrative', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Import');
    }

    /**
     * @Route("/", name="import_index", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index() {
        $this->get('twig')->addGlobal('pageTitle', 'Import');
        return $this->render('@Base/import/index.html.twig');
    }
}
