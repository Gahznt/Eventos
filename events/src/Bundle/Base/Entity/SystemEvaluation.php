<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SystemEvaluation
 *
 * @ORM\Table(name="system_evaluation")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\SystemEvaluationRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class SystemEvaluation
{
    const LIST_CRITERIAS_PREFIX = "Criteria";

    const LIST_CRITERIAS = [
        'one',
        'two',
        'three',
        'four',
        'five',
        'six',
        'seven',
        'eight',
        'nine',
        'ten',
    ];

    const CRITERIA_PRIMARY_OPTION_REJECT = 1;

    const CRITERIA_PRIMARY_OPTION_LOW_COMPECTION = 2;

    const CRITERIA_PRIMARY_OPTION_APPROVE = 3;

    const CRITERIA_PRIMARY_OPTION_APPROVE_TOTALY = 4;

    const CRITERIA_PRIMARY_OPTIONS = [
        'CRITERIA_PRIMARY_OPTION_REJECT' => self::CRITERIA_PRIMARY_OPTION_REJECT,
        'CRITERIA_PRIMARY_OPTION_LOW_COMPECTION' => self::CRITERIA_PRIMARY_OPTION_LOW_COMPECTION,
        'CRITERIA_PRIMARY_OPTION_APPROVE' => self::CRITERIA_PRIMARY_OPTION_APPROVE,
        'CRITERIA_PRIMARY_OPTION_APPROVE_TOTALY' => self::CRITERIA_PRIMARY_OPTION_APPROVE_TOTALY,
    ];

    const CRITERIA_OPTIONS_VERY_GOOD = ['very_good' => 2];

    const CRITERIA_OPTION_GOOD = ['good' => 1];

    const CRITERIA_OPTION_REGULAR = ['weak' => 0];

    const CRITERIA_OPTION_WEAK = ['weak' => -1];

    const CRITERIA_REAL_OPTIONS = [
        self::CRITERIA_OPTIONS_VERY_GOOD,
        self::CRITERIA_OPTION_GOOD,
        self::CRITERIA_OPTION_REGULAR,
        self::CRITERIA_OPTION_WEAK,
    ];

    const CRITERIA_OPTIONS = [
        'CRITERIA_OPTION_VERY_GOOD' => 'very_good',
        'CRITERIA_OPTION_GOOD' => 'good',
        'CRITERIA_OPTION_REGULAR' => 'regular',
        'CRITERIA_OPTION_WEAK' => 'weak',
    ];

    const AUTHOR_RATE_ONE_OPTIONS = [
        'AUTHOR_RATE_ONE_WEAK' => 'weak',
        'AUTHOR_RATE_ONE_REGULAR' => 'regular',
        'AUTHOR_RATE_ONE_GOOD' => 'good',
        'AUTHOR_RATE_ONE_VERY_GOOD' => 'very_good',
    ];

    const AUTHOR_RATE_TWO_OPTIONS = [
        'AUTHOR_RATE_TWO_WEAK' => 'weak',
        'AUTHOR_RATE_TWO_REGULAR' => 'regular',
        'AUTHOR_RATE_TWO_GOOD' => 'good',
        'AUTHOR_RATE_TWO_VERY_GOOD' => 'very_good',
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
     * @var UserArticles|null
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="UserArticles", inversedBy="evaluations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_articles_id", referencedColumnName="id")
     * })
     */
    private $userArticles;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="systemEvaluations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_owner_id", referencedColumnName="id")
     * })
     */
    private ?User $userOwner = null;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="reject_at", type="datetime", nullable=true)
     */
    private $rejectAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="format_error_justification", type="text", nullable=true)
     */
    private $formatErrorJustification;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="format_error_at", type="datetime", nullable=true)
     */
    private $formatErrorAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reject_justification", type="text", nullable=true)
     */
    private $rejectJustification;

    /**
     * @var string|null
     *
     * @ORM\Column(name="justification", type="text", nullable=true)
     */
    private $justification;

    /**
     * @var string|null
     *
     * @ORM\Column(name="criteria_one", type="string", length=10, nullable=true)
     */
    private $criteriaOne = "weak";

    /**
     * @var string|null
     *
     * @ORM\Column(name="criteria_two", type="string", length=10, nullable=true)
     */
    private $criteriaTwo = "weak";

    /**
     * @var string|null
     *
     * @ORM\Column(name="criteria_three", type="string", length=10, nullable=true)
     */
    private $criteriaThree = "weak";

    /**
     * @var string|null
     *
     * @ORM\Column(name="criteria_four", type="string", length=10, nullable=true)
     */
    private $criteriaFour = "weak";

    /**
     * @var string|null
     *
     * @ORM\Column(name="criteria_five", type="string", length=10, nullable=true)
     */
    private $criteriaFive = "weak";

    /**
     * @var string|null
     *
     * @ORM\Column(name="criteria_six", type="string", length=10, nullable=true)
     */
    private $criteriaSix = "weak";

    /**
     * @var string|null
     *
     * @ORM\Column(name="criteria_seven", type="string", length=10, nullable=true)
     */
    private $criteriaSeven = "weak";

    /**
     * @var string|null
     *
     * @ORM\Column(name="criteria_eight", type="string", length=10, nullable=true)
     */
    private $criteriaEight = "weak";

    /**
     * @var string|null
     *
     * @ORM\Column(name="criteria_nine", type="string", length=10, nullable=true)
     */
    private $criteriaNine = "weak";

    /**
     * @var string|null
     *
     * @ORM\Column(name="criteria_ten", type="string", length=10, nullable=true)
     */
    private $criteriaTen = "weak";

    /**
     * @var int|null
     *
     * @ORM\Column(name="criteria_final", type="integer", nullable=true)
     */
    private $criteriaFinal = 0;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_author_rated", type="boolean", nullable=true)
     */
    private $isAuthorRated = false;

    /**
     * @var string|null
     *
     * @ORM\Column(name="author_rate_one", type="string", length=10, nullable=true)
     */
    private $authorRateOne;

    /**
     * @var string|null
     *
     * @ORM\Column(name="author_rate_two", type="string", length=10, nullable=true)
     */
    private $authorRateTwo;

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
     * @ORM\OneToMany(targetEntity="SystemEvaluationLog", mappedBy="systemEvaluation", cascade={"persist"})
     */
    private $logs;

    /**
     * SystemEvaluation constructor.
     */
    public function __construct()
    {
        $this->logs = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getLogs(): Collection
    {
        return $this->logs;
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
     * @return UserArticles|null
     */
    public function getUserArticles(): ?UserArticles
    {
        return $this->userArticles;
    }

    /**
     * @param UserArticles|null $userArticles
     *
     * @return SystemEvaluation
     */
    public function setUserArticles(?UserArticles $userArticles): self
    {
        $this->userArticles = $userArticles;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getJustification(): ?string
    {
        return $this->justification;
    }

    /**
     * @param null|string $justification
     *
     * @return SystemEvaluation
     */
    public function setJustification(?string $justification): self
    {
        $this->justification = $justification;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCriteriaOne(): ?string
    {
        return $this->criteriaOne;
    }

    /**
     * @param string|null $criteriaOne
     *
     * @return SystemEvaluation
     */
    public function setCriteriaOne(?string $criteriaOne): self
    {
        $this->criteriaOne = $criteriaOne;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCriteriaTwo(): ?string
    {
        return $this->criteriaTwo;
    }

    /**
     * @param string|null $criteriaTwo
     *
     * @return SystemEvaluation
     */
    public function setCriteriaTwo(?string $criteriaTwo): self
    {
        $this->criteriaTwo = $criteriaTwo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCriteriaThree(): ?string
    {
        return $this->criteriaThree;
    }

    /**
     * @param string|null $criteriaThree
     *
     * @return SystemEvaluation
     */
    public function setCriteriaThree(?string $criteriaThree): self
    {
        $this->criteriaThree = $criteriaThree;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCriteriaFour(): ?string
    {
        return $this->criteriaFour;
    }

    /**
     * @param string|null $criteriaFour
     *
     * @return SystemEvaluation
     */
    public function setCriteriaFour(?string $criteriaFour): self
    {
        $this->criteriaFour = $criteriaFour;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCriteriaFive(): ?string
    {
        return $this->criteriaFive;
    }

    /**
     * @param string|null $criteriaFive
     *
     * @return SystemEvaluation
     */
    public function setCriteriaFive(?string $criteriaFive): self
    {
        $this->criteriaFive = $criteriaFive;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCriteriaSix(): ?string
    {
        return $this->criteriaSix;
    }

    /**
     * @param string|null $criteriaSix
     *
     * @return SystemEvaluation
     */
    public function setCriteriaSix(?string $criteriaSix): self
    {
        $this->criteriaSix = $criteriaSix;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCriteriaSeven(): ?string
    {
        return $this->criteriaSeven;
    }

    /**
     * @param string|null $criteriaSeven
     *
     * @return SystemEvaluation
     */
    public function setCriteriaSeven(?string $criteriaSeven): self
    {
        $this->criteriaSeven = $criteriaSeven;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCriteriaEight(): ?string
    {
        return $this->criteriaEight;
    }

    /**
     * @param string|null $criteriaEight
     *
     * @return SystemEvaluation
     */
    public function setCriteriaEight(?string $criteriaEight): self
    {
        $this->criteriaEight = $criteriaEight;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCriteriaNine(): ?string
    {
        return $this->criteriaNine;
    }

    /**
     * @param string|null $criteriaNine
     *
     * @return SystemEvaluation
     */
    public function setCriteriaNine(?string $criteriaNine): self
    {
        $this->criteriaNine = $criteriaNine;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCriteriaTen(): ?string
    {
        return $this->criteriaTen;
    }

    /**
     * @param string|null $criteriaTen
     *
     * @return SystemEvaluation
     */
    public function setCriteriaTen(?string $criteriaTen): self
    {
        $this->criteriaTen = $criteriaTen;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getCriteriaFinal(): ?int
    {
        return $this->criteriaFinal;
    }

    /**
     * @param int|string $criteriaFinal
     *
     * @return SystemEvaluation
     */
    public function setCriteriaFinal(?int $criteriaFinal): self
    {
        $this->criteriaFinal = $criteriaFinal;

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
     * @return SystemEvaluation
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
     * @return SystemEvaluation
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
     * @return SystemEvaluation
     */
    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return User
     */
    public function getUserOwner(): User
    {
        return $this->userOwner;
    }

    /**
     * @param User $userOwner
     *
     * @return SystemEvaluation
     */
    public function setUserOwner(User $userOwner): self
    {
        $this->userOwner = $userOwner;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getRejectAt(): ?\DateTime
    {
        return $this->rejectAt;
    }

    /**
     * @param \DateTime|null $rejectAt
     *
     * @return SystemEvaluation
     */
    public function setRejectAt(?\DateTime $rejectAt): self
    {
        $this->rejectAt = $rejectAt;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getRejectJustification(): ?string
    {
        return $this->rejectJustification;
    }

    /**
     * @param null|string $rejectJustification
     *
     * @return SystemEvaluation
     */
    public function setRejectJustification(?string $rejectJustification): self
    {
        $this->rejectJustification = $rejectJustification;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFormatErrorJustification(): ?string
    {
        return $this->formatErrorJustification;
    }

    /**
     * @param null|string $formatErrorJustification
     *
     * @return SystemEvaluation
     */
    public function setFormatErrorJustification(?string $formatErrorJustification): self
    {
        $this->formatErrorJustification = $formatErrorJustification;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getFormatErrorAt(): ?\DateTime
    {
        return $this->formatErrorAt;
    }

    /**
     * @param \DateTime|null $formatErrorAt
     *
     * @return SystemEvaluation
     */
    public function setFormatErrorAt(?\DateTime $formatErrorAt): self
    {
        $this->formatErrorAt = $formatErrorAt;

        return $this;
    }

    /**
     * @return null|string
     */
    public function __toString()
    {
        return $this->justification;
    }

    /**
     * @param SystemEvaluationLog $log
     *
     * @return $this
     */
    public function addLog(SystemEvaluationLog $log): self
    {
        if (! $this->logs->contains($log)) {
            $this->logs[] = $log;
            $log->setSystemEvaluation($this);
        }

        return $this;
    }

    /**
     * @param SystemEvaluationLog $log
     *
     * @return $this
     */
    public function removeLog(SystemEvaluationLog $log): self
    {
        if ($this->logs->contains($log)) {
            $this->logs->removeElement($log);
            // set the owning side to null (unless already changed)
            if ($log->getSystemEvaluation() === $this) {
                $log->setSystemEvaluation(null);
            }
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsAuthorRated(): ?bool
    {
        return $this->isAuthorRated;
    }

    /**
     * @param bool|null $isAuthorRated
     *
     * @return SystemEvaluation
     */
    public function setIsAuthorRated(?bool $isAuthorRated): SystemEvaluation
    {
        $this->isAuthorRated = $isAuthorRated;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthorRateOne(): ?string
    {
        return $this->authorRateOne;
    }

    /**
     * @param string|null $authorRateOne
     *
     * @return SystemEvaluation
     */
    public function setAuthorRateOne(?string $authorRateOne): SystemEvaluation
    {
        $this->authorRateOne = $authorRateOne;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthorRateTwo(): ?string
    {
        return $this->authorRateTwo;
    }

    /**
     * @param string|null $authorRateTwo
     *
     * @return SystemEvaluation
     */
    public function setAuthorRateTwo(?string $authorRateTwo): SystemEvaluation
    {
        $this->authorRateTwo = $authorRateTwo;
        return $this;
    }
}
