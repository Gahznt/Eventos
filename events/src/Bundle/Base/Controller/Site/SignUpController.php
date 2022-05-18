<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Permission;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserConsents;
use App\Bundle\Base\Form\SignUpType;
use App\Bundle\Base\Form\UserAcademicsType;
use App\Bundle\Base\Form\UserInstitutionsProgramsType;
use App\Bundle\Base\Services\UserConsents as UserConsentsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("sign_up")
 *
 * Class SignUpController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class SignUpController extends AbstractController
{
    const INIT_STEP = 0;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var UserConsentsService
     */
    private $userConsentsService;

    /**
     * SignUpController constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     * @param UserConsentsService $userConsentsService
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        Breadcrumbs $breadcrumbs,
        UrlGeneratorInterface $urlGenerator,
        UserConsentsService $userConsentsService
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Administrative', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('SIGNUP_TITLE');
        $this->passwordEncoder = $passwordEncoder;
        $this->userConsentsService = $userConsentsService;
    }

    /**
     * @Route("/", name="signUp_index", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $this->get('twig')->addGlobal('pageTitle', 'SIGNUP_TITLE');
        $user = new User();

        $user->setLocale($request->getLocale());

        $step = (int)$request->get('step', self::INIT_STEP);

        SignUpType::$step = $step;

        if ($step < 2) {
            UserInstitutionsProgramsType::$validationEnabled = false;
        }

        if ($step < 3) {
            UserAcademicsType::$validationEnabled = false;
        }

        $form = $this->createForm(SignUpType::class, $user);

        $form->handleRequest($request);

        if (! $request->isXmlHttpRequest()) {
            return $this->render('@Base/sign_up/index.html.twig', [
                'form' => $form->createView(),
                'user' => $user,
                'step' => $step,
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {

            if ($step == 5) {
                $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));

                $user->setRoles([Permission::ROLE_USER_GUEST]);
                $user->setCreatedAt(new \DateTime());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $this->userConsentsService->register(
                    $request->getClientIp(),
                    UserConsents::USER_CONSENTS_STATUS_ACCEPT,
                    UserConsents::USER_CONSENTS_TYPE_REGISTER,
                    $user
                );

                return new JsonResponse(['saved' => true, 'pass' => true], 201, ['x-step' => $step]);
            } else {
                $step++;

                $responseText = $this->renderView('@Base/sign_up/partials/_index.html.twig', [
                    'form' => $form->createView(),
                    'user' => $user,
                    'step' => $step,
                ]);

                return new JsonResponse(['saved' => false, 'pass' => true, 'responseText' => $responseText], 200, ['x-step' => $step]);
            }

        } else {

            $response = new Response($this->renderView('@Base/sign_up/partials/_index.html.twig', [
                'form' => $form->createView(),
                'user' => $user,
                'step' => $step,
            ]));

            $response->setStatusCode(500)
                ->headers->set('x-step', $step);

            return $response;
        }
    }
}
