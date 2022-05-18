<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Activity
 *
 * @ORM\Table(name="activity")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\ActivityRepository")
 */
class Activity
{
    const ACTIVITY_TYPE_SCIENTIFIC = 1;

    const ACTIVITY_TYPE_DIVISIONAL = 2;

    const ACTIVITY_TYPES = [
        'Científica' => self::ACTIVITY_TYPE_SCIENTIFIC,
        'Divisional' => self::ACTIVITY_TYPE_DIVISIONAL,
    ];

    const ACTIVITY_LANGUAGES = [
        'Português' => 1,
        'English' => 2,
        'Spanish' => 3,
    ];

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
     * @ORM\Column(name="title_portuguese", type="string", nullable=false)
     */
    private $titlePortuguese;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title_english", type="string", nullable=false)
     */
    private $titleEnglish;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title_spanish", type="string", nullable=false)
     */
    private $titleSpanish;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description_portuguese", type="text", length=65535, nullable=false)
     */
    private $descriptionPortuguese;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description_english", type="text", length=65535, nullable=false)
     */
    private $descriptionEnglish;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description_spanish", type="text", length=65535, nullable=false)
     */
    private $descriptionSpanish;

    /**
     * @var int|null
     *
     * @ORM\Column(name="activity_type", type="integer", nullable=false)
     */
    private $activityType;

    /**
     * @var int|null
     *
     * @ORM\Column(name="language", type="integer", nullable=false)
     */
    private $language;

    /**
     * @var string|null
     *
     * @ORM\Column(name="time_restriction", type="string", nullable=false)
     */
    private $timeRestriction = '';

    /**
     * @var Edition|null
     *
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="activities")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     * })
     */
    private $edition;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_global", type="boolean", nullable=true)
     */
    private $isGlobal;

    /**
     * @var Division|null
     *
     * @ORM\ManyToOne(targetEntity="Division")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="division_id", referencedColumnName="id")
     * })
     */
    private $division;

    /**
     * @var Collection|ActivitiesPanelist
     *
     * @ORM\OneToMany(targetEntity="ActivitiesPanelist", mappedBy="activity", cascade={"persist"})
     */
    private $panelists;

    /**
     * @var Collection|ActivitiesGuest
     *
     * @ORM\OneToMany(targetEntity="ActivitiesGuest", mappedBy="activity", cascade={"persist"})
     */
    private $guests;

    /**
     * Activity constructor.
     */
    public function __construct()
    {
        $this->panelists = new ArrayCollection();
        $this->guests = new ArrayCollection();
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
     * @return string|null
     */
    public function getTitlePortuguese(): ?string
    {
        return $this->titlePortuguese;
    }

    /**
     * @param string|null $titlePortuguese
     *
     * @return $this
     */
    public function setTitlePortuguese(?string $titlePortuguese): self
    {
        $this->titlePortuguese = $titlePortuguese;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitleEnglish(): ?string
    {
        return $this->titleEnglish;
    }

    /**
     * @param string|null $titleEnglish
     *
     * @return $this
     */
    public function setTitleEnglish(?string $titleEnglish): self
    {
        $this->titleEnglish = $titleEnglish;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitleSpanish(): ?string
    {
        return $this->titleSpanish;
    }

    /**
     * @param string|null $titleSpanish
     *
     * @return $this
     */
    public function setTitleSpanish(?string $titleSpanish): self
    {
        $this->titleSpanish = $titleSpanish;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $i18n = [
            'pt_br' => $this->getTitlePortuguese(),
            'en' => $this->getTitleEnglish(),
            'es' => $this->getTitleSpanish(),
        ];

        return $i18n[\Locale::getDefault()];
    }

    /**
     * @return string|null
     */
    public function getDescriptionPortuguese(): ?string
    {
        return $this->descriptionPortuguese;
    }

    /**
     * @param string|null $descriptionPortuguese
     *
     * @return $this
     */
    public function setDescriptionPortuguese(?string $descriptionPortuguese): self
    {
        $this->descriptionPortuguese = $descriptionPortuguese;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescriptionEnglish(): ?string
    {
        return $this->descriptionEnglish;
    }

    /**
     * @param string|null $descriptionEnglish
     *
     * @return $this
     */
    public function setDescriptionEnglish(?string $descriptionEnglish): self
    {
        $this->descriptionEnglish = $descriptionEnglish;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescriptionSpanish(): ?string
    {
        return $this->descriptionSpanish;
    }

    /**
     * @param string|null $descriptionSpanish
     *
     * @return $this
     */
    public function setDescriptionSpanish(?string $descriptionSpanish): self
    {
        $this->descriptionSpanish = $descriptionSpanish;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $i18n = [
            'pt_br' => $this->getDescriptionPortuguese(),
            'en' => $this->getDescriptionEnglish(),
            'es' => $this->getDescriptionSpanish(),
        ];

        return $i18n[\Locale::getDefault()];
    }

    /**
     * @return int|null
     */
    public function getActivityType(): ?int
    {
        return $this->activityType;
    }

    /**
     * @param int|null $activityType
     *
     * @return $this
     */
    public function setActivityType(?int $activityType): self
    {
        $this->activityType = $activityType;

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
     * @return $this
     */
    public function setLanguage(?int $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTimeRestriction(): ?string
    {
        return $this->timeRestriction;
    }

    /**
     * @param string|null $timeRestriction
     *
     * @return $this
     */
    public function setTimeRestriction(?string $timeRestriction): self
    {
        $this->timeRestriction = $timeRestriction;

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
     * @param Edition|null $edition
     *
     * @return $this
     */
    public function setEdition(?Edition $edition): self
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsGlobal(): ?bool
    {
        return $this->isGlobal;
    }

    /**
     * @param bool|null $isGlobal
     *
     * @return Activity
     */
    public function setIsGlobal(?bool $isGlobal): Activity
    {
        $this->isGlobal = $isGlobal;
        return $this;
    }

    /**
     * @return Division|null
     */
    public function getDivision(): ?Division
    {
        return $this->division;
    }

    /**
     * @param Division|null $division
     *
     * @return $this
     */
    public function setDivision(?Division $division): self
    {
        $this->division = $division;

        return $this;
    }

    /**
     * @return Collection|ActivitiesPanelist[]
     */
    public function getPanelists(): Collection
    {
        return $this->panelists;
    }

    /**
     * @param ActivitiesPanelist $panelist
     *
     * @return $this
     */
    public function addPanelist(ActivitiesPanelist $panelist): self
    {
        if (! $this->panelists->contains($panelist)) {
            $this->panelists[] = $panelist;
            $panelist->setActivity($this);
        }

        return $this;
    }

    /**
     * @param ActivitiesPanelist $panelist
     *
     * @return $this
     */
    public function removePanelist(ActivitiesPanelist $panelist): self
    {
        if ($this->panelists->removeElement($panelist)) {
            // set the owning side to null (unless already changed)
            if ($panelist->getActivity() === $this) {
                $panelist->setActivity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ActivitiesGuest[]
     */
    public function getGuests(): Collection
    {
        return $this->guests;
    }

    /**
     * @param ActivitiesGuest $guest
     *
     * @return $this
     */
    public function addGuest(ActivitiesGuest $guest): self
    {
        if (! $this->guests->contains($guest)) {
            $this->guests[] = $guest;
            $guest->setActivity($this);
        }

        return $this;
    }

    /**
     * @param ActivitiesGuest $guest
     *
     * @return $this
     */
    public function removeGuest(ActivitiesGuest $guest): self
    {
        if ($this->guests->removeElement($guest)) {
            // set the owning side to null (unless already changed)
            if ($guest->getActivity() === $this) {
                $guest->setActivity(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->titlePortuguese;
    }
}
