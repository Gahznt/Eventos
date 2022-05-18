<?php

namespace App\Bundle\Base\Controller\Api\VersionOne;

use App\Bundle\Base\Contracts\ApiControllerBase as Controller;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Repository\UserArticlesRepository;
use App\Bundle\Base\Services\UserArticles as UserArticlesService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("articles")
 *
 * Class UserArticlesController
 *
 * @package App\Bundle\Base\Controller\Api\VersionOne
 */
class UserArticlesController extends Controller
{
    /**
     * @var UserArticlesService
     */
    private $service;

    /**
     * @var UserArticlesRepository
     */
    private $userArticlesRepository;

    /**
     * UserArticlesController constructor.
     *
     * @param UserArticlesService $service
     * @param UserArticlesRepository $userArticlesRepository
     */
    public function __construct(
        UserArticlesService $service,
        UserArticlesRepository $userArticlesRepository
    )
    {
        $this->service = $service;
        $this->userArticlesRepository = $userArticlesRepository;
    }

    /**
     * @Route(
     *     path         = "/article_by_division",
     *     name         = "article_by_division",
     *     methods      = {"POST"}
     * )
     *
     * Usado para popular artigos usando depdrop
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function articleByDivision(Request $request)
    {
        $params = $request->get('depdrop_all_params', null);
        if (! is_array($params)) {
            return $this->responseJson([]);
        }

        $division = $params['certificate_new_divisionId'] ?? null;
        if (! $division) {
            return $this->responseJson([]);
        }

        $data = $this->service->getByDivision($division);
        return $this->responseJson($data, function ($data) {
            return ['output' => $data, 'selected' => ""];
        });
    }

    /**
     * @Route(
     *     path         = "/{edition}/find",
     *     name         = "articles_find",
     *     methods      = {"GET"}
     * )
     *
     * @param Edition $edition
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function find(Edition $edition, Request $request)
    {
        $response = [
            'results' => [],
        ];

        $userArticles = $this->userArticlesRepository->findBy([
            'editionId' => $edition->getId(),
            'deletedAt' => null,
            'status' => UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED,
        ]);

        foreach ($userArticles as $userArticle) {
            $response['results'][] = [
                'id' => $userArticle->getId(),
                'text' => sprintf('%d - %s', $userArticle->getId(), $userArticle->getTitle()),
            ];
        }

        return $this->responseJson($response);
    }
}
