<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Entity\Panel;
use App\Bundle\Base\Entity\PanelEvaluationLog as Entity;
use App\Bundle\Base\Repository\PanelEvaluationLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Class PanelEvaluationLog
 * @package App\Bundle\Base\Services
 */
class PanelEvaluationLog extends ServiceBase implements ServiceInterface
{
    /**
     * @var PanelEvaluationLogRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * PanelEvaluationLog constructor.
     * @param PanelEvaluationLogRepository $panelEvaluationLogRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        PanelEvaluationLogRepository $panelEvaluationLogRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->repository = $panelEvaluationLogRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Panel|null $panel
     * @param null|UserInterface $user
     * @param null $ip
     * @param null $action
     * @param null $reason
     * @param bool $visible
     * @return bool
     */
    public function register(
        ?Panel $panel = null,
        ?UserInterface $user = null,
        $ip = null,
        $action = null,
        $reason = null,
        $visible = false
    ) {
        try {
            $model = new Entity();
            $model->setPanel($panel);
            $model->setUser($user);
            $model->setIp($ip);
            $model->setCreatedAt(new \DateTime());
            $model->setAction($action);
            $model->setVisibleAuthor($visible);
            $model->setReason($reason);
            $this->entityManager->persist($model);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}