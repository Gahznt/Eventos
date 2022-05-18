<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\EditionSignup;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserAssociation;
use App\Bundle\Base\Form\AssociateType;
use App\Bundle\Base\Repository\UserAssociationRepository;
use App\Bundle\Base\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("associate")
 * Class AssociateController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class AssociateController extends AbstractController
{

    const PAGE_LIMIT = 10;
    const PAGE_NUM_DEFAULT = 1;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserAssociationRepository
     */
    private $userAssociationRepository;

    /**
     * AssociateController constructor.
     *
     * @param UserRepository $userRepository
     * @param UserAssociationRepository $associations
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UserRepository $userRepository, UserAssociationRepository $associations, Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator)
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Administrative', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Association');
        $this->userRepository = $userRepository;
        $this->userAssociationRepository = $associations;
    }

    /**
     * @Route("/", name="association_index", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $this->get('twig')->addGlobal('pageTitle', 'JOIN_TITLE');
        return $this->render('@Base/association/index.html.twig');
    }

    /**
     * @Route("/new/{id}", name="associate_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function associate(Request $request, User $user)
    {
        $this->get('twig')->addGlobal('pageTitle', 'JOIN_TITLE');
        $association = new UserAssociation();
        $form = $this->createForm(AssociateType::class, $association);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $association->setUser($user);
            $association->setLevel(1);

            //@TODO melhorar isso aqui, relações
            if ($user->getInstitutionsPrograms() && $user->getInstitutionsPrograms()->getInstitutionFirstId()) {
                $association->setInstitution($user->getInstitutionsPrograms()->getInstitutionFirstId());
                $association->setOtherInstitution($user->getInstitutionsPrograms()->getOtherInstitutionFirst());
            }

            if ($user->getInstitutionsPrograms() && $user->getInstitutionsPrograms()->getProgramFirstId()) {
                $association->setProgram($user->getInstitutionsPrograms()->getProgramFirstId());
                $association->setOtherProgram($user->getInstitutionsPrograms()->getOtherProgramFirst());
            }

            $value = $association::USER_ASSOCIATIONS_VALUES[$association->getType()] +
                ($association::USER_ASSOCIATIONS_DIVISION_ADITIONAL_VALUE[$association->getType()] * count($association->getAditionals()));

            $association->setValue($value);
            $association->setStatusPay($association::USER_ASSOCIATIONS_STATUS_NOT_PAY);
            $association->setCreatedAt(new DateTime());
            $association->setUpdatedAt(new DateTime());
            $association->setExpiredAt(new DateTime('now +1 year'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($association);
            $entityManager->flush();

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        $history = $this->userAssociationRepository->findBy(['user' => $user->getId()], ['updatedAt' => 'DESC']);

        return $this->render('@Base/association/new.html.twig', [
            'history' => $history,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cron", name="associate_cron", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function cron(Request $request)
    {
        $er = $this->getDoctrine()->getRepository(EditionSignup::class);

        $signUps = $er->findBy([
            'deletedAt' => null,
            'statusPay' => UserAssociation::USER_ASSOCIATIONS_STATUS_PAY,
            'wantFreeIndividualAssociation' => 1,
            'freeIndividualAssociationUserAssociation' => null,
        ], [
            'id' => 'DESC',
        ], 100);

        if (count($signUps) > 0) {
            /** @var EditionSignup $signUp */
            foreach ($signUps as $signUp) {

                $userAssociation = new UserAssociation();

                if (isset(UserAssociation::PAYMENT_MODE_INITIALS[$signUp->getPaymentMode()->getInitials()])) {
                    $userAssociation->setType(UserAssociation::PAYMENT_MODE_INITIALS[$signUp->getPaymentMode()->getInitials()]);
                }

                if ($signUp->getJoined()) {

                    $userAssociation->setUser($signUp->getJoined());

                    if ($signUp->getJoined()->getInstitutionsPrograms()) {
                        $userAssociation->setInstitution($signUp->getJoined()->getInstitutionsPrograms()->getInstitutionFirstId());
                        $userAssociation->setOtherInstitution($signUp->getJoined()->getInstitutionsPrograms()->getOtherInstitutionFirst());
                        $userAssociation->setProgram($signUp->getJoined()->getInstitutionsPrograms()->getProgramFirstId());
                        $userAssociation->setOtherProgram($signUp->getJoined()->getInstitutionsPrograms()->getOtherProgramFirst());
                    }
                }
                if ($signUp->getFreeIndividualAssociationDivision()) {
                    $userAssociation->setDivision($signUp->getFreeIndividualAssociationDivision());
                }
                $userAssociation->setCreatedAt(new DateTime());
                $userAssociation->setUpdatedAt(new DateTime());
                $userAssociation->setExpiredAt($signUp->getCreatedAt()->add(new \DateInterval('P1Y')));
                $userAssociation->setLastPay(new DateTime());
                $userAssociation->setLevel(1);
                $userAssociation->setValue(0);
                $userAssociation->setStatusPay(UserAssociation::USER_ASSOCIATIONS_STATUS_PAY);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($userAssociation);
                $entityManager->flush();

                $signUp->setFreeIndividualAssociationUserAssociation($userAssociation);

                $entityManager->persist($signUp);
                $entityManager->flush();
            }
        }

        return new Response('Terminou. ' . count($signUps) . ' associações criadas.');
    }
}
