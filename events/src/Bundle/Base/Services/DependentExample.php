<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Repository\DependentExampleRepository;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class DependentExample
 * @package App\Bundle\Base\Services
 */
class DependentExample extends ServiceBase implements ServiceInterface
{
    /**
     * @var DependentExampleRepository
     */
    private $entity;

    /**
     * DependentExample constructor.
     * @param DependentExampleRepository $exampleRepository
     */
    public function __construct(DependentExampleRepository $exampleRepository)
    {
        $this->entity = $exampleRepository;
    }

    /**
     * @return \App\Bundle\Base\Entity\DependentExample[]
     */
    public function getDependents()
    {
        return $this->entity->findAll();
    }

    /**
     * @param $id
     * @return \App\Bundle\Base\Entity\DependentExample|null
     */
    public function getDependent($id)
    {
        return $this->entity->find($id);
    }
}