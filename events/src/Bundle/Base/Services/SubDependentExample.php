<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Repository\SubDependentExampleRepository;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use PhpParser\Node\Expr\Cast\Int_;

/**
 * Class SubDependentExample
 * @package App\Bundle\Base\Services
 */
class SubDependentExample extends ServiceBase implements ServiceInterface
{
    /**
     * @var SubDependentExampleRepository
     */
    private $entity;

    /**
     * SubDependentExample constructor.
     * @param SubDependentExampleRepository $subDependentExampleRepository
     */
    public function __construct(SubDependentExampleRepository $subDependentExampleRepository)
    {
        $this->entity = $subDependentExampleRepository;
    }

    /**
     * @param $dependentExampleId
     * @return array
     */
    public function getSubDependentExamples($dependentExampleId)
    {
        return $this->entity->findCustom(['id', 'name'], ['dependentExample' => $dependentExampleId]);
    }

    /**
     * @param Int $id
     * @return \App\Bundle\Base\Entity\SubDependentExample|null
     */
    public function getSubDependentExample(Int $id)
    {
        return $this->entity->findOneBy(['dependentExample' => $id]);
    }
}