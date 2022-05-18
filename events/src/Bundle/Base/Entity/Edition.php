<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Edition
 *
 * @ORM\Table(name="edition")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\EditionRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Edition
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
     * @var int|null
     *
     * @ORM\Column(name="position", type="integer", nullable=false, options={"default"="1"})
     */
    private $position = '1';

    /**
     * @var string|null
     *
     * @ORM\Column(name="color", type="string", length=50, nullable=false)
     */
    private $color = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="place", type="string", nullable=false)
     */
    private $place = '';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_start", type="date", nullable=true)
     */
    private $dateStart;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_end", type="date", nullable=true)
     */
    private $dateEnd;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="signup_deadline", type="date", nullable=true)
     */
    private $signupDeadline;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name_portuguese", type="string", nullable=false)
     */
    private $namePortuguese = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="longname_portuguese", type="string", nullable=true)
     */
    private $longnamePortuguese;

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
     * @ORM\Column(name="longname_english", type="string", nullable=true)
     */
    private $longnameEnglish;

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
     * @ORM\Column(name="longname_spanish", type="string", nullable=true)
     */
    private $longnameSpanish;

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
     * @var bool|null
     *
     * @ORM\Column(name="is_show_home", type="boolean", nullable=true, options={"default"="1"})
     */
    private $isShowHome = true;

    /**
     * @var int|null
     *
     * @ORM\Column(name="home_position", type="integer", nullable=true)
     */
    private $homePosition = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="workload", type="string", nullable=true)
     */
    private $workload;

    /**
     * @var string|null
     *
     * @ORM\Column(name="voluntary_workload", type="string", nullable=true)
     */
    private $voluntaryWorkload;

    /**
     * @var string|null
     *
     * @ORM\Column(name="certificate_layout_path", type="string", nullable=true)
     */
    private $certificateLayoutPath;

    /**
     * @var int|null
     *
     * @ORM\Column(name="certificate_qrcode_size", type="integer", nullable=true)
     */
    private $certificateQrcodeSize = 200;

    /**
     * @var int|null
     *
     * @ORM\Column(name="certificate_qrcode_position_right", type="integer", nullable=true)
     */
    private $certificateQrcodePositionRight = 100;

    /**
     * @var int|null
     *
     * @ORM\Column(name="certificate_qrcode_position_bottom", type="integer", nullable=true)
     */
    private $certificateQrcodePositionBottom = 350;

    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="editions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     * })
     */
    private ?Event $event = null;

    /**
     * @var Collection|Subsection[]
     *
     * @ORM\OneToMany(targetEntity="Subsection", mappedBy="edition")
     * @ORM\OrderBy({"position"="ASC"})
     */
    private $subsections;

    /**
     * @var Collection|Speaker[]
     *
     * @ORM\OneToMany(targetEntity="Speaker", mappedBy="edition")
     * @ORM\OrderBy({"position"="ASC"})
     */
    private $speakers;

    /**
     * @var Collection|EditionFile[]
     *
     * @ORM\OneToMany(targetEntity="EditionFile", mappedBy="edition")
     */
    private $editionFiles;

    /**
     * @var Collection|UserArticles[]
     *
     * @ORM\OneToMany(targetEntity="UserArticles", mappedBy="editionId")
     * @ORM\OrderBy({"id"="ASC"})
     */
    private $userArticles;

    /**
     * @var Collection|EditionPaymentMode[]
     *
     * @ORM\OneToMany(targetEntity="EditionPaymentMode", mappedBy="edition")
     */
    private $editionPaymentModes;

    /**
     * @var Collection|EditionSignup[]
     *
     * @ORM\OneToMany(targetEntity="EditionSignup", mappedBy="edition")
     */
    private $editionSignups;

    /**
     * @var Collection|EditionDiscount[]
     *
     * @ORM\OneToMany(targetEntity="EditionDiscount", mappedBy="edition")
     */
    private $discounts;

    /**
     * @var Collection|SystemEvaluationConfig[]
     *
     * @ORM\OneToMany(targetEntity="SystemEvaluationConfig", mappedBy="edition")
     */
    private $systemEvaluationConfigs;

    /**
     * @var Collection|SystemEnsalementScheduling[]
     *
     * @ORM\OneToMany(targetEntity="SystemEnsalementScheduling", mappedBy="edition")
     * @ORM\OrderBy({"date"="ASC", "time"="ASC"})
     */
    private $schedulings;

    /**
     * @var Collection|Activity[]
     *
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="edition")
     */
    private $activities;

    /**
     * @var Collection|Certificate[]
     * @ORM\OneToMany(targetEntity="Certificate", mappedBy="edition")
     */
    private $certificates;

    /**
     * @var Collection|DivisionCoordinator[]
     * @ORM\OneToMany(targetEntity="DivisionCoordinator", mappedBy="edition")
     */
    private $divisionCoordinators;

    /**
     * @var Collection|Panel[]
     * @ORM\OneToMany(targetEntity="Panel", mappedBy="editionId")
     */
    private $panels;

    /**
     * @var Collection|SystemEnsalementRooms[]
     * @ORM\OneToMany(targetEntity="SystemEnsalementRooms", mappedBy="edition")
     */
    private $systemEnsalementRooms;

    /**
     * @var Collection|SystemEnsalementSessions[]
     * @ORM\OneToMany(targetEntity="SystemEnsalementSessions", mappedBy="edition")
     */
    private $systemEnsalementSessions;

    /**
     * @var Collection|SystemEnsalementSlots[]
     * @ORM\OneToMany(targetEntity="SystemEnsalementSlots", mappedBy="edition")
     */
    private $systemEnsalementSlots;

    /**
     * @var Collection|SystemEvaluationAverages[]
     * @ORM\OneToMany(targetEntity="SystemEvaluationAverages", mappedBy="edition")
     */
    private $systemEvaluationAverages;

    /**
     * @var Collection|Theme[]
     * @ORM\OneToMany(targetEntity="Theme", mappedBy="editionId")
     */
    private $themes;

    /**
     * @var Collection|Thesis[]
     * @ORM\OneToMany(targetEntity="Thesis", mappedBy="edition")
     */
    private $theses;

    /**
     * @var Collection|UserCommittee[]
     * @ORM\OneToMany(targetEntity="UserCommittee", mappedBy="edition")
     */
    private $userCommittees;

    /**
     * Edition constructor.
     */
    public function __construct()
    {
        $this->subsections = new ArrayCollection();
        $this->speakers = new ArrayCollection();
        $this->editionFiles = new ArrayCollection();
        $this->userArticles = new ArrayCollection();
        $this->editionPaymentModes = new ArrayCollection();
        $this->editionSignups = new ArrayCollection();
        $this->discounts = new ArrayCollection();
        $this->systemEvaluationConfigs = new ArrayCollection();
        $this->schedulings = new ArrayCollection();
        $this->activities = new ArrayCollection();

        $this->certificates = new ArrayCollection();
        $this->divisionCoordinators = new ArrayCollection();
        $this->panels = new ArrayCollection();
        $this->systemEnsalementRooms = new ArrayCollection();
        $this->systemEnsalementSessions = new ArrayCollection();
        $this->systemEnsalementSlots = new ArrayCollection();
        $this->systemEvaluationAverages = new ArrayCollection();
        $this->themes = new ArrayCollection();
        $this->theses = new ArrayCollection();
        $this->userCommittees = new ArrayCollection();
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
     * @param \DateTime|null $updatedAt
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
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string|null $color
     *
     * @return $this
     */
    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlace(): ?string
    {
        return $this->place;
    }

    /**
     * @param string|null $place
     *
     * @return $this
     */
    public function setPlace(?string $place): self
    {
        $this->place = $place;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateStart(): ?\DateTime
    {
        return $this->dateStart;
    }

    /**
     * @param \DateTime|null $dateStart
     *
     * @return $this
     */
    public function setDateStart(?\DateTime $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateEnd(): ?\DateTime
    {
        return $this->dateEnd;
    }

    /**
     * @param \DateTime|null $dateEnd
     *
     * @return $this
     */
    public function setDateEnd(?\DateTime $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getSignupDeadline(): ?\DateTime
    {
        return $this->signupDeadline;
    }

    /**
     * @param \DateTime|null $signupDeadline
     *
     * @return $this
     */
    public function setSignupDeadline(?\DateTime $signupDeadline): self
    {
        $this->signupDeadline = $signupDeadline;
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
     * @param string|null $namePortuguese
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
    public function getLongnamePortuguese(): ?string
    {
        return $this->longnamePortuguese;
    }

    /**
     * @param string|null $longnamePortuguese
     *
     * @return $this
     */
    public function setLongnamePortuguese(?string $longnamePortuguese): self
    {
        $this->longnamePortuguese = $longnamePortuguese;
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
    public function getNameEnglish(): ?string
    {
        return $this->nameEnglish;
    }

    /**
     * @param string|null $nameEnglish
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
    public function getLongnameEnglish(): ?string
    {
        return $this->longnameEnglish;
    }

    /**
     * @param string|null $longnameEnglish
     *
     * @return $this
     */
    public function setLongnameEnglish(?string $longnameEnglish): self
    {
        $this->longnameEnglish = $longnameEnglish;
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
    public function getNameSpanish(): ?string
    {
        return $this->nameSpanish;
    }

    /**
     * @param string|null $nameSpanish
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
    public function getLongnameSpanish(): ?string
    {
        return $this->longnameSpanish;
    }

    /**
     * @param string|null $longnameSpanish
     *
     * @return $this
     */
    public function setLongnameSpanish(?string $longnameSpanish): self
    {
        $this->longnameSpanish = $longnameSpanish;
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
     * @return string|null
     */
    public function getName(): ?string
    {
        $i18n = [
            'pt_br' => $this->getNamePortuguese(),
            'en' => $this->getNameEnglish(),
            'es' => $this->getNameSpanish(),
        ];

        return $i18n[\Locale::getDefault()];
    }

    /**
     * @return string|null
     */
    public function getLongname(): ?string
    {
        $i18n = [
            'pt_br' => $this->getLongnamePortuguese(),
            'en' => $this->getLongnameEnglish(),
            'es' => $this->getLongnameSpanish(),
        ];

        return $i18n[\Locale::getDefault()];
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
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
     * @param int|null $status
     *
     * @return $this
     */
    public function setStatus(?int $status): self
    {
        $this->status = $status;

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
     * @param bool|null $isHomolog
     *
     * @return $this
     */
    public function setIsHomolog(?bool $isHomolog): self
    {
        $this->isHomolog = $isHomolog;

        return $this;
    }

    /**
     * @return Event|null
     */
    public function getEvent(): ?Event
    {
        return $this->event;
    }

    /**
     * @param Event|null $event
     *
     * @return $this
     */
    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsShowHome(): ?bool
    {
        return $this->isShowHome;
    }

    /**
     * @param bool|null $isShowHome
     *
     * @return $this
     */
    public function setIsShowHome(?bool $isShowHome): self
    {
        $this->isShowHome = $isShowHome;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getHomePosition(): ?int
    {
        return $this->homePosition;
    }

    /**
     * @param int|null $homePosition
     *
     * @return $this
     */
    public function setHomePosition(?int $homePosition): self
    {
        $this->homePosition = $homePosition;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getWorkload(): ?string
    {
        return $this->workload;
    }

    /**
     * @param string|null $workload
     *
     * @return $this
     */
    public function setWorkload(?string $workload): self
    {
        $this->workload = $workload;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVoluntaryWorkload(): ?string
    {
        return $this->voluntaryWorkload;
    }

    /**
     * @param string|null $voluntaryWorkload
     *
     * @return $this
     */
    public function setVoluntaryWorkload(?string $voluntaryWorkload): self
    {
        $this->voluntaryWorkload = $voluntaryWorkload;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCertificateLayoutPath(): ?string
    {
        return $this->certificateLayoutPath;
    }

    /**
     * @param string|null $certificateLayoutPath
     *
     * @return $this
     */
    public function setCertificateLayoutPath(?string $certificateLayoutPath): self
    {
        $this->certificateLayoutPath = $certificateLayoutPath;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCertificateQrcodeSize(): ?int
    {
        return $this->certificateQrcodeSize;
    }

    /**
     * @param int|null $certificateQrcodeSize
     *
     * @return $this
     */
    public function setCertificateQrcodeSize(?int $certificateQrcodeSize): self
    {
        $this->certificateQrcodeSize = $certificateQrcodeSize;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCertificateQrcodePositionRight(): ?int
    {
        return $this->certificateQrcodePositionRight;
    }

    /**
     * @param int|null $certificateQrcodePositionRight
     *
     * @return $this
     */
    public function setCertificateQrcodePositionRight(?int $certificateQrcodePositionRight): self
    {
        $this->certificateQrcodePositionRight = $certificateQrcodePositionRight;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCertificateQrcodePositionBottom(): ?int
    {
        return $this->certificateQrcodePositionBottom;
    }

    /**
     * @param int|null $certificateQrcodePositionBottom
     *
     * @return $this
     */
    public function setCertificateQrcodePositionBottom(?int $certificateQrcodePositionBottom): self
    {
        $this->certificateQrcodePositionBottom = $certificateQrcodePositionBottom;
        return $this;
    }

    /**
     * @param array $args
     *
     * @return Collection|Subsection[]
     */
    public function getSubsections(array $args = []): Collection
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

        if (isset($args['isHighlight'])) {
            $args['isHighlight'] = is_array($args['isHighlight']) ?: [$args['isHighlight']];
            $criteria->andWhere(Criteria::expr()->in('isHighlight', $args['isHighlight']));
        }

        return $this->subsections->matching($criteria);
    }

    /**
     * @param Subsection $subsection
     *
     * @return $this
     */
    public function addSubsection(Subsection $subsection): self
    {
        if (! $this->subsections->contains($subsection)) {
            $this->subsections[] = $subsection;
            $subsection->setEdition($this);
        }

        return $this;
    }

    /**
     * @param Subsection $subsection
     *
     * @return $this
     */
    public function removeSubsection(Subsection $subsection): self
    {
        if ($this->subsections->removeElement($subsection)) {
            // set the owning side to null (unless already changed)
            if ($subsection->getEdition() === $this) {
                $subsection->setEdition(null);
            }
        }

        return $this;
    }

    /**
     * @param array $args
     *
     * @return Collection|Speaker[]
     */
    public function getSpeakers(array $args = []): Collection
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

        return $this->speakers->matching($criteria);
    }

    /**
     * @param Speaker $speaker
     *
     * @return $this
     */
    public function addSpeaker(Speaker $speaker): self
    {
        if (! $this->speakers->contains($speaker)) {
            $this->speakers[] = $speaker;
            $speaker->setEdition($this);
        }

        return $this;
    }

    /**
     * @param Speaker $speaker
     *
     * @return $this
     */
    public function removeSpeaker(Speaker $speaker): self
    {
        if ($this->speakers->removeElement($speaker)) {
            // set the owning side to null (unless already changed)
            if ($speaker->getEdition() === $this) {
                $speaker->setEdition(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EditionFile[]
     */
    public function getEditionFiles(): Collection
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->isNull('deletedAt'));
        return $this->editionFiles->matching($criteria);
    }

    /**
     * @param EditionFile $editionFile
     *
     * @return $this
     */
    public function addEditionFile(EditionFile $editionFile): self
    {
        if (! $this->editionFiles->contains($editionFile)) {
            $this->editionFiles[] = $editionFile;
            $editionFile->setEdition($this);
        }

        return $this;
    }

    /**
     * @param EditionFile $editionFile
     *
     * @return $this
     */
    public function removeEditionFile(EditionFile $editionFile): self
    {
        if ($this->editionFiles->removeElement($editionFile)) {
            // set the owning side to null (unless already changed)
            if ($editionFile->getEdition() === $this) {
                $editionFile->setEdition(null);
            }
        }

        return $this;
    }

    /**
     * @param array $args
     *
     * @return Collection|UserArticles[]
     */
    public function getUserArticles(array $args = []): Collection
    {
        $criteria = Criteria::create();

        $criteria->andWhere(Criteria::expr()->isNull('deletedAt'));

        if (isset($args['status'])) {
            $args['status'] = is_array($args['status']) ?: [$args['status']];
            $criteria->andWhere(Criteria::expr()->in('status', $args['status']));
        }

        if (isset($args['isPublished'])) {
//            $args['isPublished'] = is_array($args['isPublished']) ?: [$args['isPublished']];
//            $criteria->andWhere(Criteria::expr()->in('isPublished', $args['isPublished']));
            $criteria->andWhere(Criteria::expr()->eq('isPublished', $args['isPublished']));
        }

        if (isset($args['firstResult'])) {
            $criteria->setFirstResult($args['firstResult']);
        }

        if (isset($args['maxResults'])) {
            $criteria->setMaxResults($args['maxResults']);
        }

        return $this->userArticles->matching($criteria);
    }

    /**
     * @param UserArticles $userArticle
     *
     * @return $this
     */
    public function addUserArticle(UserArticles $userArticle): self
    {
        if (! $this->userArticles->contains($userArticle)) {
            $this->userArticles[] = $userArticle;
            $userArticle->setEdition($this);
        }

        return $this;
    }

    /**
     * @param UserArticles $userArticle
     *
     * @return $this
     */
    public function removeUserArticle(UserArticles $userArticle): self
    {
        if ($this->userArticles->removeElement($userArticle)) {
            // set the owning side to null (unless already changed)
            if ($userArticle->getEdition() === $this) {
                $userArticle->setEdition(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EditionPaymentMode[]
     */
    public function getEditionPaymentModes(): Collection
    {
        return $this->editionPaymentModes;
    }

    /**
     * @param EditionPaymentMode $editionPaymentMode
     *
     * @return $this
     */
    public function addEditionPaymentMode(EditionPaymentMode $editionPaymentMode): self
    {
        if (! $this->editionPaymentModes->contains($editionPaymentMode)) {
            $this->editionPaymentModes[] = $editionPaymentMode;
            $editionPaymentMode->setEdition($this);
        }

        return $this;
    }

    /**
     * @param EditionPaymentMode $editionPaymentMode
     *
     * @return $this
     */
    public function removeEditionPaymentMode(EditionPaymentMode $editionPaymentMode): self
    {
        if ($this->editionPaymentModes->removeElement($editionPaymentMode)) {
            // set the owning side to null (unless already changed)
            if ($editionPaymentMode->getEdition() === $this) {
                $editionPaymentMode->setEdition(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EditionSignup[]
     */
    public function getEditionSignups(): Collection
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->isNull('deletedAt'));
        return $this->editionSignups->matching($criteria);
    }

    /**
     * @param EditionSignup $editionSignup
     *
     * @return $this
     */
    public function addEditionSignup(EditionSignup $editionSignup): self
    {
        if (! $this->editionSignups->contains($editionSignup)) {
            $this->editionSignups[] = $editionSignup;
            $editionSignup->setEdition($this);
        }

        return $this;
    }

    /**
     * @param EditionSignup $editionSignup
     *
     * @return $this
     */
    public function removeEditionSignup(EditionSignup $editionSignup): self
    {
        if ($this->editionSignups->removeElement($editionSignup)) {
            // set the owning side to null (unless already changed)
            if ($editionSignup->getEdition() === $this) {
                $editionSignup->setEdition(null);
            }
        }

        return $this;
    }

    /**
     * @param array $args
     *
     * @return Collection|EditionDiscount[]
     */
    public function getDiscounts(array $args = []): Collection
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->isNull('deletedAt'));

        if (isset($args['userIdentifier'])) {
            $criteria->andWhere(Criteria::expr()->eq('userIdentifier', $args['userIdentifier']));
        }

        return $this->discounts->matching($criteria);
    }

    /**
     * @param EditionDiscount $discount
     *
     * @return $this
     */
    public function addDiscount(EditionDiscount $discount): self
    {
        if (! $this->discounts->contains($discount)) {
            $this->discounts[] = $discount;
            $discount->setEdition($this);
        }

        return $this;
    }

    /**
     * @param EditionDiscount $discount
     *
     * @return $this
     */
    public function removeDiscount(EditionDiscount $discount): self
    {
        if ($this->discounts->removeElement($discount)) {
            // set the owning side to null (unless already changed)
            if ($discount->getEdition() === $this) {
                $discount->setEdition(null);
            }
        }

        return $this;
    }

    /**
     * @param array $args
     *
     * @return Collection|SystemEvaluationConfig[]
     */
    public function getSystemEvaluationConfigs(array $args = []): Collection
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->isNull('deletedAt'));

        $criteria->orderBy(['createdAt' => 'DESC']);

        $criteria->setMaxResults(1);

        return $this->systemEvaluationConfigs->matching($criteria);
    }

    /**
     * @param SystemEvaluationConfig $systemEvaluationConfig
     *
     * @return $this
     */
    public function addSystemEvaluationConfig(SystemEvaluationConfig $systemEvaluationConfig): self
    {
        if (! $this->systemEvaluationConfigs->contains($systemEvaluationConfig)) {
            $this->systemEvaluationConfigs[] = $systemEvaluationConfig;
            $systemEvaluationConfig->setEdition($this);
        }

        return $this;
    }

    /**
     * @param SystemEvaluationConfig $systemEvaluationConfig
     *
     * @return $this
     */
    public function removeSystemEvaluationConfig(SystemEvaluationConfig $systemEvaluationConfig): self
    {
        if ($this->systemEvaluationConfigs->removeElement($systemEvaluationConfig)) {
            // set the owning side to null (unless already changed)
            if ($systemEvaluationConfig->getEdition() === $this) {
                $systemEvaluationConfig->setEdition(null);
            }
        }

        return $this;
    }

    /**
     * @param array $args
     *
     * @return Collection|SystemEnsalementScheduling[]
     */
    public function getSchedulings(array $args = []): Collection
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->isNull('deletedAt'));

        return $this->schedulings->matching($criteria);
    }

    /**
     * @param SystemEnsalementScheduling $scheduling
     *
     * @return $this
     */
    public function addScheduling(SystemEnsalementScheduling $scheduling): self
    {
        if (! $this->schedulings->contains($scheduling)) {
            $this->schedulings[] = $scheduling;
            $scheduling->setEdition($this);
        }

        return $this;
    }

    /**
     * @param SystemEnsalementScheduling $scheduling
     *
     * @return $this
     */
    public function removeScheduling(SystemEnsalementScheduling $scheduling): self
    {
        if ($this->schedulings->removeElement($scheduling)) {
            // set the owning side to null (unless already changed)
            if ($scheduling->getEdition() === $this) {
                $scheduling->setEdition(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    /**
     * @param Activity $activity
     *
     * @return $this
     */
    public function addActivity(Activity $activity): self
    {
        if (! $this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setEdition($this);
        }

        return $this;
    }

    /**
     * @param Activity $activity
     *
     * @return $this
     */
    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getEdition() === $this) {
                $activity->setEdition(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function __toString(): ?string
    {
        return $this->getName();
    }

    public function getApprovedArticlesFile(): ?string
    {
        if (! $this->getId()) {
            return null;
        }

        return $this->getId() . '/approved_' . md5($this->getId()) . '.zip';
    }

    public function getCertificates()
    {
        return $this->certificates;
    }

    public function getDivisionCoordinators()
    {
        return $this->divisionCoordinators;
    }

    public function getPanels()
    {
        return $this->panels;
    }

    public function getSystemEnsalementRooms()
    {
        return $this->systemEnsalementRooms;
    }

    public function getSystemEnsalementSessions()
    {
        return $this->systemEnsalementSessions;
    }

    public function getSystemEnsalementSlots()
    {
        return $this->systemEnsalementSlots;
    }

    public function getSystemEvaluationAverages()
    {
        return $this->systemEvaluationAverages;
    }

    public function getThemes()
    {
        return $this->themes;
    }

    public function getTheses()
    {
        return $this->theses;
    }

    public function getUserCommittees()
    {
        return $this->userCommittees;
    }
}
