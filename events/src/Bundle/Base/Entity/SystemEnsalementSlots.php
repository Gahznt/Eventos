<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * SystemEnsalementSlots
 * @UniqueEntity(
 *     fields={"systemEnsalementRooms", "systemEnsalementSessions"},
 *     errorPath="systemEnsalementSessions"
 * )
 *
 * @ORM\Table(name="system_ensalement_slots")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\SystemEnsalementSlotsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class SystemEnsalementSlots
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
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="systemEnsalementSlots")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     * })
     */
    private ?Edition $edition = null;

    /**
     * @var SystemEnsalementSessions|null
     *
     * @ORM\ManyToOne(targetEntity="SystemEnsalementSessions")
     * @ORM\OrderBy({"date"="ASC", "start"="ASC"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="system_ensalement_sessions_id", referencedColumnName="id")
     * })
     */
    private $systemEnsalementSessions;

    /**
     * @var SystemEnsalementRooms|null
     *
     * @ORM\ManyToOne(targetEntity="SystemEnsalementRooms")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="system_ensalement_rooms_id", referencedColumnName="id")
     * })
     */
    private $systemEnsalementRooms;

    /**
     * @var string|null
     *
     * @ORM\Column(name="link", type="string", nullable=true)
     */
    private $link = '';


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

    /**
     * @param \DateTime|null $createdAt
     *
     * @return SystemEnsalementSlots
     */
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

    /**
     * @param \DateTime|null $updatedAt
     *
     * @return SystemEnsalementSlots
     */
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

    /**
     * @param \DateTime|null $deletedAt
     *
     * @return SystemEnsalementSlots
     */
    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

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
     * @return SystemEnsalementSlots
     */
    public function setEdition(?Edition $edition): self
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * @return SystemEnsalementSessions|null
     */
    public function getSystemEnsalementSessions(): ?SystemEnsalementSessions
    {
        return $this->systemEnsalementSessions;
    }

    /**
     * @param SystemEnsalementSessions|null $systemEnsalementSessions
     *
     * @return SystemEnsalementSlots
     */
    public function setSystemEnsalementSessions(?SystemEnsalementSessions $systemEnsalementSessions): self
    {
        $this->systemEnsalementSessions = $systemEnsalementSessions;

        return $this;
    }

    /**
     * @return SystemEnsalementRooms|null
     */
    public function getSystemEnsalementRooms(): ?SystemEnsalementRooms
    {
        return $this->systemEnsalementRooms;
    }

    /**
     * @param SystemEnsalementRooms|null $systemEnsalementRooms
     *
     * @return SystemEnsalementSlots
     */
    public function setSystemEnsalementRooms(?SystemEnsalementRooms $systemEnsalementRooms): self
    {
        $this->systemEnsalementRooms = $systemEnsalementRooms;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param null|string $link
     *
     * @return SystemEnsalementSlots
     */
    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        //@TODO fazer o concat
        return (string)$this->id;
    }
}
