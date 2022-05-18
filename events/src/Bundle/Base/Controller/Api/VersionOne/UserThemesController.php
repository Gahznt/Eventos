<?php

namespace App\Bundle\Base\Controller\Api\VersionOne;

use App\Bundle\Base\Contracts\ApiControllerBase as Controller;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Services\UserThemes as UserThemesService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("themes")
 *
 * Class UserThemesController
 *
 * @package App\Bundle\Base\Controller\Api\VersionOne
 */
class UserThemesController extends Controller
{
    /**
     * @var UserThemesService
     */
    private $service;

    /**
     * @param UserThemesService $userThemesService
     */
    public function __construct(UserThemesService $userThemesService)
    {
        $this->service = $userThemesService;
    }

    /**
     * @Route(
     *     path         = "/get_by_division",
     *     name         = "get_by_division",
     *     methods      = {"POST"}
     * )
     *
     * Usado para popular temas usando depdrop
     *
     * @param Request $request
     * @param Edition|null $edition
     *
     * @return JsonResponse
     */
    public function getByDivision(Request $request)
    {
        $params = $request->get('depdrop_all_params', []);
        $parents = $request->get('depdrop_parents', []);
        
        if (empty($params) && empty($parents)) {
            return $this->responseJson([]);
        }

        $division = $params['user_articles_divisionId'] ??
            $params['division_fist_id'] ??
            $params['division_second_id'] ??
            $params['certificate_new_divisionId'] ??
            $params['coordinators_search_division'] ??
            $parents[0] ??
            null;
        if (! $division) {
            return $this->responseJson([]);
        }

        $data = $this->service->getByDivision($division);

        return $this->responseJson($data, function ($data) {
            return ['output' => $data, 'selected' => ""];
        });
    }
}
