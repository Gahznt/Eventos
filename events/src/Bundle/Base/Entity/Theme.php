<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="theme")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\ThemeRepository")
 */
class Theme
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
     * @var string
     *
     * @ORM\Column(name="initials", type="string", length=5, nullable=false)
     */
    private $initials;

    /**
     * @var string
     *
     * @ORM\Column(name="portuguese", type="text", length=65535, nullable=false)
     */
    private $portuguese;

    /**
     * @var string
     *
     * @ORM\Column(name="english", type="text", length=65535, nullable=false)
     */
    private $english;

    /**
     * @var string
     *
     * @ORM\Column(name="spanish", type="text", length=65535, nullable=false)
     */
    private $spanish;

    /**
     * @var string
     *
     * @ORM\Column(name="description_portuguese", type="text", length=0, nullable=false)
     */
    private $descriptionPortuguese;

    /**
     * @var string
     *
     * @ORM\Column(name="description_english", type="text", length=0, nullable=false)
     */
    private $descriptionEnglish;

    /**
     * @var string
     *
     * @ORM\Column(name="description_spanish", type="text", length=0, nullable=false)
     */
    private $descriptionSpanish;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ordination", type="integer", nullable=false)
     */
    private $ordination;

    /**
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="themes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     * })
     */
    private ?Edition $editionId = null;

    /**
     * @var $divisionId
     *
     * @ORM\ManyToOne(targetEntity="Division")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="division_id", referencedColumnName="id")
     * })
     */
    private $divisionId;

    public function getEditionId(): ?Edition
    {
        return $this->editionId;
    }

    public function getEdition(): ?Edition
    {
        return $this->editionId;
    }

    public function setEditionId(?Edition $edition): self
    {
        $this->editionId = $edition;

        return $this;
    }

    public function setEdition(?Edition $edition): self
    {
        $this->editionId = $edition;

        return $this;
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
     * @return null|string
     */
    public function getInitials(): ?string
    {
        return $this->initials;
    }

    /**
     * @param string $initials
     *
     * @return $this
     */
    public function setInitials(string $initials): self
    {
        $this->initials = $initials;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPortuguese(): ?string
    {
        return $this->portuguese;
    }

    /**
     * @param string $portuguese
     *
     * @return $this
     */
    public function setPortuguese(string $portuguese): self
    {
        $this->portuguese = $portuguese;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEnglish(): ?string
    {
        return $this->english;
    }

    /**
     * @param string $english
     *
     * @return $this
     */
    public function setEnglish(string $english): self
    {
        $this->english = $english;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSpanish(): ?string
    {
        return $this->spanish;
    }

    /**
     * @param string $spanish
     *
     * @return $this
     */
    public function setSpanish(string $spanish): self
    {
        $this->spanish = $spanish;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescriptionPortuguese(): ?string
    {
        return $this->descriptionPortuguese;
    }

    /**
     * @param string $descriptionPortuguese
     *
     * @return $this
     */
    public function setDescriptionPortuguese(string $descriptionPortuguese): self
    {
        $this->descriptionPortuguese = $descriptionPortuguese;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescriptionEnglish(): ?string
    {
        return $this->descriptionEnglish;
    }

    /**
     * @param string $descriptionEnglish
     *
     * @return $this
     */
    public function setDescriptionEnglish(string $descriptionEnglish): self
    {
        $this->descriptionEnglish = $descriptionEnglish;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescriptionSpanish(): ?string
    {
        return $this->descriptionSpanish;
    }

    /**
     * @param string $descriptionSpanish
     *
     * @return $this
     */
    public function setDescriptionSpanish(string $descriptionSpanish): self
    {
        $this->descriptionSpanish = $descriptionSpanish;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getOrdination(): ?int
    {
        return $this->ordination;
    }

    /**
     * @param int $ordination
     *
     * @return $this
     */
    public function setOrdination(?int $ordination): self
    {
        $this->ordination = $ordination;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDivisionId(): Division
    {
        return $this->divisionId;
    }

    /**
     * @param Division $divisionId
     *
     * @return $this
     */
    public function setDivisionId(Division $divisionId): self
    {
        $this->divisionId = $divisionId;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->portuguese;
    }
}

