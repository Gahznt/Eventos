<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\UserThemeKeyword;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserThemeKeyword|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserThemeKeyword|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserThemeKeyword[]    findAll()
 * @method UserThemeKeyword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserThemeKeywordRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserThemeKeyword::class);
        $this->setAlias('u');
    }
}
