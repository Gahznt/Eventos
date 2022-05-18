<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\ThemeSubmissionConfig;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ThemeSubmissionConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method ThemeSubmissionConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method ThemeSubmissionConfig[]    findAll()
 * @method ThemeSubmissionConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThemeSubmissionConfigRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThemeSubmissionConfig::class);
        $this->setAlias('tsc');
    }

    public function queryAll(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->addOrderBy('c.isCurrent', 'DESC');
        $queryBuilder->addOrderBy('c.isAvailable', 'DESC');
        $queryBuilder->addOrderBy('c.isEvaluationAvailable', 'DESC');
        $queryBuilder->addOrderBy('c.year', 'DESC');
        return $queryBuilder;
    }
}
