<?php

namespace App\Bundle\Base\Entity;

/**
 * Class ArticleIndicationSearch
 * @package App\Bundle\Base\Entity
 */
class ArticleIndicationSearch
{
    private $search;
    private $themes;

    /**
     * @return mixed
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * @param $themes
     * @return ArticleIndicationSearch
     */
    public function setThemes($themes): self
    {
        $this->themes = $themes;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->themes;
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
     * @return ArticleIndicationSearch
     */
    public function setSearch($search): self
    {
        $this->search = $search;

        return $this;
    }
}
