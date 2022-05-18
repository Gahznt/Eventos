<?php

namespace App\Bundle\Base\Services\Helper;

use App\Bundle\Base\Entity\Permission as Entity;
use App\Bundle\Base\Entity\UserAssociation;
use App\Bundle\Base\Repository\DivisionCoordinatorRepository;
use App\Bundle\Base\Repository\UserRepository;
use App\Bundle\Base\Repository\UserThemesResearchersRepository;

class Permission
{
    public $repository;
    public $userThemesResearchersRepository;
    public $divisionCoordinator;

    public function __construct(
        UserRepository $userRepository,
        UserThemesResearchersRepository $userThemesResearchersRepository,
        DivisionCoordinatorRepository $divisonCoordinatorRepository
    )
    {
        $this->repository = $userRepository;
        $this->userThemesResearchersRepository = $userThemesResearchersRepository;
        $this->divisionCoordinator = $divisonCoordinatorRepository;
    }

    /**
     * @return array
     */
    public static function getPermissions()
    {
        return Entity::USER_ROLES;
    }

    /**
     * @param $value
     * @return array
     */
    public static function getPermission($value)
    {
        $permission = self::getPermissions();
        $index = array_search($value,$permission);

        if (isset($permission[$index])) {
            return [ $value => $permission[$index] ];
        }

        return [ 0 => $permission[0] ];
    }

    public static function getLevels()
    {
        return UserAssociation::USER_ASSOCIATIONS_LEVEL;
    }

    public static function removeExtraPermission($permissions)
    {
        $permissions = array_flip($permissions);
        unset($permissions[Entity::ROLE_LEADER]);
        unset($permissions[Entity::ROLE_COMMITTEE]);
        unset($permissions[Entity::ROLE_DIVISION_COORDINATOR]);
        unset($permissions[Entity::ROLE_EVALUATOR]);
        unset($permissions[Entity::ROLE_USER_GUEST]);

        return array_flip($permissions);
    }
}
