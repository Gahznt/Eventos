<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * EditionSignup
 *
 * @ORM\Table(name="edition_signup")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\EditionSignupRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class EditionSignup
{
    const EDITION_SIGNUP_STATUS_PAID = 1;

    const EDITION_SIGNUP_STATUS_NOT_PAID = 0;

    const EDITION_SIGNUP_STATUS_PAY = [
        'EDITION_SIGNUP_STATUS_PAID' => self::EDITION_SIGNUP_STATUS_PAID,
        'EDITION_SIGNUP_STATUS_NOT_PAID' => self::EDITION_SIGNUP_STATUS_NOT_PAID,
    ];

    const PUBLIC_PATH = '/uploads/event_sign_up/';
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
     * @var string|null
     *
     * @ORM\Column(name="badge", type="string", nullable=false)
     */
    private $badge = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="initial_institute", type="string", nullable=false)
     */
    private $initialInstitute = '';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="status_pay", type="boolean", nullable=true)
     */
    private $statusPay = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="uploaded_file_name", type="string", nullable=true)
     */
    private $uploadedFileName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="uploaded_file_path", type="string", nullable=true)
     */
    private $uploadedFilePath;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="want_free_individual_association", type="boolean", nullable=true, options={"default"="1"})
     */
    private $wantFreeIndividualAssociation = true;

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
     * @var Division|null
     *
     * @ORM\ManyToOne(targetEntity="Division")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="free_individual_association_division_id", referencedColumnName="id")
     * })
     */
    private $freeIndividualAssociationDivision;

    /**
     * @var EditionDiscount|null
     *
     * @ORM\ManyToOne(targetEntity="EditionDiscount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_discount_id", referencedColumnName="id")
     * })
     */
    private $editionDiscount;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="editionSignups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="joined_id", referencedColumnName="id")
     * })
     */
    private ?User $joined = null;

    /**
     * @var EditionPaymentMode|null
     *
     * @ORM\ManyToOne(targetEntity="EditionPaymentMode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_payment_mode_id", referencedColumnName="id")
     * })
     */
    private $paymentMode;

    /**
     * @var Edition|null
     *
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="editionSignups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     * })
     */
    private $edition;

    /**
     * @var UserAssociation|null
     *
     * @ORM\ManyToOne(targetEntity="UserAssociation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="free_individual_association_user_association_id", referencedColumnName="id")
     * })
     */
    private $freeIndividualAssociationUserAssociation;

    /**
     * @var Collection|UserArticles[]
     *
     * @ORM\ManyToMany(targetEntity="UserArticles")
     * @ORM\JoinTable(
     *      name="edition_signup_articles",
     *      joinColumns={@ORM\JoinColumn(name="edition_signup_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_articles_id", referencedColumnName="id")}
     * )
     */
    private $userArticles;

    /**
     * EditionSignup constructor.
     */
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

    public function getEdition(): ?Edition
    {
        return $this->edition;
    }

    public function setEdition(?Edition $edition): self
    {
        $this->edition = $edition;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getJoined(): ?User
    {
        return $this->joined;
    }

    /**
     * @param User|null $joined
     *
     * @return EditionSignup
     */
    public function setJoined(?User $joined): EditionSignup
    {
        $this->joined = $joined;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBadge(): ?string
    {
        return $this->badge;
    }

    /**
     * @param string|null $badge
     *
     * @return EditionSignup
     */
    public function setBadge(?string $badge): EditionSignup
    {
        $this->badge = $badge;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInitialInstitute(): ?string
    {
        return $this->initialInstitute;
    }

    /**
     * @param string|null $initialInstitute
     *
     * @return EditionSignup
     */
    public function setInitialInstitute(?string $initialInstitute): EditionSignup
    {
        $this->initialInstitute = $initialInstitute;
        return $this;
    }

    /**
     * @return EditionPaymentMode|null
     */
    public function getPaymentMode(): ?EditionPaymentMode
    {
        return $this->paymentMode;
    }

    /**
     * @param EditionPaymentMode|null $paymentMode
     *
     * @return EditionSignup
     */
    public function setPaymentMode(?EditionPaymentMode $paymentMode): EditionSignup
    {
        $this->paymentMode = $paymentMode;
        return $this;
    }

    /**
     * @return EditionDiscount|null
     */
    public function getEditionDiscount(): ?EditionDiscount
    {
        return $this->editionDiscount;
    }

    /**
     * @param EditionDiscount|null $editionDiscount
     *
     * @return EditionSignup
     */
    public function setEditionDiscount(?EditionDiscount $editionDiscount): EditionSignup
    {
        $this->editionDiscount = $editionDiscount;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getStatusPay(): ?int
    {
        return $this->statusPay;
    }

    /**
     * @param int|null $statusPay
     *
     * @return EditionSignup
     */
    public function setStatusPay(?int $statusPay): EditionSignup
    {
        $this->statusPay = $statusPay;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUploadedFileName(): ?string
    {
        return $this->uploadedFileName;
    }

    /**
     * @param string|null $uploadedFileName
     *
     * @return EditionSignup
     */
    public function setUploadedFileName(?string $uploadedFileName): EditionSignup
    {
        $this->uploadedFileName = $uploadedFileName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUploadedFilePath(): ?string
    {
        return $this->uploadedFilePath;
    }

    /**
     * @param string|null $uploadedFilePath
     *
     * @return EditionSignup
     */
    public function setUploadedFilePath(?string $uploadedFilePath): EditionSignup
    {
        $this->uploadedFilePath = $uploadedFilePath;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getWantFreeIndividualAssociation(): ?bool
    {
        return $this->wantFreeIndividualAssociation;
    }

    /**
     * @param bool|null $wantFreeIndividualAssociation
     *
     * @return EditionSignup
     */
    public function setWantFreeIndividualAssociation(?bool $wantFreeIndividualAssociation): EditionSignup
    {
        $this->wantFreeIndividualAssociation = $wantFreeIndividualAssociation;
        return $this;
    }

    /**
     * @return Division|null
     */
    public function getFreeIndividualAssociationDivision(): ?Division
    {
        return $this->freeIndividualAssociationDivision;
    }

    /**
     * @param Division|null $freeIndividualAssociationDivision
     *
     * @return EditionSignup
     */
    public function setFreeIndividualAssociationDivision(?Division $freeIndividualAssociationDivision): EditionSignup
    {
        $this->freeIndividualAssociationDivision = $freeIndividualAssociationDivision;
        return $this;
    }

    /**
     * @return UserAssociation|null
     */
    public function getFreeIndividualAssociationUserAssociation(): ?UserAssociation
    {
        return $this->freeIndividualAssociationUserAssociation;
    }

    /**
     * @param UserAssociation|null $freeIndividualAssociationUserAssociation
     *
     * @return EditionSignup
     */
    public function setFreeIndividualAssociationUserAssociation(?UserAssociation $freeIndividualAssociationUserAssociation): EditionSignup
    {
        $this->freeIndividualAssociationUserAssociation = $freeIndividualAssociationUserAssociation;
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
     * @return EditionSignup
     */
    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

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
     * @return EditionSignup
     */
    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

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
     * @return EditionSignup
     */
    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|UserArticles[]
     */
    public function getUserArticles(): Collection
    {
        return $this->userArticles;
    }

    /**
     * @param UserArticles $userArticle
     *
     * @return $this
     */
    public function addUserArticle(UserArticles $userArticle): self
    {
        if (! $this->userArticles->contains($userArticle)) {
            $this->userArticles[] = $userArticle;
        }

        return $this;
    }

    /**
     * @param UserArticles $userArticle
     *
     * @return $this
     */
    public function removeUserArticle(UserArticles $userArticle): self
    {
        $this->userArticles->removeElement($userArticle);

        return $this;
    }

    /**
     * @return string|null
     */
    public function __toString(): ?string
    {
        return $this->badge;
    }
}
