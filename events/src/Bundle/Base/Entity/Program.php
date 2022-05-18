<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 *
 * @UniqueEntity(
 *     fields={"email", "status"},
 *     errorPath="email",
 *     message="E-mail já cadastrado"
 * )
 *
 * @ORM\Table(name="program")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\ProgramRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Program
{
    const PROGRAM_STATUS_DISABLED = 0;

    const PROGRAM_STATUS_ENABLED = 1;

    const PROGRAM_STATUS = [
        'Inactive' => self::PROGRAM_STATUS_DISABLED,
        'Active' => self::PROGRAM_STATUS_ENABLED,
    ];

    /**
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(name="paid", type="boolean", nullable=true)
     */
    private ?bool $paid = null;

    /**
     * @ORM\Column(name="status", type="smallint", nullable=true, options={"default"="1"})
     */
    private ?int $status = self::PROGRAM_STATUS_ENABLED;

    /**
     * @ORM\Column(name="sort_position", type="integer", nullable=true, options={"default"="1","unsigned"=true})
     */
    private ?int $sortPosition = 1;

    /**
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private ?string $phone = null;

    /**
     * @ORM\Column(name="cellphone", type="string", nullable=true)
     */
    private ?string $cellphone = null;

    /**
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(name="website", type="string", nullable=true)
     */
    private ?string $website = null;

    /**
     * @ORM\Column(name="street", type="string", nullable=true)
     */
    private ?string $street = null;

    /**
     * @ORM\Column(name="zipcode", type="string", nullable=true)
     */
    private ?string $zipcode = null;

    /**
     * @ORM\Column(name="number", type="string", nullable=true)
     */
    private ?string $number = null;

    /**
     * @ORM\Column(name="complement", type="string", nullable=true)
     */
    private ?string $complement = null;

    /**
     * @ORM\Column(name="neighborhood", type="string", nullable=true)
     */
    private ?string $neighborhood = null;

    /**
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    private ?City $city = null;

    /**
     * @ORM\Column(name="coordinator", type="string", nullable=true)
     */
    private ?string $coordinator = null;

    /**
     * @ORM\ManyToOne(targetEntity="Institution", inversedBy="programs", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="institution_id", referencedColumnName="id")
     * })
     */
    private ?Institution $institution = null;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private ?\DateTime $createdAt = null;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private ?\DateTime $updatedAt = null;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private ?\DateTime $deletedAt = null;

    /**
     * @ORM\OneToMany(targetEntity="UserAssociation", mappedBy="program")
     */
    private ?Collection $associations = null;

    /**
     * @ORM\OneToOne(targetEntity="User", inversedBy="associatedProgram")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private ?User $user = null;

    public function __construct()
    {
        $this->associations = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(?bool $paid): self
    {
        $this->paid = $paid;
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

    public function getSortPosition(): ?int
    {
        return $this->sortPosition;
    }

    public function setSortPosition(?int $sortPosition): self
    {
        $this->sortPosition = $sortPosition;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getCellphone(): ?string
    {
        return $this->cellphone;
    }

    public function setCellphone(?string $cellphone): self
    {
        $this->cellphone = $cellphone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;
        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function setComplement(?string $complement): self
    {
        $this->complement = $complement;
        return $this;
    }

    public function getNeighborhood(): ?string
    {
        return $this->neighborhood;
    }

    public function setNeighborhood(?string $neighborhood): self
    {
        $this->neighborhood = $neighborhood;
        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getCoordinator(): ?string
    {
        return $this->coordinator;
    }

    public function setCoordinator(?string $coordinator): self
    {
        $this->coordinator = $coordinator;
        return $this;
    }

    public function getInstitution(): ?Institution
    {
        return $this->institution;
    }

    public function setInstitution(?Institution $institution): self
    {
        $this->institution = $institution;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
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

    public function getAssociations(): ?Collection
    {
        return $this->associations;
    }

    public function addAssociation(UserAssociation $association): self
    {
        if (! $this->associations->contains($association)) {
            $this->associations[] = $association;
            $association->setProgram($this);
        }

        return $this;
    }

    public function removeAssociation(UserAssociation $association): self
    {
        if ($this->associations->contains($association)) {
            $this->associations->removeElement($association);
            // set the owning side to null (unless already changed)
            if ($association->getProgram() === $this) {
                $association->setProgram(null);
            }
        }

        return $this;
    }

    public function __toString(): ?string
    {
        return $this->name;
    }
}
