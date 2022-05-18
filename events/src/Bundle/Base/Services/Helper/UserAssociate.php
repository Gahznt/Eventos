<?php

namespace App\Bundle\Base\Services\Helper;

use App\Bundle\Base\Entity\UserAssociation;
use App\Bundle\Base\Repository\ExampleRepository;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class UserAssociate
 * @package App\Bundle\Base\Services
 */
class UserAssociate extends ServiceBase implements ServiceInterface
{
    /**
     * @var ExampleRepository
     */
    private $entity;

    /**
     * Example constructor.
     * @param UserAssociation $userAssociation
     */
    public function __construct(UserAssociation $userAssociation)
    {
        $this->entity = $userAssociation;
    }

    /**
     * @param $value
     * @return array
     */
    public static function getLevel($value)
    {
        $const = UserAssociation::USER_ASSOCIATIONS_LEVEL;
        $const = array_flip($const);


        if (isset($const[$value])) {
            return [ $value => $const[$value] ];
        }

        return [ 0 => $const[0] ];
    }
}