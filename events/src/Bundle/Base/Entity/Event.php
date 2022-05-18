<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Event
 *
 * @ORM\Table(
 *     name="event"
 * )
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\EventRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Event
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
     * @var string|null
     *
     * @ORM\Column(name="name_portuguese", type="string", nullable=false)
     */
    private $namePortuguese = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="title_portuguese", type="string", nullable=false)
     */
    private $titlePortuguese = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="description_portuguese", type="text", length=65535, nullable=false)
     */
    private $descriptionPortuguese;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name_english", type="string", nullable=false)
     */
    private $nameEnglish = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="title_english", type="string", nullable=false)
     */
    private $titleEnglish = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="description_english", type="text", length=65535, nullable=false)
     */
    private $descriptionEnglish;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name_spanish", type="string", nullable=false)
     */
    private $nameSpanish = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="title_spanish", type="string", nullable=false)
     */
    private $titleSpanish = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="description_spanish", type="text", length=65535, nullable=false)
     */
    private $descriptionSpanish;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_homolog", type="boolean", nullable=true)
     */
    private $isHomolog = false;

    /**
     * @var int|null
     *
     * @ORM\Column(name="number_words", type="smallint", nullable=false)
     */
    private $numberWords = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="issn", type="string", nullable=true)
     */
    private $issn;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_show_previous_events_home", type="boolean", nullable=true, options={"default"="1"})
     */
    private $isShowPreviousEventsHome = true;

    /**
     * @var Collection|Edition[]
     *
     * @ORM\OneToMany(targetEntity="Edition", mappedBy="event")
     * @ORM\OrderBy({"position"="ASC"})
     */
    private $editions;

    /**
     * @var Collection|Division[]
     *
     * @ORM\ManyToMany(targetEntity="Division")
     * @ORM\JoinTable(
     *     name="event_divisions",
     *     joinColumns={@ORM\JoinColumn(name="event_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="division_id", referencedColumnName="id")
     * })
     * @ORM\OrderBy({"id"="ASC"})
     */
    private $divisions;

    /**
     * Event constructor.
     */
    public function __construct()
    {
        $this->editions = new ArrayCollection();
        $this->divisions = new ArrayCollection();
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
     * @param \DateTime $createdAt
     *
     * @return $this
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
     * @param \DateTime $updatedAt
     *
     * @return $this
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
     * @param \DateTime $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNamePortuguese(): ?string
    {
        return $this->namePortuguese;
    }

    /**
     * @param string $namePortuguese
     *
     * @return $this
     */
    public function setNamePortuguese(?string $namePortuguese): self
    {
        $this->namePortuguese = $namePortuguese;
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
     * @param string $titlePortuguese
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
    public function getDescriptionPortuguese(): ?string
    {
        return $this->descriptionPortuguese;
    }

    /**
     * @param string $descriptionPortuguese
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
    public function getNameEnglish(): ?string
    {
        return $this->nameEnglish;
    }

    /**
     * @param string $nameEnglish
     *
     * @return $this
     */
    public function setNameEnglish(?string $nameEnglish): self
    {
        $this->nameEnglish = $nameEnglish;
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
     * @param string $titleEnglish
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
    public function getDescriptionEnglish(): ?string
    {
        return $this->descriptionEnglish;
    }

    /**
     * @param string $descriptionEnglish
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
    public function getNameSpanish(): ?string
    {
        return $this->nameSpanish;
    }

    /**
     * @param string $nameSpanish
     *
     * @return $this
     */
    public function setNameSpanish(?string $nameSpanish): self
    {
        $this->nameSpanish = $nameSpanish;
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
     * @param string $titleSpanish
     *
     * @return $this
     */
    public function setTitleSpanish(?string $titleSpanish): self
    {
        $this->titleSpanish = $titleSpanish;
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
     * @param string $descriptionSpanish
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
    public function getName()
    {
        $i18n = [
            'pt_br' => $this->getNamePortuguese(),
            'en' => $this->getNameEnglish(),
            'es' => $this->getNameSpanish(),
        ];

        return $i18n[\Locale::getDefault()];
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
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|string $status
     *
     * @return $this
     */
    public function setStatus(?int $status): self
    {
        $this->status = (int)$status;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsHomolog(): ?bool
    {
        return $this->isHomolog;
    }

    /**
     * @param bool|string $isHomolog
     *
     * @return $this
     */
    public function setIsHomolog(?bool $isHomolog): self
    {
        $this->isHomolog = (bool)$isHomolog;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->namePortuguese;
    }

    /**
     * @param array $args
     *
     * @return Collection|Edition[]
     */
    public function getEditions(array $args = []): Collection
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->isNull('deletedAt'));

        if (isset($args['status'])) {
            $args['status'] = is_array($args['status']) ?: [$args['status']];
            $criteria->andWhere(Criteria::expr()->in('status', $args['status']));
        } else {
            $criteria->andWhere(Criteria::expr()->eq('status', 1));
        }

        if (isset($args['isHomolog'])) {
            $args['isHomolog'] = is_array($args['isHomolog']) ?: [$args['isHomolog']];
            $criteria->andWhere(Criteria::expr()->in('isHomolog', $args['isHomolog']));
        } else {
            $criteria->andWhere(Criteria::expr()->eq('isHomolog', 0));
        }

        if (isset($args['showOnlyPrevious']) && true === $args['showOnlyPrevious']) {
            $criteria->andWhere(Criteria::expr()->lt('dateEnd', new \DateTime()));
        }

        if (isset($args['showOnlyNext']) && true === $args['showOnlyNext']) {
            $criteria->andWhere(Criteria::expr()->gte('dateEnd', new \DateTime()));
        }

        return $this->editions->matching($criteria);
    }

    /**
     * @param Edition $edition
     *
     * @return $this
     */
    public function addEdition(Edition $edition): self
    {
        if (! $this->editions->contains($edition)) {
            $this->editions[] = $edition;
            $edition->setEdition($this);
        }

        return $this;
    }

    /**
     * @param Edition $edition
     *
     * @return $this
     */
    public function removeEdition(Edition $edition): self
    {
        if ($this->editions->contains($edition)) {
            $this->editions->removeElement($edition);
            // set the owning side to null (unless already changed)
            if ($edition->getEdition() === $this) {
                $edition->setEdition(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Division
     */
    public function getDivisions(): Collection
    {
        return $this->divisions;
    }

    /**
     * @param Division $division
     *
     * @return $this
     */
    public function addDivision(Division $division): self
    {
        if (! $this->divisions->contains($division)) {
            $this->divisions[] = $division;
        }

        return $this;
    }

    /**
     * @param Division $division
     *
     * @return $this
     */
    public function removeDivision(Division $division): self
    {
        if ($this->divisions->contains($division)) {
            $this->divisions->removeElement($division);
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumberWords(): ?int
    {
        return $this->numberWords;
    }

    /**
     * @param int|null $numberWords
     *
     * @return $this
     */
    public function setNumberWords(?int $numberWords): self
    {
        $this->numberWords = (int)$numberWords;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIssn(): ?string
    {
        return $this->issn;
    }

    /**
     * @param string|null $issn
     *
     * @return $this
     */
    public function setIssn(?string $issn): Event
    {
        $this->issn = $issn;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsShowPreviousEventsHome(): ?bool
    {
        return $this->isShowPreviousEventsHome;
    }

    /**
     * @param bool|null $isShowPreviousEventsHome
     *
     * @return Event
     */
    public function setIsShowPreviousEventsHome(?bool $isShowPreviousEventsHome): Event
    {
        $this->isShowPreviousEventsHome = $isShowPreviousEventsHome;
        return $this;
    }
}
