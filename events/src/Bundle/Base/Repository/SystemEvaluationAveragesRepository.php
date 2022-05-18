<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\SystemEvaluationAverages;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SystemEvaluationAverages|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemEvaluationAverages|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemEvaluationAverages[]    findAll()
 * @method SystemEvaluationAverages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemEvaluationAveragesRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemEvaluationAverages::class);
        $this->setAlias('seav');
    }
}
