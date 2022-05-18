<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Repository\EventRepository;

/**
 * Class Event
 * @package App\Bundle\Base\Services
 */
class Event extends ServiceBase implements ServiceInterface
{
    /**
     * @var EventRepository
     */
    private $repository;

    /**
     * Event constructor.
     * @param EventRepository $eventRepository
     */
    public function __construct(EventRepository $eventRepository)
    {
        $this->repository = $eventRepository;
    }

    /**
     * @return \App\Bundle\Base\Entity\Event[]
     */
    public function getAll()
    {
        return $this->repository->findAll();
    }

    /**
     * @param $id
     * @return \App\Bundle\Base\Entity\Event|null
     */
    public function getById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param $name
     * @return \App\Bundle\Base\Entity\Event[]
     */
    public function getByName($name)
    {
        return $this->repository->findBy(['name_portuguese' => $name]);
    }
}