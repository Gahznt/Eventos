<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Speaker
 *
 * @ORM\Table(name="speaker")
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Speaker
{
    const PUBLIC_PATH = '/uploads/speaker/';
    const UPLOAD_PATH = '#KERNEL#/var/storage' . self::PUBLIC_PATH;

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
     * @var int|null
     *
     * @ORM\Column(name="type", type="smallint", nullable=false, options={"comment"="0 = Nacional, 1 = Internacional"})
     */
    private $type = '0';

    /**
     * @var int|null
     *
     * @ORM\Column(name="position", type="integer", nullable=false, options={"default"="1"})
     */
    private $position = '1';

    /**
     * @var string|null
     *
     * @ORM\Column(name="picture_path", type="string", nullable=true)
     */
    private $picturePath;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status = '0';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_homolog", type="smallint", nullable=true)
     */
    private $isHomolog = false;

    /**
     * @var string
     *
     * @ORM\Column(name="name_portuguese", type="string", nullable=false)
     */
    private $namePortuguese = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="curriculum_link_portuguese", type="string", nullable=true)
     */
    private $curriculumLinkPortuguese;

    /**
     * @var string
     *
     * @ORM\Column(name="content_portuguese", type="text", length=65535, nullable=false)
     */
    private $contentPortuguese;

    /**
     * @var string
     *
     * @ORM\Column(name="name_english", type="string", nullable=false)
     */
    private $nameEnglish;

    /**
     * @var string|null
     *
     * @ORM\Column(name="curriculum_link_english", type="string", nullable=true)
     */
    private $curriculumLinkEnglish;

    /**
     * @var string
     *
     * @ORM\Column(name="content_english", type="text", length=65535, nullable=false)
     */
    private $contentEnglish;

    /**
     * @var string
     *
     * @ORM\Column(name="name_spanish", type="string", nullable=false)
     */
    private $nameSpanish;

    /**
     * @var string|null
     *
     * @ORM\Column(name="curriculum_link_spanish", type="string", nullable=true)
     */
    private $curriculumLinkSpanish;

    /**
     * @var string
     *
     * @ORM\Column(name="content_spanish", type="text", length=65535, nullable=false)
     */
    private $contentSpanish;

    /**
     * @var Edition
     *
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="speakers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     * })
     */
    private $edition;

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
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int|null $type
     *
     * @return $this
     */
    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int|null $position
     *
     * @return $this
     */
    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPicturePath(): ?string
    {
        return $this->picturePath;
    }

    /**
     * @param string|null $picturePath
     *
     * @return $this
     */
    public function setPicturePath(?string $picturePath): self
    {
        $this->picturePath = $picturePath;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int|null $status
     *
     * @return $this
     */
    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsHomolog(): ?bool
    {
        return $this->isHomolog;
    }

    /**
     * @param bool|null $isHomolog
     *
     * @return $this
     */
    public function setIsHomolog(?bool $isHomolog): self
    {
        $this->isHomolog = $isHomolog;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNamePortuguese(): ?string
    {
        return $this->namePortuguese;
    }

    /**
     * @param string|null $namePortuguese
     *
     * @return $this
     */
    public function setNamePortuguese(?string $namePortuguese): self
    {
        $this->namePortuguese = $namePortuguese;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurriculumLinkPortuguese(): ?string
    {
        return $this->curriculumLinkPortuguese;
    }

    /**
     * @param string|null $curriculumLinkPortuguese
     *
     * @return $this
     */
    public function setCurriculumLinkPortuguese(?string $curriculumLinkPortuguese): self
    {
        $this->curriculumLinkPortuguese = $curriculumLinkPortuguese;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContentPortuguese(): ?string
    {
        return $this->contentPortuguese;
    }

    /**
     * @param string|null $contentPortuguese
     *
     * @return $this
     */
    public function setContentPortuguese(?string $contentPortuguese): self
    {
        $this->contentPortuguese = $contentPortuguese;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNameEnglish(): ?string
    {
        return $this->nameEnglish;
    }

    /**
     * @param string|null $nameEnglish
     *
     * @return $this
     */
    public function setNameEnglish(?string $nameEnglish): self
    {
        $this->nameEnglish = $nameEnglish;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurriculumLinkEnglish(): ?string
    {
        return $this->curriculumLinkEnglish;
    }

    /**
     * @param string|null $curriculumLinkEnglish
     *
     * @return $this
     */
    public function setCurriculumLinkEnglish(?string $curriculumLinkEnglish): self
    {
        $this->curriculumLinkEnglish = $curriculumLinkEnglish;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContentEnglish(): ?string
    {
        return $this->contentEnglish;
    }

    /**
     * @param string|null $contentEnglish
     *
     * @return $this
     */
    public function setContentEnglish(?string $contentEnglish): self
    {
        $this->contentEnglish = $contentEnglish;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNameSpanish(): ?string
    {
        return $this->nameSpanish;
    }

    /**
     * @param string|null $nameSpanish
     *
     * @return $this
     */
    public function setNameSpanish(?string $nameSpanish): self
    {
        $this->nameSpanish = $nameSpanish;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurriculumLinkSpanish(): ?string
    {
        return $this->curriculumLinkSpanish;
    }

    /**
     * @param string|null $curriculumLinkSpanish
     *
     * @return $this
     */
    public function setCurriculumLinkSpanish(?string $curriculumLinkSpanish): self
    {
        $this->curriculumLinkSpanish = $curriculumLinkSpanish;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContentSpanish(): ?string
    {
        return $this->contentSpanish;
    }

    /**
     * @param string|null $contentSpanish
     *
     * @return $this
     */
    public function setContentSpanish(?string $contentSpanish): self
    {
        $this->contentSpanish = $contentSpanish;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        $i18n = [
            'pt_br' => $this->getNamePortuguese(),
            'en' => $this->getNameEnglish(),
            'es' => $this->getNameSpanish(),
        ];

        return $i18n[\Locale::getDefault()];
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $i18n = [
            'pt_br' => $this->getContentPortuguese(),
            'en' => $this->getContentEnglish(),
            'es' => $this->getContentSpanish(),
        ];

        return $i18n[\Locale::getDefault()];
    }

    /**
     * @return string
     */
    public function getCurriculumLink()
    {
        $i18n = [
            'pt_br' => $this->getCurriculumLinkPortuguese(),
            'en' => $this->getCurriculumLinkEnglish(),
            'es' => $this->getCurriculumLinkSpanish(),
        ];

        return $i18n[\Locale::getDefault()];
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
     * @return $this
     */
    public function setEdition(?Edition $edition): self
    {
        $this->edition = $edition;

        return $this;
    }


}
