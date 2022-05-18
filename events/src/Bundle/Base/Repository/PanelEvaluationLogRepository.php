<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\PanelEvaluationLog;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PanelEvaluationLogRepository|null find($id, $lockMode = null, $lockVersion = null)
 * @method PanelEvaluationLogRepository|null findOneBy(array $criteria, array $orderBy = null)
 * @method PanelEvaluationLogRepository[]    findAll()
 * @method PanelEvaluationLogRepository[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method PanelEvaluationLogRepository[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 */
class PanelEvaluationLogRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PanelEvaluationLog::class);
        $this->setAlias('pel');
    }
}