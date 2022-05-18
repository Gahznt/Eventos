<?php

namespace App\Bundle\Base\Entity;

/**
 * Class CoordinatorsSearch
 * @package App\Bundle\Base\Entity
 */
class CoordinatorsSearch
{

    private $search;
    private $userThemes;
    private $division;
    private $type;

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

    public function getUserThemes()
    {
        return $this->userThemes;
    }

    public function setUserThemes($userThemes): self
    {
        $this->userThemes = $userThemes;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDivision()
    {
        return $this->division;
    }

    public function setDivision($division): self
    {
        $this->division = $division;

        return $this;
    }

    public function __toString(): string
    {
        return $this->search;
    }
}
