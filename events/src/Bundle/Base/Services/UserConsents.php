<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Repository\UserConsentsRepository;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;
use App\Bundle\Base\Entity\UserConsents as Entity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserConsents
 * @package App\Bundle\Base\Services
 */
class UserConsents extends ServiceBase implements ServiceInterface
{

    /**
     * @var UserConsentsRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * UserConsents constructor.
     * @param UserConsentsRepository $userConsentsRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserConsentsRepository $userConsentsRepository, EntityManagerInterface $entityManager)
    {
        $this->repository = $userConsentsRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $ip
     * @return Entity[]
     */
    public function findByIp(string $ip)
    {
        return $this->repository->findBy(['ip' => $ip]);
    }

    /**
     * @param int $userId
     * @return Entity[]
     */
    public function findByUser(int $userId)
    {
        return $this->repository->findBy(['user' => $userId]);
    }

    /**
     * @param null|string $ip
     * @param int|null $status
     * @param int|null $type
     * @param null|UserInterface $user
     * @param null|string $hash
     * @return bool
     */
    public function register
    (
        ?string $ip = null,
        ?int $status = 0,
        ?int $type = 0,
        ?UserInterface $user = null,
        ?string $hash = null
    )
    {
        if ($status != Entity::USER_CONSENTS_STATUS_DECLINE && $status != Entity::USER_CONSENTS_STATUS_ACCEPT) {
            throw new NotFoundHttpException('Status not found');
        }

        try {
            /**
             * @var Entity
             */
            $model = new Entity();
            $model->setCreatedAt(new \DateTime());
            $model->setIp($ip);

            $model->setStatus($status);
            $model->setType($type);
            $model->setHash($hash);
            $model->setUser($user);

            $this->entityManager->persist($model);
            $this->entityManager->flush();

            return true;
        }catch (\Exception $e) {
            return false;
        }
    }

}