<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Repository\CityRepository;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class City
 * @package App\Bundle\Base\Services
 */
class City extends ServiceBase implements ServiceInterface
{
    /**
     * @var CityRepository
     */
    private $entity;

    /**
     * City constructor.
     * @param CityRepository $cityRepository
     */
    public function __construct(CityRepository $cityRepository)
    {
        $this->entity = $cityRepository;
    }

    /**
     * @return array
     */
    public function getCities()
    {
        return $this->entity->findCustom(['id', 'name'], null, null, ['name' => 'ASC']);
    }

    public function getCityByState(?int $stateId)
    {
        return $this->entity->findCustom(['id', 'name'], ['state' => $stateId], null, ['name' => 'ASC']);
    }

    /**
     * @param $id
     * @return \App\Bundle\Base\Entity\City|null
     */
    public function getCityById($id)
    {
        return $this->entity->find($id);
    }
}
