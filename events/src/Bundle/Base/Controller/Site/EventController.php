<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\EditionPaymentMode;
use App\Bundle\Base\Entity\EditionSignup;
use App\Bundle\Base\Entity\Login;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Form\EditionSignupType;
use App\Bundle\Base\Form\LoginType;
use App\Bundle\Base\Repository\EditionRepository;
use App\Bundle\Base\Repository\EditionSignupRepository;
use App\Bundle\Base\Services\Edition as EditionService;
use App\Bundle\Base\Services\SystemEvaluationConfig;
use App\Bundle\Base\Services\User as UserService;
use App\Bundle\Base\Traits\AccessControl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;


/**
 * @Route("event")
 * Class HomeController
 *
 * @package App\Bundle\Base\Controller\Site
 */
class EventController extends AbstractController
{
    use AccessControl;

    const INIT_STEP = 1;

    /**
     * @var EditionRepository
     */
    private $editionRepository;

    /**
     * @var EditionSignupRepository
     */
    private $editionSignupRepository;

    /**
     * @var EditionService
     */
    private $editionService;

    /**
     * @var SystemEvaluationConfig
     */
    private $systemEvaluationConfigService;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $uploadPath = EditionSignup::UPLOAD_PATH;

    /**
     * EventController constructor.
     *
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     * @param EditionRepository $editionRepository
     * @param EditionSignupRepository $editionSignupRepository
     * @param EditionService $editionService
     * @param SystemEvaluationConfig $systemEvaluationConfig
     */
    public function __construct(
        Breadcrumbs             $breadcrumbs,
        UrlGeneratorInterface   $urlGenerator,
        EditionRepository       $editionRepository,
        EditionSignupRepository $editionSignupRepository,
        EditionService          $editionService,
        SystemEvaluationConfig  $systemEvaluationConfig,
        ParameterBagInterface   $parameterBag
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Events');
        $this->editionRepository = $editionRepository;
        $this->editionSignupRepository = $editionSignupRepository;
        $this->editionService = $editionService;
        $this->systemEvaluationConfigService = $systemEvaluationConfig;
        $this->parameterBag = $parameterBag;

        $this->filesystem = new Filesystem();

        $this->uploadPath = str_replace('#KERNEL#', $parameterBag->get('kernel.project_dir'), $this->uploadPath);
        if (! $this->filesystem->exists($this->uploadPath)) {
            $this->filesystem->mkdir($this->uploadPath, 0755);
        }
    }

    /**
     * @Route("/", name="event_index",methods={"GET"})
     */
    public function index()
    {
        return $this->redirect('/');
    }

    /**
     * @Route("/details/{edition}", name="event_details")
     */
    public function details(Edition $edition, UserService $userService, Breadcrumbs $breadcrumbs, ParameterBagInterface $parameterBag, TagAwareCacheInterface $cache)
    {
        if (! $edition || null !== $edition->getDeletedAt()) {
            return new Response('', 404);
        }

        $isUserSignedUp = $this->getUser() && $this->editionSignupRepository->isUserSignedUp($edition, $this->getUser());
        $isUserSignedUpAndNotListener = $this->getUser() && $this->editionSignupRepository->isUserSignedUpAndNotListener($edition, $this->getUser());
        $isUserAssociated = $this->getUser() && $userService->isAssociated($this->getUser());

        $html = $cache->get(
            'edition.details.' . $edition->getId() . '__' . $isUserSignedUp . '_' . $isUserSignedUpAndNotListener . '_' . $isUserAssociated,
            function (ItemInterface $item) use ($edition, $userService, $breadcrumbs, $parameterBag, $isUserSignedUp, $isUserSignedUpAndNotListener, $isUserAssociated) {
                $item->expiresAfter(30 * 24 * 60 * 60);
                $item->tag('edition.details.' . $edition->getId());

                $breadcrumbs->addItem('Events');
                $this->get('twig')->addGlobal('pageTitle', 'EVENT_DETAILS_TITLE');

                $label = $edition->getName();

                $uploadPath = str_replace('#KERNEL#', $parameterBag->get('kernel.project_dir'), UserArticles::UPLOAD_PATH);
                $linkPath = UserArticles::PUBLIC_PATH;

                return $this->render('/event/show/index.html.twig', compact(
                    'label',
                    'edition',
                    'isUserSignedUp',
                    'isUserSignedUpAndNotListener',
                    'isUserAssociated',
                    'uploadPath',
                    'linkPath'
                ));
            }
        );

        return $html;
    }

    /**
     * @param FormInterface $form
     * @param EditionSignup $editionSignup
     * @param SluggerInterface $slugger
     * @param ParameterBagInterface $parameterBag
     */
    protected function saveSignUpFile(FormInterface $form, EditionSignup $editionSignup, SluggerInterface $slugger, ParameterBagInterface $parameterBag)
    {
        $fileFile = $form->get('file')->getData();

        if ($fileFile) {
            $originalFilename = pathinfo($fileFile->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $ext = $fileFile->guessExtension();
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $ext;
            $newFilename = ltrim(mb_substr($newFilename, -254), '-');
            try {
                $fileFile->move(
                    $this->uploadPath . $editionSignup->getEdition()->getId(),
                    $newFilename
                );

                $editionSignup->setUploadedFileName(mb_substr($originalFilename . '.' . $ext, -254));
                $editionSignup->setUploadedFilePath($newFilename);
            } catch (\Exception $e) {

            }
        }
    }

    /**
     * @Route("/{edition}/sign_up/", name="event_signUp_edition",methods={"GET", "POST"})
     *
     * @param Edition $edition
     * @param Request $request
     * @param UserService $userService
     * @param SluggerInterface $slugger
     * @param ParameterBagInterface $parameterBag
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function signUp(Edition $edition, Request $request, UserService $userService, SluggerInterface $slugger, ParameterBagInterface $parameterBag)
    {
        if (! $edition instanceof Edition) {
            throw new NotFoundHttpException('Edition not found');
        }

        /** @var User $user */
        $user = $this->getUser();
        $isGuest = ! $user;

        $isAssociated = $user && $userService->isAssociated($user);
        $discounts = $user ? $edition->getDiscounts(['userIdentifier' => $user->getIdentifier()]) : null;
        $discountPercentage = $discounts && $discounts->count() > 0 ? $discounts[0]->getPercentage() : 0;

        $locale = $request->getLocale();
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_TITLE');
        $editionSignup = new EditionSignup();
        $form = $this->createForm(EditionSignupType::class, $editionSignup, ['edition' => $edition, 'isAssociated' => $isAssociated, 'discount' => $discountPercentage, 'user' => $user]);
        $login = new Login();
        $formLogin = $this->createForm(LoginType::class, $login);
        $form->handleRequest($request);
        $step = (int)$request->get('step', self::INIT_STEP);
        $dataUser = [];

        if (! $isGuest) {

            // somente uma inscrição por usuário
            $signUpRepository = $this->getDoctrine()->getRepository(EditionSignup::class);
            $numberOfSignUps = $signUpRepository->count([
                'edition' => $edition->getId(),
                'joined' => $user->getId(),
                'deletedAt' => null,
            ]);

            if ($numberOfSignUps >= 1) {
                $this->addFlash('error.dashboard', 'SIGNUP_QUANTITY_EXCEEDED');
                return $this->redirectToRoute('dashboard_user_index');
            }

            // $user = $this->getUser();
            $institution = null;
            $program = null;

            if ($user->getInstitutionsPrograms()) {
                $institution = $user->getInstitutionsPrograms()->getInstitutionFirstId();
                $program = $user->getInstitutionsPrograms()->getProgramFirstId();
            }

            $dataUser = [
                'name' => $user->getName(),
                'document' => $user->getIdentifier(),
                'email' => $user->getEmail(),
                'phone' => $user->getPhone(),
                'institution' => $institution,
                'program' => $program,
                'editionSignup' => $editionSignup,
            ];
        }

        //@TODO fazer o acionamento de associação e processar pagamento
        if (! $isGuest && $step === 1) {
            $step = 2;
        }

        if ($request->isXmlHttpRequest()) {

            if ($form->isSubmitted() && $form->isValid()) {

                if ($step == 2) {
                    $editionSignup->setEdition($edition);
                    $editionSignup->setJoined($this->getUser());
                    $editionSignup->setStatusPay($editionSignup::EDITION_SIGNUP_STATUS_PAY['EDITION_SIGNUP_STATUS_NOT_PAID']);
                    $editionSignup->setCreatedAt(new \DateTime());

                    if ($discounts->count() > 0) {
                        $editionSignup->setEditionDiscount($discounts[0]);
                    }

                    if ($discountPercentage >= 100) {
                        $editionSignup->setStatusPay(EditionSignup::EDITION_SIGNUP_STATUS_PAID);
                    }

                    // força por motivos de segurança
                    if (! $editionSignup->getPaymentMode()->getHasFreeIndividualAssociation()) {
                        $editionSignup->setWantFreeIndividualAssociation(false);
                        $editionSignup->setFreeIndividualAssociationDivision(null);
                    }

                    $this->saveSignUpFile($form, $editionSignup, $slugger, $parameterBag);

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($editionSignup);
                    $entityManager->flush();

                    $content = $this->renderView('@Base/event/sign_up/partials/_index.html.twig', [
                        'form' => $form->createView(),
                        'step' => $step,
                        'is_guest' => $isGuest,
                        'loginForm' => $formLogin->createView(),
                        'locale' => $locale,
                        'dataUser' => $dataUser,
                        'edition' => $edition,
                        'editionSignup' => $editionSignup,
                    ]);

                    return new JsonResponse(['saved' => true, 'pass' => true, 'content' => $content], 200, ['x-step' => $step]);
                } else {
                    $content = $this->renderView('@Base/event/sign_up/partials/_index.html.twig', [
                        'form' => $form->createView(),
                        'step' => $step,
                        'is_guest' => $isGuest,
                        'loginForm' => $formLogin->createView(),
                        'locale' => $locale,
                        'dataUser' => $dataUser,
                        'edition' => $edition,
                        'editionSignup' => $editionSignup,
                    ]);

                    return new JsonResponse(['saved' => false, 'pass' => true, 'content' => $content], 200, ['x-step' => $step + 1]);
                }
            } else {

                $response = new Response($this->renderView('@Base/event/sign_up/partials/_index.html.twig', [
                    'form' => $form->createView(),
                    'step' => $step,
                    'is_guest' => $isGuest,
                    'loginForm' => $formLogin->createView(),
                    'locale' => $locale,
                    'dataUser' => $dataUser,
                    'edition' => $edition,
                    'editionSignup' => $editionSignup,
                ]));

                $response->setStatusCode(500)
                    ->headers->set('x-step', $step);

                return $response;
            }
        }

        return $this->render('@Base/event/sign_up/index.html.twig', [
            'form' => $form->createView(),
            'step' => $step,
            'is_guest' => $isGuest,
            'loginForm' => $formLogin->createView(),
            'locale' => $locale,
            'dataUser' => $dataUser,
            'edition' => $edition,
            'editionSignup' => $editionSignup,
        ]);
    }

    /**
     * @param HttpClientInterface $client
     *
     * @param EditionSignup $editionSignup
     * @param HttpClientInterface $client
     * @param UserService $userService
     * @param SluggerInterface $slugger
     *
     * @return string
     */
    protected function paymentRequest(EditionSignup $editionSignup, HttpClientInterface $client, UserService $userService, SluggerInterface $slugger)
    {
        /** @var User $user */
        $user = $this->getUser();

        $isAssociated = EditionPaymentMode::TYPE_ASSOCIATED == $editionSignup->getPaymentMode()->getType();
        $discount = $editionSignup->getEditionDiscount();

        $content = '';

        try {
            $response = $client->request(
                'POST',
                'https://anpad.net.br/pagamento',
                [
                    'body' => [
                        'evento' => $slugger->slug($editionSignup->getEdition()->getNamePortuguese())->toString(), //Obrigatório
                        'tipoInscricao' => $editionSignup->getPaymentMode()->getInitials(), // Obrigatório
                        'email' => $user->getEmail(), // Obrigatório
                        'nome' => $user->getName(),// Obrigatório
                        'endereco' => $user->getStreet(),// Obrigatório
                        'numero' => $user->getNumber(),// Obrigatório
                        'complemento' => $user->getComplement() ?: '',// Opcional
                        'bairro' => $user->getNeighborhood() ?: 'Sem Bairro',// Obrigatório
                        'cidade' => $user->getCity()->getName(),// Obrigatório
                        'uf' => $user->getCity()->getState()->getIso2(), // Obrigatório
                        'cep' => $user->getZipcode(), // Obrigatório
                        'cpf' => $user->getIdentifier(), // Obrigatório
                        'transacao' => $editionSignup->getId(), // Obrigatório
                        'instituicao' => $user->getInstitutionsPrograms()->getInstitutionFirstId()->getName(), // Obrigatório
                        'programa' => $user->getInstitutionsPrograms()->getProgramFirstId()->getName(), // Obrigatório
                        'associado' => $isAssociated ? 'S' : 'N', // Obrigatório
                        'desconto' => $discount && $discount->getPercentage() > 0 ? $discount->getPercentage() : 0, // Obrigatório
                        'isento' => $discount && $discount->getPercentage() >= 100 ? 'S' : 'N', // Opcional
                    ],
                ]
            );

            // $statusCode = $response->getStatusCode();
            // $statusCode = 200
            // $contentType = $response->getHeaders()['content-type'][0];
            // $contentType = 'application/json'
            $content = $response->getContent();
            // $content = '{"id":521583, "name":"symfony-docs", ...}'
            // $content = $response->toArray();
            // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]
        } catch (\Exception $e) {

        }

        return $content;
    }

    /**
     * @param EditionSignup $editionSignup
     * @param HttpClientInterface $client
     * @param SluggerInterface $slugger
     *
     * @return string
     */
    protected function paymentCancel(EditionSignup $editionSignup, HttpClientInterface $client, SluggerInterface $slugger)
    {
        $content = '';

        try {
            $response = $client->request(
                'POST',
                'https://anpad.net.br/inscricao/cancel',
                [
                    'body' => [
                        'evento' => $slugger->slug($editionSignup->getEdition()->getNamePortuguese())->toString(), //Obrigatório
                        'transacao' => $editionSignup->getId(), // Obrigatório
                    ],
                ]
            );

            $content = $response->getContent();
        } catch (\Exception $e) {

        }

        return $content;
    }

    /**
     * @Route("/{edition}/sign_up/{editionSignup}/payment", name="event_signUp_edition_payment",methods={"GET"})
     *
     * @param Edition $edition
     * @param EditionSignup $editionSignup
     * @param Request $request
     * @param UserService $userService
     * @param HttpClientInterface $client
     * @param SluggerInterface $slugger
     *
     * @return Response
     */
    public function signUpPayment(Edition $edition, EditionSignup $editionSignup, Request $request, UserService $userService, HttpClientInterface $client, SluggerInterface $slugger)
    {
        $this->get('twig')->addGlobal('pageTitle', 'EVENT_TITLE');

        $content = $this->paymentRequest($editionSignup, $client, $userService, $slugger);

        /*$content = preg_replace('/<title>.*?<\/title>/m', '', $content);
        $content = preg_replace('/<(\!|\/)?(DOCTYPE|html|head|meta|title|body)([^>]*)?>/m', '', $content);*/
        $content = str_replace('="/', '="https://anpad.net.br/', $content);
        $content = str_replace('action="boleto', 'action="https://anpad.net.br/boleto', $content);
        $content = str_replace('action="pagseguro', 'action="https://anpad.net.br/pagseguro', $content);
        $content = str_replace('action="empenho', 'action="https://anpad.net.br/empenho', $content);

        return new Response($content);
        /*return $this->render('@Base/event/sign_up/payment.html.twig', [
            'content' => $content,
        ]);*/
    }

    /**
     * @Route("/sign_up/{signUp}/show", name="event_signUp_show", methods={"GET"})
     *
     * @param EditionSignup $signUp
     * @param Request $request
     * @param Breadcrumbs $breadcrumbs
     *
     * @return void
     */
    public function show(EditionSignup $signUp, Request $request, Breadcrumbs $breadcrumbs): Response
    {
        $this->isOwnerUser($signUp->getJoined());

        $breadcrumbs->addItem('SUBSCRIPTION');

        $this->get('twig')->addGlobal('pageTitle', 'Ver Inscrição');

        return $this->render('@Base/event/sign_up/show.html.twig', [
            'data' => $signUp,
        ]);
    }

    /**
     * @Route("/sign_up/{signUp}/delete", name="event_signUp_delete", methods={"GET","POST"})
     *
     * @param EditionSignup $signUp
     * @param Request $request
     * @param HttpClientInterface $client
     * @param SluggerInterface $slugger
     *
     * @return Response
     */
    public function delete(EditionSignup $signUp, Request $request, HttpClientInterface $client, SluggerInterface $slugger): Response
    {
        $this->isOwnerUser($signUp->getJoined());

        // $config = $this->systemEvaluationConfigService->getArray($signUp->getEdition());

        if (! $this->systemEvaluationConfigService->freeSignup($signUp->getEdition()) ||
            EditionSignup::EDITION_SIGNUP_STATUS_PAY['EDITION_SIGNUP_STATUS_PAID'] == $signUp->getStatusPay()) {

            $this->addFlash('error.dashboard', 'A ação não é permitida.');

            return $this->redirect('/');
        }

        // cancela o pagamento
        $this->paymentCancel($signUp, $client, $slugger);

        $signUp->setDeletedAt(new \DateTime('now'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($signUp);
        $em->flush();

        $this->addFlash('success.dashboard', 'DASHBOARD_SIGNUP_DELETE_MSG');

        return $this->redirect('/');
    }

    /**
     * @Route("/sign_up/paymentNotificationd371a852317e9f26c072a1480d90d7697d36572056ae8c58de13a7ce5bc", name="event_signUp_payment_notification", methods={"GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse|RedirectResponse|Response
     */
    public function paymentNotification(Request $request)
    {
        $id = $request->query->get('transacao');

        $signUp = $this->getDoctrine()->getRepository(EditionSignup::class)->find($id);

        if (empty($signUp)) {
            return new JsonResponse(['status' => false]);
        }

        $signUp->setUpdatedAt(new \DateTime('now'));
        $signUp->setStatusPay(EditionSignup::EDITION_SIGNUP_STATUS_PAID);

        $em = $this->getDoctrine()->getManager();
        $em->persist($signUp);
        $em->flush();

        return new JsonResponse(['status' => true]);
    }
}
