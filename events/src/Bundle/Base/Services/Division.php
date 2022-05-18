<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Repository\DivisionRepository;

/**
 * Class Division
 * @package App\Bundle\Base\Services
 */
class Division extends ServiceBase implements ServiceInterface
{
    /**
     * @var DivisionRepository
     */
    private $entity;

    /**
     * Division constructor.
     * @param DivisionRepository $divisionRepository
     */
    public function __construct(DivisionRepository $divisionRepository)
    {
        $this->entity = $divisionRepository;
    }

    public function getDivisions()
    {
        return $this->entity->findCustom(['id', 'portuguese'], null, null, ['name' => 'ASC']);
    }

    /**
     * @param $id
     * @return \App\Bundle\Base\Entity\Division|null
     */
    function getDivisionById($id)
    {
        return $this->entity->find($id);
    }

}