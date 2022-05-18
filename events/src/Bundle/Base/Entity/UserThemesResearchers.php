<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_themes_researchers")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\UserThemesResearchersRepository")
 */
class UserThemesResearchers
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
     * @ORM\ManyToOne(targetEntity="UserThemes", inversedBy="userThemesResearchers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_themes_id", referencedColumnName="id")
     * })
     */
    private $userThemes;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userThemesResearchers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="researcher_id", referencedColumnName="id")
     * })
     */
    private $researcher;

    /**
     * @ORM\Column(name="biography", type="text", nullable=true)
     */
    private ?string $biography = null;

    /**
     * @ORM\Column(name="curriculum_lattes_link", type="text", nullable=true)
     */
    private ?string $curriculumLattesLink;

    /**
     * @ORM\Column(name="is_postgraduate_program_professor", type="boolean", nullable=true)
     */
    private ?bool $isPostgraduateProgramProfessor = false;

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
     * @return UserThemes
     */
    public function getUserThemes(): UserThemes
    {
        return $this->userThemes;
    }

    /**
     * @param UserThemes $userThemes
     *
     * @return UserThemesResearchers
     */
    public function setUserThemes(UserThemes $userThemes): self
    {
        $this->userThemes = $userThemes;

        return $this;
    }

    public function getResearcher(): ?User
    {
        return $this->researcher;
    }

    public function setResearcher(User $researcher): self
    {
        $this->researcher = $researcher;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;
        return $this;
    }

    public function getCurriculumLattesLink(): ?string
    {
        return $this->curriculumLattesLink;
    }

    public function setCurriculumLattesLink(?string $curriculumLattesLink): self
    {
        $this->curriculumLattesLink = $curriculumLattesLink;
        return $this;
    }

    public function isPostgraduateProgramProfessor(): ?bool
    {
        return $this->isPostgraduateProgramProfessor;
    }

    public function setIsPostgraduateProgramProfessor(?bool $isPostgraduateProgramProfessor): self
    {
        $this->isPostgraduateProgramProfessor = $isPostgraduateProgramProfessor;
        return $this;
    }

    public function __toString()
    {
        if (! $this->getResearcher()) {
            return (string)$this->getId();
        }

        return $this->getResearcher()->getName();
    }

}
