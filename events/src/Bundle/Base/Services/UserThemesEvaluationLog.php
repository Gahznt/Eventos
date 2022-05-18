<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesEvaluationLog as Entity;
use App\Bundle\Base\Repository\UserThemesEvaluationLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Class UserThemesEvaluationLog
 * @package App\Bundle\Base\Services
 */
class UserThemesEvaluationLog extends ServiceBase implements ServiceInterface
{
    /**
     * @var UserThemesEvaluationLogRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UserThemesEvaluationLog constructor.
     * @param UserThemesEvaluationLogRepository $userThemesEvaluationLogRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        UserThemesEvaluationLogRepository $userThemesEvaluationLogRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->repository = $userThemesEvaluationLogRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param UserThemes|null $userThemes
     * @param null|UserInterface $user
     * @param null $ip
     * @param null $action
     * @param null $reason
     * @param bool $visible
     * @return bool
     */
    public function register(
        ?UserThemes $userThemes = null,
        ?UserInterface $user = null,
        $ip = null,
        $action = null,
        $reason = null,
        $visible = false
    ) {
        try {
            $model = new Entity();
            $model->setUserThemes($userThemes);
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