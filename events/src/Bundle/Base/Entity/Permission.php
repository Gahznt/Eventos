<?php

namespace App\Bundle\Base\Entity;

/**
 * Class Permission
 * @package App\Bundle\Base\Entity
 */
/**
 * Class Permission
 * @package App\Bundle\Base\Entity
 */
class Permission
{
    /**
     *
     */
    const PREFIX_ROLE = 'ROLE_';
    /**
     *
     */
    const ROLE_ADMIN_OPERATIONAL = 'ROLE_ADMIN_OPERATIONAL';
    /**
     *
     */
    const ROLE_USER_GUEST = 'ROLE_USER_GUEST';
    /**
     *
     */
    const ROLE_ADMIN = 'ROLE_ADMIN';
    /**
     *
     */
    const ROLE_LEADER = 'ROLE_LEADER';
    /**
     *
     */
    const ROLE_DIVISION_COORDINATOR = 'ROLE_DIVISION_COORDINATOR';

    const ROLE_COMMITTEE = 'ROLE_COMMITTEE';

    /**
     *
     */
    const ROLE_EVALUATOR = 'ROLE_EVALUATOR';
    /**
     *
     */
    const ROLE_DIRECTOR = 'ROLE_DIRECTOR';
    /**
     *
     */
    const USER_ROLES = [
        self::ROLE_ADMIN_OPERATIONAL => self::ROLE_ADMIN_OPERATIONAL,
        self::ROLE_ADMIN => self::ROLE_ADMIN,
        self::ROLE_LEADER => self::ROLE_LEADER,
        self::ROLE_DIVISION_COORDINATOR => self::ROLE_DIVISION_COORDINATOR,
        self::ROLE_COMMITTEE => self::ROLE_COMMITTEE,
        self::ROLE_EVALUATOR => self::ROLE_EVALUATOR,
        self::ROLE_DIRECTOR => self::ROLE_DIRECTOR,
        self::ROLE_USER_GUEST => self::ROLE_USER_GUEST
    ];

    const USER_COORDINATORS_ROLES = [
        self::ROLE_LEADER => self::ROLE_LEADER,
        self::ROLE_DIVISION_COORDINATOR => self::ROLE_DIVISION_COORDINATOR,
        self::ROLE_COMMITTEE => self::ROLE_COMMITTEE,
    ];

    /**
     * @var
     */
    private $search;

    /**
     * @var
     */
    private $permissions;

    /**
     * @var
     */
    private $levels;

    /**
     * @return mixed
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return mixed
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * @param $levels
     * @return Permission
     */
    public function setLevels($levels): self
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * @param $permissions
     * @return Permission
     */
    public function setPermissions($permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }

    public function setSearch($search): self
    {
        $this->search = $search;

        return $this;
    }

    public function __toString()
    {
        return $this->search;
    }
}
