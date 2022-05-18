<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_themes")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\UserThemesRepository")
 */
class UserThemes
{
    const THEME_EVALUATION_STATUS_WAITING = 1;
    const THEME_EVALUATION_STATUS_NOT_SELECTED = 3;
    const THEME_EVALUATION_STATUS_SELECTED = 5;
    const THEME_EVALUATION_APPROVED = 2;
    const THEME_EVALUATION_STATUS_CANCELED = 4;

    const THEME_EVALUATION_STATUS_WAITING_ARCHIVE = 101;
    const THEME_EVALUATION_STATUS_NOT_SELECTED_ARCHIVE = 103;
    const THEME_EVALUATION_STATUS_SELECTED_ARCHIVE = 105;
    const THEME_EVALUATION_APPROVED_ARCHIVE = 102;
    const THEME_EVALUATION_STATUS_CANCELED_ARCHIVE = 104;

    const THEME_EVALUATION_STATUS = [
        'THEME_EVALUATION_STATUS_WAITING' => self::THEME_EVALUATION_STATUS_WAITING,
        'THEME_EVALUATION_STATUS_NOT_SELECTED' => self::THEME_EVALUATION_STATUS_NOT_SELECTED,
        'THEME_EVALUATION_STATUS_SELECTED' => self::THEME_EVALUATION_STATUS_SELECTED,
        'THEME_EVALUATION_STATUS_APPROVED' => self::THEME_EVALUATION_APPROVED,
        'THEME_EVALUATION_STATUS_CANCELED' => self::THEME_EVALUATION_STATUS_CANCELED,

        'THEME_EVALUATION_STATUS_WAITING_ARCHIVE' => self::THEME_EVALUATION_STATUS_WAITING_ARCHIVE,
        'THEME_EVALUATION_STATUS_NOT_SELECTED_ARCHIVE' => self::THEME_EVALUATION_STATUS_NOT_SELECTED_ARCHIVE,
        'THEME_EVALUATION_STATUS_SELECTED_ARCHIVE' => self::THEME_EVALUATION_STATUS_SELECTED_ARCHIVE,
        'THEME_EVALUATION_STATUS_APPROVED_ARCHIVE' => self::THEME_EVALUATION_APPROVED_ARCHIVE,
        'THEME_EVALUATION_STATUS_CANCELED_ARCHIVE' => self::THEME_EVALUATION_STATUS_CANCELED_ARCHIVE,
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userThemes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private ?User $user = null;

    /**
     * @var Division
     *
     * @ORM\ManyToOne(targetEntity="Division", inversedBy="themes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="division_id", referencedColumnName="id")
     * })
     */
    private $division;

    /**
     * @var UserThemesDetails
     *
     * @ORM\OneToOne(targetEntity="UserThemesDetails", mappedBy="userThemes", cascade={"persist"})
     */
    private $details;

    /**
     * @ORM\OneToMany(targetEntity="UserThemesBibliographies", mappedBy="userThemes", cascade={"persist"})
     */
    private $userThemesBibliographies;

    /**
     * @ORM\OneToMany(targetEntity="UserThemesResearchers", mappedBy="userThemes", cascade={"persist"})
     */
    private $userThemesResearchers;

    /**
     * @ORM\OneToMany(targetEntity="UserThemesReviewers", mappedBy="userThemes", cascade={"persist"})
     */
    private $userThemesReviewers;

    /**
     * @ORM\ManyToOne(targetEntity="ThemeSubmissionConfig", inversedBy="themes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="theme_submission_config", referencedColumnName="id")
     * })
     */
    private ?ThemeSubmissionConfig $themeSubmissionConfig = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var int|null
     *
     * @ORM\Column(name="position", type="integer", nullable=false, options={"default"="1"})
     */
    private $position = '1';

    /**
     * @ORM\OneToMany(targetEntity="UserThemesEvaluationLog", mappedBy="userThemes", cascade={"persist"})
     * @ORM\OrderBy({"createdAt":"DESC"})
     */
    private Collection $logs;

    /**
     * @var Collection|UserArticles[]
     * @ORM\OneToMany(targetEntity="UserArticles", mappedBy="userThemes")
     */
    private $userArticles;

    public function __construct()
    {
        $this->userThemesBibliographies = new ArrayCollection();
        $this->userThemesResearchers = new ArrayCollection();
        $this->userThemesReviewers = new ArrayCollection();
        $this->logs = new ArrayCollection();

        $this->userArticles = new ArrayCollection();
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): UserThemes
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return UserThemesBibliographies[]
     */
    public function getUserThemesBibliographies(): Collection
    {
        return $this->userThemesBibliographies;
    }

    /**
     * @return UserThemesResearchers[]
     */
    public function getUserThemesResearchers(): Collection
    {
        return $this->userThemesResearchers;
    }

    /**
     * @return UserThemesReviewers[]
     */
    public function getUserThemesReviewers(): Collection
    {
        return $this->userThemesReviewers;
    }

    public function addUserThemesBibliography(UserThemesBibliographies $userThemesBibliographies): self
    {
        if (! empty($userThemesBibliographies->getName())) {
            $this->userThemesBibliographies[] = $userThemesBibliographies;
            $userThemesBibliographies->setUserThemes($this);
        }

        return $this;
    }

    public function removeUserThemesBibliography(UserThemesBibliographies $userThemesBibliographies)
    {
        $this->userThemesBibliographies->removeElement($userThemesBibliographies);
    }

    public function addUserThemesResearcher(UserThemesResearchers $userThemesResearchers): self
    {
        if (! empty($userThemesResearchers->getResearcher())) {
            $this->userThemesResearchers[] = $userThemesResearchers;
            $userThemesResearchers->setUserThemes($this);
        }

        return $this;
    }

    public function removeUserThemesResearcher(UserThemesResearchers $userThemesResearchers)
    {
        $this->userThemesResearchers->removeElement($userThemesResearchers);
    }

    public function addUserThemesReviewer(UserThemesReviewers $userThemesReviewers): self
    {
        if (! empty($userThemesReviewers->getName())) {
            $this->userThemesReviewers[] = $userThemesReviewers;
            $userThemesReviewers->setUserThemes($this);
        }

        return $this;
    }

    public function removeUserThemesReviewer(UserThemesReviewers $userThemesReviewers)
    {
        $this->userThemesReviewers->removeElement($userThemesReviewers);
    }

    public function getDivision(): ?Division
    {
        return $this->division;
    }

    public function setDivision(?Division $division): UserThemes
    {
        $this->division = $division;
        return $this;
    }

    public function getDetails(): ?UserThemesDetails
    {
        return $this->details;
    }

    public function setDetails(UserThemesDetails $details): self
    {
        $this->details = $details;
        // inverso
        $details->setUserThemes($this);

        return $this;
    }

    public function getThemeSubmissionConfig(): ?ThemeSubmissionConfig
    {
        return $this->themeSubmissionConfig;
    }

    public function setThemeSubmissionConfig(?ThemeSubmissionConfig $themeSubmissionConfig): UserThemes
    {
        $this->themeSubmissionConfig = $themeSubmissionConfig;
        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function __toString(): string
    {
        return (string)$this->getId();
    }

    /**
     * @return UserThemesEvaluationLog[]
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(UserThemesEvaluationLog $log): self
    {
        if (! $this->logs->contains($log)) {
            $this->logs[] = $log;
            $log->setUserThemes($this);
        }

        return $this;
    }

    public function removeLog(UserThemesEvaluationLog $log): self
    {
        if ($this->logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getUserThemes() === $this) {
                $log->setUserThemes(null);
            }
        }

        return $this;
    }

    public function getUserArticles()
    {
        return $this->userArticles;
    }
}
