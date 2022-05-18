<?php

namespace App\Bundle\Base\Entity;

/**
 * Class ThemeEvaluationList
 *
 * @package App\Bundle\Base\Entity
 */
class ThemeEvaluationList
{

    /**
     * @var UserThemes|null
     */
    private $id;

    /**
     * @var Edition|null
     */
    private $editionId;

    /**
     * @var Division|null
     */
    private $divisionId;

    /**
     * @var string|null
     */
    private $portugueseDescription;

    /**
     * @var string|null
     */
    private $englishDescription;

    /**
     * @var string|null
     */
    private $spanishDescription;

    /**
     * @var string|null
     */
    private $portugueseTitle;

    /**
     * @var string|null
     */
    private $englishTitle;

    /**
     * @var string|null
     */
    private $spanishTitle;

    /**
     * @var string|null
     */
    private $portugueseKeywords;

    /**
     * @var string|null
     */
    private $englishKeywords;

    /**
     * @var string|null
     */
    private $spanishKeywords;

    /**
     * @var UserThemesBibliographies|null
     */
    private $userThemesBibliographies;

    /**
     * @var UserThemesResearchers|null
     */
    private $userThemesResearchers;

    /**
     * @var UserThemesReviewers|null
     */
    private $userThemesReviewers;

    /**
     * @var int|null
     */
    private $status;

    /**
     * @var string|null
     */
    private $search;

    /**
     * @return UserThemes|null
     */
    public function getId(): ?UserThemes
    {
        return $this->id;
    }

    /**
     * @param UserThemes|null $id
     *
     * @return ThemeEvaluationList
     */
    public function setId(UserThemes $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setEditionId(?Edition $editionId): self
    {
        $this->editionId = $editionId;

        return $this;
    }

    public function setEdition(?Edition $editionId): self
    {
        $this->editionId = $editionId;

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

    /**
     * @return Division|null
     */
    public function getDivisionId(): ?Division
    {
        return $this->divisionId;
    }

    /**
     * @param Division $divisionId
     *
     * @return ThemeEvaluationList
     */
    public function setDivisionId(Division $divisionId): self
    {
        $this->divisionId = $divisionId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPortugueseDescription(): ?string
    {
        return $this->portugueseDescription;
    }

    /**
     * @param null|string $portugueseDescription
     *
     * @return ThemeEvaluationList
     */
    public function setPortugueseDescription(?string $portugueseDescription): self
    {
        $this->portugueseDescription = $portugueseDescription;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEnglishDescription(): ?string
    {
        return $this->englishDescription;
    }

    /**
     * @param null|string $englishDescription
     *
     * @return ThemeEvaluationList
     */
    public function setEnglishDescription(?string $englishDescription): self
    {
        $this->englishDescription = $englishDescription;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSpanishDescription(): ?string
    {
        return $this->spanishDescription;
    }

    /**
     * @param null|string $spanishDescription
     *
     * @return ThemeEvaluationList
     */
    public function setSpanishDescription(?string $spanishDescription): self
    {
        $this->spanishDescription = $spanishDescription;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPortugueseTitle(): ?string
    {
        return $this->portugueseTitle;
    }

    /**
     * @param null|string $portugueseTitle
     *
     * @return ThemeEvaluationList
     */
    public function setPortugueseTitle(?string $portugueseTitle): self
    {
        $this->portugueseTitle = $portugueseTitle;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEnglishTitle(): ?string
    {
        return $this->englishTitle;
    }

    /**
     * @param null|string $englishTitle
     *
     * @return ThemeEvaluationList
     */
    public function setEnglishTitle(?string $englishTitle): self
    {
        $this->englishTitle = $englishTitle;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSpanishTitle(): ?string
    {
        return $this->spanishTitle;
    }

    /**
     * @param null|string $spanishTitle
     *
     * @return ThemeEvaluationList
     */
    public function setSpanishTitle(?string $spanishTitle): self
    {
        $this->spanishTitle = $spanishTitle;

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
     * @return ThemeEvaluationList
     */
    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param null|string $search
     *
     * @return ThemeEvaluationList
     */
    public function setSearch(?string $search): self
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPortugueseKeywords(): ?string
    {
        return $this->portugueseKeywords;
    }

    /**
     * @param null|string $portugueseKeywords
     *
     * @return ThemeEvaluationList
     */
    public function setPortugueseKeywords(?string $portugueseKeywords): self
    {
        $this->portugueseKeywords = $portugueseKeywords;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEnglishKeywords(): ?string
    {
        return $this->englishKeywords;
    }

    /**
     * @param null|string $englishKeywords
     *
     * @return ThemeEvaluationList
     */
    public function setEnglishKeywords(?string $englishKeywords): self
    {
        $this->englishKeywords = $englishKeywords;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSpanishKeywords(): ?string
    {
        return $this->spanishKeywords;
    }

    /**
     * @param null|string $spanishKeywords
     *
     * @return ThemeEvaluationList
     */
    public function setSpanishKeywords(?string $spanishKeywords): self
    {
        $this->spanishKeywords = $spanishKeywords;

        return $this;
    }

    /**
     * @return UserThemesBibliographies|null
     */
    public function getUserThemesBibliographies(): ?UserThemesBibliographies
    {
        return $this->userThemesBibliographies;
    }

    /**
     * @param UserThemesBibliographies $userThemesBibliographies
     *
     * @return ThemeEvaluationList
     */
    public function setUserThemesBibliographies(UserThemesBibliographies $userThemesBibliographies): self
    {
        $this->userThemesBibliographies = $userThemesBibliographies;

        return $this;
    }

    /**
     * @return UserThemesResearchers|null
     */
    public function getUserThemesResearchers(): ?UserThemesResearchers
    {
        return $this->userThemesResearchers;
    }

    /**
     * @param UserThemesResearchers $userThemesResearchers
     *
     * @return ThemeEvaluationList
     */
    public function setUserThemesResearchers(UserThemesResearchers $userThemesResearchers): self
    {
        $this->userThemesResearchers = $userThemesResearchers;

        return $this;
    }

    /**
     * @return UserThemesReviewers|null
     */
    public function getUserThemesReviewers(): ?UserThemesReviewers
    {
        return $this->userThemesReviewers;
    }

    /**
     * @param $userThemesReviewers
     *
     * @return ThemeEvaluationList
     */
    public function setUserThemesReviewers($userThemesReviewers): self
    {
        $this->userThemesReviewers = $userThemesReviewers;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->portugueseDescription;
    }
}
