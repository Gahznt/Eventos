<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\SystemEvaluationAveragesArticles;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SystemEvaluationAveragesArticles|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemEvaluationAveragesArticles|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemEvaluationAveragesArticles[]    findAll()
 * @method SystemEvaluationAveragesArticles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method SystemEvaluationAveragesArticles[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 * @method SystemEvaluationAveragesArticles[]    executeSql(string $sql)
 */
class SystemEvaluationAveragesArticlesAveragesRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemEvaluationAveragesArticles::class);
        $this->setAlias('seaa');
    }
}
