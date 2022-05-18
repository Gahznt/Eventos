<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserEvaluationArticles
 *
 * @ORM\Table(name="user_evaluation_articles")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\UserEvaluationArticlesRepository")
 */
class UserEvaluationArticles
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
     * @var Division|null
     * @ORM\ManyToOne(targetEntity="Division")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="division_first_id", referencedColumnName="id")
     * })
     */
    private $divisionFirstId;

    /**
     * @var Division|null
     * @ORM\ManyToOne(targetEntity="Division")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="division_second_id", referencedColumnName="id")
     * })
     */
    private $divisionSecondId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="keyword_one", type="text", nullable=true)
     */
    private $keywordOne;

    /**
     * @var string|null
     *
     * @ORM\Column(name="keyword_two", type="text", nullable=true)
     */
    private $keywordTwo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="keyword_three", type="text", nullable=true)
     */
    private $keywordThree;

    /**
     * @var string|null
     *
     * @ORM\Column(name="keyword_four", type="text", nullable=true)
     */
    private $keywordFour;

    /**
     * @var string|null
     *
     * @ORM\Column(name="keyword_five", type="text", nullable=true)
     */
    private $keywordFive;

    /**
     * @var string|null
     *
     * @ORM\Column(name="keyword_six", type="text", nullable=true)
     */
    private $keywordSix;

    /**
     * @var UserThemes|null
     * @ORM\ManyToOne(targetEntity="UserThemes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="theme_first_id", referencedColumnName="id")
     * })
     */
    private $themeFirstId;

    /**
     * @var UserThemes|null
     * @ORM\ManyToOne(targetEntity="UserThemes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="theme_second_id", referencedColumnName="id")
     * })
     */
    private $themeSecondId;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="portuguese", type="boolean", nullable=false)
     */
    private $portuguese = true;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="english", type="boolean", nullable=false)
     */
    private $english = false;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="spanish", type="boolean", nullable=false)
     */
    private $spanish = false;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="userEvaluationArticles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="want_evaluate", type="boolean", nullable=true)
     */
    private $wantEvaluate = false;

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
     * @return Division|null
     */
    public function getDivisionFirstId(): ?Division
    {
        return $this->divisionFirstId;
    }

    /**
     * @param Division|null $divisionFirstId
     * @return $this
     */
    public function setDivisionFirstId(?Division $divisionFirstId): self
    {
        $this->divisionFirstId = $divisionFirstId;

        return $this;
    }

    /**
     * @return Division|null
     */
    public function getDivisionSecondId(): ?Division
    {
        return $this->divisionSecondId;
    }

    /**
     * @param Division|null $divisionSecondId
     * @return $this
     */
    public function setDivisionSecondId(?Division $divisionSecondId): self
    {
        $this->divisionSecondId = $divisionSecondId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKeywordOne(): ?string
    {
        return $this->keywordOne;
    }

    /**
     * @param string|null $keywordOne
     * @return $this
     */
    public function setKeywordOne(?string $keywordOne): self
    {
        $this->keywordOne = $keywordOne;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKeywordTwo(): ?string
    {
        return $this->keywordTwo;
    }

    /**
     * @param string|null $keywordTwo
     * @return $this
     */
    public function setKeywordTwo(?string $keywordTwo): self
    {
        $this->keywordTwo = $keywordTwo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKeywordThree(): ?string
    {
        return $this->keywordThree;
    }

    /**
     * @param string|null $keywordThree
     * @return $this
     */
    public function setKeywordThree(?string $keywordThree): self
    {
        $this->keywordThree = $keywordThree;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKeywordFour(): ?string
    {
        return $this->keywordFour;
    }

    /**
     * @param string|null $keywordFour
     * @return $this
     */
    public function setKeywordFour(?string $keywordFour): self
    {
        $this->keywordFour = $keywordFour;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKeywordFive(): ?string
    {
        return $this->keywordFive;
    }

    /**
     * @param string|null $keywordFive
     * @return $this
     */
    public function setKeywordFive(?string $keywordFive): self
    {
        $this->keywordFive = $keywordFive;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getKeywordSix(): ?string
    {
        return $this->keywordSix;
    }

    /**
     * @param string|null $keywordSix
     * @return $this
     */
    public function setKeywordSix(?string $keywordSix): self
    {
        $this->keywordSix = $keywordSix;

        return $this;
    }

    /**
     * @return UserThemes|null
     */
    public function getThemeFirstId(): ?UserThemes
    {
        return $this->themeFirstId;
    }

    /**
     * @param UserThemes|null $themeFirstId
     * @return $this
     */
    public function setThemeFirstId(?UserThemes $themeFirstId): self
    {
        $this->themeFirstId = $themeFirstId;

        return $this;
    }

    /**
     * @return UserThemes|null
     */
    public function getThemeSecondId(): ?UserThemes
    {
        return $this->themeSecondId;
    }

    /**
     * @param UserThemes|null $themeSecondId
     * @return $this
     */
    public function setThemeSecondId(?UserThemes $themeSecondId): self
    {
        $this->themeSecondId = $themeSecondId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPortuguese(): bool
    {
        return $this->portuguese;
    }

    /**
     * @param bool|null $portuguese
     * @return $this
     */
    public function setPortuguese(?bool $portuguese): self
    {
        $this->portuguese = $portuguese;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnglish(): bool
    {
        return $this->english;
    }

    /**
     * @param bool|null $english
     * @return $this
     */
    public function setEnglish(?bool $english): self
    {
        $this->english = $english;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSpanish(): bool
    {
        return $this->spanish;
    }

    /**
     * @return bool
     */
    public function isWantEvaluate(): bool
    {
        return $this->wantEvaluate;
    }

    /**
     * @param bool|null $spanish
     * @return $this
     */
    public function setSpanish(?bool $spanish): self
    {
        $this->spanish = $spanish;

        return $this;
    }

    /**
     * @param bool|null $wantEvaluate
     * @return $this
     */
    public function setWantEvaluate(?bool $wantEvaluate): self
    {
        $this->wantEvaluate = $wantEvaluate;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }

    /**
     * @return bool|null
     */
    public function getPortuguese(): ?bool
    {
        return $this->portuguese;
    }

    /**
     * @return bool|null
     */
    public function getEnglish(): ?bool
    {
        return $this->english;
    }

    /**
     * @return bool|null
     */
    public function getSpanish(): ?bool
    {
        return $this->spanish;
    }

    /**
     * @return bool|null
     */
    public function getWantEvaluate(): ?bool
    {
        return $this->wantEvaluate;
    }
}
