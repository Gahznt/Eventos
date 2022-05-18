<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * UserArticles
 *
 * @ORM\Table(name="user_articles")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\UserArticlesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class UserArticles
{
    const ARTICLE_EVALUATION_STATUS_WAITING = 1;

    const ARTICLE_EVALUATION_STATUS_APPROVED = 2;

    const ARTICLE_EVALUATION_STATUS_REPROVED = 3;

    const ARTICLE_EVALUATION_STATUS_CANCELED = 4;

    const ARTICLE_RESULTING_FROM_RESEARCH_PROJECT = 1;

    const ARTICLE_RESULTING_FROM_ONGOING_DISSERTATION = 2;

    const ARTICLE_RESULTING_FROM_CONCLUDED_DISSERTATION = 3;

    const ARTICLE_RESULTING_FROM_ONGOING_THESIS = 4;

    const ARTICLE_RESULTING_FROM_COMPLETED_THESIS = 5;

    const ARTICLE_RESULTING_FROM_OTHER = 6;

    const ARTICLE_EVALUATION_STATUS = [
        'ARTICLE_EVALUATION_STATUS_WAITING' => self::ARTICLE_EVALUATION_STATUS_WAITING,
        'ARTICLE_EVALUATION_STATUS_APPROVED' => self::ARTICLE_EVALUATION_STATUS_APPROVED,
        'ARTICLE_EVALUATION_STATUS_REPROVED' => self::ARTICLE_EVALUATION_STATUS_REPROVED,
        'ARTICLE_EVALUATION_STATUS_CANCELED' => self::ARTICLE_EVALUATION_STATUS_CANCELED,
    ];

    const LANGUAGES = ['Português' => 1, 'Inglês' => 2, 'Espanhol' => 3];

    const FRAMES = [
        'Theoretical-Empirical Article' => 1,
        'Theoretical Essay' => 2,
        'Technological Article' => 4,
        'Cases for Teaching' => 5,
    ];

    const FRAMES_DIVISIONAL = [
        'Theoretical-Empirical Article' => 1,
        'Theoretical Essay' => 2,
        'Technological Article' => 4,
    ];

    const status = ['Good' => 1, 'Very good' => '2', 'Regular' => 3];

    /**
     * Constante de decorrência
     */
    const ARTICLE_RESULTING_FROM = [
        'Research project' => self::ARTICLE_RESULTING_FROM_RESEARCH_PROJECT,
        'Master’s thesis in progress' => self::ARTICLE_RESULTING_FROM_ONGOING_DISSERTATION,
        'Completed Master’s thesis' => self::ARTICLE_RESULTING_FROM_CONCLUDED_DISSERTATION,
        'PhD dissertation in progress' => self::ARTICLE_RESULTING_FROM_ONGOING_THESIS,
        'Completed PhD dissertation' => self::ARTICLE_RESULTING_FROM_COMPLETED_THESIS,
        'Other' => self::ARTICLE_RESULTING_FROM_OTHER,
    ];

    const PUBLIC_PATH = '/uploads/articles/';
    const UPLOAD_PATH = '#KERNEL#/var/storage' . self::PUBLIC_PATH;
    const STATS_PATH = '#KERNEL#/var/storage/article_stats/';

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userArticles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $userId;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_deleted_id", referencedColumnName="id")
     * })
     */
    private $userDeletedId;

    /**
     * @var Edition
     *
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="userArticles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     * })
     */
    private $editionId;

    /**
     * @ORM\ManyToOne(targetEntity="Division", inversedBy="userArticles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="division_id", referencedColumnName="id")
     * })
     */
    private ?Division $divisionId = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="last_id", type="integer", nullable=true)
     */
    private $lastId;

    /**
     * @ORM\ManyToOne(targetEntity="UserThemes", inversedBy="userArticles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_themes_id", referencedColumnName="id")
     * })
     */
    private ?UserThemes $userThemes = null;

    /**
     * @var UserThemes|null
     *
     * @ORM\ManyToOne(targetEntity="UserThemes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="original_user_themes_id", referencedColumnName="id")
     * })
     */
    private $originalUserThemes;

    /**
     * @ORM\ManyToOne(targetEntity="Method", inversedBy="userArticles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="method_id", referencedColumnName="id")
     * })
     */
    private ?Method $methodId = null;

    /**
     * @ORM\ManyToOne(targetEntity="Theory", inversedBy="userArticles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="theory_id", referencedColumnName="id")
     * })
     */
    private ?Theory $theoryId = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", nullable=false)
     */
    private $title;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="portuguese", type="boolean", nullable=true)
     */
    private $portuguese;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="english", type="boolean", nullable=true)
     */
    private $english;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="spanish", type="boolean", nullable=true)
     */
    private $spanish;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="job_complete", type="boolean", nullable=true)
     */
    private $jobComplete;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="resume_flag", type="boolean", nullable=true)
     */
    private $resumeFlag;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip", type="string", nullable=true)
     */
    private $ip;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="rac_bar", type="boolean", nullable=true)
     */
    private $racBar;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="never_publish", type="boolean", nullable=true)
     */
    private $neverPublish;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="confirm_files_correct", type="boolean", nullable=true)
     */
    private $confirmFilesCorrect;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="premium", type="boolean", nullable=true)
     */
    private $premium;

    /**
     * @var string|null
     *
     * @ORM\Column(name="resume", type="text", nullable=true)
     */
    private $resume;

    /**
     * @var string|null
     *
     * @ORM\Column(name="acknowledgment", type="text", nullable=true)
     */
    private $acknowledgment;

    /**
     * @var int|null
     *
     * @ORM\Column(name="language", type="integer", nullable=true)
     */
    private $language;

    /**
     * @var int|null
     *
     * @ORM\Column(name="frame", type="integer", nullable=true)
     */
    private $frame;

    /**
     * @ORM\OneToMany(targetEntity="UserArticlesAuthors", mappedBy="userArticles", cascade={"persist"})
     * @ORM\OrderBy({"order"="ASC"})
     */
    private $userArticlesAuthors;

    /**
     * @ORM\OneToMany(targetEntity="UserArticlesKeywords", mappedBy="userArticlesId")
     */
    private $userArticlesKeywords;

    /**
     * @ORM\OneToMany(targetEntity="SystemEvaluationIndications", mappedBy="userArticles", cascade={"persist"})
     */
    private $indications;

    /**
     * @ORM\OneToMany(targetEntity="UserArticlesFiles", mappedBy="userArticles", cascade={"persist"})
     */
    private $userArticlesFiles;

    /**
     * @ORM\OneToMany(targetEntity="SystemEvaluation", mappedBy="userArticles", cascade={"persist"})
     */
    private $evaluations;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_published", type="boolean", nullable=true)
     */
    private $isPublished = false;

    /**
     * @var array|null
     *
     * @ORM\Column(name="keywords", type="json", nullable=true)
     */
    private $keywords;

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
     * @ORM\ManyToOne(targetEntity=Modality::class)
     */
    private $modality;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $isRelatedThemeEnanpad;

    /**
     * UserArticles constructor.
     */
    public function __construct()
    {
        $this->userArticlesAuthors = new ArrayCollection();
        $this->userArticlesKeywords = new ArrayCollection();
        $this->userArticlesFiles = new ArrayCollection();
        $this->indications = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
    }

    /**
     * @return Collection|SystemEvaluation[]
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    /**
     * @return Collection|SystemEvaluationIndications[]
     */
    public function getIndications(): Collection
    {
        return $this->indications;
    }

    /**
     * @return Collection|UserArticlesAuthors[]
     */
    public function getUserArticlesAuthors(): Collection
    {
        return $this->userArticlesAuthors;
    }

    /**
     * @param UserArticlesAuthors $userArticlesAuthors
     *
     * @return $this
     */
    public function addUserArticlesAuthor(UserArticlesAuthors $userArticlesAuthors): self
    {
        $this->userArticlesAuthors[] = $userArticlesAuthors;
        $userArticlesAuthors->setUserArticles($this);

        return $this;
    }

    /**
     * @param UserArticlesAuthors $userArticlesAuthors
     */
    public function removeUserArticlesAuthor(UserArticlesAuthors $userArticlesAuthors)
    {
        $this->userArticlesAuthors->removeElement($userArticlesAuthors);
    }

    /**
     * @return Collection|UserArticlesFiles[]
     */
    public function getUserArticlesFiles(): Collection
    {
        return $this->userArticlesFiles;
    }

    /**
     * @param UserArticlesFiles $userArticlesFiles
     *
     * @return $this
     */
    public function addUserArticlesFile(UserArticlesFiles $userArticlesFiles): self
    {
        $this->userArticlesFiles[] = $userArticlesFiles;
        $userArticlesFiles->setUserArticles($this);

        return $this;
    }

    /**
     * @param UserArticlesFiles $userArticlesFiles
     */
    public function removeUserArticlesFile(UserArticlesFiles $userArticlesFiles)
    {
        $this->userArticlesFiles->removeElement($userArticlesFiles);
    }

    /**
     * @return Collection|UserArticlesKeywords[]
     */
    public function getUserArticlesKeywords(): Collection
    {
        return $this->userArticlesKeywords;
    }

    /**
     * @param UserArticlesKeywords $userArticlesKeywords
     *
     * @return $this
     */
    public function addUserArticlesKeyword(UserArticlesKeywords $userArticlesKeywords): self
    {
        $this->userArticlesKeywords[] = $userArticlesKeywords;
        $userArticlesKeywords->setUserArticlesId($this);

        return $this;
    }

    /**
     * @param UserArticlesKeywords $userArticlesKeywords
     */
    public function removeUserArticlesKeyword(UserArticlesKeywords $userArticlesKeywords)
    {
        $this->userArticlesKeywords->removeElement($userArticlesKeywords);
    }

    /**
     * @return bool|null
     */
    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    /**
     * @param bool|null $isPublished
     *
     * @return UserArticles
     */
    public function setIsPublished(?bool $isPublished): UserArticles
    {
        $this->isPublished = $isPublished;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getKeywords()
    {

        if (! is_string($this->keywords)) {
            return $this->keywords;
        }

        return json_decode($this->keywords);
    }

    /**
     * @param $keywords
     *
     * @return $this
     */
    public function setKeywords($keywords): self
    {
        $this->keywords = $keywords;

        return $this;
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
     * @return User
     */
    public function getUserId(): User
    {
        return $this->userId;
    }

    /**
     * @param User $userId
     *
     * @return $this
     */
    public function setUserId(User $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUserDeletedId(): ?User
    {
        return $this->userDeletedId;
    }

    /**
     * @param User| null $userDeletedId
     *
     * @return $this
     */
    public function setUserDeletedId(?User $userDeletedId): self
    {
        $this->userDeletedId = $userDeletedId;

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

    public function setEditionId(Edition $editionId): self
    {
        $this->editionId = $editionId;

        return $this;
    }

    public function setEdition(Edition $editionId): self
    {
        $this->editionId = $editionId;

        return $this;
    }

    /**
     * @return Division
     */
    public function getDivisionId(): ?Division
    {
        return $this->divisionId;
    }

    /**
     * @param Division $divisionId
     *
     * @return $this
     */
    public function setDivisionId(Division $divisionId): self
    {
        $this->divisionId = $divisionId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserThemes()
    {
        return $this->userThemes;
    }

    /**
     * @param $userThemes
     *
     * @return $this
     */
    public function setUserThemes(?UserThemes $userThemes): self
    {
        $this->userThemes = $userThemes;

        if (empty($this->originalUserThemes)) {
            return $this->setOriginalUserThemes($userThemes);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOriginalUserThemes()
    {
        return $this->originalUserThemes;
    }

    /**
     * @param $originalUserThemes
     *
     * @return $this
     */
    public function setOriginalUserThemes(?UserThemes $originalUserThemes): self
    {
        $this->originalUserThemes = $originalUserThemes;

        return $this;
    }

    public function getMethodId(): ?Method
    {
        return $this->methodId;
    }

    public function setMethodId(?Method $methodId): self
    {
        $this->methodId = $methodId;

        return $this;
    }

    public function getTheoryId(): ?Theory
    {
        return $this->theoryId;
    }

    public function setTheoryId(?Theory $theoryId): self
    {
        $this->theoryId = $theoryId;

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
     * @return bool|null
     */
    public function getPortuguese(): ?bool
    {
        return $this->portuguese;
    }

    /**
     * @param bool|null $portuguese
     *
     * @return $this
     */
    public function setPortuguese(?bool $portuguese): self
    {
        $this->portuguese = $portuguese;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEnglish(): ?bool
    {
        return $this->english;
    }

    /**
     * @param bool|null $english
     *
     * @return $this
     */
    public function setEnglish(?bool $english): self
    {
        $this->english = $english;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getSpanish(): ?bool
    {
        return $this->spanish;
    }

    /**
     * @param bool|null $spanish
     *
     * @return $this
     */
    public function setSpanish(?bool $spanish): self
    {
        $this->spanish = $spanish;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getJobComplete(): ?bool
    {
        return $this->jobComplete;
    }

    /**
     * @param bool|null $jobComplete
     *
     * @return $this
     */
    public function setJobComplete(?bool $jobComplete): self
    {
        $this->jobComplete = $jobComplete;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getResumeFlag(): ?bool
    {
        return $this->resumeFlag;
    }

    /**
     * @param bool|null $resumeFlag
     *
     * @return $this
     */
    public function setResumeFlag(?bool $resumeFlag): self
    {
        $this->resumeFlag = $resumeFlag;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getRacBar(): ?bool
    {
        return $this->racBar;
    }

    /**
     * @param bool|null $racBar
     *
     * @return $this
     */
    public function setRacBar(?bool $racBar): self
    {
        $this->racBar = $racBar;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getNeverPublish(): ?bool
    {
        return $this->neverPublish;
    }

    /**
     * @param bool|null $neverPublish
     *
     * @return $this
     */
    public function setNeverPublish(?bool $neverPublish): self
    {
        $this->neverPublish = $neverPublish;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getConfirmFilesCorrect(): ?bool
    {
        return $this->confirmFilesCorrect;
    }

    /**
     * @param bool|null $confirmFilesCorrect
     *
     * @return $this
     */
    public function setConfirmFilesCorrect(?bool $confirmFilesCorrect): self
    {
        $this->confirmFilesCorrect = $confirmFilesCorrect;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getResume(): ?string
    {
        return $this->resume;
    }

    /**
     * @param string|null $resume
     *
     * @return $this
     */
    public function setResume(?string $resume): self
    {
        $this->resume = $resume;

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
    public function getFrame(): ?int
    {
        return $this->frame;
    }

    /**
     * @param int|null $frame
     *
     * @return $this
     */
    public function setFrame(?int $frame): self
    {
        $this->frame = $frame;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAcknowledgment(): ?string
    {
        return $this->acknowledgment;
    }

    /**
     * @param string|null $acknowledgment
     *
     * @return $this
     */
    public function setAcknowledgment(?string $acknowledgment): self
    {
        $this->acknowledgment = $acknowledgment;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getPremium(): ?bool
    {
        return $this->premium;
    }

    /**
     * @param bool|null $premium
     *
     * @return $this
     */
    public function setPremium(?bool $premium): self
    {
        $this->premium = $premium;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLastId(): ?int
    {
        return $this->lastId;
    }

    /**
     * @param int|null $lastId
     *
     * @return UserArticles
     */
    public function setLastId(?int $lastId): UserArticles
    {
        $this->lastId = $lastId;
        return $this;
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
     * @return UserArticles
     */
    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @param null|string $ip
     *
     * @return UserArticles
     */
    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

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
     * @return UserArticles
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
     * @return UserArticles
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
     * @return UserArticles
     */
    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return sprintf('%s - %s', $this->id, $this->title);
    }

    /**
     * @param SystemEvaluationIndications $indication
     *
     * @return $this
     */
    public function addIndication(SystemEvaluationIndications $indication): self
    {
        if (! $this->indications->contains($indication)) {
            $this->indications[] = $indication;
            $indication->setUserArticles($this);
        }

        return $this;
    }

    /**
     * @param SystemEvaluationIndications $indication
     *
     * @return $this
     */
    public function removeIndication(SystemEvaluationIndications $indication): self
    {
        if ($this->indications->contains($indication)) {
            $this->indications->removeElement($indication);
            // set the owning side to null (unless already changed)
            if ($indication->getUserArticles() === $this) {
                $indication->setUserArticles(null);
            }
        }

        return $this;
    }

    /**
     * @param SystemEvaluation $evaluation
     *
     * @return $this
     */
    public function addEvaluation(SystemEvaluation $evaluation): self
    {
        if (! $this->evaluations->contains($evaluation)) {
            $this->evaluations[] = $evaluation;
            $evaluation->setUserArticles($this);
        }

        return $this;
    }

    /**
     * @param SystemEvaluation $evaluation
     *
     * @return $this
     */
    public function removeEvaluation(SystemEvaluation $evaluation): self
    {
        if ($this->evaluations->contains($evaluation)) {
            $this->evaluations->removeElement($evaluation);
            // set the owning side to null (unless already changed)
            if ($evaluation->getUserArticles() === $this) {
                $evaluation->setUserArticles(null);
            }
        }

        return $this;
    }

    public function getApprovedFile(): ?string
    {
        if (! $this->getId()) {
            return null;
        }

        return $this->getEdition()->getId() . '/approved/' . md5($this->getId()) . '.pdf';
    }

    public function getModality(): ?Modality
    {
        return $this->modality;
    }

    public function setModality(?Modality $modality): self
    {
        $this->modality = $modality;

        return $this;
    }

    public function getIsRelatedThemeEnanpad(): ?int
    {
        return $this->isRelatedThemeEnanpad;
    }

    public function setIsRelatedThemeEnanpad(?int $isRelatedThemeEnanpad): self
    {
        $this->isRelatedThemeEnanpad = $isRelatedThemeEnanpad;

        return $this;
    }

}
