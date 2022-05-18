<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\UserAcademics;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAcademics|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAcademics|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAcademics[]    findAll()
 * @method UserAcademics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAcademicsRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAcademics::class);
        $this->setAlias('u');
    }
}
