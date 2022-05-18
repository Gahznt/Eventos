<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\UserArticlesKeywords;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserArticlesKeywords|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserArticlesKeywords|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserArticlesKeywords[]    findAll()
 * @method UserArticlesKeywords[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserArticlesKeywords[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 */
class UserArticlesKeywordsRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserArticlesKeywords::class);
        $this->setAlias('uak');
    }
}
