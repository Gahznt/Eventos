<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\UserArticlesAuthors;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserArticlesAuthors|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserArticlesAuthors|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserArticlesAuthors[]    findAll()
 * @method UserArticlesAuthors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserArticlesAuthors[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 */
class UserArticlesAuthorsRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserArticlesAuthors::class);
        $this->setAlias('uaa');
    }
}
