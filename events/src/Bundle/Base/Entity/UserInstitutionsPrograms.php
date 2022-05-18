<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserInstitutionsPrograms
 *
 * @ORM\Table(name="user_institutions_programs")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\UserInstitutionsProgramsRepository")
 */
class UserInstitutionsPrograms
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
     * @ORM\Column(name="link", type="date", nullable=true)
     */
    private $link = null;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="unlink", type="date", nullable=true)
     */
    private $unlink = null;

    /**
     * @var State
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="state_first_id", referencedColumnName="id")
     * })
     */
    private $stateFirstId;

    /**
     * @var State
     *
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="state_second_id", referencedColumnName="id")
     * })
     */
    private $stateSecondId;

    /**
     * @var Institution
     *
     * @ORM\ManyToOne(targetEntity="Institution")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="institution_first_id", referencedColumnName="id")
     * })
     */
    private $institutionFirstId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="other_institution_first", type="string", nullable=true)
     */
    private $otherInstitutionFirst;

    /**
     * @var Institution
     *
     * @ORM\ManyToOne(targetEntity="Institution")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="institution_second_id", referencedColumnName="id")
     * })
     */
    private $institutionSecondId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="other_institution_second", type="string", nullable=true)
     */
    private $otherInstitutionSecond;

    /**
     * @var Program
     *
     * @ORM\ManyToOne(targetEntity="Program")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="program_first_id", referencedColumnName="id")
     * })
     */
    private $programFirstId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="other_program_first", type="string", nullable=true)
     */
    private $otherProgramFirst;

    /**
     * @var Program
     *
     * @ORM\ManyToOne(targetEntity="Program")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="program_second_id", referencedColumnName="id")
     * })
     */
    private $programSecondId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="other_program_second", type="string", nullable=true)
     */
    private $otherProgramSecond;

    /**
     * @var User|null
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="institutionsPrograms")
     */
    private $user;

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
     * @return \DateTimeInterface|null
     */
    public function getLink(): ?\DateTimeInterface
    {
        return $this->link;
    }

    /**
     * @param \DateTimeInterface|null $link
     *
     * @return $this
     */
    public function setLink(?\DateTimeInterface $link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUnlink(): ?\DateTimeInterface
    {
        return $this->unlink;
    }

    /**
     * @param \DateTimeInterface|null $unlink
     *
     * @return $this
     */
    public function setUnlink(?\DateTimeInterface $unlink): self
    {
        $this->unlink = $unlink;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     *
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Institution|null
     */
    public function getInstitutionFirstId(): ?Institution
    {
        return $this->institutionFirstId;
    }

    /**
     * @param Institution|null $institutionFirstId
     *
     * @return $this
     */
    public function setInstitutionFirstId(?Institution $institutionFirstId): self
    {
        $this->institutionFirstId = $institutionFirstId;

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
     * @return $this
     */
    public function setOtherInstitutionFirst(?string $otherInstitutionFirst): UserInstitutionsPrograms
    {
        $this->otherInstitutionFirst = $otherInstitutionFirst;
        return $this;
    }

    /**
     * @return Institution|null
     */
    public function getInstitutionSecondId(): ?Institution
    {
        return $this->institutionSecondId;
    }

    /**
     * @param Institution|null $institutionSecondId
     *
     * @return $this
     */
    public function setInstitutionSecondId(?Institution $institutionSecondId): self
    {
        $this->institutionSecondId = $institutionSecondId;

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
     * @return $this
     */
    public function setOtherInstitutionSecond(?string $otherInstitutionSecond): UserInstitutionsPrograms
    {
        $this->otherInstitutionSecond = $otherInstitutionSecond;
        return $this;
    }

    /**
     * @return Program|null
     */
    public function getProgramFirstId(): ?Program
    {
        return $this->programFirstId;
    }

    /**
     * @param Program|null $programFirstId
     *
     * @return $this
     */
    public function setProgramFirstId(?Program $programFirstId): self
    {
        $this->programFirstId = $programFirstId;

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
     * @return $this
     */
    public function setOtherProgramFirst(?string $otherProgramFirst): UserInstitutionsPrograms
    {
        $this->otherProgramFirst = $otherProgramFirst;
        return $this;
    }

    /**
     * @return Program|null
     */
    public function getProgramSecondId(): ?Program
    {
        return $this->programSecondId;
    }

    /**
     * @param Program|null $programSecondId
     *
     * @return $this
     */
    public function setProgramSecondId(?Program $programSecondId): self
    {
        $this->programSecondId = $programSecondId;

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
     * @return $this
     */
    public function setOtherProgramSecond(?string $otherProgramSecond): UserInstitutionsPrograms
    {
        $this->otherProgramSecond = $otherProgramSecond;
        return $this;
    }

    /**
     * @return State|null
     */
    public function getStateFirstId(): ?State
    {
        return $this->stateFirstId;
    }

    /**
     * @param State|null $stateFirstId
     *
     * @return $this
     */
    public function setStateFirstId(?State $stateFirstId): self
    {
        $this->stateFirstId = $stateFirstId;

        return $this;
    }

    /**
     * @return State|null
     */
    public function getStateSecondId(): ?State
    {
        return $this->stateSecondId;
    }

    /**
     * @param State|null $stateSecondId
     *
     * @return $this
     */
    public function setStateSecondId(?State $stateSecondId): self
    {
        $this->stateSecondId = $stateSecondId;

        return $this;
    }

    public function __toString(): string
    {
        if (! $this->getId()) {
            return '';
        }

        $desc = '';
        if (
            $this->getProgramFirstId()
            && (
                $this->getProgramFirstId()->getId() !== 99999
                || ($this->getProgramFirstId()->getId() === 99999 && $this->getOtherProgramFirst() != '')
            )
            && $this->getInstitutionFirstId()
            && (
                $this->getInstitutionFirstId()->getId() !== 99999
                || ($this->getInstitutionFirstId()->getId() === 99999 && $this->getOtherInstitutionFirst() != '')
            )
        ) {
            $desc .= '(';
            $desc .= $this->getInstitutionsProgramsFirst();
            $desc .= ')';
        }

        if (
            $this->getProgramSecondId()
            && (
                $this->getProgramSecondId()->getId() !== 99999
                || ($this->getProgramSecondId()->getId() === 99999 && $this->getOtherProgramSecond() != '')
            )
            && $this->getInstitutionSecondId()
            && (
                $this->getInstitutionSecondId()->getId() !== 99999
                || ($this->getInstitutionSecondId()->getId() === 99999 && $this->getOtherInstitutionSecond() != '')
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
            $this->getProgramFirstId()
            && (
                $this->getProgramFirstId()->getId() !== 99999
                || ($this->getProgramFirstId()->getId() === 99999 && $this->getOtherProgramFirst() != '')
            )
            && $this->getInstitutionFirstId()
            && (
                $this->getInstitutionFirstId()->getId() !== 99999
                || ($this->getInstitutionFirstId()->getId() === 99999 && $this->getOtherInstitutionFirst() != '')
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
            $this->getInstitutionFirstId()
            && (
                $this->getInstitutionFirstId()->getId() !== 99999
                || ($this->getInstitutionFirstId()->getId() === 99999 && $this->getOtherInstitutionFirst() != '')
            )
        ) {
            if ($this->getInstitutionFirstId()->getId() === 99999 && $this->getOtherInstitutionFirst() != '') {
                $desc .= $this->getOtherInstitutionFirst();
            } else {
                $desc .= $this->getInstitutionFirstId()->getInitials() . ' - ' . $this->getInstitutionFirstId()->getName();
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
            $this->getProgramFirstId()
            && (
                $this->getProgramFirstId()->getId() !== 99999
                || ($this->getProgramFirstId()->getId() === 99999 && $this->getOtherProgramFirst() != '')
            )
        ) {
            if ($this->getProgramFirstId()->getId() === 99999 && $this->getOtherProgramFirst() != '') {
                $desc .= $this->getOtherProgramFirst();
            } else {
                $desc .= $this->getProgramFirstId()->getName();
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
            $this->getProgramSecondId()
            && (
                $this->getProgramSecondId()->getId() !== 99999
                || ($this->getProgramSecondId()->getId() === 99999 && $this->getOtherProgramSecond() != '')
            )
            && $this->getInstitutionSecondId()
            && (
                $this->getInstitutionSecondId()->getId() !== 99999
                || ($this->getInstitutionSecondId()->getId() === 99999 && $this->getOtherInstitutionSecond() != '')
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
            $this->getInstitutionSecondId()
            && (
                $this->getInstitutionSecondId()->getId() !== 99999
                || ($this->getInstitutionSecondId()->getId() === 99999 && $this->getOtherInstitutionSecond() != '')
            )
        ) {
            if ($this->getInstitutionSecondId()->getId() === 99999 && $this->getOtherInstitutionSecond() != '') {
                $desc .= $this->getOtherInstitutionSecond();
            } else {
                $desc .= $this->getInstitutionSecondId()->getInitials() . ' - ' . $this->getInstitutionSecondId()->getName();
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
            $this->getProgramSecondId()
            && (
                $this->getProgramSecondId()->getId() !== 99999
                || ($this->getProgramSecondId()->getId() === 99999 && $this->getOtherProgramSecond() != '')
            )
        ) {
            if ($this->getProgramSecondId()->getId() === 99999 && $this->getOtherProgramSecond() != '') {
                $desc .= $this->getOtherProgramSecond();
            } else {
                $desc .= $this->getProgramSecondId()->getName();
            }
        }

        return $desc;
    }
}
