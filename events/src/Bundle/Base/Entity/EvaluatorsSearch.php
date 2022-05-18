<?php

namespace App\Bundle\Base\Entity;

/**
 * Class EvaluatorsSearch
 * @package App\Bundle\Base\Entity
 */
class EvaluatorsSearch
{

    private $search;
    private $users;

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param $search
     * @return EvaluatorsSearch
     */
    public function setSearch($search): self
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param $users
     * @return EvaluatorsSearch
     */
    public function setUsers($users): self
    {
        $this->users = $users;

        return $this;
    }

    public function __toString(): string
    {
        return $this->search;
    }
}
