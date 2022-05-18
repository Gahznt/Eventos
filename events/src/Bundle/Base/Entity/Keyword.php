<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Keyword
 *
 * @ORM\Table(name="keyword")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\KeywordRepository")
 */
class Keyword
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
     * @var string|null
     *
     * @ORM\Column(name="portuguese", type="string", length=200, nullable=false)
     */
    private $portuguese;

    /**
     * @var string|null
     *
     * @ORM\Column(name="english", type="string", length=200, nullable=false)
     */
    private $english;

    /**
     * @var string|null
     *
     * @ORM\Column(name="spanish", type="string", length=200, nullable=false)
     */
    private $spanish;

    /**
     * @var Division|null
     *
     * @ORM\ManyToOne(targetEntity="Division")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="division_id", referencedColumnName="id")
     * })
     */
    private $division;

    /**
     * @var UserThemes|null
     *
     * @ORM\ManyToOne(targetEntity="UserThemes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="theme_id", referencedColumnName="id")
     * })
     */
    private $theme;

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
    public function getPortuguese(): ?string
    {
        return $this->portuguese;
    }

    /**
     * @param string|null $portuguese
     *
     * @return $this
     */
    public function setPortuguese(?string $portuguese): self
    {
        $this->portuguese = $portuguese;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEnglish(): ?string
    {
        return $this->english;
    }

    /**
     * @param string|null $english
     *
     * @return $this
     */
    public function setEnglish(?string $english): self
    {
        $this->english = $english;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSpanish(): ?string
    {
        return $this->spanish;
    }

    /**
     * @param string|null $spanish
     *
     * @return $this
     */
    public function setSpanish(?string $spanish): self
    {
        $this->spanish = $spanish;

        return $this;
    }

    /**
     * @return Division|null
     */
    public function getDivision(): ?Division
    {
        return $this->division;
    }

    /**
     * @param Division|null $division
     *
     * @return $this
     */
    public function setDivision(?Division $division): self
    {
        $this->division = $division;

        return $this;
    }

    /**
     * @return UserThemes|null
     */
    public function getTheme(): ?UserThemes
    {
        return $this->theme;
    }

    /**
     * @param UserThemes|null $theme
     *
     * @return $this
     */
    public function setTheme(?UserThemes $theme): self
    {
        $this->theme = $theme;

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
