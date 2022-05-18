<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Repository\CountryRepository;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class Country
 * @package App\Bundle\Base\Services
 */
class Country extends ServiceBase implements ServiceInterface
{
    /**
     * @var CountryRepository
     */
    private $entity;

    /**
     * Country constructor.
     * @param CountryRepository $countryRepository
     */
    public function __construct(CountryRepository $countryRepository)
    {
        $this->entity = $countryRepository;
    }

    /**
     * @return array
     */
    function getCountries()
    {
        return $this->entity->findCustom(
            ['id', 'name'],
            null,
            null,
            ['name' => 'ASC']);
    }

    /**
     * @param String $name
     * @return array
     */
    function getCountryByName(String $name)
    {
        return $this->entity->findCustom(
            ['id', 'name'],
            ['name' => $name],
            1);
    }

    /**
     * @param $id
     * @return \App\Bundle\Base\Entity\Country|null
     */
    function getCountryById($id)
    {
        return $this->entity->find($id);
    }

    /**
     * @param $stateId
     * @return array
     */
    function getCountryByStateId($stateId)
    {
        return $this->entity->findCustom(
            ['state_id' => $stateId],
            ['name' => 'ASC']
        );
    }

    /**
     * @param $cityId
     * @return array
     */
    function getCountryByCityId($cityId)
    {
        return $this->entity->findCustom(
            ['city_id' => $cityId],
            ['name' => 'ASC']
        );
    }
}