<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\SystemEvaluationLog;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SystemEvaluationLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemEvaluationLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemEvaluationLog[]    findAll()
 * @method SystemEvaluationLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemEvaluationLogRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemEvaluationLog::class);
        $this->setAlias('sl');
    }
}
