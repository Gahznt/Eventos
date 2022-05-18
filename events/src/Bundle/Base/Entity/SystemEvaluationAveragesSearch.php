<?php

namespace App\Bundle\Base\Entity;

/**
 * Class SystemEvaluationAveragesSearch
 * @package App\Bundle\Base\Entity
 */
class SystemEvaluationAveragesSearch
{

    /**
     * @var
     */
    private $primary;

    /**
     * @var
     */
    private $secondary;

    /**
     * @var int|null
     */
    private $saved = 2;

    /**
     * @return mixed
     */
    public function getPrimary()
    {
        return $this->primary;
    }

    /**
     * @param $primary
     * @return SystemEvaluationAveragesSearch
     */
    public function setPrimary($primary): self
    {
        $this->primary = $primary;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSecondary()
    {
        return $this->secondary;
    }

    /**
     * @param $secondary
     * @return SystemEvaluationAveragesSearch
     */
    public function setSecondary($secondary): self
    {
        $this->secondary = $secondary;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSaved(): ?int
    {
        return $this->saved;
    }

    /**
     * @param int|null $saved
     * @return SystemEvaluationAveragesSearch
     */
    public function setSaved(?int $saved): self
    {
        $this->saved = $saved;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->primary.' - '.$this->secondary;
    }
}
