<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * EditionDiscount
 *
 * @UniqueEntity(
 *     fields={"edition", "userIdentifier", "isActive"},
 *     errorPath="userIdentifier",
 *     message="Desconto já concedido para este Identificador"
 * )
 *
 * @ORM\Table(name="edition_discount")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\EditionDiscountRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class EditionDiscount
{
    const TYPE_DIRECTOR = 1;
    const TYPE_DIVISION_COORDINATOR = 2;
    const TYPE_THEME_LEADER = 3;
    const TYPE_MAGAZINE_EDITOR = 4;
    const TYPE_AGENT = 5;
    const TYPE_VOLUNTARY = 6;
    const TYPE_STUDENT = 7;
    const TYPE_SCIENTIFIC_COMMITTEE = 8;

    const TYPE_OTHER = 99;

    const TYPES = [
        'Diretor(a)' => self::TYPE_DIRECTOR,
        'Coordenador(a) de Divisão' => self::TYPE_DIVISION_COORDINATOR,
        'Líder de Tema' => self::TYPE_THEME_LEADER,
        'Editor(a) de Revista' => self::TYPE_MAGAZINE_EDITOR,
        'Representante CNPq/CAPES' => self::TYPE_AGENT,
        'Voluntário(a)' => self::TYPE_VOLUNTARY,
        'Discente' => self::TYPE_STUDENT,
        'Comitê Científico' => self::TYPE_SCIENTIFIC_COMMITTEE,

        'Outro' => self::TYPE_OTHER,
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
     * @ORM\Column(name="user_identifier", type="string", nullable=true)
     */
    private $userIdentifier;

    /**
     * @var float|null
     *
     * @ORM\Column(name="percentage", type="float", precision=12, scale=2, nullable=true, options={"default"="0.00"})
     */
    private $percentage = '0.00';

    /**
     * @var int|null
     *
     * @ORM\Column(name="type", type="smallint", nullable=true, options={"default"="99"})
     */
    private $type = '99';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true, options={"default"="1"})
     */
    private $isActive = true;

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
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="discounts")
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
     * @return string|null
     */
    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    /**
     * @param string|null $userIdentifier
     *
     * @return EditionDiscount
     */
    public function setUserIdentifier(?string $userIdentifier): EditionDiscount
    {
        $this->userIdentifier = $userIdentifier;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPercentage(): ?float
    {
        return $this->percentage;
    }

    /**
     * @param float|null $percentage
     *
     * @return EditionDiscount
     */
    public function setPercentage(?float $percentage): EditionDiscount
    {
        $this->percentage = $percentage;
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
     * @return EditionDiscount
     */
    public function setType(?int $type): EditionDiscount
    {
        $this->type = $type;
        return $this;
    }

    public function getEdition(): ?Edition
    {
        return $this->edition;
    }

    public function setEdition(?Edition $edition): self
    {
        $this->edition = $edition;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * @param bool|null $isActive
     *
     * @return EditionDiscount
     */
    public function setIsActive(?bool $isActive): EditionDiscount
    {
        $this->isActive = $isActive;
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
     * @return EditionDiscount
     */
    public function setCreatedAt(?\DateTime $createdAt): EditionDiscount
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
     * @return EditionDiscount
     */
    public function setDeletedAt(?\DateTime $deletedAt): EditionDiscount
    {
        $this->deletedAt = $deletedAt;
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
     * @return EditionDiscount
     */
    public function setUpdatedAt(?\DateTime $updatedAt): EditionDiscount
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
