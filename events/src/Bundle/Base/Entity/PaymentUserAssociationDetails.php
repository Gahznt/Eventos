<?php

namespace App\Bundle\Base\Entity;

use App\Repository\Bundle\Base\Entity\PaymentUserAssociationDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentUserAssociationDetailsRepository::class)
 */
class PaymentUserAssociationDetails
{
    const PAYMENT_USER_ASSOCIATION_DETAILS_STATUS_PAID = 1;
    const PAYMENT_USER_ASSOCIATION_DETAILS_STATUS_DISREGARDED = 2;
    const PAYMENT_USER_ASSOCIATION_DETAILS_STATUS_VALUE = 3;
    const PAYMENT_USER_ASSOCIATION_DETAILS_STATUS_DATE = 4;
    const PAYMENT_USER_ASSOCIATION_DETAILS_STATUS_NOT_FOUND = 4;

    const PAYMENT_USER_ASSOCIATION_DETAILS_MESSAGE_SETTLED = 'Boleto já foi quitado anteriormente';
    const PAYMENT_USER_ASSOCIATION_DETAILS_ERROR_SETTLED = 'Boleto já foi quitado anteriormente';
    const PAYMENT_USER_ASSOCIATION_DETAILS_ERROR_VALUE = 'Valor do boleto é diferente';
    const PAYMENT_USER_ASSOCIATION_DETAILS_ERROR_DATE = 'Data de pagamente e vencimento incompatíveis';
    const PAYMENT_USER_ASSOCIATION_DETAILS_ERROR_NOT_FOUND = 'Associação de Usuário não encontrada';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PaymentUserAssociation::class, inversedBy="paymentUserAssociationDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $paymentUserAssociation;

    /**
     * @ORM\Column(type="string")
     */
    private $bankSlip;

    /**
     * @ORM\Column(type="float")
     */
    private $bankSlipAmount;

    /**
     * @ORM\Column(type="float")
     */
    private $feeAmount;

    /**
     * @ORM\Column(type="float")
     */
    private $netAmount;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\Column(type="string")
     */
    private $note;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $operation;

    /**
     * @ORM\Column(type="date")
     */
    private $payday;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dueDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

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
     * @return PaymentUserAssociation|null
     */
    public function getPaymentUserAssociation(): ?PaymentUserAssociation
    {
        return $this->paymentUserAssociation;
    }

    /**
     * @param PaymentUserAssociation|null $paymentUserAssociation
     *
     * @return $this
     */
    public function setPaymentUserAssociation(?PaymentUserAssociation $paymentUserAssociation): self
    {
        $this->paymentUserAssociation = $paymentUserAssociation;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBankSlip(): ?string
    {
        return $this->bankSlip;
    }

    /**
     * @param string $bankSlip
     *
     * @return $this
     */
    public function setBankSlip(string $bankSlip): self
    {
        $this->bankSlip = $bankSlip;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getBankSlipAmount(): ?float
    {
        return $this->bankSlipAmount;
    }

    /**
     * @param float $bankSlipAmount
     *
     * @return $this
     */
    public function setBankSlipAmount(float $bankSlipAmount): self
    {
        $this->bankSlipAmount = $bankSlipAmount;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getFeeAmount(): ?float
    {
        return $this->feeAmount;
    }

    /**
     * @param float $feeAmount
     *
     * @return $this
     */
    public function setFeeAmount(float $feeAmount): self
    {
        $this->feeAmount = $feeAmount;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getNetAmount(): ?float
    {
        return $this->netAmount;
    }

    /**
     * @param float $netAmount
     *
     * @return $this
     */
    public function setNetAmount(float $netAmount): self
    {
        $this->netAmount = $netAmount;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string $note
     *
     * @return $this
     */
    public function setNote(string $note): self
    {
        $this->note = $note;

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
     * @return string|null
     */
    public function getOperation(): ?string
    {
        return $this->operation;
    }

    /**
     * @param string $operation
     *
     * @return $this
     */
    public function setOperation(string $operation): self
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getPayday(): ?\DateTimeInterface
    {
        return $this->payday;
    }

    /**
     * @param \DateTimeInterface $payday
     *
     * @return $this
     */
    public function setPayday(\DateTimeInterface $payday): self
    {
        $this->payday = $payday;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    /**
     * @param \DateTimeInterface|null $dueDate
     *
     * @return $this
     */
    public function setDueDate(?\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

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
}
