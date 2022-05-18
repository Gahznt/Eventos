<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserArticlesAuthors
 *
 * @ORM\Table(name="user_articles_authors")
 * @ORM\Entity
 */
class UserArticlesAuthors
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
     * @var int|null
     *
     * @ORM\Column(name="order_author", type="smallint", nullable=true, options={"default"="1"})
     */
    private $order = '1';

    /**
     * @var string|null
     *
     * @ORM\Column(name="other_institution_first", type="string", nullable=true)
     */
    private $otherInstitutionFirst;

    /**
     * @var string|null
     *
     * @ORM\Column(name="other_institution_second", type="string", nullable=true)
     */
    private $otherInstitutionSecond;

    /**
     * @var string|null
     *
     * @ORM\Column(name="other_program_first", type="string", nullable=true)
     */
    private $otherProgramFirst;

    /**
     * @var string|null
     *
     * @ORM\Column(name="other_program_second", type="string", nullable=true)
     */
    private $otherProgramSecond;

    /**
     * @var UserArticles
     *
     * @ORM\ManyToOne(targetEntity="UserArticles", inversedBy="userArticlesAuthors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_articles_id", referencedColumnName="id")
     * })
     */
    private $userArticles;
    private $userArticlesId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userArticlesAuthors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_author_id", referencedColumnName="id")
     * })
     */
    private ?User $userAuthor = null;
    private ?User $userAuthorId = null;

    /**
     * @var Institution
     *
     * @ORM\ManyToOne(targetEntity="Institution")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="institution_first_id", referencedColumnName="id")
     * })
     */
    private $institutionFirst;

    /**
     * @var Institution
     *
     * @ORM\ManyToOne(targetEntity="Institution")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="institution_second_id", referencedColumnName="id")
     * })
     */
    private $institutionSecond;

    /**
     * @var Program
     *
     * @ORM\ManyToOne(targetEntity="Program")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="program_first_id", referencedColumnName="id")
     * })
     */
    private $programFirst;

    /**
     * @var Program
     *
     * @ORM\ManyToOne(targetEntity="Program")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="program_second_id", referencedColumnName="id")
     * })
     */
    private $programSecond;

    /**
     * @var State
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="state_first_id", referencedColumnName="id")
     * })
     */
    private $stateFirst;

    /**
     * @var State
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="state_second_id", referencedColumnName="id")
     * })
     */
    private $stateSecond;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_presented", type="boolean", nullable=true, options={"default"="0"})
     */
    private $isPresented = false;

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
    public function getOrder(): ?int
    {
        return $this->order;
    }

    /**
     * @param int|null $order
     */
    public function setOrder(?int $order): void
    {
        $this->order = $order;
    }

    /**
     * @return User|null
     */
    public function getUserAuthorId(): ?User
    {
        return $this->userAuthor;
    }

    /**
     * @param User|null $userAuthorId
     *
     * @return $this
     */
    public function setUserAuthorId(?User $userAuthorId): self
    {
        $this->userAuthor = $userAuthorId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOtherInstitutionFirst(): ?string
    {
        return $this->otherInstitutionFirst;
    }

    /**
     * @param string|null $otherInstitutionFirst
     *
     * @return UserArticlesAuthors
     */
    public function setOtherInstitutionFirst(?string $otherInstitutionFirst): UserArticlesAuthors
    {
        $this->otherInstitutionFirst = $otherInstitutionFirst;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOtherInstitutionSecond(): ?string
    {
        return $this->otherInstitutionSecond;
    }

    /**
     * @param string|null $otherInstitutionSecond
     *
     * @return UserArticlesAuthors
     */
    public function setOtherInstitutionSecond(?string $otherInstitutionSecond): UserArticlesAuthors
    {
        $this->otherInstitutionSecond = $otherInstitutionSecond;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOtherProgramFirst(): ?string
    {
        return $this->otherProgramFirst;
    }

    /**
     * @param string|null $otherProgramFirst
     *
     * @return UserArticlesAuthors
     */
    public function setOtherProgramFirst(?string $otherProgramFirst): UserArticlesAuthors
    {
        $this->otherProgramFirst = $otherProgramFirst;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOtherProgramSecond(): ?string
    {
        return $this->otherProgramSecond;
    }

    /**
     * @param string|null $otherProgramSecond
     *
     * @return UserArticlesAuthors
     */
    public function setOtherProgramSecond(?string $otherProgramSecond): UserArticlesAuthors
    {
        $this->otherProgramSecond = $otherProgramSecond;
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
     * @return $this
     */
    public function setUserArticles(?UserArticles $userArticles): UserArticlesAuthors
    {
        $this->userArticles = $userArticles;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getUserAuthor(): ?User
    {
        return $this->userAuthor;
    }

    /**
     * @param User|null $userAuthor
     *
     * @return $this
     */
    public function setUserAuthor(?User $userAuthor): UserArticlesAuthors
    {
        $this->userAuthor = $userAuthor;
        return $this;
    }

    /**
     * @return Institution|null
     */
    public function getInstitutionFirst(): ?Institution
    {
        return $this->institutionFirst;
    }

    /**
     * @param Institution|null $institutionFirst
     *
     * @return $this
     */
    public function setInstitutionFirst(?Institution $institutionFirst): UserArticlesAuthors
    {
        $this->institutionFirst = $institutionFirst;
        return $this;
    }

    /**
     * @return Institution|null
     */
    public function getInstitutionSecond(): ?Institution
    {
        return $this->institutionSecond;
    }

    /**
     * @param Institution|null $institutionSecond
     *
     * @return $this
     */
    public function setInstitutionSecond(?Institution $institutionSecond): UserArticlesAuthors
    {
        $this->institutionSecond = $institutionSecond;
        return $this;
    }

    /**
     * @return Program|null
     */
    public function getProgramFirst(): ?Program
    {
        return $this->programFirst;
    }

    /**
     * @param Program|null $programFirst
     *
     * @return $this
     */
    public function setProgramFirst(?Program $programFirst): UserArticlesAuthors
    {
        $this->programFirst = $programFirst;
        return $this;
    }

    /**
     * @return Program|null
     */
    public function getProgramSecond(): ?Program
    {
        return $this->programSecond;
    }

    /**
     * @param Program|null $programSecond
     *
     * @return $this
     */
    public function setProgramSecond(?Program $programSecond): UserArticlesAuthors
    {
        $this->programSecond = $programSecond;
        return $this;
    }

    /**
     * @return State|null
     */
    public function getStateFirst(): ?State
    {
        return $this->stateFirst;
    }

    /**
     * @param State|null $stateFirst
     *
     * @return $this
     */
    public function setStateFirst(?State $stateFirst): UserArticlesAuthors
    {
        $this->stateFirst = $stateFirst;
        return $this;
    }

    /**
     * @return State|null
     */
    public function getStateSecond(): ?State
    {
        return $this->stateSecond;
    }

    /**
     * @param State|null $stateSecond
     *
     * @return $this
     */
    public function setStateSecond(?State $stateSecond): self
    {
        $this->stateSecond = $stateSecond;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsPresented(): ?bool
    {
        return $this->isPresented;
    }

    /**
     * @param bool|null $isPresented
     *
     * @return $this
     */
    public function setIsPresented(?bool $isPresented): self
    {
        $this->isPresented = $isPresented;
        return $this;
    }

    public function getInstitutionsPrograms(): string
    {
        if (! $this->getId()) {
            return '';
        }

        $desc = '';
        if (
            $this->getProgramFirst()
            && (
                $this->getProgramFirst()->getId() !== 99999
                || ($this->getProgramFirst()->getId() === 99999 && $this->getOtherProgramFirst() != '')
            )
            && $this->getInstitutionFirst()
            && (
                $this->getInstitutionFirst()->getId() !== 99999
                || ($this->getInstitutionFirst()->getId() === 99999 && $this->getOtherInstitutionFirst() != '')
            )
        ) {
            $desc .= '(';
            $desc .= $this->getInstitutionsProgramsFirst();
            $desc .= ')';
        }

        if (
            $this->getProgramSecond()
            && (
                $this->getProgramSecond()->getId() !== 99999
                || ($this->getProgramSecond()->getId() === 99999 && $this->getOtherProgramSecond() != '')
            )
            && $this->getInstitutionSecond()
            && (
                $this->getInstitutionSecond()->getId() !== 99999
                || ($this->getInstitutionSecond()->getId() === 99999 && $this->getOtherInstitutionSecond() != '')
            )
        ) {
            if (! empty($desc)) {
                $desc .= ' - ';
            }

            $desc .= '(';
            $desc .= $this->getInstitutionsProgramsSecond();
            $desc .= ')';
        }

        return $desc;
    }

    public function getInstitutionsProgramsFirst(): string
    {
        if (! $this->getId()) {
            return '';
        }

        $desc = '';

        if (
            $this->getProgramFirst()
            && (
                $this->getProgramFirst()->getId() !== 99999
                || ($this->getProgramFirst()->getId() === 99999 && $this->getOtherProgramFirst() != '')
            )
            && $this->getInstitutionFirst()
            && (
                $this->getInstitutionFirst()->getId() !== 99999
                || ($this->getInstitutionFirst()->getId() === 99999 && $this->getOtherInstitutionFirst() != '')
            )
        ) {
            $desc .= $this->getProgramsFirst();
            $desc .= ' / ';
            $desc .= $this->getInstitutionsFirst();
        }

        return $desc;
    }

    public function getInstitutionsFirst(): string
    {
        if (! $this->getId()) {
            return '';
        }

        $desc = '';

        if (
            $this->getInstitutionFirst()
            && (
                $this->getInstitutionFirst()->getId() !== 99999
                || ($this->getInstitutionFirst()->getId() === 99999 && $this->getOtherInstitutionFirst() != '')
            )
        ) {
            if ($this->getInstitutionFirst()->getId() === 99999 && $this->getOtherInstitutionFirst() != '') {
                $desc .= $this->getOtherInstitutionFirst();
            } else {
                $desc .= $this->getInstitutionFirst()->getInitials() . ' - ' . $this->getInstitutionFirst()->getName();
            }
        }

        return $desc;
    }

    public function getProgramsFirst(): string
    {
        if (! $this->getId()) {
            return '';
        }

        $desc = '';

        if (
            $this->getProgramFirst()
            && (
                $this->getProgramFirst()->getId() !== 99999
                || ($this->getProgramFirst()->getId() === 99999 && $this->getOtherProgramFirst() != '')
            )
        ) {
            if ($this->getProgramFirst()->getId() === 99999 && $this->getOtherProgramFirst() != '') {
                $desc .= $this->getOtherProgramFirst();
            } else {
                $desc .= $this->getProgramFirst()->getName();
            }
        }

        return $desc;
    }

    public function getInstitutionsProgramsSecond(): string
    {
        if (! $this->getId()) {
            return '';
        }

        $desc = '';

        if (
            $this->getProgramSecond()
            && (
                $this->getProgramSecond()->getId() !== 99999
                || ($this->getProgramSecond()->getId() === 99999 && $this->getOtherProgramSecond() != '')
            )
            && $this->getInstitutionSecond()
            && (
                $this->getInstitutionSecond()->getId() !== 99999
                || ($this->getInstitutionSecond()->getId() === 99999 && $this->getOtherInstitutionSecond() != '')
            )
        ) {
            $desc .= $this->getProgramsSecond();
            $desc .= ' / ';
            $desc .= $this->getInstitutionsSecond();
        }

        return $desc;
    }

    public function getInstitutionsSecond(): string
    {
        if (! $this->getId()) {
            return '';
        }

        $desc = '';

        if (
            $this->getInstitutionSecond()
            && (
                $this->getInstitutionSecond()->getId() !== 99999
                || ($this->getInstitutionSecond()->getId() === 99999 && $this->getOtherInstitutionSecond() != '')
            )
        ) {
            if ($this->getInstitutionSecond()->getId() === 99999 && $this->getOtherInstitutionSecond() != '') {
                $desc .= $this->getOtherInstitutionSecond();
            } else {
                $desc .= $this->getInstitutionSecond()->getInitials() . ' - ' . $this->getInstitutionSecond()->getName();
            }
        }

        return $desc;
    }

    public function getProgramsSecond(): string
    {
        if (! $this->getId()) {
            return '';
        }

        $desc = '';

        if (
            $this->getProgramSecond()
            && (
                $this->getProgramSecond()->getId() !== 99999
                || ($this->getProgramSecond()->getId() === 99999 && $this->getOtherProgramSecond() != '')
            )
        ) {
            if ($this->getProgramSecond()->getId() === 99999 && $this->getOtherProgramSecond() != '') {
                $desc .= $this->getOtherProgramSecond();
            } else {
                $desc .= $this->getProgramSecond()->getName();
            }
        }

        return $desc;
    }
}
