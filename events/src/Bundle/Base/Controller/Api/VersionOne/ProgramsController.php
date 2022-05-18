<?php


namespace App\Bundle\Base\Controller\Api\VersionOne;


use App\Bundle\Base\Contracts\ApiControllerBase as Controller;
use App\Bundle\Base\Repository\ProgramRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("programs")
 *
 * Class ProgramsController
 * @package App\Bundle\Base\Controller\Api\VersionOne
 */
class ProgramsController extends Controller
{
    /**
     * @var ProgramRepository
     */
    private ProgramRepository $programRepository;

    /**
     * UserController constructor.
     *
     * @param ProgramRepository $programRepository
     */
    public function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    /**
     * @Route(
     *     path         = "/search",
     *     name         = "associated_programs_html",
     *     methods      = {"GET"}
     * )
     *
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return JsonResponse
     */
    public function getPrograms(Request $request, PaginatorInterface $paginator): Response
    {
        $search = $request->get('search');
        $results = $paginator->paginate($this->programRepository->findByFilters(compact('search')), $request->query->get('page', 1), 20);

        return $this->render('@Base/programs/index.html.twig', [
            'programs' => $results,
        ]);
    }
}