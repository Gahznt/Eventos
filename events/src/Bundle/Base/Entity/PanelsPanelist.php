<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PanelsPanelist
 *
 * @ORM\Table(name="panels_panelist"),
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\PanelRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class PanelsPanelist
{
    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var Panel
     *
     * @ORM\ManyToOne(targetEntity="Panel", inversedBy="panelsPanelists")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="panel_id", referencedColumnName="id")
     * })
     */
    private $panelId;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="panelsPanelists")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="panelist_id", referencedColumnName="id")
     * })
     */
    private $panelistId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="proponent_curriculum_lattes_link", type="text", nullable=true)
     */
    private $proponentCurriculumLattesLink;

    /**
     * @var string|null
     *
     * @ORM\Column(name="proponent_curriculum_pdf_path", type="text", nullable=true)
     */
    private $proponentCurriculumPdfPath;

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
     * @return null|string
     */
    public function getProponentCurriculumLattesLink(): ?string
    {
        return $this->proponentCurriculumLattesLink;
    }

    /**
     * @param null|string $proponent_curriculum_lattes_link
     *
     * @return PanelsPanelist
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
        return $this->proponentCurriculumPdfPath;
    }

    /**
     * @param null|string $proponent_curriculum_pdf_path
     *
     * @return PanelsPanelist
     */
    public function setProponentCurriculumPdfPath(?string $proponent_curriculum_pdf_path): self
    {
        $this->proponentCurriculumPdfPath = $proponent_curriculum_pdf_path;

        return $this;
    }

    /**
     * @return Panel
     */
    public function getPanelId(): Panel
    {
        return $this->panelId;
    }

    /**
     * @param Panel $panelId
     *
     * @return PanelsPanelist
     */
    public function setPanelId(Panel $panelId): self
    {
        $this->panelId = $panelId;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getPanelistId(): ?User
    {
        return $this->panelistId;
    }

    /**
     * @param User $panelistId
     *
     * @return PanelsPanelist
     */
    public function setPanelistId(User $panelistId): self
    {
        $this->panelistId = $panelistId;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->id;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
