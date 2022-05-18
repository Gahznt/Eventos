<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * UserArticlesFiles
 *
 * @ORM\Table(name="user_articles_files")
 * @ORM\Entity
 */
class UserArticlesFiles
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
     * @ORM\Column(name="path", type="text", length=65535, nullable=true)
     */
    private $path;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="real_article", type="boolean", nullable=true)
     */
    private $realArticle;

    /**
     * @var UserArticles
     *
     * @ORM\ManyToOne(targetEntity="UserArticles", inversedBy="userArticlesFiles")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_articles_id", referencedColumnName="id")
     * })
     */
    private $userArticles;
    private $userArticlesId;

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
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     *
     * @return $this
     */
    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return UserArticles|null
     */
    public function getuserArticles(): ?UserArticles
    {
        return $this->userArticles;
    }

    /**
     * @param UserArticles|null userArticles
     *
     * @return $this
     */
    public function setUserArticles(?UserArticles $userArticles): self
    {
        $this->userArticles = $userArticles;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getRealArticle(): ?bool
    {
        return $this->realArticle;
    }

    /**
     * @param bool|null $realArticle
     *
     * @return $this
     */
    public function setRealArticle(?bool $realArticle): self
    {
        $this->realArticle = $realArticle;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

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
