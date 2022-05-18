<?php

namespace App\Bundle\Base\Entity;

use App\Bundle\Base\Services\Helper\PanelEvaluation;
use Armenio\Common\Entity\EntityProperty;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Panel
 *
 * @ORM\Table(name="panel")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\PanelRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Panel
{
    const LANGUAGE = ['Português' => 0, 'Inglês' => 1, 'Espanhol' => 2];

    const PANEL_EVALUATION_STATUS = [
        'PANEL_EVALUATION_STATUS_WAITING' => 1,
        'PANEL_EVALUATION_STATUS_APPROVED' => 2,
        'PANEL_EVALUATION_STATUS_REPROVED' => 3,
        'PANEL_EVALUATION_STATUS_CANCELED' => 4,
    ];

    const PUBLIC_PATH = '/uploads/panel/';
    const UPLOAD_PATH = '#KERNEL#/var/storage' . self::PUBLIC_PATH;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="language", type="boolean", nullable=true)
     */
    private $language = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var string|null
     *
     * @ORM\Column(name="justification", type="text", length=65535, nullable=true)
     */
    private $justification;

    /**
     * @var string|null
     *
     * @ORM\Column(name="suggestion", type="text", length=65535, nullable=true)
     */
    private $suggestion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="proponent_curriculum_lattes_link", type="text", length=16777215, nullable=true)
     */
    private $proponentCurriculumLattesLink;

    /**
     * @var string|null
     *
     * @ORM\Column(name="proponent_curriculum_pdf_path", type="text", length=16777215, nullable=true)
     */
    private $proponentCurriculumPdfPath;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status_evaluation", type="smallint", nullable=true)
     */
    private $statusEvaluation;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private ?\DateTime $createdAt = null;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private ?\DateTime $deletedAt = null;

    /**
     * @var Division|null
     *
     * @ORM\ManyToOne(targetEntity="Division")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="division_id", referencedColumnName="id")
     * })
     */
    private $divisionId;

    /**
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="panels")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     * })
     */
    private ?Edition $editionId = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="panels")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="proponent_id", referencedColumnName="id")
     * })
     */
    private ?User $proponentId = null;

    /**
     * @var Collection|PanelsPanelist[]
     *
     * @ORM\OneToMany(targetEntity="PanelsPanelist", mappedBy="panelId", cascade={"persist", "remove"})
     */
    private $panelsPanelists;

    /**
     * @var Collection|PanelEvaluationLog[]
     *
     * @ORM\OneToMany(targetEntity="PanelEvaluationLog", mappedBy="panel", cascade={"persist", "remove"})
     */
    private $panelEvaluationLogs;

    /**
     * @var int|null
     */
    private $statusEvaluationText;

    /**
     * Panel constructor.
     */
    public function __construct()
    {
        $this->panelsPanelists = new ArrayCollection();
        $this->panelEvaluationLogs = new ArrayCollection();
    }

    /**
     * @return Collection|PanelsPanelist[]
     */
    public function getPanelsPanelists(): Collection
    {
        return $this->panelsPanelists;
    }

    /**
     * @param PanelsPanelist $panelsPanelist
     *
     * @return Panel
     */
    public function addPanelsPanelist(PanelsPanelist $panelsPanelist): self
    {
        $this->panelsPanelists[] = $panelsPanelist;
        $panelsPanelist->setPanelId($this);

        return $this;
    }

    /**
     * @param PanelsPanelist $panelsPanelist
     */
    public function removePanelsPanelist(PanelsPanelist $panelsPanelist)
    {
        $this->panelsPanelists->removeElement($panelsPanelist);
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
     * @return Division|null
     */
    public function getDivisionId(): ?Division
    {
        return $this->divisionId;
    }

    /**
     * @param Division|null $divisionId
     *
     * @return $this
     */
    public function setDivisionId(?Division $divisionId): self
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
     * @return int|null
     */
    public function getLanguage(): ?int
    {
        return $this->language;
    }

    /**
     * @param int|null $language
     *
     * @return $this
     */
    public function setLanguage(?int $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @param string|null $title
     *
     * @return $this
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getJustification(): ?string
    {
        return $this->justification;
    }

    /**
     * @param string|null $justification
     *
     * @return $this
     */
    public function setJustification(?string $justification): self
    {
        $this->justification = $justification;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getProponentId(): ?User
    {
        return $this->proponentId;
    }

    /**
     * @param User|null $proponentId
     *
     * @return $this
     */
    public function setProponentId(?User $proponentId): self
    {
        $this->proponentId = $proponentId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProponentCurriculumLattesLink(): ?string
    {
        return $this->proponentCurriculumLattesLink;
    }

    /**
     * @param string|null $proponentCurriculumLattesLink
     *
     * @return $this
     */
    public function setProponentCurriculumLattesLink(?string $proponentCurriculumLattesLink): self
    {
        $this->proponentCurriculumLattesLink = $proponentCurriculumLattesLink;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProponentCurriculumPdfPath(): ?string
    {
        return $this->proponentCurriculumPdfPath;
    }

    /**
     * @param string|null $proponentCurriculumPdfPath
     *
     * @return $this
     */
    public function setProponentCurriculumPdfPath(?string $proponentCurriculumPdfPath): self
    {
        $this->proponentCurriculumPdfPath = $proponentCurriculumPdfPath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSuggestion(): ?string
    {
        return $this->suggestion;
    }

    /**
     * @param string|null $suggestion
     *
     * @return $this
     */
    public function setSuggestion(?string $suggestion): self
    {
        $this->suggestion = $suggestion;

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
     * @param int|null $statusEvaluation
     *
     * @return $this
     */
    public function setStatusEvaluation(?int $statusEvaluation): self
    {
        $this->statusEvaluation = $statusEvaluation;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatusEvaluationText(): ?string
    {
        return $this->statusEvaluationText;
    }

    /**
     * @return $this
     */
    public function setStatusEvaluationText(): self
    {
        $this->statusEvaluationText = PanelEvaluation::getStatusText($this->statusEvaluation);
        return $this;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->title;
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
     * @return Panel
     */
    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

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
     * @return $this
     */
    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return PanelEvaluationLog[]|Collection<int, PanelEvaluationLog>
     */
    public function getPanelEvaluationLogs(): Collection
    {
        return $this->panelEvaluationLogs;
    }

    public function addPanelEvaluationLog(PanelEvaluationLog $panelEvaluationLog): self
    {
        if (! $this->panelEvaluationLogs->contains($panelEvaluationLog)) {
            $this->panelEvaluationLogs[] = $panelEvaluationLog;
            $panelEvaluationLog->setPanel($this);
        }

        return $this;
    }

    public function removePanelEvaluationLog(PanelEvaluationLog $panelEvaluationLog): self
    {
        if ($this->panelEvaluationLogs->removeElement($panelEvaluationLog)) {
            // set the owning side to null (unless already changed)
            if ($panelEvaluationLog->getPanel() === $this) {
                $panelEvaluationLog->setPanel(null);
            }
        }

        return $this;
    }
}
