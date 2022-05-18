<?php

namespace App\Bundle\Base\Entity;

class ThemeEvaluationAction
{

    /**
    * @var UserThemes|null
    */
    private $id;

    /**
     * @var Division|null
     */
    private $divisionId;

    /**
     * @var string|null
     */
    private $portugueseTitle;

    /**
     * @var string|null
     */
    private $portugueseDescription;

    /**
     * @var int|null
     */
    private $status;

    /**
     * @var string|null
     *
     */
    private $action;

    /**
     * @var string|null
     *
     */
    private $reason;


    /**
     * @return UserThemes|null
     */
    public function getId(): ?UserThemes
    {
        return $this->id;
    }

    /**
     * @param UserThemes|null $id
     * @return ThemeEvaluationAction
     */
    public function setId(UserThemes $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return Division|null
     */
    public function getDivisionId(): ?Division
    {
        return $this->divisionId;
    }

    /**
     * @param Division $divisionId
     * @return ThemeEvaluationAction
     */
    public function setDivisionId(Division $divisionId): self
    {
        $this->divisionId = $divisionId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPortugueseTitle(): ?string
    {
        return $this->portugueseTitle;
    }

    /**
     * @param null|string $portugueseTitle
     * @return ThemeEvaluationAction
     */
    public function setPortugueseTitle(?string $portugueseTitle): self
    {
        $this->portugueseTitle = $portugueseTitle;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPortugueseDescription(): ?string
    {
        return $this->portugueseDescription;
    }

    /**
     * @param null|string $portugueseDescription
     * @return ThemeEvaluationAction
     */
    public function setPortugueseDescription(?string $portugueseDescription): self
    {
        $this->portugueseDescription = $portugueseDescription;

        return $this;
    }


    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     * @return ThemeEvaluationAction
     */
    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @param null|string $action
     * @return ThemeEvaluationAction
     */
    public function setAction(?string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @param null|string $reason
     * @return ThemeEvaluationAction
     */
    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

}