<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\PaymentUserAssociation;
use App\Bundle\Base\Repository\PaymentUserAssociationDetailsRepository;
use App\Bundle\Base\Services\User as UserService;
use App\Bundle\Base\Traits\AccessControl;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/manager")
 *
 * Class ManagerProgramsController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class ManagerPaymentUserAssociationDetailsController extends AbstractController
{
    use AccessControl;

    /**
     * @var PaymentUserAssociationDetailsRepository
     */
    private $paymentUserAssociationDetailsRepository;

    /**
     * @var Breadcrumbs
     */
    private $breadcrumbsService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * ManagerPaymentUserAssociationDetailsController constructor.
     *
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     * @param PaymentUserAssociationDetailsRepository $paymentUserAssociationDetailsRepository
     */
    public function __construct(
        Breadcrumbs $breadcrumbs,
        UrlGeneratorInterface $urlGenerator,
        PaymentUserAssociationDetailsRepository $paymentUserAssociationDetailsRepository
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $this->breadcrumbsService = $breadcrumbs;
        $this->urlGenerator = $urlGenerator;
        $this->paymentUserAssociationDetailsRepository = $paymentUserAssociationDetailsRepository;
    }

    /**
     * @return array
     */
    protected function getMenuBreadcumb()
    {
        return [
            ['label' => 'MANAGER_MB_DASHBOARD', 'href' => '/'],
            ['label' => 'MANAGER_MB_PAYMENT_USER_ASSOCIATION', 'href' => '/gestor', 'active' => true],
        ];
    }

    /**
     * @Route("/{paymentUserAssociation}/paymentUserAssociationDetails", name="manager_payment_user_association_details_index", methods={"GET"})
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param PaymentUserAssociation $paymentUserAssociation
     * @return Response
     */
    public function paymentUserAssociationDetailsIndex(PaginatorInterface $paginator, Request $request, PaymentUserAssociation $paymentUserAssociation)
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_ADMIN_OPERATIONAL');
        if (!$hasAccess) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $this->breadcrumbsService->addItem('PAYMENT_USER_ASSOCIATION', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('PAYMENT_USER_ASSOCIATION_DETAILS');
        $this->get('twig')->addGlobal('pageTitle', 'PAYMENT_USER_ASSOCIATION');

        if (!$paymentUserAssociation) {
            return new Response('', 404);
        }

        $results = $paginator->paginate($this->paymentUserAssociationDetailsRepository->findBy([
            'paymentUserAssociation' => $paymentUserAssociation->getId()
        ]), $request->query->get('page', 1), 20);

        return $this->render('@Base/gestor/tabs/payment_user_association_details/list.html.twig', [
            'paymentUserAssociation' => $paymentUserAssociation,
            'paymentUserAssociationDetails' => $results,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
        ]);
    }
}
