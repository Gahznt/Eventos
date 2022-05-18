<?php

namespace App\Bundle\Base\Controller\Api\VersionOne;

use App\Bundle\Base\Contracts\ApiControllerBase as Controller;
use App\Bundle\Base\Entity\Program;
use App\Bundle\Base\Repository\ProgramRepository;
use App\Bundle\Base\Services\Keyword;
use App\Bundle\Base\Services\Theme;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("academic")
 *
 * Class AcademicController
 * @package App\Bundle\Base\Controller\Api\VersionOne
 */
class AcademicController extends Controller
{
    private $themeService;
    private $keywordService;

    public function __construct(Theme $theme, Keyword $keyword)
    {
        $this->themeService = $theme;
        $this->keywordService = $keyword;
    }

    /**
     * @Route(
     *     path         = "/theme_by_division",
     *     name         = "theme_by_division",
     *     methods      = {"POST"}
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getThemeByDivision(Request $request)
    {
        $id = $request->get('depdrop_parents', null);

        return $this->responseJson($this->themeService->getThemeByDivision($id), function ($data) {
            return  ['output' => $data, 'selected' => ""];
        });
    }

    /**
     * @Route(
     *     path         = "/keyword_by_theme",
     *     name         = "keyword_by_theme",
     *     methods      = {"POST"}
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getKeywordByTheme(Request $request)
    {
        $id = $request->get('depdrop_parents', null);

        return $this->responseJson($this->keywordService->getKeywordByThemeId($id[1]), function ($data) {
            return  ['output' => $data, 'selected' => ""];
        });
    }

    /**
     * @Route(
     *     path         = "/program_by_institution",
     *     name         = "program_by_institution",
     *     methods      = {"GET"}
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function programByInstitution(Request $request)
    {
        $institution = $request->get('institution', null);

        $em = $this->getDoctrine()->getManager();
        /** @var ProgramRepository $er */
        $er = $em->getRepository(Program::class);
        $qb = $er->createQueryBuilder($er->getAlias());

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq($er->replaceFieldAlias('institution'), $institution),
            $qb->expr()->isNull($er->replaceFieldAlias('institution'))
        ));

        return $this->responseJson($qb->getQuery()->getResult(), function ($result) {

            $data = [];

            foreach ($result as $item){
                $data[$item->getId()] = $item->getName();
            }

            return $data;
        });
    }

}
