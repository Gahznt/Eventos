<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="user_themes_evaluation_log")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\UserThemesEvaluationLogRepository")
 */
class UserThemesEvaluationLog
{
    const ACTION_SUBMISSION = 'SUBMISSÃO';
    const ACTION_CANCEL_SUBMISSION = 'CANCELAMENTO DE SUBMISSÃO';
    const ACTION_UPDATE_STATUS = 'ALTERAÇÃO DE STATUS';
    const ACTION_UPDATE_DATA = 'ALTERAÇÃO DE DADOS';
    const ACTION_CONSIDERATION = 'CONSIDERAÇÃO';

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="bigint", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var UserThemes
     *
     * @ORM\ManyToOne(targetEntity="UserThemes", inversedBy="logs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_themes_id", referencedColumnName="id")
     * })
     */
    private $userThemes;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userThemesEvaluationLogs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private ?User $user = null;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip", type="string", nullable=true)
     */
    private $ip;

    /**
     * @var string|null
     *
     * @ORM\Column(name="action", type="string", nullable=true)
     */
    private $action;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reason", type="text", nullable=true)
     */
    private $reason;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="visible_author", type="boolean", nullable=false, options={"default"="1"})
     */
    private $visibleAuthor;

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
     * @return mixed
     */
    public function getUserThemes()
    {
        return $this->userThemes;
    }

    /**
     * @param $userThemes
     *
     * @return UserThemesEvaluationLog
     */
    public function setUserThemes(?UserThemes $userThemes): self
    {
        $this->userThemes = $userThemes;

        return $this;
    }


    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return UserThemesEvaluationLog
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

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
     * @return UserThemesEvaluationLog
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    /**
     * @return null|string
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @param null|string $ip
     *
     * @return UserThemesEvaluationLog
     */
    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }


    /**
     * @return null|string
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @param null|string $action
     *
     * @return UserThemesEvaluationLog
     */
    public function setAction(?string $action): self
    {
        $this->action = $action;

        return $this;
    }


    /**
     * @return null|string
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @param null|string $reason
     *
     * @return UserThemesEvaluationLog
     */
    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }


    /**
     * @return null|string
     */
    public function getVisibleAuthor(): ?bool
    {
        return $this->visibleAuthor;
    }

    /**
     * @param null|bool $visibleAuthor
     *
     * @return UserThemesEvaluationLog
     */
    public function setVisibleAuthor(?bool $visibleAuthor): self
    {
        $this->visibleAuthor = $visibleAuthor;

        return $this;
    }
}
