<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SystemEvaluationConfig
 *
 * @ORM\Table(name="system_evaluation_config")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\SystemEvaluationConfigRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class SystemEvaluationConfig
{
    const ENABLE = 1;
    const DISABLE = 0;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Submissão de Artigos
     *
     * @var int|null
     *
     * @ORM\Column(name="article_submission_available", type="integer", nullable=false)
     */
    private $articeSubmissionAvaliable = 0;

    /**
     * Avaliação de Artigos
     *
     * @var int|null
     *
     * @ORM\Column(name="evaluate_article_available", type="integer", nullable=false)
     */
    private $evaluateArticleAvaliable = 0;

    /**
     * Resultados Finais
     *
     * @var int|null
     *
     * @ORM\Column(name="results_available", type="integer", nullable=false)
     */
    private $resultsAvaliable = 0;

    /**
     * Disponibilizar artigos para avaliação
     *
     * @var int|null
     *
     * @ORM\Column(name="article_free", type="integer", nullable=false)
     */
    private $articeFree = 0;

    /**
     * Gerar Certificados
     *
     * @var int|null
     *
     * @ORM\Column(name="automatic_certiticates", type="integer", nullable=false)
     */
    private $automaticCertiticates = 0;

    /**
     * Liberar Certificados para Download
     *
     * @var int|null
     *
     * @ORM\Column(name="free_certiticates", type="integer", nullable=false)
     */
    private $freeCertiticates = 0;

    /**
     * Ensalamento geral
     *
     * @var int|null
     *
     * @ORM\Column(name="ensalement_general", type="integer", nullable=false)
     */
    private $ensalementGeneral = 0;

    /**
     * Ensalamento prioritário
     *
     * @var int|null
     *
     * @ORM\Column(name="ensalement_priority", type="integer", nullable=false)
     */
    private $ensalementPriority = 0;

    /**
     * Liberar Seções
     *
     * @var int|null
     *
     * @ORM\Column(name="free_sections", type="integer", nullable=false)
     */
    private $freeSections = 0;

    /**
     * Liberar inscrições
     *
     * @var int|null
     *
     * @ORM\Column(name="free_signup", type="integer", nullable=false)
     */
    private $freeSignup = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip", type="string", nullable=true)
     */
    private $ip;

    /**
     * Submissão de Painéis
     *
     * @var int|null
     *
     * @ORM\Column(name="panel_submission_available", type="integer", nullable=false)
     */
    private $panelSubmissionAvailable = 0;

    /**
     * Submissão de Teses
     *
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=false, name="thesis_submission_available")
     */
    private $thesisSubmissionAvailable = 0;

    /**
     * Programação detalhada (subsections, Minha Agenda)
     *
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=false, name="detailed_scheduling_available")
     */
    private $detailedSchedulingAvailable = 0;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="systemEvaluationConfigs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private ?User $user = null;

    /**
     * @var Edition|null
     *
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="systemEvaluationConfigs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     * })
     */
    private $edition;

    /**
     * @return int|null
     */
    public function getArticeSubmissionAvaliable(): ?int
    {
        return $this->articeSubmissionAvaliable;
    }

    /**
     * @param int|null $articeSubmissionAvaliable
     *
     * @return SystemEvaluationConfig
     */
    public function setArticeSubmissionAvaliable(?int $articeSubmissionAvaliable): self
    {
        $this->articeSubmissionAvaliable = $articeSubmissionAvaliable;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getEvaluateArticleAvaliable(): ?int
    {
        return $this->evaluateArticleAvaliable;
    }

    /**
     * @param int|null $evaluateArticleAvaliable
     *
     * @return SystemEvaluationConfig
     */
    public function setEvaluateArticleAvaliable(?int $evaluateArticleAvaliable): self
    {
        $this->evaluateArticleAvaliable = $evaluateArticleAvaliable;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getResultsAvaliable(): ?int
    {
        return $this->resultsAvaliable;
    }

    /**
     * @param int|null $resultsAvaliable
     *
     * @return SystemEvaluationConfig
     */
    public function setResultsAvaliable(?int $resultsAvaliable): self
    {
        $this->resultsAvaliable = $resultsAvaliable;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getArticeFree(): ?int
    {
        return $this->articeFree;
    }

    /**
     * @param int|null $articeFree
     *
     * @return SystemEvaluationConfig
     */
    public function setArticeFree(?int $articeFree): self
    {
        $this->articeFree = $articeFree;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getAutomaticCertiticates(): ?int
    {
        return $this->automaticCertiticates;
    }

    /**
     * @param int|null $automaticCertiticates
     *
     * @return SystemEvaluationConfig
     */
    public function setAutomaticCertiticates(?int $automaticCertiticates): self
    {
        $this->automaticCertiticates = $automaticCertiticates;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFreeCertiticates(): ?int
    {
        return $this->freeCertiticates;
    }

    /**
     * @param int|null $freeCertiticates
     *
     * @return SystemEvaluationConfig
     */
    public function setFreeCertiticates(?int $freeCertiticates): self
    {
        $this->freeCertiticates = $freeCertiticates;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getEnsalementGeneral(): ?int
    {
        return $this->ensalementGeneral;
    }

    /**
     * @param int|null $ensalementGeneral
     *
     * @return SystemEvaluationConfig
     */
    public function setEnsalementGeneral(?int $ensalementGeneral): self
    {
        $this->ensalementGeneral = $ensalementGeneral;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getEnsalementPriority(): ?int
    {
        return $this->ensalementPriority;
    }

    /**
     * @param int|null $ensalementPriority
     *
     * @return SystemEvaluationConfig
     */
    public function setEnsalementPriority(?int $ensalementPriority): self
    {
        $this->ensalementPriority = $ensalementPriority;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFreeSections(): ?int
    {
        return $this->freeSections;
    }

    /**
     * @param int|null $freeSections
     *
     * @return SystemEvaluationConfig
     */
    public function setFreeSections(?int $freeSections): self
    {
        $this->freeSections = $freeSections;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFreeSignup(): ?int
    {
        return $this->freeSignup;
    }

    public function setFreeSignup(?int $freeSignup): self
    {
        $this->freeSignup = $freeSignup;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return $this
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime|null $createdAt
     *
     * @return SystemEvaluationConfig
     */
    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime|null $updatedAt
     *
     * @return SystemEvaluationConfig
     */
    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime|null $deletedAt
     *
     * @return SystemEvaluationConfig
     */
    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return SystemEvaluationConfig
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Edition|null
     */
    public function getEdition(): ?Edition
    {
        return $this->edition;
    }

    /**
     * @param Edition $edition
     *
     * @return SystemEvaluationConfig
     */
    public function setEdition(Edition $edition): self
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @param null|string $ip
     *
     * @return SystemEvaluationConfig
     */
    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPanelSubmissionAvailable(): ?int
    {
        return $this->panelSubmissionAvailable;
    }

    /**
     * @param int|null $panelSubmissionAvailable
     *
     * @return SystemEvaluationConfig
     */
    public function setPanelSubmissionAvailable(?int $panelSubmissionAvailable): SystemEvaluationConfig
    {
        $this->panelSubmissionAvailable = $panelSubmissionAvailable;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getThesisSubmissionAvailable(): ?int
    {
        return $this->thesisSubmissionAvailable;
    }

    /**
     * @param int|null $thesisSubmissionAvailable
     *
     * @return SystemEvaluationConfig
     */
    public function setThesisSubmissionAvailable(?int $thesisSubmissionAvailable): SystemEvaluationConfig
    {
        $this->thesisSubmissionAvailable = $thesisSubmissionAvailable;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getDetailedSchedulingAvailable(): ?int
    {
        return $this->detailedSchedulingAvailable;
    }

    /**
     * @param int|null $detailedSchedulingAvailable
     *
     * @return SystemEvaluationConfig
     */
    public function setDetailedSchedulingAvailable(?int $detailedSchedulingAvailable): SystemEvaluationConfig
    {
        $this->detailedSchedulingAvailable = $detailedSchedulingAvailable;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->user;
    }
}
