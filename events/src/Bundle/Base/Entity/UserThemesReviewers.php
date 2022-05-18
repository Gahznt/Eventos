<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_themes_reviewers")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\UserThemesReviewersRepository")
 */
class UserThemesReviewers
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
     * @ORM\ManyToOne(targetEntity="UserThemes", inversedBy="userThemesReviewers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_themes_id", referencedColumnName="id")
     * })
     */
    private $userThemes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="link_lattes", type="string", nullable=false)
     */
    private $linkLattes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", nullable=false)
     */
    private $email;

    /**
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private ?string $phone = null;

    /**
     *
     * @ORM\Column(name="cellphone", type="string", nullable=true)
     */
    private $cellphone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="institute", type="string", nullable=false)
     */
    private $institute;

    /**
     * @var string|null
     *
     * @ORM\Column(name="program", type="string", nullable=false)
     */
    private $program;

    /**
     * @var string|null
     *
     * @ORM\Column(name="state", type="string", nullable=false)
     */
    private $state;

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
     * @param UserThemes|null $userThemes
     * @return UserThemesReviewers
     */
    public function setUserThemes(?UserThemes $userThemes): self
    {
        $this->userThemes = $userThemes;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     * @return UserThemesReviewers
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLinkLattes(): ?string
    {
        return $this->linkLattes;
    }

    /**
     * @param null|string $linkLattes
     * @return UserThemesReviewers
     */
    public function setLinkLattes(?string $linkLattes): self
    {
        $this->linkLattes = $linkLattes;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     * @return UserThemesReviewers
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param $phone
     * @return UserThemesReviewers
     */
    public function setPhone($phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCellphone()
    {
        return $this->cellphone;
    }

    /**
     * @param $cellphone
     * @return UserThemesReviewers
     */
    public function setCellphone($cellphone): self
    {
        $this->cellphone = $cellphone;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getInstitute(): ?string
    {
        return $this->institute;
    }

    /**
     * @param null|string $institute
     * @return UserThemesReviewers
     */
    public function setInstitute(?string $institute): self
    {
        $this->institute = $institute;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getProgram(): ?string
    {
        return $this->program;
    }

    /**
     * @param null|string $program
     * @return UserThemesReviewers
     */
    public function setProgram(?string $program): self
    {
        $this->program = $program;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param null|string $state
     * @return UserThemesReviewers
     */
    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return null|string
     */
    public function __toString()
    {
        return $this->name;
    }

}
