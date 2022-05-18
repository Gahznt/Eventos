<?php

namespace App\Bundle\Base\Entity;

/**
 * Class ArticleEvaluationSearch
 * @package App\Bundle\Base\Entity
 */
class ArticleEvaluationSearch
{

    private $search;
    private $division;
    private $userThemes;

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param $search
     * @return ArticleEvaluationSearch
     */
    public function setSearch($search): self
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return Division|null
     */
    public function getDivision(): ?Division
    {
        return $this->division;
    }

    /**
     * @param Division|null $division
     * @return ArticleEvaluationSearch
     */
    public function setDivision(?Division $division): self
    {
        $this->division = $division;

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
     * @return ArticleEvaluationSearch
     */
    public function setUserThemes($userThemes): self
    {
        $this->userThemes = $userThemes;

        return $this;
    }

    public function __toString(): string
    {
        return $this->search;
    }
}
