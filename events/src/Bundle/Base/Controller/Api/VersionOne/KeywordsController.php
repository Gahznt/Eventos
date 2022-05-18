<?php

namespace App\Bundle\Base\Controller\Api\VersionOne;

use App\Bundle\Base\Contracts\ApiControllerBase as Controller;
use App\Bundle\Base\Services\UserThemes as UserThemesService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("keywords")
 *
 * Class KeywordsController
 * @package App\Bundle\Base\Controller\Api\VersionOne
 */
class KeywordsController extends Controller
{
    /**
     * @var UserThemesService
     */
    private $userThemesService;

    /**
     * @param UserThemesService $theme
     */
    public function __construct(UserThemesService $theme)
    {
        $this->userThemesService = $theme;        
    }

    /**
     * @Route(
     *     path         = "/get_by_themelang",
     *     name         = "get_by_themelang",
     *     methods      = {"POST"}
     * )
     *
     * Usado para popular keywords usando depdrop
     * @param Request $request
     * @return JsonResponse
     */
    public function getByThemeLang(Request $request)
    {
        $params = $request->get('depdrop_all_params', null);        
        if (! is_array($params)) {
            return $this->responseJson([]);
        }

        if (array_key_exists('user_articles_language', $params)) {
            $lang = $params['user_articles_language'];
        } elseif ($request->getSession()->get('_locale')) {
            $lang = UserThemesService::$languages[ $request->getSession()->get('_locale') ];
        } else {
            $lang = "1";
        }
        
        $theme = $params['user_articles_userThemes'] ?? $params['theme_first_id'] ?? $params['theme_second_id'] ?? null;

        if (! $theme) {
            return $this->responseJson([]);
        }
        
        $data = $this->userThemesService->getKeywordsByThemeLang($theme, $lang);

        return $this->responseJson($data, function ($data) {
            return  ['output' => $data, 'selected' => ""];
        });
    }
}
