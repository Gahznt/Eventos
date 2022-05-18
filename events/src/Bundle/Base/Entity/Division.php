<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Division
 *
 * @ORM\Table(
 *     name="division"
 * )
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\DivisionRepository")
 */
class Division
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
     * @var string|null
     *
     * @ORM\Column(name="initials", type="string", length=20, nullable=false)
     */
    private $initials;

    /**
     * @var Collection|DivisionCoordinator[]
     * @ORM\OneToMany(targetEntity="DivisionCoordinator", mappedBy="division")
     */
    private $divisionCoordinators;

    /**
     * @var Collection|UserCommittee[]
     * @ORM\OneToMany(targetEntity="UserCommittee", mappedBy="division")
     */
    private $userCommittees;

    /**
     * @var Collection|UserAssociation[]
     *
     * @ORM\OneToMany(targetEntity="UserAssociation", mappedBy="division")
     */
    private $userAssociation;

    /**
     * @var Collection|UserThemes[]
     * @ORM\OneToMany(targetEntity="UserThemes", mappedBy="division")
     * @ORM\OrderBy({"position"="ASC"})
     */
    private $themes;

    /**
     * @var Collection|UserArticles[]
     * @ORM\OneToMany(targetEntity="UserArticles", mappedBy="divisionId")
     */
    private $userArticles;

    /**
     * Division constructor.
     */
    public function __construct()
    {
        $this->divisionCoordinators = new ArrayCollection();
        $this->userCommittees = new ArrayCollection();
        $this->themes = new ArrayCollection();
        $this->userAssociation = new ArrayCollection();

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
     * @return string|null
     */
    public function getPortuguese()
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
     * @return string|null
     */
    public function getEnglish()
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
     * @return string|null
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
     * @return string
     */
    public function getName()
    {
        $i18n = [
            'pt_br' => $this->getPortuguese(),
            'en' => $this->getEnglish(),
            'es' => $this->getSpanish(),
        ];

        return $this->getInitials() . ' - ' . $i18n[\Locale::getDefault()];
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
     * @return $this
     */
    public function setInitials(?string $initials): self
    {
        $this->initials = $initials;
        return $this;
    }

    /**
     * @param array $args
     *
     * @return Collection|DivisionCoordinator[]
     */
    public function getDivisionCoordinators($args): Collection
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->isNull('deletedAt'));

        if (isset($args['edition'])) {
            $args['edition'] = is_array($args['edition']) ?: [(int)$args['edition']];

            $criteria->andWhere(Criteria::expr()->in('edition', $args['edition']));
        }

        return $this->divisionCoordinators->matching($criteria);
    }

    /**
     * @param DivisionCoordinator $divisionCoordinator
     *
     * @return $this
     */
    public function addDivisionCoordinator(DivisionCoordinator $divisionCoordinator): self
    {
        if (! $this->divisionCoordinators->contains($divisionCoordinator)) {
            $this->divisionCoordinators[] = $divisionCoordinator;
            $divisionCoordinator->setDivision($this);
        }

        return $this;
    }

    /**
     * @param DivisionCoordinator $divisionCoordinator
     *
     * @return $this
     */
    public function removeDivisionCoordinator(DivisionCoordinator $divisionCoordinator): self
    {
        if ($this->divisionCoordinators->removeElement($divisionCoordinator)) {
            // set the owning side to null (unless already changed)
            if ($divisionCoordinator->getDivision() === $this) {
                $divisionCoordinator->setDivision(null);
            }
        }

        return $this;
    }

    /**
     * @param array $args
     *
     * @return Collection|UserCommittee[]
     */
    public function getUserCommittees($args): Collection
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->isNull('deletedAt'));

        if (isset($args['edition'])) {
            $args['edition'] = is_array($args['edition']) ?: [(int)$args['edition']];

            $criteria->andWhere(Criteria::expr()->in('edition', $args['edition']));
        }

        return $this->userCommittees->matching($criteria);
    }

    /**
     * @param UserCommittee $userCommittee
     *
     * @return $this
     */
    public function addUserCommittee(UserCommittee $userCommittee): self
    {
        if (! $this->userCommittees->contains($userCommittee)) {
            $this->userCommittees[] = $userCommittee;
            $userCommittee->setDivision($this);
        }

        return $this;
    }

    /**
     * @param UserCommittee $userCommittee
     *
     * @return $this
     */
    public function removeUserCommittee(UserCommittee $userCommittee): self
    {
        if ($this->userCommittees->removeElement($userCommittee)) {
            // set the owning side to null (unless already changed)
            if ($userCommittee->getDivision() === $this) {
                $userCommittee->setDivision(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserAssociation[]
     */
    public function getUserAssociation(): Collection
    {
        return $this->userAssociation;
    }

    /**
     * @param UserAssociation $userAssociation
     *
     * @return $this
     */
    public function addUserAssociation(UserAssociation $userAssociation): self
    {
        if (! $this->userAssociation->contains($userAssociation)) {
            $this->userAssociation[] = $userAssociation;
            $userAssociation->setDivision($this);
        }

        return $this;
    }

    /**
     * @param UserAssociation $userAssociation
     *
     * @return $this
     */
    public function removeUserAssociation(UserAssociation $userAssociation): self
    {
        if ($this->userAssociation->removeElement($userAssociation)) {
            // set the owning side to null (unless already changed)
            if ($userAssociation->getDivision() === $this) {
                $userAssociation->setDivision(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserThemes[]
     */
    public function getThemes(): Collection
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->eq('status', UserThemes::THEME_EVALUATION_APPROVED));

        return $this->themes->matching($criteria);
    }

    /**
     * @param UserThemes $theme
     *
     * @return $this
     */
    public function addTheme(UserThemes $theme): self
    {
        if (! $this->themes->contains($theme)) {
            $this->themes[] = $theme;
            $theme->setDivision($this);
        }

        return $this;
    }

    /**
     * @param UserThemes $theme
     *
     * @return $this
     */
    public function removeTheme(UserThemes $theme): self
    {
        if ($this->themes->removeElement($theme)) {
            // set the owning side to null (unless already changed)
            if ($theme->getDivision() === $this) {
                $theme->setDivision(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
