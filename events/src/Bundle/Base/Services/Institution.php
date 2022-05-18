<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Repository\InstitutionRepository;
use App\Bundle\Base\Repository\StateRepository;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class State
 * @package App\Bundle\Base\Services
 */
class Institution extends ServiceBase implements ServiceInterface
{
    /**
     * @var StateRepository
     */
    private $entity;

    /**
     * Institution constructor.
     * @param InstitutionRepository $institutionRepository
     */
    public function __construct(InstitutionRepository $institutionRepository)
    {
        $this->entity = $institutionRepository;
    }

    /**
     * @return array
     */
    function getById()
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
}