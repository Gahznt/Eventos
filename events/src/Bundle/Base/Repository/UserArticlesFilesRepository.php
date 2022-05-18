<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\UserArticlesFiles;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserArticlesFiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserArticlesFiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserArticlesFiles[]    findAll()
 * @method UserArticlesFiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserArticlesFiles[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 */
class UserArticlesFilesRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserArticlesFiles::class);
        $this->setAlias('uf');
    }
}
