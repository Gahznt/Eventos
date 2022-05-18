<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Entity\SystemEvaluation;
use App\Bundle\Base\Repository\SystemEvaluationLogRepository;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Entity\SystemEvaluationLog as Log;
use App\Bundle\Base\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DependentExample
 * @package App\Bundle\Base\Services
 */
class SystemEvaluationLog extends ServiceBase implements ServiceInterface
{
    /**
     * @var SystemEvaluationLogRepository
     */
    private $entity;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SystemEvaluationLog constructor.
     * @param SystemEvaluationLogRepository $systemEvaluationLogRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        SystemEvaluationLogRepository $systemEvaluationLogRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->entity = $systemEvaluationLogRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param SystemEvaluation $systemEvaluation
     * @param null|string $content
     * @param int|null $status
     * @param null|string $ip
     * @param User|null $userLog
     * @return bool
     */
    public function register(
        SystemEvaluation $systemEvaluation,
        ?string $content = null,
        ?int $status = null,
        ?string $ip = null,
        ?User $userLog = null
    ) {
       try {
           $systemEvaluationLog = new Log();
           $systemEvaluationLog->setCreatedAt(new \DateTime());
           $systemEvaluationLog->setContent($content);
           $systemEvaluationLog->setStatus($status);
           $systemEvaluationLog->setIp($ip);
           $systemEvaluationLog->setSystemEvaluation($systemEvaluation);
           $systemEvaluationLog->setUserLog($userLog);

           $this->entityManager->persist($systemEvaluationLog);
           $this->entityManager->flush();

           return true;
       }catch (\Exception $e) {
           return false;
       }
    }
}