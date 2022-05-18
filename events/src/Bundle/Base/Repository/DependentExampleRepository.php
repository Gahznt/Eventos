<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\DependentExample;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DependentExample|null find($id, $lockMode = null, $lockVersion = null)
 * @method DependentExample|null findOneBy(array $criteria, array $orderBy = null)
 * @method DependentExample[]    findAll()
 * @method DependentExample[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DependentExampleRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DependentExample::class);
        $this->setAlias('d');
    }
}
