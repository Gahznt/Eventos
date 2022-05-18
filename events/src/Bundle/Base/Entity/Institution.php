<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Institution
 *
 * @ORM\Table(name="institution")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\InstitutionRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Institution
{
    const STATUS_INACTIVE = 0;

    const STATUS_ACTIVE = 1;

    const INSTITUTION_STATUS = [
        'STATUS_INACTIVE' => self::STATUS_INACTIVE,
        'STATUS_ACTIVE' => self::STATUS_ACTIVE,
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
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=200, nullable=false, options={"comment"="prirmaria ou secundaria definida em código"})
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="initials", type="string", length=50, nullable=true)
     */
    private $initials;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="type", type="boolean", nullable=false)
     */
    private $type;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="paid", type="boolean", nullable=false)
     */
    private $paid;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="status", type="boolean", nullable=false, options={"default"="1"})
     */
    private $status = true;

    /**
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private ?string $phone = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cellphone", type="string", nullable=true)
     */
    private $cellphone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=150, nullable=false)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="website", type="string", length=150, nullable=false)
     */
    private $website;

    /**
     * @var string|null
     *
     * @ORM\Column(name="street", type="string", length=150, nullable=false)
     */
    private $street;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zipcode", type="string", length=9, nullable=false)
     */
    private $zipcode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="number", type="integer", nullable=false)
     */
    private $number;

    /**
     * @var string|null
     *
     * @ORM\Column(name="complement", type="string", length=45, nullable=true)
     */
    private $complement;

    /**
     * @var string|null
     *
     * @ORM\Column(name="neighborhood", type="string", length=15, nullable=false)
     */
    private $neighborhood;

    /**
     * @var string|null
     *
     * @ORM\Column(name="coordinator", type="string", length=150, nullable=false)
     */
    private $coordinator;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sort_position", type="integer", nullable=false, options={"default"="1","unsigned"=true})
     */
    private $sortPosition = '1';

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
     * @var City|null
     *
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * })
     */
    private $city;

    /**
     * @var Collection|UserAssociation[]
     *
     * @ORM\OneToMany(targetEntity="UserAssociation", mappedBy="institution")
     */
    private $associations;

    /**
     * @var Collection|Program[]
     *
     * @ORM\OneToMany(targetEntity=Program::class, mappedBy="institution", cascade={"persist"})
     */
    private $programs;

    /**
     * Institution constructor.
     */
    public function __construct()
    {
        $this->associations = new ArrayCollection();
        $this->programs = new ArrayCollection();
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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getType(): ?bool
    {
        return $this->type;
    }

    /**
     * @param bool $type
     *
     * @return $this
     */
    public function setType(bool $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getPaid(): ?bool
    {
        return $this->paid;
    }

    /**
     * @param bool $paid
     *
     * @return $this
     */
    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     *
     * @return $this
     */
    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPhone(): ?int
    {
        return $this->phone;
    }

    /**
     * @param int|null $phone
     *
     * @return $this
     */
    public function setPhone(?int $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getCellphone(): ?int
    {
        return $this->cellphone;
    }

    /**
     * @param int|null $cellphone
     *
     * @return $this
     */
    public function setCellphone(?int $cellphone): self
    {
        $this->cellphone = $cellphone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param string $website
     *
     * @return $this
     */
    public function setWebsite(string $website): self
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string $street
     *
     * @return $this
     */
    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    /**
     * @param string $zipcode
     *
     * @return $this
     */
    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * @param int|null $number
     *
     * @return $this
     */
    public function setNumber(?int $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getComplement(): ?string
    {
        return $this->complement;
    }

    /**
     * @param string|null $complement
     *
     * @return $this
     */
    public function setComplement(?string $complement): self
    {
        $this->complement = $complement;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNeighborhood(): ?string
    {
        return $this->neighborhood;
    }

    /**
     * @param string $neighborhood
     *
     * @return $this
     */
    public function setNeighborhood(string $neighborhood): self
    {
        $this->neighborhood = $neighborhood;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCoordinator(): ?string
    {
        return $this->coordinator;
    }

    /**
     * @param string $coordinator
     *
     * @return $this
     */
    public function setCoordinator(string $coordinator): self
    {
        $this->coordinator = $coordinator;

        return $this;
    }

    /**
     * @return Collection|UserAssociation[]
     */
    public function getAssociations(): Collection
    {
        return $this->associations;
    }


    /**
     * @return City|null
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param City|null $city
     *
     * @return $this
     */
    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param UserAssociation $association
     *
     * @return $this
     */
    public function addAssociation(UserAssociation $association): self
    {
        if (! $this->associations->contains($association)) {
            $this->associations[] = $association;
            $association->setInstitution($this);
        }

        return $this;
    }

    /**
     * @param UserAssociation $association
     *
     * @return $this
     */
    public function removeAssociation(UserAssociation $association): self
    {
        if ($this->associations->contains($association)) {
            $this->associations->removeElement($association);
            // set the owning side to null (unless already changed)
            if ($association->getInstitution() === $this) {
                $association->setInstitution(null);
            }
        }

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSortPosition(): ?int
    {
        return $this->sortPosition;
    }

    /**
     * @param int|null $sortPosition
     *
     * @return Institution
     */
    public function setSortPosition(?int $sortPosition): Institution
    {
        $this->sortPosition = $sortPosition;
        return $this;
    }

    /**
     * @return string
     */
    public function getInitials(): ?string
    {
        return $this->initials;
    }

    /**
     * @param string $initials
     *
     * @return Institution
     */
    public function setInitials(string $initials): Institution
    {
        $this->initials = $initials;
        return $this;
    }

    /**
     * @return Collection|Program[]
     */
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    public function addProgram(Program $program): self
    {
        if (! $this->programs->contains($program)) {
            $this->programs[] = $program;
            $program->setInstitution($this);
        }

        return $this;
    }

    public function removeProgram(Program $program): self
    {
        if ($this->programs->removeElement($program)) {
            // set the owning side to null (unless already changed)
            if ($program->getInstitution() === $this) {
                $program->setInstitution(null);
            }
        }

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTimeInterface $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }
}
