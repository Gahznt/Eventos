<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\UserThemesBibliographies;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserThemesBibliographies|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserThemesBibliographies|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserThemesBibliographies[]    findAll()
 * @method UserThemesBibliographies[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserThemesBibliographies[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 */
class UserThemesBibliographiesRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserThemesBibliographies::class);
        $this->setAlias('utb');
    }
}
