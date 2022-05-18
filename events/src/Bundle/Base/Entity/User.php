<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\UserRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class User implements UserInterface
{
    const USER_LOCALE_PT = 'pt_br';
    const USER_LOCALE_EN = 'en';
    // const USER_LOCALE_ES = 'es';
    const USER_LOCALES = [
        'Portuguese' => self::USER_LOCALE_PT,
        'English' => self::USER_LOCALE_EN,
        // 'Spanish' => self::USER_LOCALE_ES,
    ];

    const USER_RECORD_TYPE_BRAZILIAN = 0;
    // const USER_RECORD_TYPE_LEGAL_PERSON = 1;
    const USER_RECORD_TYPE_FOREIGN = 2;
    const USER_RECORD_TYPE = [
        'Nationality_Brazilian' => self::USER_RECORD_TYPE_BRAZILIAN,
        // 'Legal person' => self::USER_RECORD_TYPE_LEGAL_PERSON,
        'Nationality_Foreign' => self::USER_RECORD_TYPE_FOREIGN,
    ];

    const USER_ACADEMIC_STATUS_PROGRESS = 2;
    const USER_ACADEMIC_STATUS_DONE = 1;
    const USER_ACADEMIC_STATUS = [
        'In progress' => self::USER_ACADEMIC_STATUS_PROGRESS,
        'Done' => self::USER_ACADEMIC_STATUS_DONE,
    ];

    const USER_LEVEL_MASTER = 1;
    const USER_LEVEL_GRADUATE = 2;
    const USER_LEVEL_DOCTORATE = 3;
    const USER_LEVELS = [
        'Graduate' => self::USER_LEVEL_GRADUATE,
        'Master' => self::USER_LEVEL_MASTER,
        'Doctorate' => self::USER_LEVEL_DOCTORATE,
    ];

    const USER_FOREIGN_USE_CPF_YES = 1;
    const USER_FOREIGN_USE_CPF_NO = 0;
    const USER_FOREIGN_USE_CPF = [
        'Yes' => self::USER_FOREIGN_USE_CPF_YES,
        'No' => self::USER_FOREIGN_USE_CPF_NO,
    ];

    const USER_FOREIGN_USE_PASSPORT_YES = 1;
    const USER_FOREIGN_USE_PASSPORT_AUTOMATIC = 0;
    const USER_FOREIGN_USE_PASSPORT = [
        'Passport' => self::USER_FOREIGN_USE_PASSPORT_YES,
        'E-mail' => self::USER_FOREIGN_USE_PASSPORT_AUTOMATIC,
    ];

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
     * @ORM\Column(name="locale", type="string", nullable=true)
     */
    private $locale = self::USER_LOCALE_PT;

    /**
     * @var bool|null
     * @ORM\Column(name="is_foreign_use_cpf", type="boolean", nullable=true)
     */
    private $isForeignUseCpf = self::USER_FOREIGN_USE_CPF_YES;

    /**
     * @var int|null
     * @ORM\Column(name="is_foreign_use_passport", type="smallint", nullable=true)
     */
    private $isForeignUsePassport = self::USER_FOREIGN_USE_PASSPORT_YES;

    /**
     * @ORM\Column(name="roles", type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @var string|null
     * @ORM\Column(name="identifier", type="string", nullable=true)
     */
    private $identifier = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    private $name = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="nickname", type="string", nullable=true)
     */
    private $nickname = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email = '';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var string
     *
     *
     * @ORM\Column(name="password", type="string", nullable=true)
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zipcode", type="string", nullable=true)
     */
    private $zipcode = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="street", type="string", nullable=true)
     */
    private $street = '';

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", nullable=true)
     */
    private $number;

    /**
     * @var string|null
     *
     * @ORM\Column(name="complement", type="string", nullable=true)
     */
    private $complement;

    /**
     * @var string|null
     *
     * @ORM\Column(name="neighborhood", type="string", nullable=true)
     */
    private $neighborhood = '';

    /**
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private ?string $phone = null;

    /**
     *
     * @ORM\Column(name="cellphone", type="string", nullable=true)
     */
    private $cellphone;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="portuguese", type="boolean", nullable=true)
     */
    private $portuguese;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="english", type="boolean", nullable=true)
     */
    private $english;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="spanish", type="boolean", nullable=true)
     */
    private $spanish;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="newsletter_associated", type="boolean", nullable=true)
     */
    private $newsletterAssociated;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="newsletter_events", type="boolean", nullable=true)
     */
    private $newsletterEvents;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="newsletter_partners", type="boolean", nullable=true)
     */
    private $newsletterPartners;

    /**
     * @var int|null
     *
     * @ORM\Column(name="record_type", type="smallint", nullable=true)
     */
    private $recordType = self::USER_RECORD_TYPE_BRAZILIAN;

    /**
     * @var Collection|UserAcademics[]
     * @ORM\OneToMany(targetEntity="UserAcademics", mappedBy="user", cascade={"persist", "remove"})
     */
    private $academics;

    /**
     * @ORM\OneToOne(targetEntity="UserEvaluationArticles", mappedBy="user", cascade={"persist"})
     */
    private $userEvaluationArticles;

    /**
     * @var UserInstitutionsPrograms|null
     * @ORM\OneToOne(targetEntity="UserInstitutionsPrograms", mappedBy="user", cascade={"persist"})
     */
    private $institutionsPrograms;

    /**
     * @var Collection|UserAssociation[]
     * @ORM\OneToMany(targetEntity="UserAssociation", mappedBy="user", cascade={"persist"})
     */
    private $associations;

    /**
     * @var Collection|UserThemesResearchers[]
     * @ORM\OneToMany(targetEntity="UserThemesResearchers", mappedBy="researcher", cascade={"persist"})
     */
    private $userThemesResearchers;

    /**
     * @var Collection|DivisionCoordinator[]
     * @ORM\OneToMany(targetEntity="DivisionCoordinator", mappedBy="coordinator", cascade={"persist"})
     */
    private $userDivisionCoordinator;

    /**
     * @var Collection|UserArticles[]
     * @ORM\OneToMany(targetEntity="UserArticles", mappedBy="userId", cascade={"persist"})
     */
    private $userArticles;

    /**
     * @var Collection|SystemEvaluationIndications[]
     * @ORM\OneToMany(targetEntity="SystemEvaluationIndications", mappedBy="userEvaluator", cascade={"persist"})
     */
    private $indications;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="brazilian", type="boolean", nullable=true)
     */
    private $brazilian;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="status", type="boolean", nullable=true, options={"default"="1"})
     */
    private $status = true;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * })
     */
    private $city;

    /**
     * @var int|null
     *
     * @ORM\Column(name="extension", type="integer", nullable=true)
     */
    private $extension;
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="expired_at", type="datetime", nullable=true)
     */
    private $expiredAt;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="payment", type="smallint", nullable=true)
     */
    private $payment;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="last_pay", type="datetime", nullable=true)
     */
    private $lastPay;

    /**
     * @var int|null
     *
     * @ORM\Column(name="level", type="smallint", nullable=true)
     */
    private $level;

    /**
     * @var Collection|Method[]
     *
     * @ORM\ManyToMany(targetEntity="Method")
     * @ORM\JoinTable(
     *     name="user_methods",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="method_id", referencedColumnName="id")
     * })
     */
    private $methods;

    /**
     * @var Collection|Theory[]
     *
     * @ORM\ManyToMany(targetEntity="Theory")
     * @ORM\JoinTable(
     *     name="user_theories",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="theory_id", referencedColumnName="id")
     * })
     */
    private $theories;

    /**
     * @var Collection|UserCommittee[]
     * @ORM\OneToMany(targetEntity="UserCommittee", mappedBy="user")
     */
    private $userCommittees;

    /**
     * @ORM\OneToOne(targetEntity="Program", mappedBy="user")
     */
    private ?Program $associatedProgram = null;

    /**
     * @var Collection|ActivitiesGuest[]
     * @ORM\OneToMany(targetEntity="ActivitiesGuest", mappedBy="guest")
     */
    private $activitiesGuests;

    /**
     * @var Collection|ActivitiesPanelist[]
     * @ORM\OneToMany(targetEntity="ActivitiesPanelist", mappedBy="panelist")
     */
    private $activitiesPanelists;

    /**
     * @var Collection|Certificate[]
     * @ORM\OneToMany(targetEntity="Certificate", mappedBy="user")
     */
    private $certificates;

    /**
     * @var Collection|EditionSignup[]
     * @ORM\OneToMany(targetEntity="EditionSignup", mappedBy="joined")
     */
    private $editionSignups;

    /**
     * @var Collection|Panel[]
     * @ORM\OneToMany(targetEntity="Panel", mappedBy="proponentId")
     */
    private $panels;

    /**
     * @var Collection|PanelEvaluationLog[]
     * @ORM\OneToMany(targetEntity="PanelEvaluationLog", mappedBy="user")
     */
    private $panelEvaluationLogs;

    /**
     * @var Collection|PanelsPanelist[]
     * @ORM\OneToMany(targetEntity="PanelsPanelist", mappedBy="panelistId")
     */
    private $panelsPanelists;

    /**
     * @var Collection|SystemEnsalementScheduling[]
     * @ORM\OneToMany(targetEntity="SystemEnsalementScheduling", mappedBy="userRegister")
     */
    private $systemEnsalementSchedulingUserRegisters;

    /**
     * @var Collection|SystemEnsalementScheduling[]
     * @ORM\OneToMany(targetEntity="SystemEnsalementScheduling", mappedBy="coordinatorDebater1")
     */
    private $systemEnsalementSchedulingCoordinatorDebaters1;

    /**
     * @var Collection|SystemEnsalementScheduling[]
     * @ORM\OneToMany(targetEntity="SystemEnsalementScheduling", mappedBy="coordinatorDebater2")
     */
    private $systemEnsalementSchedulingCoordinatorDebaters2;

    /**
     * @var Collection|SystemEvaluation[]
     * @ORM\OneToMany(targetEntity="SystemEvaluation", mappedBy="userOwner")
     */
    private $systemEvaluations;

    /**
     * @var Collection|SystemEvaluationAverages[]
     * @ORM\OneToMany(targetEntity="SystemEvaluationAverages", mappedBy="user")
     */
    private $systemEvaluationAverages;

    /**
     * @var Collection|SystemEvaluationConfig[]
     * @ORM\OneToMany(targetEntity="SystemEvaluationConfig", mappedBy="user")
     */
    private $systemEvaluationConfigs;

    /**
     * @var Collection|SystemEvaluationLog[]
     * @ORM\OneToMany(targetEntity="SystemEvaluationLog", mappedBy="userLog")
     */
    private $systemEvaluationLogs;

    /**
     * @var Collection|Thesis[]
     * @ORM\OneToMany(targetEntity="Thesis", mappedBy="user")
     */
    private $theses;

    /**
     * @var Collection|UserArticlesAuthors[]
     * @ORM\OneToMany(targetEntity="UserArticlesAuthors", mappedBy="userAuthor")
     */
    private $userArticlesAuthors;

    /**
     * @var Collection|UserConsents[]
     * @ORM\OneToMany(targetEntity="UserConsents", mappedBy="user")
     */
    private $userConsents;

    /**
     * @var Collection|UserThemeKeyword[]
     * @ORM\OneToMany(targetEntity="UserThemeKeyword", mappedBy="user")
     */
    private $userThemeKeywords;

    /**
     * @var Collection|UserThemes[]
     * @ORM\OneToMany(targetEntity="UserThemes", mappedBy="user")
     */
    private $userThemes;

    /**
     * @var Collection|UserThemesEvaluationLog[]
     * @ORM\OneToMany(targetEntity="UserThemesEvaluationLog", mappedBy="user")
     */
    private $userThemesEvaluationLogs;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->academics = new ArrayCollection();
        $this->associations = new ArrayCollection();
        $this->userThemesResearchers = new ArrayCollection();
        $this->userDivisionCoordinator = new ArrayCollection();
        $this->userArticles = new ArrayCollection();
        $this->indications = new ArrayCollection();
        $this->methods = new ArrayCollection();
        $this->theories = new ArrayCollection();
        $this->userCommittees = new ArrayCollection();

        $this->activitiesGuests = new ArrayCollection();
        $this->activitiesPanelists = new ArrayCollection();
        $this->certificates = new ArrayCollection();
        $this->editionSignups = new ArrayCollection();
        $this->panels = new ArrayCollection();
        $this->panelEvaluationLogs = new ArrayCollection();
        $this->panelsPanelists = new ArrayCollection();
        $this->systemEnsalementSchedulingUserRegisters = new ArrayCollection();
        $this->systemEnsalementSchedulingCoordinatorDebaters1 = new ArrayCollection();
        $this->systemEnsalementSchedulingCoordinatorDebaters2 = new ArrayCollection();
        $this->systemEvaluations = new ArrayCollection();
        $this->systemEvaluationAverages = new ArrayCollection();
        $this->systemEvaluationConfigs = new ArrayCollection();
        $this->systemEvaluationLogs = new ArrayCollection();
        $this->theses = new ArrayCollection();
        $this->userArticlesAuthors = new ArrayCollection();
        $this->userConsents = new ArrayCollection();
        $this->userThemeKeywords = new ArrayCollection();
        $this->userThemes = new ArrayCollection();
        $this->userThemesEvaluationLogs = new ArrayCollection();
    }

    /**
     * @return Collection|UserArticles[]
     */
    public function getUserArticles(): Collection
    {
        return $this->userArticles;
    }

    /**
     * @return UserEvaluationArticles
     */
    public function getUserEvaluationArticles(): ?UserEvaluationArticles
    {
        return $this->userEvaluationArticles;
    }

    /**
     * @param UserEvaluationArticles UserEvaluationArticles
     *
     * @return $this
     */
    public function setUserEvaluationArticles(UserEvaluationArticles $userEvaluationArticles): self
    {
        if (! empty($userEvaluationArticles->getDivisionFirstId()) || ! empty($userEvaluationArticles->getDivisionSecondId())) {
            $this->userEvaluationArticles = $userEvaluationArticles;
            $userEvaluationArticles->setUser($this);
        }

        return $this;
    }

    /**
     * @return UserInstitutionsPrograms|null
     */
    public function getInstitutionsPrograms(): ?UserInstitutionsPrograms
    {
        return $this->institutionsPrograms;
    }

    /**
     * @param UserInstitutionsPrograms $userInstitutionsPrograms
     *
     * @return $this
     */
    public function setInstitutionsPrograms(UserInstitutionsPrograms $userInstitutionsPrograms): self
    {
        $this->institutionsPrograms = $userInstitutionsPrograms;
        $userInstitutionsPrograms->setUser($this);

        return $this;
    }

    /**
     * @return Collection|UserAcademics[]
     */
    public function getAcademics(): Collection
    {
        return $this->academics;
    }

    /**
     * @param UserAcademics $academic
     *
     * @return $this
     */
    public function addAcademic(UserAcademics $academic): self
    {
        if (! $this->academics->contains($academic)) {
            $this->academics[] = $academic;
            $academic->setUser($this);
        }

        return $this;
    }

    /**
     * @param UserAcademics $academic
     *
     * @return $this
     */
    public function removeAcademic(UserAcademics $academic): self
    {
        if ($this->academics->removeElement($academic)) {
            // set the owning side to null (unless already changed)
            if ($academic->getUser() === $this) {
                $academic->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return array|null
     */
    public function getRoles(): ?array
    {
        return array_unique($this->roles);
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
    public function getLocale(): ?string
    {
        return $this->locale;
    }

    /**
     * @param string|null $locale
     *
     * @return User
     */
    public function setLocale(?string $locale): User
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsForeignUseCpf(): ?bool
    {
        return $this->isForeignUseCpf;
    }

    /**
     * @param bool|null $isForeignUseCpf
     *
     * @return User
     */
    public function setIsForeignUseCpf(?bool $isForeignUseCpf): User
    {
        $this->isForeignUseCpf = $isForeignUseCpf;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIsForeignUsePassport(): ?int
    {
        return $this->isForeignUsePassport;
    }

    /**
     * @param int|null $isForeignUsePassport
     *
     * @return User
     */
    public function setIsForeignUsePassport(?int $isForeignUsePassport): User
    {
        $this->isForeignUsePassport = $isForeignUsePassport;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param null|string $identifier
     *
     * @return User
     */
    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     *
     * @return User
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @param null|string $nickname
     *
     * @return User
     */
    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     *
     * @return User
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * @param \DateTimeInterface|null $birthday
     *
     * @return User
     */
    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    /**
     * @param null|string $zipcode
     *
     * @return User
     */
    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param null|string $street
     *
     * @return User
     */
    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * @param $number
     *
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getComplement(): ?string
    {
        return $this->complement;
    }

    /**
     * @param null|string $complement
     *
     * @return User
     */
    public function setComplement(?string $complement): self
    {
        $this->complement = $complement;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getNeighborhood(): ?string
    {
        return $this->neighborhood;
    }

    /**
     * @param null|string $neighborhood
     *
     * @return User
     */
    public function setNeighborhood(?string $neighborhood): self
    {
        $this->neighborhood = $neighborhood;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }


    /**
     * @param $phone
     *
     * @return $this
     */
    public function setPhone($phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCellphone()
    {
        return $this->cellphone;
    }

    /**
     * @param $cellphone
     *
     * @return $this
     */
    public function setCellphone($cellphone)
    {
        $this->cellphone = $cellphone;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getPortuguese(): ?bool
    {
        return $this->portuguese;
    }

    /**
     * @param bool|null $portuguese
     *
     * @return User
     */
    public function setPortuguese(?bool $portuguese): self
    {
        $this->portuguese = $portuguese;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEnglish(): ?bool
    {
        return $this->english;
    }

    /**
     * @param bool|null $english
     *
     * @return User
     */
    public function setEnglish(?bool $english): self
    {
        $this->english = $english;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getSpanish(): ?bool
    {
        return $this->spanish;
    }

    /**
     * @param bool|null $spanish
     *
     * @return User
     */
    public function setSpanish(?bool $spanish): self
    {
        $this->spanish = $spanish;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getNewsletterAssociated(): ?bool
    {
        return $this->newsletterAssociated;
    }

    /**
     * @param bool|null $newsletterAssociated
     *
     * @return User
     */
    public function setNewsletterAssociated(?bool $newsletterAssociated): self
    {
        $this->newsletterAssociated = $newsletterAssociated;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getNewsletterEvents(): ?bool
    {
        return $this->newsletterEvents;
    }

    /**
     * @param bool|null $newsletterEvents
     *
     * @return User
     */
    public function setNewsletterEvents(?bool $newsletterEvents): self
    {
        $this->newsletterEvents = $newsletterEvents;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getNewsletterPartners(): ?bool
    {
        return $this->newsletterPartners;
    }

    /**
     * @param bool|null $newsletterPartners
     *
     * @return User
     */
    public function setNewsletterPartners(?bool $newsletterPartners): self
    {
        $this->newsletterPartners = $newsletterPartners;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getBrazilian(): ?bool
    {
        return $this->brazilian;
    }

    /**
     * @param bool|null $brazilian
     *
     * @return User
     */
    public function setBrazilian(?bool $brazilian): self
    {
        $this->brazilian = $brazilian;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * @param bool|null $status
     *
     * @return User
     */
    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return City|null
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param City|null $city
     *
     * @return User
     */
    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUsername()
    {
        return $this->identifier;
    }

    /**
     * @return int|null
     */
    public function getExtension(): ?int
    {
        return $this->extension;
    }

    /**
     * @param int|null $extension
     *
     * @return $this
     */
    public function setExtension(?int $extension): self
    {
        $this->extension = $extension;

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
     * @return User
     */
    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpiredAt(): ?\DateTime
    {
        return $this->expiredAt;
    }

    /**
     * @param \DateTime|null $expiredAt
     *
     * @return User
     */
    public function setExpiredAt(?\DateTime $expiredAt): User
    {
        $this->expiredAt = $expiredAt;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPayment(): ?int
    {
        return $this->payment;
    }

    /**
     * @param int|null $payment
     *
     * @return User
     */
    public function setPayment(?int $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastPay(): ?\DateTime
    {
        return $this->lastPay;
    }

    /**
     * @param \DateTime|null $lastPay
     *
     * @return User
     */
    public function setLastPay(?\DateTime $lastPay): self
    {
        $this->lastPay = $lastPay;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLevel(): ?int
    {
        return $this->level;
    }

    /**
     * @param int|null $level
     *
     * @return User
     */
    public function setLevel(?int $level): self
    {
        $this->level = $level;

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
     * @return $this
     */
    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param array $args
     *
     * @return Collection|UserAssociation[]
     */
    public function getAssociations(array $args = []): Collection
    {
        $criteria = Criteria::create();

        if (isset($args['showOnlyActive']) && true === $args['showOnlyActive']) {
            $criteria->andWhere(Criteria::expr()->gte('expiredAt', new \DateTime()));
            $criteria->andWhere(Criteria::expr()->eq('statusPay', UserAssociation::USER_ASSOCIATIONS_STATUS_PAY));
        }

        return $this->associations->matching($criteria);
    }

    /**
     * @return string|null
     */
    public function getRecordTypeString(): ?string
    {
        return array_search($this->getRecordType(), self::USER_RECORD_TYPE);
    }

    /**
     * @return int|null
     */
    public function getRecordType(): ?int
    {
        return $this->recordType;
    }

    /**
     * @param int|null $recordType
     *
     * @return User
     */
    public function setRecordType(?int $recordType): self
    {
        $this->recordType = $recordType;

        return $this;
    }

    /**
     * retorna se é um cadastro pessoal
     *
     * @return bool
     */
    public function isPrivate()
    {
        if ($this->recordType !== 1)
            return true;

        return false;
    }

    /**
     * @return Collection|UserThemesResearchers[]
     */
    public function getUserThemesResearchers(): Collection
    {
        return $this->userThemesResearchers;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return (string)$this->identifier;
    }

    /**
     * @param UserAssociation $association
     *
     * @return $this
     */
    public function addAssociation(UserAssociation $association): self
    {
        if (! $this->associations->contains($association)) {
            $this->associations[] = $association;
            $association->setUser($this);
        }

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
     * @return User
     */
    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @param UserAssociation $association
     *
     * @return $this
     */
    public function removeAssociation(UserAssociation $association): self
    {
        if ($this->associations->contains($association)) {
            $this->associations->removeElement($association);
            // set the owning side to null (unless already changed)
            if ($association->getUser() === $this) {
                $association->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @param UserThemesResearchers $userThemesResearcher
     *
     * @return $this
     */
    public function addUserThemesResearcher(UserThemesResearchers $userThemesResearcher): self
    {
        if (! $this->userThemesResearchers->contains($userThemesResearcher)) {
            $this->userThemesResearchers[] = $userThemesResearcher;
            $userThemesResearcher->setResearcher($this);
        }

        return $this;
    }

    /**
     * @param UserThemesResearchers $userThemesResearcher
     *
     * @return $this
     */
    public function removeUserThemesResearcher(UserThemesResearchers $userThemesResearcher): self
    {
        if ($this->userThemesResearchers->contains($userThemesResearcher)) {
            $this->userThemesResearchers->removeElement($userThemesResearcher);
            // set the owning side to null (unless already changed)
            if ($userThemesResearcher->getResearcher() === $this) {
                $userThemesResearcher->setResearcher(null);
            }
        }

        return $this;
    }

    /**
     * @return DivisionCoordinator[]
     */
    public function getUserDivisionCoordinator(): Collection
    {
        $criteria = Criteria::create();
        $criteria->andWhere(Criteria::expr()->isNull('deletedAt'));

        return $this->userDivisionCoordinator->matching($criteria);
    }

    public function addUserDivisionCoordinator(DivisionCoordinator $userDivisionCoordinator): self
    {
        if (! $this->userDivisionCoordinator->contains($userDivisionCoordinator)) {
            $this->userDivisionCoordinator[] = $userDivisionCoordinator;
            $userDivisionCoordinator->setCoordinator($this);
        }

        return $this;
    }

    public function removeUserDivisionCoordinator(DivisionCoordinator $userDivisionCoordinator): self
    {
        if ($this->userDivisionCoordinator->contains($userDivisionCoordinator)) {
            $this->userDivisionCoordinator->removeElement($userDivisionCoordinator);
            // set the owning side to null (unless already changed)
            if ($userDivisionCoordinator->getCoordinator() === $this) {
                $userDivisionCoordinator->setCoordinator(null);
            }
        }

        return $this;
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
            $userArticle->setUserId($this);
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
        if ($this->userArticles->contains($userArticle)) {
            $this->userArticles->removeElement($userArticle);
            // set the owning side to null (unless already changed)
            if ($userArticle->getUserId() === $this) {
                $userArticle->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SystemEvaluationIndications[]
     */
    public function getIndications(): Collection
    {
        return $this->indications;
    }

    /**
     * @param SystemEvaluationIndications $indication
     *
     * @return $this
     */
    public function addIndication(SystemEvaluationIndications $indication): self
    {
        if (! $this->indications->contains($indication)) {
            $this->indications[] = $indication;
            $indication->setUserEvaluator($this);
        }

        return $this;
    }

    /**
     * @param SystemEvaluationIndications $indication
     *
     * @return $this
     */
    public function removeIndication(SystemEvaluationIndications $indication): self
    {
        if ($this->indications->contains($indication)) {
            $this->indications->removeElement($indication);
            // set the owning side to null (unless already changed)
            if ($indication->getUserEvaluator() === $this) {
                $indication->setUserEvaluator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Method[]
     */
    public function getMethods(): Collection
    {
        return $this->methods;
    }

    /**
     * @param Method $method
     *
     * @return $this
     */
    public function addMethod(Method $method): self
    {
        if (! $this->methods->contains($method)) {
            $this->methods[] = $method;
        }

        return $this;
    }

    /**
     * @param Method $method
     *
     * @return $this
     */
    public function removeMethod(Method $method): self
    {
        $this->methods->removeElement($method);

        return $this;
    }

    /**
     * @return Collection|Theory[]
     */
    public function getTheories(): Collection
    {
        return $this->theories;
    }

    /**
     * @param Theory $theory
     *
     * @return $this
     */
    public function addTheory(Theory $theory): self
    {
        if (! $this->theories->contains($theory)) {
            $this->theories[] = $theory;
        }

        return $this;
    }

    /**
     * @param Theory $theory
     *
     * @return $this
     */
    public function removeTheory(Theory $theory): self
    {
        $this->theories->removeElement($theory);

        return $this;
    }

    /**
     * @return UserCommittee[]
     */
    public function getUserCommittees(): Collection
    {
        return $this->userCommittees;
    }

    public function addUserCommittee(UserCommittee $userCommittee): self
    {
        if (! $this->userCommittees->contains($userCommittee)) {
            $this->userCommittees[] = $userCommittee;
            $userCommittee->setUser($this);
        }

        return $this;
    }

    public function removeUserCommittee(UserCommittee $userCommittee): self
    {
        if ($this->userCommittees->removeElement($userCommittee)) {
            // set the owning side to null (unless already changed)
            if ($userCommittee->getUser() === $this) {
                $userCommittee->setUser(null);
            }
        }

        return $this;
    }

    public function getAssociatedProgram(): ?Program
    {
        return $this->associatedProgram;
    }

    public function setAssociatedProgram(?Program $associatedProgram): User
    {
        $this->associatedProgram = $associatedProgram;
        return $this;
    }

    public function getActivitiesGuests()
    {
        return $this->activitiesGuests;
    }

    public function getActivitiesPanelists()
    {
        return $this->activitiesPanelists;
    }

    public function getCertificates()
    {
        return $this->certificates;
    }

    public function getEditionSignups()
    {
        return $this->editionSignups;
    }

    public function getPanels()
    {
        return $this->panels;
    }

    public function getPanelEvaluationLogs()
    {
        return $this->panelEvaluationLogs;
    }

    public function getPanelsPanelists()
    {
        return $this->panelsPanelists;
    }

    public function getSystemEnsalementSchedulingUserRegisters()
    {
        return $this->systemEnsalementSchedulingUserRegisters;
    }

    public function getSystemEnsalementSchedulingCoordinatorDebaters1()
    {
        return $this->systemEnsalementSchedulingCoordinatorDebaters1;
    }

    public function getSystemEnsalementSchedulingCoordinatorDebaters2()
    {
        return $this->systemEnsalementSchedulingCoordinatorDebaters2;
    }

    public function getSystemEvaluations()
    {
        return $this->systemEvaluations;
    }

    public function getSystemEvaluationAverages()
    {
        return $this->systemEvaluationAverages;
    }

    public function getSystemEvaluationConfigs()
    {
        return $this->systemEvaluationConfigs;
    }

    public function getSystemEvaluationLogs()
    {
        return $this->systemEvaluationLogs;
    }

    public function getTheses()
    {
        return $this->theses;
    }

    public function getUserArticlesAuthors()
    {
        return $this->userArticlesAuthors;
    }

    public function getUserConsents()
    {
        return $this->userConsents;
    }

    public function getUserThemeKeywords()
    {
        return $this->userThemeKeywords;
    }

    public function getUserThemes()
    {
        return $this->userThemes;
    }

    public function getUserThemesEvaluationLogs()
    {
        return $this->userThemesEvaluationLogs;
    }
}
