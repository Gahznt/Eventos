<?php

namespace App\Bundle\Base\Entity;

/**
 * Class PanelEvaluationList
 *
 * @package App\Bundle\Base\Entity
 */
class PanelEvaluationList
{

    /**
     * @var Panel|null
     */
    private $id;

    /**
     * @var Edition|null
     */
    private $editionId;

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
    private $language;

    /**
     * @var string|null
     */
    private $justification;

    /**
     * @var User
     */
    private $proponentId;

    /**
     * @var string|null
     */
    private $proponentCurriculumLattesLink;

    /**
     * @var string|null
     */
    private $proponentCurriculumPdfPath;

    /**
     * @var string|null
     */
    private $suggestion;

    /**
     * @var PanelsPanelist|null
     */
    private $panelsPanelists;

    /**
     * @var int|null
     */
    private $statusEvaluation;

    /**
     * @var string|null
     */
    private $search;

    /**
     * @return PanelsPanelist
     */
    public function getPanelsPanelists(): ?PanelsPanelist
    {
        return $this->panelsPanelists;
    }

    /**
     * @return PanelsPanelist
     */
    public function setPanelsPanelists(PanelsPanelist $PanelsPanelist): self
    {
        $this->panelsPanelists = $PanelsPanelist;

        return $this;
    }

    /**
     * @return Panel|null
     */
    public function getId(): ?Panel
    {
        return $this->id;
    }

    /**
     * @param Panel $id
     *
     * @return Panel
     */
    public function setId(Panel $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEditionId(): ?Edition
    {
        return $this->editionId;
    }

    public function getEdition(): ?Edition
    {
        return $this->editionId;
    }

    public function setEditionId(?Edition $editionId): self
    {
        $this->editionId = $editionId;

        return $this;
    }

    public function setEdition(?Edition $editionId): self
    {
        $this->editionId = $editionId;

        return $this;
    }

    /**
     * @return Division
     */
    public function getDivisionId(): ?Division
    {
        return $this->divisionId;
    }

    /**
     * @param Division $divisionId
     *
     * @return Panel
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
     * @param null|string $title
     *
     * @return Panel
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLanguage(): ?int
    {
        return $this->language;
    }

    /**
     * @param int|null $language
     *
     * @return Panel
     */
    public function setLanguage(?int $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getJustification(): ?string
    {
        return $this->justification;
    }

    /**
     * @param null|string $justification
     *
     * @return Panel
     */
    public function setJustification(?string $justification): self
    {
        $this->justification = $justification;

        return $this;
    }

    /**
     * @return User
     */
    public function getProponentId(): ?User
    {
        return $this->proponentId;
    }

    /**
     * @param User $proponentId
     *
     * @return Panel
     */
    public function setProponentId(User $proponentId): self
    {
        $this->proponentId = $proponentId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getProponentCurriculumLattesLink(): ?string
    {
        return $this->proponentCurriculumPdfPath;
    }

    /**
     * @param null|string $proponent_curriculum_lattes_link
     *
     * @return Panel
     */
    public function setProponentCurriculumLattesLink(?string $proponent_curriculum_lattes_link): self
    {
        $this->proponentCurriculumLattesLink = $proponent_curriculum_lattes_link;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getProponentCurriculumPdfPath(): ?string
    {
        return $this->proponentCurriculumLattesLink;
    }

    /**
     * @param null|string $proponent_curriculum_pdf_path
     *
     * @return Panel
     */
    public function setProponentCurriculumPdfPath(?string $proponent_curriculum_pdf_path): self
    {
        $this->proponentCurriculumPdfPath = $proponent_curriculum_pdf_path;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSuggestion(): ?string
    {
        return $this->suggestion;
    }

    /**
     * @param null|string $suggestion
     *
     * @return Panel
     */
    public function setSuggestion(?string $suggestion): self
    {
        $this->suggestion = $suggestion;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getStatusEvaluation(): ?int
    {
        return $this->statusEvaluation;
    }

    public function setStatusEvaluation(?int $statusEvaluation): self
    {
        $this->statusEvaluation = $statusEvaluation;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param null|string $search
     *
     * @return PanelEvaluationList
     */
    public function setSearch(?string $search): self
    {
        $this->search = $search;

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }
}
