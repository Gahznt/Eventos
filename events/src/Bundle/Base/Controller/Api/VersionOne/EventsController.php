<?php

namespace App\Bundle\Base\Controller\Api\VersionOne;

use App\Bundle\Base\Contracts\ApiControllerBase as Controller;
use App\Bundle\Base\Services\Edition;
use App\Bundle\Base\Services\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("event")
 *
 * Class EventsController
 * @package App\Bundle\Base\Controller\Api\VersionOne
 */
class EventsController extends Controller
{
    private $eventService;
    private $editionService;

    public function __construct(Edition $edition, Event $event)
    {
        $this->eventService = $event;
        $this->editionService = $edition;
    }

    /**
     * @Route(
     *     path         = "/edition_by_event",
     *     name         = "edition_by_event",
     *     methods      = {"POST"}
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getEditionByEvent(Request $request)
    {
        $id = $request->get('depdrop_parents', null);

        return $this->responseJson($this->editionService->getByEvent($id), function ($data) {
            return  ['output' => $data, 'selected' => ""];
        });
    }

    /**
     * @Route(
     *     path         = "/events",
     *     name         = "events",
     *     methods      = {"GET","POST"}
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getEvents(Request $request)
    {
        $id = $request->get('depdrop_parents', null);

        if ($id) {
            $data = $this->eventService->getById($id);
        }else{
            $data = $this->eventService->getAll();
        }

        return $this->responseJson($data, function ($data) {
            return  ['output' => $data, 'selected' => ""];
        });
    }
}
