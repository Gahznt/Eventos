<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\State;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method State|null find($id, $lockMode = null, $lockVersion = null)
 * @method State|null findOneBy(array $criteria, array $orderBy = null)
 * @method State[]    findAll()
 * @method State[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method State[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [], $in = [])
 */
class StateRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, State::class);
        $this->setAlias('s');
    }
}
