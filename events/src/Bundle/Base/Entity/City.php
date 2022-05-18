<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * City
 *
 * @ORM\Table(name="city")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\CityRepository")
 */
class City
{
    public const OTHER_CITY_ID = 15685;

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
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="state_code", type="string", nullable=false)
     */
    private $codeState = '';

    /**
     * @var string
     *
     * @ORM\Column(name="country_code", type="string", length=2, nullable=false, options={"fixed"=true})
     */
    private $codeCountry = '';

    /**
     * @var string
     *
     * @ORM\Column(name="latitude", type="decimal", precision=10, scale=8, nullable=false)
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="decimal", precision=11, scale=8, nullable=false)
     */
    private $longitude;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime*
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedOn;

    /**
     * @var bool
     *
     * @ORM\Column(name="flag", type="boolean", nullable=false, options={"default"="1"})
     */
    private $flag = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="wikiDataId", type="string", nullable=true, options={"comment"="Rapid API GeoDB Cities"})
     */
    private $wikidataid;

    /**
     * @var State
     *
     * @ORM\ManyToOne(targetEntity="State", inversedBy="cities")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     * })
     */
    private $state;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    private $country;

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
     * @return City
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCodeState(): ?string
    {
        return $this->codeState;
    }

    /**
     * @param string $codeState
     *
     * @return City
     */
    public function setCodeState(string $codeState): self
    {
        $this->codeState = $codeState;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCodeCountry(): ?string
    {
        return $this->codeCountry;
    }

    /**
     * @param string $codeCountry
     *
     * @return City
     */
    public function setCodeCountry(string $codeCountry): self
    {
        $this->codeCountry = $codeCountry;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     *
     * @return City
     */
    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     *
     * @return City
     */
    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

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
     * @return City
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedOn(): ?\DateTimeInterface
    {
        return $this->updatedOn;
    }

    /**
     * @param \DateTimeInterface $updatedOn
     *
     * @return City
     */
    public function setUpdatedOn(\DateTimeInterface $updatedOn): self
    {
        $this->updatedOn = $updatedOn;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getFlag(): ?bool
    {
        return $this->flag;
    }

    /**
     * @param bool $flag
     *
     * @return City
     */
    public function setFlag(bool $flag): self
    {
        $this->flag = $flag;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getWikidataid(): ?string
    {
        return $this->wikidataid;
    }

    /**
     * @param null|string $wikidataid
     *
     * @return City
     */
    public function setWikidataid(?string $wikidataid): self
    {
        $this->wikidataid = $wikidataid;

        return $this;
    }

    /**
     * @return State|null
     */
    public function getState(): ?State
    {
        return $this->state;
    }

    /**
     * @param State|null $state
     *
     * @return City
     */
    public function setState(?State $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return Country|null
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * @param Country|null $country
     *
     * @return City
     */
    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
