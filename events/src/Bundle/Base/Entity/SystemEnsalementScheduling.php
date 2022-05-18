<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * SystemEnsalementScheduling
 *
 * @UniqueEntity(
 *     fields={"edition", "coordinatorDebater1", "systemEnsalementSessions"},
 *     errorPath="systemEnsalementSlots",
 *     message="Este Slot já está vinculado aos Participantes"
 * )
 *
 * @UniqueEntity(
 *     fields={"edition", "coordinatorDebater2", "systemEnsalementSessions"},
 *     errorPath="systemEnsalementSlots",
 *     message="Este Slot já está vinculado aos Participantes"
 * )
 *
 * @UniqueEntity(
 *     fields={"edition", "systemEnsalementSlots"},
 *     errorPath="systemEnsalementSlots",
 *     message="Este Slot já está vinculado à outra Seção"
 * )
 *
 * @ORM\Table(name="system_ensalement_scheduling")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\SystemEnsalementSchedulingRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class SystemEnsalementScheduling
{
    const LANGUAGES = ['Português' => 1, 'English' => 2, 'Spanish' => 3];

    const SECTION_FORMATS = [
        'Aprimoramento' => 1,
        'Interativa' => 2,
        'Tradicional' => 3,
    ];

    const TYPE_COORDINATOR = 1;
    const TYPE_DEBATER = 2;
    const TYPE_COORDINATOR_DEBATER = 3;

    const COORDINATOR_DEBATER_TYPES = [
        'Coordenador' => self::TYPE_COORDINATOR,
        'Debatedor' => self::TYPE_DEBATER,
        'Coordenador/Debatedor' => self::TYPE_COORDINATOR_DEBATER,
    ];

    const PUBLIC_PATH = '/uploads/schedules/';
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
     * @var int|null
     *
     * @ORM\Column(name="content_type", type="smallint", nullable=true, options={"unsigned"=true,"comment"="1=activity, 2=panel"})
     */
    private $contentType;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="accept", type="boolean", nullable=true)
     */
    private $accept = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="time", type="time", nullable=true)
     */
    private $time;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="priority", type="boolean", nullable=true)
     */
    private $priority = '0';

    /**
     * @var int|null
     *
     * @ORM\Column(name="language", type="smallint", nullable=true)
     */
    private $language;

    /**
     * @var int|null
     *
     * @ORM\Column(name="format", type="smallint", nullable=true)
     */
    private $format;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var int|null
     *
     * @ORM\Column(name="coordinator_debater_1_type", type="smallint", nullable=true, options={"unsigned"=true,"comment"="1=coordinator, 2=debater, 3=coordinator/debater"})
     */
    private $coordinatorDebater1Type;

    /**
     * @var int|null
     *
     * @ORM\Column(name="coordinator_debater_2_type", type="smallint", nullable=true, options={"unsigned"=true,"comment"="1=coordinator, 2=debater, 3=coordinator/debater"})
     */
    private $coordinatorDebater2Type;

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
     * @var Division|null
     *
     * @ORM\ManyToOne(targetEntity="Division")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="division_id", referencedColumnName="id")
     * })
     */
    private $division;

    /**
     * @var Edition
     *
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="schedulings")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     * })
     */
    private $edition;

    /**
     * @var Panel
     *
     * @ORM\ManyToOne(targetEntity="Panel")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="panel_id", referencedColumnName="id")
     * })
     */
    private $panel;

    /**
     * @var Activity
     *
     * @ORM\ManyToOne(targetEntity="Activity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="activity_id", referencedColumnName="id")
     * })
     */
    private $activity;

    /**
     * @var SystemEnsalementSlots
     *
     * @ORM\ManyToOne(targetEntity="SystemEnsalementSlots")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="system_ensalement_slots_id", referencedColumnName="id")
     * })
     */
    private $systemEnsalementSlots;

    /**
     * @var SystemEnsalementSessions|null
     *
     * @ORM\ManyToOne(targetEntity="SystemEnsalementSessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="system_ensalement_sessions_id", referencedColumnName="id")
     * })
     */
    private $systemEnsalementSessions;

    /**
     * @var UserThemes
     *
     * @ORM\ManyToOne(targetEntity="UserThemes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_themes_id", referencedColumnName="id")
     * })
     */
    private $userThemes;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="systemEnsalementSchedulingUserRegisters")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_register_id", referencedColumnName="id")
     * })
     */
    private ?User $userRegister = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="systemEnsalementSchedulingCoordinatorDebaters1")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="coordinator_debater_1_id", referencedColumnName="id")
     * })
     */
    private ?User $coordinatorDebater1 = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="systemEnsalementSchedulingCoordinatorDebaters2")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="coordinator_debater_2_id", referencedColumnName="id")
     * })
     */
    private ?User $coordinatorDebater2 = null;

    /**
     * @ORM\OneToMany(targetEntity="SystemEnsalementSchedulingArticles", mappedBy="systemEnsalementSheduling", cascade={"persist", "remove"})
     * @var ArrayCollection
     */
    private $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
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
     * @return int|null
     */
    public function getContentType(): ?int
    {
        return $this->contentType;
    }

    /**
     * @param int|null $contentType
     *
     * @return $this
     */
    public function setContentType(?int $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getAccept(): ?bool
    {
        return $this->accept;
    }

    /**
     * @param bool|null $accept
     *
     * @return $this
     */
    public function setAccept(?bool $accept): self
    {
        $this->accept = $accept;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface|null $date
     *
     * @return $this
     */
    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    /**
     * @param \DateTimeInterface|null $time
     *
     * @return $this
     */
    public function setTime(?\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getPriority(): ?bool
    {
        return $this->priority;
    }

    /**
     * @param bool|null $priority
     *
     * @return $this
     */
    public function setPriority(?bool $priority): self
    {
        $this->priority = $priority;

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
     * @return int|null
     */
    public function getFormat(): ?int
    {
        return $this->format;
    }

    /**
     * @param int|null $format
     *
     * @return $this
     */
    public function setFormat(?int $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
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
     * @return int|null
     */
    public function getCoordinatorDebater1Type(): ?int
    {
        return $this->coordinatorDebater1Type;
    }

    /**
     * @param int|null $coordinatorDebater1Type
     *
     * @return $this
     */
    public function setCoordinatorDebater1Type(?int $coordinatorDebater1Type): self
    {
        $this->coordinatorDebater1Type = $coordinatorDebater1Type;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCoordinatorDebater2Type(): ?int
    {
        return $this->coordinatorDebater2Type;
    }

    /**
     * @param int|null $coordinatorDebater2Type
     *
     * @return $this
     */
    public function setCoordinatorDebater2Type(?int $coordinatorDebater2Type): self
    {
        $this->coordinatorDebater2Type = $coordinatorDebater2Type;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface|null $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface|null $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTimeInterface|null $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

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
     * @return Panel|null
     */
    public function getPanel(): ?Panel
    {
        return $this->panel;
    }

    /**
     * @param Panel|null $panel
     *
     * @return $this
     */
    public function setPanel(?Panel $panel): self
    {
        $this->panel = $panel;

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
     * @return SystemEnsalementSlots|null
     */
    public function getSystemEnsalementSlots(): ?SystemEnsalementSlots
    {
        return $this->systemEnsalementSlots;
    }

    /**
     * @param SystemEnsalementSlots|null $systemEnsalementSlots
     *
     * @return $this
     */
    public function setSystemEnsalementSlots(?SystemEnsalementSlots $systemEnsalementSlots): self
    {
        $this->systemEnsalementSlots = $systemEnsalementSlots;

        return $this;
    }

    /**
     * @return SystemEnsalementSessions|null
     */
    public function getSystemEnsalementSessions(): ?SystemEnsalementSessions
    {
        return $this->systemEnsalementSessions;
    }

    /**
     * @param SystemEnsalementSessions|null $systemEnsalementSessions
     *
     * @return SystemEnsalementScheduling
     */
    public function setSystemEnsalementSessions(?SystemEnsalementSessions $systemEnsalementSessions): SystemEnsalementScheduling
    {
        $this->systemEnsalementSessions = $systemEnsalementSessions;
        return $this;
    }

    /**
     * @return UserThemes|null
     */
    public function getUserThemes(): ?UserThemes
    {
        return $this->userThemes;
    }

    /**
     * @param UserThemes|null $userThemes
     *
     * @return $this
     */
    public function setUserThemes(?UserThemes $userThemes): self
    {
        $this->userThemes = $userThemes;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUserRegister(): ?User
    {
        return $this->userRegister;
    }

    /**
     * @param User|null $userRegister
     *
     * @return $this
     */
    public function setUserRegister(?User $userRegister): self
    {
        $this->userRegister = $userRegister;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getCoordinatorDebater1(): ?User
    {
        return $this->coordinatorDebater1;
    }

    /**
     * @param User|null $coordinatorDebater1
     *
     * @return $this
     */
    public function setCoordinatorDebater1(?User $coordinatorDebater1): self
    {
        $this->coordinatorDebater1 = $coordinatorDebater1;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getCoordinatorDebater2(): ?User
    {
        return $this->coordinatorDebater2;
    }

    /**
     * @param User|null $coordinatorDebater2
     *
     * @return $this
     */
    public function setCoordinatorDebater2(?User $coordinatorDebater2): self
    {
        $this->coordinatorDebater2 = $coordinatorDebater2;

        return $this;
    }

    /**
     * @return Collection|SystemEnsalementSchedulingArticles[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    /**
     * @param SystemEnsalementSchedulingArticles $article
     *
     * @return $this
     */
    public function addArticle(SystemEnsalementSchedulingArticles $article): self
    {
        if (! $this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setSystemEnsalementSheduling($this);
        }

        return $this;
    }

    /**
     * @param SystemEnsalementSchedulingArticles $article
     *
     * @return $this
     */
    public function removeArticle(SystemEnsalementSchedulingArticles $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getSystemEnsalementSheduling() === $this) {
                $article->setSystemEnsalementSheduling(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->title;
    }
}
