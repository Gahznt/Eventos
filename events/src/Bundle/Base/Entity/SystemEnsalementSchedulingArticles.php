<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SystemEnsalementSchedulingArticles
 *
 * @ORM\Table(name="system_ensalement_scheduling_articles")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\SystemEnsalementSchedulingArticlesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class SystemEnsalementSchedulingArticles
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
     * @var SystemEnsalementScheduling
     *
     * @ORM\ManyToOne(targetEntity="SystemEnsalementScheduling", inversedBy="articles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="system_ensalement_sheduling_id", referencedColumnName="id")
     * })
     */
    private $systemEnsalementSheduling;

    /**
     * @var UserArticles
     *
     * @ORM\ManyToOne(targetEntity="UserArticles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_articles_id", referencedColumnName="id")
     * })
     */
    private $userArticles;

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
     * @return SystemEnsalementScheduling|null
     */
    public function getSystemEnsalementSheduling(): ?SystemEnsalementScheduling
    {
        return $this->systemEnsalementSheduling;
    }

    /**
     * @param SystemEnsalementScheduling|null $systemEnsalementSheduling
     *
     * @return $this
     */
    public function setSystemEnsalementSheduling(?SystemEnsalementScheduling $systemEnsalementSheduling): self
    {
        $this->systemEnsalementSheduling = $systemEnsalementSheduling;

        return $this;
    }

    /**
     * @return UserArticles|null
     */
    public function getUserArticles(): ?UserArticles
    {
        return $this->userArticles;
    }

    /**
     * @param UserArticles|null $userArticles
     *
     * @return $this
     */
    public function setUserArticles(?UserArticles $userArticles): self
    {
        $this->userArticles = $userArticles;

        return $this;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->getUserArticles()->getTitle();
    }

}
