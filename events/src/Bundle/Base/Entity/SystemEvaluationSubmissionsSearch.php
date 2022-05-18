<?php

namespace App\Bundle\Base\Entity;

/**
 * Class SystemEvaluationSubmissionsSearch
 * @package App\Bundle\Base\Entity
 */
/**
 * Class SystemEvaluationSubmissionsSearch
 * @package App\Bundle\Base\Entity
 */
class SystemEvaluationSubmissionsSearch
{

    /**
     * @var
     */
    private $search;

    /**
     * @var
     */
    private $userThemes;

    /**
     * @var
     */
    private $status;

    /**
     * @var
     */
    private $type;

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param $search
     * @return SystemEvaluationSubmissionsSearch
     */
    public function setSearch($search): self
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserThemes()
    {
        return $this->userThemes;
    }

    /**
     * @param $userThemes
     * @return SystemEvaluationSubmissionsSearch
     */
    public function setUserThemes($userThemes): self
    {
        $this->userThemes = $userThemes;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $status
     * @return SystemEvaluationSubmissionsSearch
     */
    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $type
     * @return SystemEvaluationSubmissionsSearch
     */
    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->search;
    }
}
