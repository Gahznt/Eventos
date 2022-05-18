<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserArticlesKeywords
 *
 * @ORM\Table(name="user_articles_keywords")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\UserArticlesKeywordsRepository")
 */
class UserArticlesKeywords
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
     * @var Keyword
     *
     * @ORM\ManyToOne(targetEntity="Keyword")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="keyword_id", referencedColumnName="id")
     * })
     */
    private $keywords;

    /**
     * @var UserArticles
     *
     * @ORM\ManyToOne(targetEntity="UserArticles", inversedBy="userArticlesKeywords")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_articles_id", referencedColumnName="id")
     * })
     */
    private $userArticlesId;

    public function create()
    {
        return new self;
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
     * @return UserArticles
     */
    public function getUserArticlesId(): UserArticles
    {
        return $this->userArticlesId;
    }

    /**
     * @param UserArticles $userArticlesId
     *
     * @return $this
     */
    public function setUserArticlesId(UserArticles $userArticlesId): self
    {
        $this->userArticlesId = $userArticlesId;

        return $this;
    }

    /**
     * @return Keyword|null
     */
    public function getKeywords(): ?Keyword
    {
        return $this->keywords;
    }

    /**
     * @param Keyword|null $keywords
     *
     * @return $this
     */
    public function setKeywords(?Keyword $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->id;
    }
}
