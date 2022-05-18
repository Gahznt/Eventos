<?php

namespace App\Bundle\Base\Controller\Api\VersionOne;

use App\Bundle\Base\Contracts\ApiControllerBase as Controller;
use App\Bundle\Base\Services\City;
use App\Bundle\Base\Services\State;
use App\Bundle\Base\Services\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("location")
 *
 * Class LocationController
 *
 * @package App\Bundle\Base\Controller\Api\VersionOne
 */
class LocationController extends Controller
{
    private $stateService;
    private $cityService;
    private $userService;

    public function __construct(City $city, State $state, User $user)
    {
        $this->stateService = $state;
        $this->cityService = $city;
        $this->userService = $user;
    }

    /**
     * @Route(
     *     path         = "/state_by_country",
     *     name         = "state_by_country",
     *     methods      = {"POST"}
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getStateByCountry(Request $request)
    {
        $id = $request->get('depdrop_parents', null);

        return $this->responseJson($this->stateService->getStateByCountryId($id), function ($data) {
            return ['output' => $data, 'selected' => ""];
        });
    }

    /**
     * @Route(
     *     path         = "/city_by_state_and_country",
     *     name         = "city_by_state_and_country",
     *     methods      = {"POST"}
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getCityByStateAndCountry(Request $request)
    {
        $stateAndCountry = $request->get('depdrop_parents', null);
        $stateId = is_array($stateAndCountry) && ! empty($stateAndCountry)
            ? count($stateAndCountry) > 1 ? $stateAndCountry[1] : $stateAndCountry[0]
            : null;

        $data = [];
        if (! empty($stateId)) {
            $data = $this->cityService->getCityByState($stateId);
        }

        return $this->responseJson($data, function ($data) {
            return ['output' => $data, 'selected' => ""];
        });
    }

    /**
     * @Route(
     *     path         = "/user_by_country",
     *     name         = "user_by_country",
     *     methods      = {"GET"}
     * )
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getUserByCountry(Request $request)
    {
        $countryId = intval($request->get('countryId', 0));
        $cpf = (string)($request->get('cpf', ""));

        return $this->responseJson($this->userService->getUserByCountry($countryId, $cpf), function ($data) {
            return ['data' => $data];
        });
    }
}
