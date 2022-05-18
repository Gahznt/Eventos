<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Repository\StateRepository;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class State
 * @package App\Bundle\Base\Services
 */
class State extends ServiceBase implements ServiceInterface
{
    /**
     * @var StateRepository
     */
    private $entity;

    /**
     * State constructor.
     * @param StateRepository $StateRepository
     */
    public function __construct(StateRepository $StateRepository)
    {
        $this->entity = $StateRepository;
    }

    public function defaultState()
    {
        return 'Paraná';
    }

    /**
     * @return array
     */
    function getStates()
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
    function getStateByName(String $name)
    {
        return $this->entity->findCustom(
            ['id', 'name'],
            ['name' => $name],
            1);
    }

    /**
     * @param $id
     * @return \App\Bundle\Base\Entity\State|null
     */
    function getStateById($id)
    {
        return $this->entity->find($id);
    }

    /**
     * @param $countryId
     * @return array
     */
    function getStateByCountryId($countryId)
    {
        return $this->entity->findCustom(
            ['id', 'name'],
            ['country' => $countryId],
            null,
            ['name' => 'ASC']
        );
    }

    /**
     * @param $countryId
     * @param $otherId
     * @return array
     */
    function getStateByCountryIdAndOther($countryId, $otherId)
    {
        return $this->entity->findCustom(
            ['id', 'name'],
            null,
            null,
            ['name' => 'ASC'],
            ['country' => [$countryId, $otherId]]
        );
    }

}