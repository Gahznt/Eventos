<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\UserConsents;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserConsents|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserConsents|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserConsents[]    findAll()
 * @method UserConsents[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserConsentsRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserConsents::class);
        $this->setAlias('uc');
    }
}
