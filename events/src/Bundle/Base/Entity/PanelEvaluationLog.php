<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PanelEvaluationLog
 *
 * @ORM\Table(name="panel_evaluation_log")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\PanelEvaluationLogRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class PanelEvaluationLog
{
    const ACTION_SUBMISSION = 'SUBMISSÃO';

    const ACTION_CANCEL_SUBMISSION = 'CANCELAMENTO DE SUBMISSÃO';

    const ACTION_UPDATE_STATUS = 'ALTERAÇÃO DE STATUS';

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
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private ?\DateTime $createdAt = null;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private ?\DateTime $deletedAt = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip", type="text", length=65535, nullable=false)
     */
    private $ip;

    /**
     * @var string|null
     *
     * @ORM\Column(name="action", type="text", length=65535, nullable=false)
     */
    private $action;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reason", type="text", length=65535, nullable=true)
     */
    private $reason;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="visible_author", type="boolean", nullable=false, options={"default"="1"})
     */
    private $visibleAuthor = true;

    /**
     * @ORM\ManyToOne(targetEntity="Panel", inversedBy="panelEvaluationLogs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="panel_id", referencedColumnName="id")
     * })
     */
    private ?Panel $panel = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="panelEvaluationLogs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private ?User $user = null;

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
     * @return Panel|null
     */
    public function getPanel()
    {
        return $this->panel;
    }

    /**
     * @param Panel|null $panel
     *
     * @return $this
     */
    public function setPanel(?Panel $panel): self
    {
        $this->panel = $panel;

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
     * @return string|null
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @param string|null $ip
     *
     * @return $this
     */
    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }


    /**
     * @return string|null
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @param string|null $action
     *
     * @return $this
     */
    public function setAction(?string $action): self
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @param string|null $reason
     *
     * @return $this
     */
    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }


    /**
     * @return bool|null
     */
    public function getVisibleAuthor(): ?bool
    {
        return $this->visibleAuthor;
    }

    /**
     * @param bool|null $visibleAuthor
     *
     * @return $this
     */
    public function setVisibleAuthor(?bool $visibleAuthor): self
    {
        $this->visibleAuthor = $visibleAuthor;

        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
