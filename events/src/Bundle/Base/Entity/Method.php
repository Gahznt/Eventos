<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Method
 *
 * @ORM\Table(
 *     name="method"
 * )
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\MethodRepository")
 */
class Method
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
     * @ORM\Column(name="portuguese", type="string", nullable=false)
     */
    private $portuguese;

    /**
     * @var string|null
     *
     * @ORM\Column(name="english", type="string", nullable=false)
     */
    private $english;

    /**
     * @var string|null
     *
     * @ORM\Column(name="spanish", type="string", nullable=false)
     */
    private $spanish;

    /**
     * @var Collection|UserArticles[]
     * @ORM\OneToMany(targetEntity="UserArticles", mappedBy="methodId")
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
     * @return string|null
     */
    public function getPortuguese(): ?string
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
    public function getEnglish(): ?string
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

        return $i18n[\Locale::getDefault()];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    public function getUserArticles()
    {
        return $this->userArticles;
    }
}
