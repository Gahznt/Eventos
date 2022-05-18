<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * SystemEvaluationAveragesArticles
 *
 * @ORM\Table(name="system_evaluation_averages_articles")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\SystemEvaluationAveragesArticlesAveragesRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class SystemEvaluationAveragesArticles
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
     * @var UserArticles|null
     *
     * @ORM\ManyToOne(targetEntity="UserArticles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_articles_id", referencedColumnName="id")
     * })
     */
    private $userArticles;

    /**
     * @var SystemEvaluationAverages|null
     *
     * @ORM\ManyToOne(targetEntity="SystemEvaluationAverages", inversedBy="userArticles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="system_evaluation_averages_id", referencedColumnName="id")
     * })
     */
    private $systemEvaluationAverages;

    /**
     * @return SystemEvaluationAveragesArticles
     */
    public function create()
    {
        return new self();
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

    /**
     * @param \DateTime|null $createdAt
     *
     * @return SystemEvaluationAveragesArticles
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
     * @return SystemEvaluationAveragesArticles
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
     * @return SystemEvaluationAveragesArticles
     */
    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

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
     * @return SystemEvaluationAveragesArticles
     */
    public function setUserArticles(?UserArticles $userArticles): self
    {
        $this->userArticles = $userArticles;

        return $this;
    }

    /**
     * @return SystemEvaluationAverages|null
     */
    public function getSystemEvaluationAverages(): ?SystemEvaluationAverages
    {
        return $this->systemEvaluationAverages;
    }

    /**
     * @param SystemEvaluationAverages|null $systemEvaluationAverages
     *
     * @return SystemEvaluationAveragesArticles
     */
    public function setSystemEvaluationAverages(?SystemEvaluationAverages $systemEvaluationAverages): self
    {
        $this->systemEvaluationAverages = $systemEvaluationAverages;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->id;
    }
}
