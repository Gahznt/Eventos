<?php

namespace App\Bundle\Base\Entity;

/**
 * Class SystemEvaluationIndicationsSearch
 * @package App\Bundle\Base\Entity
 */
/**
 * Class SystemEvaluationIndicationsSearch
 * @package App\Bundle\Base\Entity
 */
class SystemEvaluationIndicationsSearch
{

    /**
     * @var
     */
    private $level;

    /**
     * @var
     */
    private $theme;

    /**
     * @var
     */
    private $division;

    /**
     * @var
     */
    private $search;

    /**
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param $level
     * @return SystemEvaluationIndicationsSearch
     */
    public function setLevel($level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param $theme
     * @return SystemEvaluationIndicationsSearch
     */
    public function setTheme($theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * @param $division
     * @return SystemEvaluationIndicationsSearch
     */
    public function setDivision($division): self
    {
        $this->division = $division;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param $search
     * @return SystemEvaluationIndicationsSearch
     */
    public function setSearch($search): self
    {
        $this->search = $search;

        return $this;
    }
}
