<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * State
 *
 * @ORM\Table(name="state")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\StateRepository")
 */
class State
{
    const OTHER_STATE_ID = 4004;

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
     * @ORM\Column(name="country_code", type="string", length=2, nullable=false, options={"fixed"=true})
     */
    private $codeCountry = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="fips_code", type="string", nullable=true)
     */
    private $fipsCode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="iso2", type="string", nullable=true)
     */
    private $iso2;

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
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="states")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    private $country;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="City", mappedBy="state")
     * @ORM\OrderBy({"name"="ASC"})
     */
    private $cities;

    /**
     * State constructor.
     */
    public function __construct()
    {
        $this->cities = new ArrayCollection();
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
     * @return string|null
     */
    public function getCodeCountry(): ?string
    {
        return $this->codeCountry;
    }

    /**
     * @param string $codeCountry
     *
     * @return $this
     */
    public function setCodeCountry(string $codeCountry): self
    {
        $this->codeCountry = $codeCountry;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFipsCode(): ?string
    {
        return $this->fipsCode;
    }

    /**
     * @param string|null $fipsCode
     *
     * @return $this
     */
    public function setFipsCode(?string $fipsCode): self
    {
        $this->fipsCode = $fipsCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIso2(): ?string
    {
        return $this->iso2;
    }

    /**
     * @param string|null $iso2
     *
     * @return $this
     */
    public function setIso2(?string $iso2): self
    {
        $this->iso2 = $iso2;

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
     * @param \DateTimeInterface $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
     * @return $this
     */
    public function setFlag(bool $flag): self
    {
        $this->flag = $flag;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getWikidataid(): ?string
    {
        return $this->wikidataid;
    }

    /**
     * @param string|null $wikidataid
     *
     * @return $this
     */
    public function setWikidataid(?string $wikidataid): self
    {
        $this->wikidataid = $wikidataid;

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
     * @return $this
     */
    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection | City[]
     */
    public function getCities(): Collection
    {
        return $this->cities;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @param City $city
     *
     * @return $this
     */
    public function addCity(City $city): self
    {
        if (! $this->cities->contains($city)) {
            $this->cities[] = $city;
            $city->setState($this);
        }

        return $this;
    }

    /**
     * @param City $city
     *
     * @return $this
     */
    public function removeCity(City $city): self
    {
        if ($this->cities->contains($city)) {
            $this->cities->removeElement($city);
            // set the owning side to null (unless already changed)
            if ($city->getState() === $this) {
                $city->setState(null);
            }
        }

        return $this;
    }
}
