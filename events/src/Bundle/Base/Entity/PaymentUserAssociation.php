<?php

namespace App\Bundle\Base\Entity;

use App\Bundle\Base\Repository\PaymentUserAssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentUserAssociationRepository::class)
 */
class PaymentUserAssociation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    private $filename;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="integer")
     */
    private $errors;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=PaymentUserAssociationDetails::class, mappedBy="paymentUserAssociation")
     */
    private $paymentUserAssociationDetails;

    /**
     * PaymentUserAssociation constructor.
     */
    public function __construct()
    {
        $this->paymentUserAssociationDetails = new ArrayCollection();
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
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     *
     * @return $this
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     *
     * @return $this
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getErrors(): ?int
    {
        return $this->errors;
    }

    /**
     * @param int $errors
     *
     * @return $this
     */
    public function setErrors(int $errors): self
    {
        $this->errors = $errors;

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
    public function setCreatedAt(\DateTimeInterface $createdAt): self
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
     * @param \DateTimeInterface|null $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|PaymentUserAssociationDetails[]
     */
    public function getPaymentUserAssociationDetailss(): Collection
    {
        return $this->paymentUserAssociationDetails;
    }

    /**
     * @param PaymentUserAssociationDetails $settlement
     *
     * @return $this
     */
    public function addPaymentUserAssociationDetails(PaymentUserAssociationDetails $settlement): self
    {
        if (!$this->paymentUserAssociationDetails->contains($settlement)) {
            $this->paymentUserAssociationDetails[] = $settlement;
            $settlement->setPaymentUserAssociation($this);
        }

        return $this;
    }

    /**
     * @param PaymentUserAssociationDetails $settlement
     *
     * @return $this
     */
    public function removePaymentUserAssociationDetails(PaymentUserAssociationDetails $settlement): self
    {
        if ($this->paymentUserAssociationDetails->contains($settlement)) {
            $this->paymentUserAssociationDetails->removeElement($settlement);
            // set the owning side to null (unless already changed)
            if ($settlement->getPaymentUserAssociation() === $this) {
                $settlement->setPaymentUserAssociation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PaymentUserAssociationDetails[]
     */
    public function getPaymentUserAssociationDetails(): Collection
    {
        return $this->paymentUserAssociationDetails;
    }

    /**
     * @param PaymentUserAssociationDetails $paymentUserAssociationDetail
     *
     * @return $this
     */
    public function addPaymentUserAssociationDetail(PaymentUserAssociationDetails $paymentUserAssociationDetail): self
    {
        if (!$this->paymentUserAssociationDetails->contains($paymentUserAssociationDetail)) {
            $this->paymentUserAssociationDetails[] = $paymentUserAssociationDetail;
            $paymentUserAssociationDetail->setPaymentUserAssociation($this);
        }

        return $this;
    }

    /**
     * @param PaymentUserAssociationDetails $paymentUserAssociationDetail
     *
     * @return $this
     */
    public function removePaymentUserAssociationDetail(PaymentUserAssociationDetails $paymentUserAssociationDetail): self
    {
        if ($this->paymentUserAssociationDetails->removeElement($paymentUserAssociationDetail)) {
            // set the owning side to null (unless already changed)
            if ($paymentUserAssociationDetail->getPaymentUserAssociation() === $this) {
                $paymentUserAssociationDetail->setPaymentUserAssociation(null);
            }
        }

        return $this;
    }
}
