<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\UserEvaluationArticles;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserEvaluationArticles|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserEvaluationArticles|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserEvaluationArticles[]    findAll()
 * @method UserEvaluationArticles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserEvaluationArticles[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 */
class UserEvaluationArticlesRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEvaluationArticles::class);
        $this->setAlias('u');
    }
}