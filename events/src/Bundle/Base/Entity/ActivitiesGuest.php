<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivitiesGuest
 *
 * @ORM\Table(name="activities_guest")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\ActivityRepository")
 */
class ActivitiesGuest
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
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name;

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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="activitiesGuests")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="guest_id", referencedColumnName="id")
     * })
     */
    private ?User $guest = null;

    /**
     * @var Activity|null
     *
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="guests")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
     * })
     */
    private $activity;

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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getGuest(): ?User
    {
        return $this->guest;
    }

    public function setGuest(?User $guest): self
    {
        $this->guest = $guest;

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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
