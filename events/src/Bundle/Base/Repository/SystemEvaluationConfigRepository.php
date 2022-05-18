<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\SystemEvaluationConfig;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SystemEvaluationConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemEvaluationConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemEvaluationConfig[]    findAll()
 * @method SystemEvaluationConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemEvaluationConfigRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemEvaluationConfig::class);
        $this->setAlias('sec');
    }
}
