<?php

namespace App\Bundle\Base\Entity;

class PanelEvaluationAction
{

    /**
    * @var Panel|null
    */
    private $id;

    /**
     * @var Division|null
     */
    private $divisionId;

    /**
     * @var string|null
     */
    private $title;

    /**
     * @var int|null
     */
    private $statusEvaluation;

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
     * @return Panel|null
     */
    public function getId(): ?Panel
    {
        return $this->id;
    }

    /**
     * @param Panel|null $id
     * @return PanelEvaluationAction
     */
    public function setId(Panel $id): self
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
     * @return PanelEvaluationAction
     */
    public function setDivisionId(Division $divisionId): self
    {
        $this->divisionId = $divisionId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param null|string $portugueseTitle
     * @return PanelEvaluationAction
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatusEvaluation(): ?int
    {
        return $this->statusEvaluation;
    }

    /**
     * @param int|null $status
     * @return PanelEvaluationAction
     */
    public function setStatusEvaluation(?int $status): self
    {
        $this->statusEvaluation = $status;

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
     * @return PanelEvaluationAction
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
     * @return PanelEvaluationAction
     */
    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

}
