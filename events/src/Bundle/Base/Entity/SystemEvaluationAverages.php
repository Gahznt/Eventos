<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SystemEvaluationAverages
 *
 * @ORM\Table(name="system_evaluation_averages")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\SystemEvaluationAveragesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class SystemEvaluationAverages
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="systemEvaluationAverages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="systemEvaluationAverages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     * })
     */
    private ?Edition $edition = null;

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
     * @var float|null
     *
     * @ORM\Column(name="point_primary", type="decimal", nullable=true)
     */
    private $primary;

    /**
     * @var float|null
     *
     * @ORM\Column(name="point_secondary", type="decimal", nullable=true)
     */
    private $secondary;

    /**
     * @ORM\OneToMany(targetEntity="SystemEvaluationAveragesArticles", mappedBy="systemEvaluationAverages", cascade={"persist"})
     */
    private $userArticles;

    public function __construct()
    {
        $this->userArticles = new ArrayCollection();
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
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Edition|null
     */
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
     * @return Division|null
     */
    public function getDivision(): ?Division
    {
        return $this->division;
    }

    public function setDivision(?Division $division): self
    {
        $this->division = $division;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPrimary(): ?int
    {
        return $this->primary;
    }

    public function setPrimary(?float $primary): self
    {
        $this->primary = $primary;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSecondary(): ?int
    {
        return $this->secondary;
    }

    public function setSecondary(?float $secondary): self
    {
        $this->secondary = $secondary;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->user;
    }

    /**
     * @return Collection|SystemEvaluationAveragesArticles[]
     */
    public function getUserArticles(): Collection
    {
        return $this->userArticles;
    }

    public function addUserArticle(SystemEvaluationAveragesArticles $userArticle): self
    {
        if (! $this->userArticles->contains($userArticle)) {
            $this->userArticles[] = $userArticle;
            $userArticle->setSystemEvaluationAverages($this);
            $userArticle->setCreatedAt(new \DateTime());
        }

        return $this;
    }

    public function removeUserArticle(SystemEvaluationAveragesArticles $userArticle): self
    {
        if ($this->userArticles->contains($userArticle)) {
            $this->userArticles->removeElement($userArticle);
            // set the owning side to null (unless already changed)
            if ($userArticle->getSystemEvaluationAverages() === $this) {
                $userArticle->setSystemEvaluationAverages(null);
            }
        }

        return $this;
    }
}
