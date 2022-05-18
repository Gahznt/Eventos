<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\PaymentUserAssociation;
use App\Bundle\Base\Entity\PaymentUserAssociationDetails;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserAssociation;
use App\Bundle\Base\Repository\PaymentUserAssociationRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/manager/payment_user_associations")
 *
 * Class ManagerPaymentUserAssociationsController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class ManagerPaymentUserAssociationsController extends AbstractController
{
    /**
     * @var PaymentUserAssociationRepository
     */
    private $paymentUserAssociationRepository;

    /**
     * @var Breadcrumbs
     */
    private $breadcrumbsService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * ManagerController constructor.
     *
     * @param PaymentUserAssociationRepository $paymentUserAssociationRepository
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        PaymentUserAssociationRepository $paymentUserAssociationRepository,
        Breadcrumbs $breadcrumbs,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $this->paymentUserAssociationRepository = $paymentUserAssociationRepository;
        $this->breadcrumbsService = $breadcrumbs;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return array
     */
    protected function getMenuBreadcumb()
    {
        return [
            ['label' => 'MANAGER_MB_DASHBOARD', 'href' => '/'],
            ['label' => 'ASSOCIAÇÃO DE PAGAMENTO DE USUÁRIO', 'href' => '/manager/payment_user_associations', 'active' => true],
        ];
    }

    /**
     * @Route("/", name="manager_payment_user_associations_index", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     *
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_ADMIN_OPERATIONAL');
        if (!$hasAccess) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $this->breadcrumbsService->addItem('PAYMENT_USER_ASSOCIATION');
        $this->get('twig')->addGlobal('pageTitle', 'PAYMENT_USER_ASSOCIATION');

        $results = $paginator->paginate($this->paymentUserAssociationRepository->findAll(), $request->query->get('page', 1), 20);

        return $this->render('@Base/gestor/tabs/payment_user_associations/list.html.twig', [
            'paymentUserAssociations' => $results,
            'menuBreadcumb' => $this->getMenuBreadcumb(),
        ]);
    }

    /**
     * @Route("/new", name="manager_payment_user_associations_new", methods={"GET", "POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request)
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_ADMIN_OPERATIONAL');
        if (!$hasAccess) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $this->breadcrumbsService->addItem('PAYMENT_USER_ASSOCIATION', $this->urlGenerator->generate('manager_index'));
        $this->breadcrumbsService->addItem('PAYMENT_USER_ASSOCIATION_NEW');
        $this->get('twig')->addGlobal('pageTitle', 'PAYMENT_USER_ASSOCIATION');

        $paymentUserAssociation = new PaymentUserAssociation();

        if (!$request->isMethod('POST')) {
            return $this->render('@Base/gestor/tabs/payment_user_associations/form.html.twig', [
                'paymentUserAssociation' => $paymentUserAssociation,
                'menuBreadcumb' => $this->getMenuBreadcumb(),
            ]);
        }

        if (!$request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        $this->processFile($request->files->get('file'));

        try {
            $this->addFlash('success', 'Payment User Association created');
        } catch (\Exception $e) {
            return new Response('', 500);
        }

        $status = 201; // criado

        return new JsonResponse([], $status);
    }


    private function processFile(UploadedFile $file)
    {
        /** @var User $user */
        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();

        $handle = @fopen($file->getPathname(), "r");
        if ($handle) {
            $paymentUserAssociation = new PaymentUserAssociation();
            $paymentUserAssociation->setUser($user);
            $paymentUserAssociation->setFilename($file->getFilename());
            $paymentUserAssociation->setQuantity(0);
            $paymentUserAssociation->setErrors(0);
            $paymentUserAssociation->setCreatedAt(new DateTime('now'));

            $entityManager->persist($paymentUserAssociation);

            while (($buffer = fgets($handle, 4096)) !== false) {
                $line = $this->processLine(explode(';', $buffer));

                if (count($line) === 0) {
                    continue;
                }

                $paymentUserAssociation->setQuantity($paymentUserAssociation->getQuantity() + 1);

                if (substr($line['operation'], 0, 2) !== "LQ") {
                    continue;
                }

                $paymentUserAssociationDetails = new PaymentUserAssociationDetails();
                $paymentUserAssociationDetails->setPayday($line['payday']);
                $paymentUserAssociationDetails->setDueDate($line['dueDate']);
                $paymentUserAssociationDetails->setBankSlipAmount($line['bankSlipAmount']);
                $paymentUserAssociationDetails->setFeeAmount($line['feeAmount']);
                $paymentUserAssociationDetails->setNetAmount($line['netAmount']);
                $paymentUserAssociationDetails->setOperation($line['operation']);
                $paymentUserAssociationDetails->setBankSlip($line['bankSlip']);
                $paymentUserAssociationDetails->setName($line['name']);
                $paymentUserAssociationDetails->setPaymentUserAssociation($paymentUserAssociation);
                $paymentUserAssociationDetails->setCreatedAt(new DateTime('now'));

                $userAssociationId = substr($line['bankSlip'], 14, 6);
                /** @var UserAssociation $userAssociation */
                $userAssociation = $entityManager->getRepository(UserAssociation::class)->find((int)$userAssociationId);

                if (!$userAssociation) {
                    $paymentUserAssociation->setErrors($paymentUserAssociation->getErrors() + 1);

                    $paymentUserAssociationDetails->setStatus(PaymentUserAssociationDetails::PAYMENT_USER_ASSOCIATION_DETAILS_STATUS_NOT_FOUND);
                    $paymentUserAssociationDetails->setNote(PaymentUserAssociationDetails::PAYMENT_USER_ASSOCIATION_DETAILS_ERROR_NOT_FOUND);

                    $entityManager->persist($paymentUserAssociation);
                    $entityManager->persist($paymentUserAssociationDetails);
                    $entityManager->flush();

                    continue;
                }

                $erro = false;

                if (UserAssociation::USER_ASSOCIATIONS_STATUS_PAY == $userAssociation->getStatusPay()) {
                    $erro = true;
                    $paymentUserAssociationDetails->setStatus(PaymentUserAssociationDetails::PAYMENT_USER_ASSOCIATION_DETAILS_STATUS_DISREGARDED);
                    $paymentUserAssociationDetails->setNote(PaymentUserAssociationDetails::PAYMENT_USER_ASSOCIATION_DETAILS_ERROR_SETTLED);
                }

                if ($userAssociation->getValue() != $paymentUserAssociationDetails->getBankSlipAmount()) {
                    $erro = true;
                    $paymentUserAssociationDetails->setStatus(PaymentUserAssociationDetails::PAYMENT_USER_ASSOCIATION_DETAILS_STATUS_VALUE);
                    $paymentUserAssociationDetails->setNote(PaymentUserAssociationDetails::PAYMENT_USER_ASSOCIATION_DETAILS_ERROR_VALUE);
                }

                if ($paymentUserAssociationDetails->getPayday() > $paymentUserAssociationDetails->getDueDate()) {
                    $erro = true;
                    $paymentUserAssociationDetails->setStatus(PaymentUserAssociationDetails::PAYMENT_USER_ASSOCIATION_DETAILS_STATUS_DATE);
                    $paymentUserAssociationDetails->setNote(PaymentUserAssociationDetails::PAYMENT_USER_ASSOCIATION_DETAILS_ERROR_DATE);
                }

                if (!$erro) {
                    try {
                        $userAssociation->setStatusPay(UserAssociation::USER_ASSOCIATIONS_STATUS_PAY);
                        $userAssociation->setLastPay(new DateTime('now'));
                        $entityManager->persist($userAssociation);

                        $paymentUserAssociationDetails->setStatus(PaymentUserAssociationDetails::PAYMENT_USER_ASSOCIATION_DETAILS_STATUS_PAID);
                        $paymentUserAssociationDetails->setNote(PaymentUserAssociationDetails::PAYMENT_USER_ASSOCIATION_DETAILS_MESSAGE_SETTLED);
                    } catch (Exception $e) {
                        throw new Exception($e->getMessage());
                    }
                } else {
                    $paymentUserAssociation->setErrors($paymentUserAssociation->getErrors() + 1);
                }

                $entityManager->persist($paymentUserAssociation);
                $entityManager->persist($paymentUserAssociationDetails);
            }
            if (!feof($handle)) {
                echo "Erro: falha inexperada de fgets()\n";
            }

            fclose($handle);

            $entityManager->flush();
        }
    }

    private function processLine($line)
    {
        /*
           Linha do arquivo de cobranÃ§a
           0    ;1           ;2 ;3  ;4                    ;5;6                     ;7       ;8       ;9  ;10            ;11           ;12;13        ;14        ;15        ;16
           00598;000000015112;17;027;000292218700001523013; ;LUCIANO TAKAO TOYSHIMA;13012017;00000000;RG ;00000000017720;0000000000396;D ;0000000000;0000000396;0000000000;00000
        */

        if ($line[0] === '') {
            return [];
        }

        $lineProcessed = [
            'payday' => new DateTime(substr($line[8], 4, 4) . '-' . substr($line[8], 2, 2) . '-' . substr($line[8], 0, 2)),
            'dueDate' => new DateTime(substr($line[7], 4, 4) . '-' . substr($line[7], 2, 2) . '-' . substr($line[7], 0, 2)),
            'bankSlipAmount' => substr($line[10], 0, strlen($line[10]) - 2) . '.' . substr($line[10], strlen($line[10]) - 2, 2),
            'feeAmount' => substr($line[14], 0, strlen($line[14]) - 2) . '.' . substr($line[14], strlen($line[14]) - 2, 2),
            'netAmount' => substr($line[11], 0, strlen($line[11]) - 2) . '.' . substr($line[11], strlen($line[11]) - 2, 2),
            'bankSlip' => $line[4],
            'name' => $line[6],
            'operation' => trim($line[9]),
        ];

        return $lineProcessed;
    }
}
