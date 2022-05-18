<?php

namespace App\Bundle\Base\Controller\Site;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 *
 * Class LocaleController
 * @package App\Bundle\Base\Controller\Site
 */
class LocaleController extends AbstractController
{
    /**
     * @const string
     */
    const REDIRECT_ROUTE = 'index';

    /**
     * @Route("/", name="locale", methods={"GET"}, requirements={"locale"="pt_BR|en_US|es_ES"})
     *
     * @return RedirectResponse
     */
    public function locale(): RedirectResponse
    {
        // a lógica de gravação do cookie é executada em
        // \App\Bundle\Base\EventListener\KernelSubscriber
        return $this->redirectToRoute(static::REDIRECT_ROUTE);
    }
}
