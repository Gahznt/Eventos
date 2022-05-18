<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * SystemEvaluation
 *
 * @ORM\Table(name="system_evaluation_indications")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\SystemEvaluationIndicationsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deleted_at", timeAware=false, hardDelete=false)
 */
class SystemEvaluationIndications
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
     * @var UserArticles|null
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="UserArticles", inversedBy="indications")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_articles_id", referencedColumnName="id")
     * })
     */
    private $userArticles;

    /**
     * @var User
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="indications")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_evaluator_id", referencedColumnName="id")
     * })
     */
    private $userEvaluator;

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
    private $deleted_at;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="valid", type="boolean", nullable=true)
     */
    private $valid;

    /**
     * @return bool|null
     */
    public function getValid(): ?bool
    {
        return $this->valid;
    }

    /**
     * @param bool|null $valid
     *
     * @return SystemEvaluationIndications
     */
    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

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
     * @return UserArticles|null
     */
    public function getUserArticles(): ?UserArticles
    {
        return $this->userArticles;
    }

    public function setUserArticles(?UserArticles $userArticles): self
    {
        $this->userArticles = $userArticles;

        return $this;
    }

    /**
     * @return User
     */
    public function getUserEvaluator(): User
    {
        return $this->userEvaluator;
    }

    public function setUserEvaluator(User $userEvaluator): self
    {
        $this->userEvaluator = $userEvaluator;

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
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTime $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * @return null|string
     */
    public function __toString()
    {
        return (string)$this->id;
    }
}
