<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Repository\MethodRepository;

/**
 * Class Method
 * @package App\Bundle\Base\Services
 */
class Method extends ServiceBase implements ServiceInterface
{
    /**
     * @var MethodRepository
     */
    private $entity;

    /**
     * Method constructor.
     * @param MethodRepository $methodRepository
     */
    public function __construct(MethodRepository $methodRepository)
    {
        $this->entity = $methodRepository;
    }

    /**
     * @return \App\Bundle\Base\Entity\Method[]
     */
    public function getMethods()
    {
        return $this->entity->findAll();
    }
}