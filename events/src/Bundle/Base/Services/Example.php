<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Repository\ExampleRepository;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class Example
 * @package App\Bundle\Base\Services
 */
class Example extends ServiceBase implements ServiceInterface
{
    /**
     * @var ExampleRepository
     */
    private $entity;

    /**
     * Example constructor.
     * @param ExampleRepository $exampleRepository
     */
    public function __construct(ExampleRepository $exampleRepository)
    {
        $this->entity = $exampleRepository;
    }

    public function getExamples()
    {
        return $this->entity->findCustom(['id', 'name']);
    }
}