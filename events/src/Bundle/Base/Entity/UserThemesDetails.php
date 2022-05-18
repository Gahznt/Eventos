<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="user_themes_details")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\UserThemesDetailsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class UserThemesDetails
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
     * @var UserThemes
     *
     * @ORM\OneToOne(targetEntity="UserThemes", inversedBy="details")
     * @ORM\JoinColumn(name="user_themes_id", referencedColumnName="id")
     */
    private $userThemes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="portuguese_description", type="text", nullable=true)
     */
    private $portugueseDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(name="english_description", type="text", nullable=true)
     */
    private $englishDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(name="spanish_description", type="text", nullable=true)
     */
    private $spanishDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(name="portuguese_title", type="string", nullable=true)
     */
    private $portugueseTitle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="english_title", type="string", nullable=true)
     */
    private $englishTitle;

    /**
     * @var string|null
     *
     * @ORM\Column(name="spanish_title", type="string", nullable=true)
     */
    private $spanishTitle;

    /**
     * @var string|array
     * @ORM\Column(name="portuguese_keywords", type="json", nullable=true)
     */
    private $portugueseKeywords;

    /**
     * @var string|array
     * @ORM\Column(name="english_keywords", type="json", nullable=true)
     */
    private $englishKeywords;

    /**
     * @var string|array
     * @ORM\Column(name="spanish_keywords", type="json", nullable=true)
     */
    private $spanishKeywords;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUserThemes(): UserThemes
    {
        return $this->userThemes;
    }

    public function setUserThemes(?UserThemes $userThemes): self
    {
        $this->userThemes = $userThemes;

        return $this;
    }

    public function getPortugueseDescription(): ?string
    {
        return $this->portugueseDescription;
    }

    public function setPortugueseDescription(?string $portugueseDescription): self
    {
        $this->portugueseDescription = $portugueseDescription;

        return $this;
    }

    public function getEnglishDescription(): ?string
    {
        return $this->englishDescription;
    }

    public function setEnglishDescription(?string $englishDescription): self
    {
        $this->englishDescription = $englishDescription;

        return $this;
    }

    public function getSpanishDescription(): ?string
    {
        return $this->spanishDescription;
    }

    public function setSpanishDescription(?string $spanishDescription): self
    {
        $this->spanishDescription = $spanishDescription;

        return $this;
    }

    public function getDescription(): ?string
    {
        $i18n = [
            'pt_br' => $this->getPortugueseDescription(),
            'en' => $this->getEnglishDescription(),
            'es' => $this->getSpanishDescription(),
        ];

        return $i18n[\Locale::getDefault()];
    }

    public function getPortugueseTitle(): ?string
    {
        return $this->portugueseTitle;
    }

    public function setPortugueseTitle(?string $portugueseTitle): self
    {
        $this->portugueseTitle = $portugueseTitle;

        return $this;
    }

    public function getEnglishTitle(): ?string
    {
        return $this->englishTitle;
    }

    public function setEnglishTitle(?string $englishTitle): self
    {
        $this->englishTitle = $englishTitle;

        return $this;
    }

    public function getSpanishTitle(): ?string
    {
        return $this->spanishTitle;
    }

    public function setSpanishTitle(?string $spanishTitle): self
    {
        $this->spanishTitle = $spanishTitle;

        return $this;
    }

    public function getTitle(): ?string
    {
        $i18n = [
            'pt_br' => $this->getPortugueseTitle(),
            'en' => $this->getEnglishTitle(),
            'es' => $this->getSpanishTitle(),
        ];

        return $i18n[\Locale::getDefault()];
    }

    public function getPortugueseKeywords(): ?array
    {
        if (! is_string($this->portugueseKeywords)) {
            return $this->portugueseKeywords;
        }

        return json_decode($this->portugueseKeywords);
    }

    public function setPortugueseKeywords($portugueseKeywords = null): self
    {
        if (is_string($portugueseKeywords)) {
            $this->portugueseKeywords = json_decode($portugueseKeywords);
        } else {
            $this->portugueseKeywords = $portugueseKeywords;
        }

        return $this;
    }

    public function getEnglishKeywords(): ?array
    {
        if (! is_string($this->englishKeywords)) {
            return $this->englishKeywords;
        }

        return json_decode($this->englishKeywords);
    }

    public function setEnglishKeywords($englishKeywords = null): self
    {
        if (is_string($englishKeywords)) {
            $this->englishKeywords = json_decode($englishKeywords);
        } else {
            $this->englishKeywords = $englishKeywords;
        }

        return $this;
    }

    public function getSpanishKeywords(): ?array
    {
        if (! is_string($this->spanishKeywords)) {
            return $this->spanishKeywords;
        }

        return json_decode($this->spanishKeywords);
    }

    public function setSpanishKeywords($spanishKeywords = null): self
    {
        if (is_string($spanishKeywords)) {
            $this->spanishKeywords = json_decode($spanishKeywords);
        } else {
            $this->spanishKeywords = $spanishKeywords;
        }

        return $this;
    }

    public function getKeywords(): ?array
    {
        $i18n = [
            'pt_br' => $this->getPortugueseKeywords(),
            'en' => $this->getEnglishKeywords(),
            'es' => $this->getSpanishKeywords(),
        ];

        return $i18n[\Locale::getDefault()];
    }

    public function getDivision(): ?Division
    {
        return $this->division;
    }

    public function setDivision(Division $division): self
    {
        $this->division = $division;

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

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
