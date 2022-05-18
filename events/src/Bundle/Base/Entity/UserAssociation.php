<?php


namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserAssociation
 *
 * @ORM\Table(name="user_association")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\UserAssociationRepository")
 */
class UserAssociation
{

    /**
     * 1 - Professor(a) ou pesquisador(a)                   => 3
     * 2 - Estudante                                        => 2
     * 3 - Profissionais de Administração ou áreas afins    => 4
     * 6 - Download Anais Eletrônicos (Pessoa Jurídica)     => 1
     */

    const USER_ASSOCIATIONS_TYPE_ENTERPRISE = [
        'ASSOC_TYPE_ENTERPRISE' => 1,
    ];

    const USER_ASSOCIATIONS_TYPE = [
        'ASSOC_TYPE_STUDENT' => 2,
        'ASSOC_TYPE_PROFESSOR' => 3,
        'ASSOC_TYPE_PROFESSIONAL' => 4,
    ];

    // relacionado com EditionPaymentMode::INITIALS
    const PAYMENT_MODE_INITIALS = [
        'C' => 3,
        'CD' => 3,
        'E' => 2,
        'ED' => 2,
        'A' => 3,
    ];

    /**
     *
     */
    const USER_PAYMENT_FILTER = [
        'USERLIST_PAYMENT_PAID' => 1,
        'USERLIST_PAYMENT_NOT_PAID' => 0,
    ];

    /**
     *
     */
    const USER_PAYMENT_DAYS_FILTER = [
        'USERLIST_PAYMENT_LAST_WEEK' => 7,
        'USERLIST_PAYMENT_LAST_FORTNIGHT' => 15,
        'USERLIST_PAYMENT_LAST_MONTH' => 30,
        'USERLIST_PAYMENT_LAST_QUARTER' => 90,
        'USERLIST_PAYMENT_LAST_YEAR' => 365,
    ];

    /**
     *
     */
    const USER_ASSOCIATIONS_VALUES = [
        1 => 300,
        2 => 25,
        3 => 50,
        4 => 50,
    ];

    const USER_ASSOCIATIONS_DIVISION_ADITIONAL_VALUE = [
        2 => 15,
        3 => 25,
        4 => 25,
    ];

    /**
     * associado    => 1
     * compras      => 2
     */

    const USER_ASSOCIATIONS_LEVEL = [
        'USER_ASSOCIATIONS_LEVEL_UNDEF' => 0,
        'USER_ASSOCIATIONS_LEVEL_REGISTER' => 1,
        'USER_ASSOCIATIONS_LEVEL_DOWNLOAD' => 2,
        'USER_ASSOCIATIONS_LEVEL_OTHER' => 3,
        'USER_ASSOCIATIONS_LEVEL_SPEAKER' => 4,
        'USER_ASSOCIATIONS_LEVEL_INACTIVE' => 5,
    ];

    /**
     *
     */
    const USER_ASSOCIATIONS_STATUS_PAY = 1;

    /**
     *
     */
    const USER_ASSOCIATIONS_STATUS_NOT_PAY = 0;

    /**
     * NÚMERO DO CONVÊNIO E-COMMERCE BB
     */
    const USER_ASSOCIATIONS_ECOMMERCE_NUMBER = '319054';

    /**
     * NÚMERO DO CONVÊNIO COBRANÇA BB
     */
    const USER_ASSOCIATIONS_AGREEMENT_NUMBER = '3140678';

    /**
     * DIAS PARA VENCIMENTO DO BOLETO
     */
    const USER_ASSOCIATIONS_NUMBER_DAYS = '1';


    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="associations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var int|null
     * @Assert\NotBlank()
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var Institution
     * @ORM\ManyToOne(targetEntity="Institution", inversedBy="associations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="institution_id", referencedColumnName="id")
     * })
     */
    private $institution;

    /**
     * @var string|null
     *
     * @ORM\Column(name="other_institution", type="string", nullable=true)
     */
    private $otherInstitution;

    /**
     * @var Program
     * @ORM\ManyToOne(targetEntity="Program", inversedBy="associations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="program_id", referencedColumnName="id")
     * })
     */
    private $program;

    /**
     * @var string|null
     *
     * @ORM\Column(name="other_program", type="string", nullable=true)
     */
    private $otherProgram;

    /**
     * @var Division
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Division", inversedBy="userAssociation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="division_id", referencedColumnName="id")
     * })
     */
    private $division;

    /**
     * @var Collection|Division
     *
     * @ORM\ManyToMany(targetEntity="Division")
     * @ORM\JoinTable(
     *     name="user_association_divisions",
     *     joinColumns={@ORM\JoinColumn(name="user_association_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="division_id", referencedColumnName="id")
     * })
     */
    private $aditionals;

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
     * @ORM\Column(name="expired_at", type="datetime", nullable=true)
     */
    private $expiredAt;

    /**
     * @var \DateTime*
     * @ORM\Column(name="last_pay", type="datetime", nullable=true)
     */
    private $lastPay;

    /**
     * @var int|null $level
     *
     * @ORM\Column(name="level", type="integer")
     */
    private $level;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * @var int|null $statusPay
     *
     * @ORM\Column(name="status_pay", type="integer")
     */
    private $statusPay;

    /**
     * UserAssociation constructor.
     */
    public function __construct()
    {
        $this->aditionals = new ArrayCollection();
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
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return UserAssociation
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return UserAssociation
     */
    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Institution|null
     */
    public function getInstitution(): ?Institution
    {
        return $this->institution;
    }

    /**
     * @param Institution $institution
     *
     * @return UserAssociation
     */
    public function setInstitution(Institution $institution): self
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * @return Program|null
     */
    public function getProgram(): ?Program
    {
        return $this->program;
    }

    /**
     * @param Program $program
     *
     * @return UserAssociation
     */
    public function setProgram(Program $program): self
    {
        $this->program = $program;

        return $this;
    }

    /**
     * @return Division
     */
    public function getDivision(): ?Division
    {
        return $this->division;
    }

    /**
     * @param Division $division
     *
     * @return UserAssociation
     */
    public function setDivision(Division $division): self
    {
        $this->division = $division;

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
     * @param \DateTime|null $createdAtAt
     *
     * @return UserAssociation
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
     * @return UserAssociation
     */
    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpiredAt(): ?\DateTime
    {
        return $this->expiredAt;
    }

    /**
     * @param \DateTime|null $expiredAt
     *
     * @return UserAssociation
     */
    public function setExpiredAt(?\DateTime $expiredAt): UserAssociation
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastPay(): ?\DateTime
    {
        return $this->lastPay;
    }

    /**
     * @param \DateTime|null $lastPay
     *
     * @return UserAssociation
     */
    public function setLastPay(?\DateTime $lastPay): UserAssociation
    {
        $this->lastPay = $lastPay;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatusPay(): ?int
    {
        return $this->statusPay;
    }

    /**
     * @param int $statusPay
     *
     * @return UserAssociation
     */
    public function setStatusPay(?int $statusPay): UserAssociation
    {
        $this->statusPay = $statusPay;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLevel(): ?int
    {
        return $this->level;
    }

    /**
     * @param int $level
     *
     * @return UserAssociation|null
     */
    public function setLevel(?int $level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(?float $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return false|int|string
     */
    public function getLevelString()
    {
        return array_search($this->level, self::USER_ASSOCIATIONS_LEVEL);
    }

    /**
     * @return false|int|string
     */
    public function getTypeString()
    {
        return array_search($this->type, self::USER_ASSOCIATIONS_TYPE) ??
            array_search($this->type, self::USER_ASSOCIATIONS_TYPE_ENTERPRISE);
    }

    /**
     * @return string
     */
    public function getValueString()
    {
        if (! isset(self::USER_ASSOCIATIONS_VALUES[$this->type])) {
            return 0;
        }

        return self::USER_ASSOCIATIONS_VALUES[$this->type];
    }

    /**
     * @return Collection|Division
     */
    public function getAditionals(): Collection
    {
        return $this->aditionals;
    }

    /**
     * @param Division $aditional
     *
     * @return $this
     */
    public function addAditional(Division $aditional): self
    {
        if (! $this->aditionals->contains($aditional)) {
            $this->aditionals[] = $aditional;
        }

        return $this;
    }

    /**
     * @param Division $aditional
     *
     * @return $this
     */
    public function removeAditional(Division $aditional): self
    {
        $this->aditionals->removeElement($aditional);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getOtherInstitution(): ?string
    {
        return $this->otherInstitution;
    }

    /**
     * @param string|null $otherInstitution
     *
     * @return UserAssociation
     */
    public function setOtherInstitution(?string $otherInstitution): UserAssociation
    {
        $this->otherInstitution = $otherInstitution;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOtherProgram(): ?string
    {
        return $this->otherProgram;
    }

    /**
     * @param string|null $otherProgram
     *
     * @return UserAssociation
     */
    public function setOtherProgram(?string $otherProgram): UserAssociation
    {
        $this->otherProgram = $otherProgram;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->id;
    }

    public function getInstitutions(): string
    {
        if (! $this->getId()) {
            return '';
        }

        $desc = '';

        if (
            $this->getInstitution()
            && (
                $this->getInstitution()->getId() !== 99999
                || ($this->getInstitution()->getId() === 99999 && $this->getOtherInstitution() != '')
            )
        ) {
            if ($this->getInstitution()->getId() === 99999 && $this->getOtherInstitution() != '') {
                $desc .= $this->getOtherInstitution();
            } else {
                $desc .= $this->getInstitution()->getInitials() . ' - ' . $this->getInstitution()->getName();
            }
        }

        return $desc;
    }

    public function getPrograms(): string
    {
        if (! $this->getId()) {
            return '';
        }

        $desc = '';

        if (
            $this->getProgram()
            && (
                $this->getProgram()->getId() !== 99999
                || ($this->getProgram()->getId() === 99999 && $this->getOtherProgram() != '')
            )
        ) {
            if ($this->getProgram()->getId() === 99999 && $this->getOtherProgram() != '') {
                $desc .= $this->getOtherProgram();
            } else {
                $desc .= $this->getProgram()->getName();
            }
        }

        return $desc;
    }
}
