<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Services\User as UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 *
 * Class SecurityController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class SecurityController extends AbstractController
{
    /**
     * @Route("/custom_login_form", name="custom_login_form")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function custom_login_form(Request $request)
    {
        return new Response($this->renderView('@Base/security/custom_login_form.html.twig', [

        ]));
    }

    /**
     * @Route("/custom_login_action", name="custom_login_action")
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserService $userService
     *
     * @return JsonResponse
     */
    public function custom_login_action(
        Request $request,
        EntityManagerInterface $entityManager,
        // CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        UserService $userService
    )
    {
        /*if (! $request->isMethod('POST')) {
            return new Response('', 404);
        }*/

        $credentials = [
            'identifier' => $request->get('_identifier'),
            'password' => $request->get('_password'),
            'csrf_token' => $request->get('__csrf_token'),
        ];

        /*$token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (! $csrfTokenManager->isTokenValid($token)) {
            return new JsonResponse([
                'error' => 'Identifier could not be found.',
            ], 400);
        }*/

        /** @var User $user */
        $user = $entityManager->getRepository(User::class)->findOneBy(['identifier' => $credentials['identifier']]);

        if (! $user) {
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['identifier']]);
        }

        if (! $user) {
            // fail authentication with a custom error
            return new JsonResponse([
                'error' => 'Identifier could not be found.',
            ], 401);
        }

        if (! $passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            // fail authentication with a custom error
            return new JsonResponse([
                'error' => 'Identifier could not be found.',
            ], 401);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'identifier' => $user->getIdentifier(),
            'email' => $user->getEmail(),
            'isAdmin' => $userService->isAdmin($user),
            'isAdminOperational' => $userService->isUser($user),
            'isEvaluator' => $userService->isEvaluator($user),
            'isDivisionCoordinator' => $userService->isDivisionCoordinator($user),
            'isDivisionCommittee' => $userService->isDivisionCommittee($user),
            'isThemeLead' => $userService->isThemeLead($user),
        ]);
    }

    /**
     * @Route("/login", name="login")
     *
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request)
    {
        if ($request->isXmlHttpRequest()) {

            $defaults = $request->headers->get('defaults');
            $flagError = false;
            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();

            if (! empty($error)) {
                $flagError = true;
            }

            $response = new Response($this->renderView('@Base/security/partials/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
                'defaults' => $defaults,
            ]));

            $response->setStatusCode(200)
                ->headers->set('x-error', $flagError);

            return $response;
        }

        return $this->redirect('/');
    }

    /**
     * @Route("/password_recovery", name="password_recovery")
     *
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param MailerInterface $mailer
     *
     * @return Response
     */
    public function password_recovery(Request $request, TranslatorInterface $translator, MailerInterface $mailer)
    {
        $statusCode = 200;

        if ($request->isXmlHttpRequest()) {
            $userRepository = $this->getDoctrine()->getRepository(User::class);
            /** @var User $user */
            $user = $userRepository->findOneBy([
                'identifier' => $request->get('identifier'),
            ]);

            if (! $user) {
                $user = $userRepository->findOneBy([
                    'email' => $request->get('identifier'),
                ]);
            }

            if (! $user || ! $user->getEmail()) {
                $statusCode = 400;
                $this->addFlash('error', 'USER_NOT_FOUND');
            } else {

                $newPassword = @base_convert(microtime(false), 10, 36);
                $user->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();

                $email = new Email();
                $email
                    ->from('ANPAD <noreply@anpad.org.br>')
                    ->to($user->getEmail())
                    //->cc('cc@example.com')
                    //->bcc('bcc@example.com')
                    //->replyTo('replyto@example.com')
                    //->priority(Email::PRIORITY_HIGH)
                    ->subject($translator->trans('USER_PASSWORD_CHANGED_EMAIL_TITLE'))
                    ->text($translator->trans('USER_PASSWORD_CHANGED_EMAIL_BODY_TEXT', [
                        '%name%' => $user->getName(),
                        '%password%' => $newPassword,
                    ]))
                    ->html($translator->trans('USER_PASSWORD_CHANGED_EMAIL_BODY_HTML', [
                        '%name%' => $user->getName(),
                        '%password%' => $newPassword,
                    ]));

                try {
                    $mailer->send($email);
                } catch (TransportExceptionInterface $e) {
                    $statusCode = 400;
                    $this->addFlash('error', 'ERROR_0_MSG');
                }

                $this->addFlash('success', 'USER_NEW_PASSWORD_SENT');
            }
        }

        return new Response($this->renderView('@Base/security/partials/password_recovery.html.twig', [

        ]), $statusCode);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
