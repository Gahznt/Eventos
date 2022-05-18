<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Repository\ProgramRepository;

/**
 * Class Program
 * @package App\Bundle\Base\Services
 */
class Program extends ServiceBase implements ServiceInterface
{
    /**
     * @var ProgramRepository
     */
    private $entity;

    /**
     * Program constructor.
     * @param ProgramRepository $programRepository
     */
    public function __construct(ProgramRepository $programRepository)
    {
        $this->entity = $programRepository;
    }

    /**
     * @return \App\Bundle\Base\Entity\Program[]
     */
    public function getPrograms()
    {
        return $this->entity->findAll();
    }
}