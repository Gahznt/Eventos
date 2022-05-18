<?php

namespace App\Bundle\Base\Contracts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Closure;

/**
 * Class ApiControllerBase
 * @package App\Bundle\Base\Contracts
 */
abstract class ApiControllerBase extends AbstractController
{
    /**
     * @param array|null $data
     * @param \Closure|null $format
     * @return JsonResponse
     */
    public function responseJson(?Array $data, ?Closure $format = null)
    {
        $code = JsonResponse::HTTP_OK;

        if (is_null($data)) {
            $code = JsonResponse::HTTP_BAD_REQUEST;
        }

        if (is_array($data) && empty($data)) {
            $code = JsonResponse::HTTP_NOT_FOUND;
        }

        if ($format instanceof Closure) {
            $data = $format($data);
        }

        return new JsonResponse($data,$code);
    }
}