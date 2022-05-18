<?php

namespace App\Bundle\Base\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="system_evaluation_log")
 * @ORM\Entity(repositoryClass="App\Bundle\Base\Repository\SystemEvaluationLogRepository")
 */
class SystemEvaluationLog
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
     * @var SystemEvaluation|null
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="SystemEvaluation", inversedBy="logs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sytem_evaluation_id", referencedColumnName="id")
     * })
     */
    private $systemEvaluation;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="systemEvaluationLogs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_log_id", referencedColumnName="id")
     * })
     */
    private ?User $userLog = null;

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
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var int|null
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ip", type="string", nullable=true)
     */
    private $ip;

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
     * @return SystemEvaluation
     */
    public function getSystemEvaluation(): SystemEvaluation
    {
        return $this->systemEvaluation;
    }

    /**
     * @param SystemEvaluation $systemEvaluation
     *
     * @return SystemEvaluationLog
     */
    public function setSystemEvaluation(SystemEvaluation $systemEvaluation): self
    {
        $this->systemEvaluation = $systemEvaluation;

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
     * @return SystemEvaluationLog
     */
    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param null|string $content
     *
     * @return SystemEvaluationLog
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;

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
     * @return SystemEvaluationLog
     */
    public function setStatus(?int $status): self
    {
        $this->status = $status;

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
     * @return SystemEvaluationLog
     */
    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return User
     */
    public function getUserLog(): User
    {
        return $this->userLog;
    }

    /**
     * @param User|null $userLog
     *
     * @return SystemEvaluationLog
     */
    public function setUserLog(?User $userLog): self
    {
        $this->userLog = $userLog;

        return $this;
    }

    /**
     * @return null|string
     */
    public function __toString()
    {
        return (string)$this->status;
    }
}
