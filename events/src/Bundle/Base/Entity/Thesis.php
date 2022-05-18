<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Thesis
 *
 * @ORM\Table(name="thesis")
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Thesis
{
    const LANGUAGE = [
        'Português' => 0,
        'Inglês' => 1,
        'Espanhol' => 2,
    ];

    const MODALITIES = [
        'Modalidade 1: Iniciantes' => 1,
        'Modalidade 2: Projetos Estruturados' => 2,
    ];

    const CONFIRMED_NO = 0;
    const CONFIRMED_YES = 1;

    const EVALUATION_STATUS_WAITING = 1;

    const EVALUATION_STATUS_APPROVED = 2;

    const EVALUATION_STATUS_REPROVED = 3;

    const EVALUATION_STATUS_CANCELED = 4;

    const EVALUATION_STATUS = [
        'EVALUATION_STATUS_WAITING' => self::EVALUATION_STATUS_WAITING,
        'EVALUATION_STATUS_APPROVED' => self::EVALUATION_STATUS_APPROVED,
        'EVALUATION_STATUS_REPROVED' => self::EVALUATION_STATUS_REPROVED,
        'EVALUATION_STATUS_CANCELED' => self::EVALUATION_STATUS_CANCELED,
    ];

    const PUBLIC_PATH = '/uploads/thesis/';
    const UPLOAD_PATH = '#KERNEL#/var/storage' . self::PUBLIC_PATH;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var int|null
     *
     * @ORM\Column(name="language", type="smallint", nullable=true)
     */
    private $language;

    /**
     * @var string|null
     *
     * @ORM\Column(name="modality", type="string", nullable=true)
     */
    private $modality;

    /**
     * @var string|null
     *
     * @ORM\Column(name="advisor_name", type="string", nullable=true)
     */
    private $advisorName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="thesis_file_path", type="string", nullable=true)
     */
    private $thesisFilePath;

    /**
     * @var string|null
     *
     * @ORM\Column(name="agreement_file_path", type="string", nullable=true)
     */
    private $agreementFilePath;

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
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var Division
     *
     * @ORM\ManyToOne(targetEntity="Division")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="division_id", referencedColumnName="id")
     * })
     */
    private $division;

    /**
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="theses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     * })
     */
    private ?Edition $edition = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="theses")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private ?User $user = null;

    /**
     * @var UserThemes
     *
     * @ORM\ManyToOne(targetEntity="UserThemes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_themes_id", referencedColumnName="id")
     * })
     */
    private $userThemes;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status = 1;

    /**
     * @var int|null
     *
     * @ORM\Column(name="confirmed", type="smallint", nullable=true)
     */
    private $confirmed;

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
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return $this
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLanguage(): ?int
    {
        return $this->language;
    }

    /**
     * @param int|null $language
     *
     * @return $this
     */
    public function setLanguage(?int $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getModality(): ?string
    {
        return $this->modality;
    }

    /**
     * @param string|null $modality
     *
     * @return $this
     */
    public function setModality(?string $modality): self
    {
        $this->modality = $modality;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAdvisorName(): ?string
    {
        return $this->advisorName;
    }

    /**
     * @param string|null $advisorName
     *
     * @return $this
     */
    public function setAdvisorName(?string $advisorName): self
    {
        $this->advisorName = $advisorName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getThesisFilePath(): ?string
    {
        return $this->thesisFilePath;
    }

    /**
     * @param string|null $thesisFilePath
     *
     * @return $this
     */
    public function setThesisFilePath(?string $thesisFilePath): self
    {
        $this->thesisFilePath = $thesisFilePath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAgreementFilePath(): ?string
    {
        return $this->agreementFilePath;
    }

    /**
     * @param string|null $agreementFilePath
     *
     * @return $this
     */
    public function setAgreementFilePath(?string $agreementFilePath): self
    {
        $this->agreementFilePath = $agreementFilePath;

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
     * @return Division|null
     */
    public function getDivision(): ?Division
    {
        return $this->division;
    }

    /**
     * @param Division|null $division
     *
     * @return $this
     */
    public function setDivision(?Division $division): self
    {
        $this->division = $division;

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
     * @return $this
     */
    public function setEdition(?Edition $edition): self
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     *
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return UserThemes|null
     */
    public function getUserThemes(): ?UserThemes
    {
        return $this->userThemes;
    }

    /**
     * @param UserThemes|null $userThemes
     *
     * @return $this
     */
    public function setUserThemes(?UserThemes $userThemes): self
    {
        $this->userThemes = $userThemes;

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
     * @return Thesis
     */
    public function setStatus(?int $status): Thesis
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getConfirmed(): ?int
    {
        return $this->confirmed;
    }

    /**
     * @param int|null $confirmed
     *
     * @return Thesis
     */
    public function setConfirmed(?int $confirmed): self
    {
        $this->confirmed = $confirmed;
        return $this;
    }
}
