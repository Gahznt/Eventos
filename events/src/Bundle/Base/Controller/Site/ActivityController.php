<?php

namespace App\Bundle\Base\Controller\Site;

use App\Bundle\Base\Entity\Activity;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Form\ActivitiesGuestsType;
use App\Bundle\Base\Form\ActivitiesPanelistsType;
use App\Bundle\Base\Form\ActivityType;
use App\Bundle\Base\Repository\ActivityRepository;
use App\Bundle\Base\Services\SystemEvaluationConfig;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

/**
 * @Route("/activity")
 *
 * Class ActivityController
 *
 * @package App\Bundle\Base\Controller
 */
class ActivityController extends AbstractController
{
    /**
     * @const int
     */
    const INIT_STEP = 1;

    /**
     * @var ActivityRepository
     */
    private $activityRepository;

    /**
     * @var SystemEvaluationConfig
     */
    private $systemEvaluationConfigService;

    /**
     * ActivityController constructor.
     *
     * @param ActivityRepository $activityRepository
     * @param Breadcrumbs $breadcrumbs
     * @param UrlGeneratorInterface $urlGenerator
     * @param SystemEvaluationConfig $systemEvaluationConfig
     */
    public function __construct(
        ActivityRepository $activityRepository,
        Breadcrumbs $breadcrumbs,
        UrlGeneratorInterface $urlGenerator,
        SystemEvaluationConfig $systemEvaluationConfig
    )
    {
        $breadcrumbs->addItem('ANPAD', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('Activities', $urlGenerator->generate('index'));
        $breadcrumbs->addItem('New');
        $this->activityRepository = $activityRepository;
        $this->systemEvaluationConfigService = $systemEvaluationConfig;
    }

    /**
     * @Route("/{edition}/index", name="activity_submission_index", methods={"GET","POST"})
     *
     * @param Edition $edition
     * @param Request $request
     *
     * @return JsonResponse|Response
     */
    public function index(Edition $edition, Request $request)
    {
        $hasAccess = $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_ADMIN_OPERATIONAL');
        if (! $hasAccess) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $this->get('twig')->addGlobal('pageTitle', 'ACTIVITY_TITLE');
        $activity = new Activity();
        $activity->setEdition($edition);

        $step = (int)$request->get('step', self::INIT_STEP);

        ActivityType::$step = $step;

        if ($step < 2) {
            ActivitiesPanelistsType::$validationEnabled = false;
        }

        if ($step < 3) {
            ActivitiesGuestsType::$validationEnabled = false;
        }

        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if (! $form->isSubmitted()) {
            return $this->render('@Base/activity/index.html.twig', [
                'form' => $form->createView(),
                'activity' => $activity,
                'step' => $step,
                'submmited' => false,
            ]);
        }

        if (! $request->isXmlHttpRequest()) {
            return new Response('', 405);
        }

        if (! $form->isValid()) {
            return new Response($this->renderView('@Base/activity/partials/_index.html.twig', [
                'form' => $form->createView(),
                'activity' => $activity,
                'step' => $step,
                'submmited' => true,
            ]), 400, ['x-step' => $step]);
        }

        $saved = false;
        $status = 200; // ok
        if ($step === 3) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($activity);
            $entityManager->flush();

            $saved = true;
            $status = 201; // criado
        }


        //@TODO salvar arquivos
        return new JsonResponse(['saved' => $saved, 'pass' => true, 'step' => $step + 1], $status,
            ['x-step' => $step + 1]);
    }
}
