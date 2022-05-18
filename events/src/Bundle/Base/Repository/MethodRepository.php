<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\Method;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Method|null find($id, $lockMode = null, $lockVersion = null)
 * @method Method|null findOneBy(array $criteria, array $orderBy = null)
 * @method Method[]    findAll()
 * @method Method[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MethodRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Method::class);
        $this->setAlias('m');
    }
}
