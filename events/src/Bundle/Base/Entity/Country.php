<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Country
 *
 * @ORM\Table(
 *     name="country"
 * )
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\CountryRepository")
 */
class Country
{
    public const DEFAULT_LOCATE_ID = 31;
    public const OTHER_LOCATE_ID = 248;

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
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="name_english", type="string", nullable=false)
     */
    private $nameEnglish;

    /**
     * @var string|null
     *
     * @ORM\Column(name="iso3", type="string", length=3, nullable=true, options={"fixed"=true})
     */
    private $iso3;

    /**
     * @var string|null
     *
     * @ORM\Column(name="iso2", type="string", length=2, nullable=true, options={"fixed"=true})
     */
    private $iso2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phonecode", type="string", nullable=true)
     */
    private $phonecode;

    /**
     * @var string|null
     *
     * @ORM\Column(name="capital", type="string", nullable=true)
     */
    private $capital;

    /**
     * @var string|null
     *
     * @ORM\Column(name="currency", type="string", nullable=true)
     */
    private $currency;

    /**
     * @var string|null
     *
     * @ORM\Column(name="native", type="string", nullable=true)
     */
    private $native;

    /**
     * @var string|null
     *
     * @ORM\Column(name="emoji", type="string", length=191, nullable=true)
     */
    private $emoji;

    /**
     * @var string|null
     *
     * @ORM\Column(name="emojiU", type="string", length=191, nullable=true)
     */
    private $emojiu;

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
     * @ORM\OneToMany(targetEntity="State", mappedBy="country")
     */
    private $states;


    /**
     * Country constructor.
     */
    public function __construct()
    {
        $this->states = new ArrayCollection();
    }

    /**
     * @return Collection | City[]
     */
    public function getStates(): Collection
    {
        return $this->states;
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
     * @return Country
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getNameEnglish(): ?string
    {
        return $this->nameEnglish;
    }

    /**
     * @param string $nameEnglish
     *
     * @return Country
     */
    public function setNameEnglish(string $nameEnglish): self
    {
        $this->nameEnglish = $nameEnglish;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getIso3(): ?string
    {
        return $this->iso3;
    }

    /**
     * @param null|string $iso3
     *
     * @return Country
     */
    public function setIso3(?string $iso3): self
    {
        $this->iso3 = $iso3;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getIso2(): ?string
    {
        return $this->iso2;
    }

    /**
     * @param null|string $iso2
     *
     * @return Country
     */
    public function setIso2(?string $iso2): self
    {
        $this->iso2 = $iso2;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPhonecode(): ?string
    {
        return $this->phonecode;
    }

    /**
     * @param null|string $phonecode
     *
     * @return Country
     */
    public function setPhonecode(?string $phonecode): self
    {
        $this->phonecode = $phonecode;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCapital(): ?string
    {
        return $this->capital;
    }

    /**
     * @param null|string $capital
     *
     * @return Country
     */
    public function setCapital(?string $capital): self
    {
        $this->capital = $capital;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param null|string $currency
     *
     * @return Country
     */
    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getNative(): ?string
    {
        return $this->native;
    }

    /**
     * @param null|string $native
     *
     * @return Country
     */
    public function setNative(?string $native): self
    {
        $this->native = $native;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmoji(): ?string
    {
        return $this->emoji;
    }

    /**
     * @param null|string $emoji
     *
     * @return Country
     */
    public function setEmoji(?string $emoji): self
    {
        $this->emoji = $emoji;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmojiu(): ?string
    {
        return $this->emojiu;
    }

    /**
     * @param null|string $emojiu
     *
     * @return Country
     */
    public function setEmojiu(?string $emojiu): self
    {
        $this->emojiu = $emojiu;

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
     * @return Country
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
     * @return Country
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
     * @return Country
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
     * @return Country
     */
    public function setWikidataid(?string $wikidataid): self
    {
        $this->wikidataid = $wikidataid;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
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
     * @param State $state
     *
     * @return $this
     */
    public function addState(State $state): self
    {
        if (! $this->states->contains($state)) {
            $this->states[] = $state;
            $state->setCountry($this);
        }

        return $this;
    }

    /**
     * @param State $state
     *
     * @return $this
     */
    public function removeState(State $state): self
    {
        if ($this->states->contains($state)) {
            $this->states->removeElement($state);
            // set the owning side to null (unless already changed)
            if ($state->getCountry() === $this) {
                $state->setCountry(null);
            }
        }

        return $this;
    }
}
