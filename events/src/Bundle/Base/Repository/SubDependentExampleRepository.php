<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\SubDependentExample;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubDependentExample|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubDependentExample|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubDependentExample[]    findAll()
 * @method SubDependentExample[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubDependentExampleRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubDependentExample::class);
        $this->setAlias('s');
    }
}
