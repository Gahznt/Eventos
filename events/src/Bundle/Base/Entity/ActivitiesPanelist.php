<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivitiesPanelist
 *
 * @ORM\Table(name="activities_panelist")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\ActivityRepository")
 */
class ActivitiesPanelist
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
     * @var Activity|null
     *
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="panelists")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
     * })
     */
    private $activity;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="activitiesPanelists")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="panelist_id", referencedColumnName="id")
     * })
     */
    private ?User $panelist = null;

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
     * @return Activity|null
     */
    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    /**
     * @param Activity|null $activity
     *
     * @return $this
     */
    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getPanelist(): ?User
    {
        return $this->panelist;
    }

    public function setPanelist(?User $panelist): self
    {
        $this->panelist = $panelist;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->id;
    }
}
