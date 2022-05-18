<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Subsection
 *
 * @ORM\Table(name="subsection")
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Subsection
{
    /**
     * Constante de tipos
     */
    const SUBSECTION_TYPES = [
        'Simples (apenas texto)' => 1,
        // 'Galeria de fotos' => 2,
        'Palestrantes' => 8,
        'Coordenação e Comitê / Temas de Interesse' => 9,
        'Atividades Interdivisional/Divisional' => 13,
        'Resultados' => 10,
        'Inscrições' => 11,
        //'Programação Sintética' => 6,
        'Programação Detalhada' => 7,
        'Trabalhos Aprovados' => 3,
        'Trabalhos indicados à premiação' => 12,
        // 'Destaques' => 4,
        // 'Divisões/Áreas' => 5,
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type = '';

    /**
     * @var int|null
     *
     * @ORM\Column(name="position", type="integer", nullable=false, options={"default"="1"})
     */
    private $position = '1';

    /**
     * @var int|null
     *
     * @ORM\Column(name="is_highlight", type="smallint", nullable=true)
     */
    private $isHighlight = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="name_portuguese", type="string", nullable=false)
     */
    private $namePortuguese = '';

    /**
     * @var string
     *
     * @ORM\Column(name="front_call_portuguese", type="string", nullable=false)
     */
    private $frontCallPortuguese = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description_portuguese", type="text", length=65535, nullable=true)
     */
    private $descriptionPortuguese;

    /**
     * @var string
     *
     * @ORM\Column(name="name_english", type="string", nullable=false)
     */
    private $nameEnglish = '';

    /**
     * @var string
     *
     * @ORM\Column(name="front_call_english", type="string", nullable=false)
     */
    private $frontCallEnglish = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description_english", type="text", length=65535, nullable=true)
     */
    private $descriptionEnglish;

    /**
     * @var string
     *
     * @ORM\Column(name="name_spanish", type="string", nullable=false)
     */
    private $nameSpanish = '';

    /**
     * @var string
     *
     * @ORM\Column(name="front_call_spanish", type="string", nullable=false)
     */
    private $frontCallSpanish = '';

    /**
     * @var string
     *
     * @ORM\Column(name="description_spanish", type="text", length=65535, nullable=true)
     */
    private $descriptionSpanish;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_homolog", type="smallint", nullable=true)
     */
    private $isHomolog = false;

    /**
     * @var Edition
     *
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="subsections")
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
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface|null $createdAt
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
     * @return \DateTimeInterface|null
     */
    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTimeInterface|null $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string|null $type
     *
     * @return $this
     */
    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int|null $position
     *
     * @return $this
     */
    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getIsHighlight(): ?int
    {
        return $this->isHighlight;
    }

    /**
     * @param int|null $isHighlight
     *
     * @return $this
     */
    public function setIsHighlight(?int $isHighlight): self
    {
        $this->isHighlight = $isHighlight;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNamePortuguese(): ?string
    {
        return $this->namePortuguese;
    }

    /**
     * @param string|null $namePortuguese
     *
     * @return $this
     */
    public function setNamePortuguese(?string $namePortuguese): self
    {
        $this->namePortuguese = $namePortuguese;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFrontCallPortuguese(): ?string
    {
        return $this->frontCallPortuguese;
    }

    /**
     * @param string|null $frontCallPortuguese
     *
     * @return $this
     */
    public function setFrontCallPortuguese(?string $frontCallPortuguese): self
    {
        $this->frontCallPortuguese = $frontCallPortuguese;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescriptionPortuguese(): ?string
    {
        return $this->descriptionPortuguese;
    }

    /**
     * @param string|null $descriptionPortuguese
     *
     * @return $this
     */
    public function setDescriptionPortuguese(?string $descriptionPortuguese): self
    {
        $this->descriptionPortuguese = $descriptionPortuguese;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNameEnglish(): ?string
    {
        return $this->nameEnglish;
    }

    /**
     * @param string|null $nameEnglish
     *
     * @return $this
     */
    public function setNameEnglish(?string $nameEnglish): self
    {
        $this->nameEnglish = $nameEnglish;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFrontCallEnglish(): ?string
    {
        return $this->frontCallEnglish;
    }

    /**
     * @param string|null $frontCallEnglish
     *
     * @return $this
     */
    public function setFrontCallEnglish(?string $frontCallEnglish): self
    {
        $this->frontCallEnglish = $frontCallEnglish;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescriptionEnglish(): ?string
    {
        return $this->descriptionEnglish;
    }

    /**
     * @param string|null $descriptionEnglish
     *
     * @return $this
     */
    public function setDescriptionEnglish(?string $descriptionEnglish): self
    {
        $this->descriptionEnglish = $descriptionEnglish;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNameSpanish(): ?string
    {
        return $this->nameSpanish;
    }

    /**
     * @param string|null $nameSpanish
     *
     * @return $this
     */
    public function setNameSpanish(?string $nameSpanish): self
    {
        $this->nameSpanish = $nameSpanish;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFrontCallSpanish(): ?string
    {
        return $this->frontCallSpanish;
    }

    /**
     * @param string|null $frontCallSpanish
     *
     * @return $this
     */
    public function setFrontCallSpanish(?string $frontCallSpanish): self
    {
        $this->frontCallSpanish = $frontCallSpanish;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescriptionSpanish(): ?string
    {
        return $this->descriptionSpanish;
    }

    /**
     * @param string|null $descriptionSpanish
     *
     * @return $this
     */
    public function setDescriptionSpanish(?string $descriptionSpanish): self
    {
        $this->descriptionSpanish = $descriptionSpanish;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        $i18n = [
            'pt_br' => $this->getNamePortuguese(),
            'en' => $this->getNameEnglish(),
            'es' => $this->getNameSpanish(),
        ];

        return $i18n[\Locale::getDefault()];
    }

    /**
     * @return string
     */
    public function getFrontCall()
    {
        $i18n = [
            'pt_br' => $this->getFrontCallPortuguese(),
            'en' => $this->getFrontCallEnglish(),
            'es' => $this->getFrontCallSpanish(),
        ];

        return $i18n[\Locale::getDefault()];
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $i18n = [
            'pt_br' => $this->getDescriptionPortuguese(),
            'en' => $this->getDescriptionEnglish(),
            'es' => $this->getDescriptionSpanish(),
        ];

        return $i18n[\Locale::getDefault()];
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     *
     * @return $this
     */
    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsHomolog(): ?bool
    {
        return $this->isHomolog;
    }

    /**
     * @param bool|null $isHomolog
     *
     * @return $this
     */
    public function setIsHomolog(?bool $isHomolog): self
    {
        $this->isHomolog = $isHomolog;

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
        return $this->namePortuguese;
    }


}
