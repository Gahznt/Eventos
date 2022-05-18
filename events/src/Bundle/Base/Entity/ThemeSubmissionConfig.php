<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="theme_submission_config")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\ThemeSubmissionConfigRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class ThemeSubmissionConfig
{
    const IS_CURRENT_TRUE = true;
    const IS_CURRENT_FALSE = false;

    const IS_CURRENT = [
        'Sim' => self::IS_CURRENT_TRUE ? 1 : 0,
        'Não' => self::IS_CURRENT_FALSE ? 1 : 0,
    ];

    const IS_AVAILABLE_TRUE = true;
    const IS_AVAILABLE_FALSE = false;

    const IS_AVAILABLE = [
        'Sim' => self::IS_AVAILABLE_TRUE ? 1 : 0,
        'Não' => self::IS_AVAILABLE_FALSE ? 1 : 0,
    ];

    /**
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $deletedAt = null;

    /**
     * @ORM\Column(name="year", type="smallint", length=4, nullable=true)
     */
    private ?string $year = null;

    /**
     * @ORM\Column(name="is_available", type="boolean", nullable=true)
     */
    private ?bool $isAvailable = self::IS_AVAILABLE_FALSE;

    /**
     * @ORM\Column(name="is_current", type="boolean", nullable=true)
     */
    private ?bool $isCurrent = self::IS_CURRENT_FALSE;

    /**
     * @ORM\Column(name="is_evaluation_available", type="boolean", nullable=true)
     */
    private ?bool $isEvaluationAvailable = self::IS_AVAILABLE_FALSE;

    /**
     * @ORM\Column(name="is_result_available", type="boolean", nullable=true)
     */
    private ?bool $isResultAvailable = self::IS_AVAILABLE_FALSE;

    /**
     * @var UserThemes[]
     * @ORM\OneToMany(targetEntity="UserThemes", mappedBy="themeSubmissionConfig", cascade={"persist", "remove"})
     */
    private ?Collection $themes;

    public function __construct()
    {
        $this->themes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(?bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;
        return $this;
    }

    public function getIsCurrent(): ?bool
    {
        return $this->isCurrent;
    }

    public function setIsCurrent(?bool $isCurrent): self
    {
        $this->isCurrent = $isCurrent;
        return $this;
    }

    public function getIsEvaluationAvailable(): ?bool
    {
        return $this->isEvaluationAvailable;
    }

    public function setIsEvaluationAvailable(?bool $isEvaluationAvailable): self
    {
        $this->isEvaluationAvailable = $isEvaluationAvailable;
        return $this;
    }

    public function getIsResultAvailable(): ?bool
    {
        return $this->isResultAvailable;
    }

    public function setIsResultAvailable(?bool $isResultAvailable): self
    {
        $this->isResultAvailable = $isResultAvailable;
        return $this;
    }

    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(UserThemes $theme): self
    {
        if (! $this->themes->contains($theme)) {
            $this->themes[] = $theme;
            $theme->setThemeSubmissionConfig($this);
        }

        return $this;
    }

    public function removeTheme(UserThemes $theme): self
    {
        if ($this->themes->removeElement($theme)) {
            // set the owning side to null (unless already changed)
            if ($theme->getThemeSubmissionConfig() === $this) {
                $theme->setThemeSubmissionConfig(null);
            }
        }

        return $this;
    }
}
