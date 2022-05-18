<?php

namespace App\Bundle\Base\Controller\Api\VersionOne;

use App\Bundle\Base\Contracts\ApiControllerBase as Controller;
use App\Bundle\Base\Services\SubDependentExample;
use Proxies\__CG__\App\Bundle\Base\Entity\Theme;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * 
 * @Route("examples")
 *
 * Class ExamplesController
 * @package App\Bundle\Base\Controller\Site
 */
class ExamplesController extends Controller
{
    /**
     * @var SubDependentExample
     */
    private $subDependentExampleService;
    private $teste;

    /**
     * ExamplesController constructor.
     * @param SubDependentExample $subDependentExample
     */
    public function __construct(SubDependentExample $subDependentExample, \App\Bundle\Base\Services\Theme $teste)
    {
        $this->teste = $teste;
        $this->subDependentExampleService = $subDependentExample;
    }

    /**
     * @Route(
     *     path         = "/sub-dependent-example",
     *     name         = "sub_dependent_example",
     *     methods      = {"POST"}
     * )
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getSubDependentExamples(Request $request)
    {
        $id = $request->get('depdrop_parents', null);

        return $this->responseJson($this->subDependentExampleService->getSubDependentExamples($id), function ($data) use ($id) {
           return  ['output' => $data, 'selected' => ""];
        });
    }

    /**
     * @Route(
     *     path         = "/teste",
     *     name         = "teste",
     *     methods      = {"GET"}
     * )
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTeste(Request $request)
    {
        return $this->teste->getThemeById(1);
    }
}