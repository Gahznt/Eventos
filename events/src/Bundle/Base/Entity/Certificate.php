<?php

namespace App\Bundle\Base\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="certificate")
 * @ORM\Entity
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class Certificate
{
    const ACTIVE_ON = 1;
    const ACTIVE_OFF = 2;

    public static $PART_CIENT = 1;  // Atividade Científica
    public static $PART_DIV = 2;    // Atividade Divisional
    public static $APRES = 3;       // Apresentação de Trabalhos
    public static $AVAL = 4;        // Avaliação de Trabalhos
    public static $COM_CIENT = 5;   // Comitês Científicos
    public static $COORD_DIV = 6;   // Coordenador de Divisão
    public static $COORD_SES = 7;   // Coordenador de Sessão
    public static $COORD_DEB = 8;   // Coordenador / Debatedor
    public static $DEB_SESS = 9;    // Debatedor de Sessão
    public static $INDIC_PREM = 10; // Indicados Prêmio
    public static $PART = 11;       // Participante
    public static $LIDER = 12;      // Líder de Tema
    public static $PREM = 13;       // Premiados
    public static $VOLUNTARIO = 14; // Voluntário
    public static $MANUAL = 99;     // Manual

    const CERTIFICATE_TYPE_APRESENTACAO = 3;
    const CERTIFICATE_TYPE_AVALIACAO = 4;
    const CERTIFICATE_TYPE_PARTICIPANTE = 11;
    const CERTIFICATE_TYPE_COORDENADOR_SESSAO = 7;
    const CERTIFICATE_TYPE_DEBATEDOR_SESSAO = 9;
    const CERTIFICATE_TYPE_COORDENADOR_DEBATEDOR_SESSAO = 8;
    const CERTIFICATE_TYPE_COORDENADOR_DIVISAO = 6;
    const CERTIFICATE_TYPE_COMITE_CIENTIFICO = 5;
    const CERTIFICATE_TYPE_LIDER_TEMA = 12;
    const CERTIFICATE_TYPE_INDICADOS_PREMIACAO = 10;
    const CERTIFICATE_TYPE_PREMIADOS = 13;
    const CERTIFICATE_TYPE_VOLUNTARIO = 14;
    const CERTIFICATE_TYPE_MANUAL = 99;

    const CERTIFICATE_TYPES = [
        'Apresentação de Trabalhos' => self::CERTIFICATE_TYPE_APRESENTACAO,
        'Avaliação de Trabalhos' => self::CERTIFICATE_TYPE_AVALIACAO,
        'Participante' => self::CERTIFICATE_TYPE_PARTICIPANTE,
        'Coordenador de Sessão' => self::CERTIFICATE_TYPE_COORDENADOR_SESSAO,
        'Debatedor de Sessão' => self::CERTIFICATE_TYPE_DEBATEDOR_SESSAO,
        'Coordenador/Debatedor de Sessão' => self::CERTIFICATE_TYPE_COORDENADOR_DEBATEDOR_SESSAO,
        'Coordenador de Divisão' => self::CERTIFICATE_TYPE_COORDENADOR_DIVISAO,
        'Comitê Científico' => self::CERTIFICATE_TYPE_COMITE_CIENTIFICO,
        'Líder de Tema' => self::CERTIFICATE_TYPE_LIDER_TEMA,
        'Indicados à Premiação' => self::CERTIFICATE_TYPE_INDICADOS_PREMIACAO,
        'Premiados' => self::CERTIFICATE_TYPE_PREMIADOS,
        'Voluntário' => self::CERTIFICATE_TYPE_VOLUNTARIO,
        'Adicional' => self::CERTIFICATE_TYPE_MANUAL,
    ];

    const CERTIFICATE_TEMPLATE_MAP = [
        self::CERTIFICATE_TYPE_APRESENTACAO => 'APRES',
        self::CERTIFICATE_TYPE_AVALIACAO => 'AVAL',
        self::CERTIFICATE_TYPE_PARTICIPANTE => 'PART',
        self::CERTIFICATE_TYPE_COORDENADOR_SESSAO => 'COORD_SES',
        self::CERTIFICATE_TYPE_DEBATEDOR_SESSAO => 'DEB_SESS',
        self::CERTIFICATE_TYPE_COORDENADOR_DEBATEDOR_SESSAO => 'COORD_DEB',
        self::CERTIFICATE_TYPE_COORDENADOR_DIVISAO => 'COORD_DIV',
        self::CERTIFICATE_TYPE_COMITE_CIENTIFICO => 'COM_CIENT',
        self::CERTIFICATE_TYPE_LIDER_TEMA => 'LIDER',
        self::CERTIFICATE_TYPE_INDICADOS_PREMIACAO => 'INDIC_PREM',
        self::CERTIFICATE_TYPE_PREMIADOS => 'PREM',
        self::CERTIFICATE_TYPE_VOLUNTARIO => 'VOLUNTARIO',
        self::CERTIFICATE_TYPE_MANUAL => 'MANUAL',
    ];

    const GENERATE_PATH = '#KERNEL#/var/storage/certificates/';
    const LAYOUT_PATH = '#KERNEL#/var/storage/certificate_layout/';
    const HTML_PATH = '#KERNEL#/var/storage/certificate_html/';
    const QRCODE_PATH = '#KERNEL#/var/storage/certificate_qrcode/';

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Edition", inversedBy="certificates")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="edition_id", referencedColumnName="id")
     * })
     */
    private ?Edition $edition = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="certificates")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private ?User $user = null;

    /**
     * @var int|null
     *
     * @ORM\Column(name="type", type="smallint", nullable=true)
     */
    private $type;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true, options={"default"="0"})
     */
    private $isActive = false;

    /**
     * @var array|null
     *
     * @ORM\Column(name="variables", type="json", nullable=true)
     */
    private $variables = [];

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
     * @var Collection|UserArticles[]
     *
     * @ORM\ManyToMany(targetEntity="UserArticles", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *      name="certificates_user_articles",
     *      joinColumns={@ORM\JoinColumn(name="certificate_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_articles_id", referencedColumnName="id")}
     * )
     */
    private $userArticles;

    /**
     * @var Collection|UserThemes[]
     *
     * @ORM\ManyToMany(targetEntity="UserThemes", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *      name="certificates_user_themes",
     *      joinColumns={@ORM\JoinColumn(name="certificate_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_themes_id", referencedColumnName="id")}
     * )
     * @ORM\OrderBy({"position"="ASC"})
     */
    private $userThemes;

    /**
     * @var Collection|Activity[]
     *
     * @ORM\ManyToMany(targetEntity="Activity", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *      name="certificates_activities",
     *      joinColumns={@ORM\JoinColumn(name="certificate_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="activity_id", referencedColumnName="id")}
     * )
     */
    private $activities;

    /**
     * @var Collection|Panel[]
     *
     * @ORM\ManyToMany(targetEntity="Panel", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *      name="certificates_panels",
     *      joinColumns={@ORM\JoinColumn(name="certificate_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="panel_id", referencedColumnName="id")}
     * )
     */
    private $panels;

    /**
     * @var Collection|Thesis[]
     *
     * @ORM\ManyToMany(targetEntity="Thesis", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *      name="certificates_theses",
     *      joinColumns={@ORM\JoinColumn(name="certificate_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="thesis_id", referencedColumnName="id")}
     * )
     */
    private $theses;

    /**
     * @var Collection|Division[]
     *
     * @ORM\ManyToMany(targetEntity="Division", cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *      name="certificates_divisions",
     *      joinColumns={@ORM\JoinColumn(name="certificate_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="division_id", referencedColumnName="id")}
     * )
     */
    private $divisions;

    /**
     * Certificate constructor.
     */
    public function __construct()
    {
        $this->userArticles = new ArrayCollection();
        $this->userThemes = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->panels = new ArrayCollection();
        $this->theses = new ArrayCollection();
        $this->divisions = new ArrayCollection();
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
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * @param bool|null $isActive
     *
     * @return $this
     */
    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getVariables(): ?array
    {
        return $this->variables;
    }

    /**
     * @param array|null $variables
     *
     * @return Certificate
     */
    public function setVariables(?array $variables): Certificate
    {
        $this->variables = $variables;
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
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime|null $updatedAt
     *
     * @return $this
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
     * @return $this
     */
    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
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
     * @return Collection|UserThemes[]
     */
    public function getUserThemes(): Collection
    {
        return $this->userThemes;
    }

    /**
     * @param UserThemes $userTheme
     *
     * @return $this
     */
    public function addUserTheme(UserThemes $userTheme): self
    {
        if (! $this->userThemes->contains($userTheme)) {
            $this->userThemes[] = $userTheme;
        }

        return $this;
    }

    /**
     * @param UserThemes $userTheme
     *
     * @return $this
     */
    public function removeUserTheme(UserThemes $userTheme): self
    {
        $this->userThemes->removeElement($userTheme);

        return $this;
    }

    /**
     * @return Activity[]|Collection
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * @param Activity $activity
     *
     * @return $this
     */
    public function addActivity(Activity $activity): self
    {
        if (! $this->activities->contains($activity)) {
            $this->activities[] = $activity;
        }

        return $this;
    }

    /**
     * @param Activity $activity
     *
     * @return $this
     */
    public function removeActivity(Activity $activity): self
    {
        $this->activities->removeElement($activity);

        return $this;
    }

    /**
     * @return Panel[]|Collection
     */
    public function getPanels()
    {
        return $this->panels;
    }

    /**
     * @param Panel $panel
     *
     * @return $this
     */
    public function addPanel(Panel $panel): self
    {
        if (! $this->panels->contains($panel)) {
            $this->panels[] = $panel;
        }

        return $this;
    }

    /**
     * @param Panel $panel
     *
     * @return $this
     */
    public function removePanel(Panel $panel): self
    {
        $this->panels->removeElement($panel);

        return $this;
    }

    /**
     * @return Thesis[]|Collection
     */
    public function getTheses()
    {
        return $this->theses;
    }

    /**
     * @param Thesis $thesis
     *
     * @return $this
     */
    public function addThesis(Thesis $thesis): self
    {
        if (! $this->theses->contains($thesis)) {
            $this->theses[] = $thesis;
        }

        return $this;
    }

    /**
     * @param Thesis $thesis
     *
     * @return $this
     */
    public function removeThesis(Thesis $thesis): self
    {
        $this->theses->removeElement($thesis);

        return $this;
    }

    /**
     * @return Division[]|Collection
     */
    public function getDivisions()
    {
        return $this->divisions;
    }

    /**
     * @param Division $division
     *
     * @return $this
     */
    public function addDivision(Division $division): self
    {
        if (! $this->divisions->contains($division)) {
            $this->divisions[] = $division;
        }

        return $this;
    }

    /**
     * @param Division $division
     *
     * @return $this
     */
    public function removeDivision(Division $division): self
    {
        $this->divisions->removeElement($division);

        return $this;
    }

    public function getHash(): string
    {
        return base64_encode(password_hash($this->getId(), PASSWORD_BCRYPT));
    }
}
