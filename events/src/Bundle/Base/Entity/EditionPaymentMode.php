<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * EditionPaymentMode
 *
 * @ORM\Table(name="edition_payment_mode")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\EditionPaymentModeRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class EditionPaymentMode
{
    const TYPE_NOT_ASSOCIATED = 1;
    const TYPE_ASSOCIATED = 2;
    const TYPE_ALL = 3;

    const TYPES = [
        'Não Associados' => self::TYPE_NOT_ASSOCIATED,
        'Associados' => self::TYPE_ASSOCIATED,
        'Todos' => self::TYPE_ALL,
    ];

    // relacionado com UserAssociation::PAYMENT_MODE_INITIALS
    const INITIALS = [
        'Congressista' => 'C',
        'Congressita Associado' => 'CD',
        'Estudante' => 'E',
        'Estudante Associado' => 'ED',
        'Aposentando' => 'A',
        'Ouvinte' => 'O',
        'Líder de Tema' => 'L'
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
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name = '';

    /**
     * @var float|null
     *
     * @ORM\Column(name="value", type="float", precision=12, scale=2, nullable=false, options={"default"="0.00"})
     */
    private $value = '0.00';

    /**
     * @var int|null
     *
     * @ORM\Column(name="type", type="smallint", nullable=true, options={"default"="3"})
     */
    private $type = '3';

    /**
     * @var string|null
     *
     * @ORM\Column(name="initials", type="string", length=20, nullable=true, options={"default"="C"})
     */
    private $initials = 'C';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="has_free_individual_association", type="boolean", nullable=true, options={"default"="1"})
     */
    private $hasFreeIndividualAssociation = true;

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
     * @var Edition|null
     *
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="editionPaymentModes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     * })
     */
    private $edition;

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
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return EditionPaymentMode
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getValue(): ?float
    {
        return $this->value;
    }

    /**
     * @param float|null $value
     *
     * @return EditionPaymentMode
     */
    public function setValue(?float $value): self
    {
        $this->value = $value;

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
     * @param int|null $type
     *
     * @return EditionPaymentMode
     */
    public function setType(?int $type): EditionPaymentMode
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInitials(): ?string
    {
        return $this->initials;
    }

    /**
     * @param string|null $initials
     *
     * @return EditionPaymentMode
     */
    public function setInitials(?string $initials): EditionPaymentMode
    {
        $this->initials = $initials;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getHasFreeIndividualAssociation(): ?bool
    {
        return $this->hasFreeIndividualAssociation;
    }

    /**
     * @param bool|null $hasFreeIndividualAssociation
     *
     * @return EditionPaymentMode
     */
    public function setHasFreeIndividualAssociation(?bool $hasFreeIndividualAssociation): EditionPaymentMode
    {
        $this->hasFreeIndividualAssociation = $hasFreeIndividualAssociation;
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
     * @param \DateTime|null $createdAt
     *
     * @return EditionPaymentMode
     */
    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime|null $deletedAt
     *
     * @return EditionPaymentMode
     */
    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime|null $updatedAt
     *
     * @return EditionPaymentMode
     */
    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Edition|null
     */
    public function getEdition(): ?Edition
    {
        return $this->edition;
    }

    /**
     * @param Edition|null $edition
     *
     * @return $this
     */
    public function setEdition(?Edition $edition): self
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
