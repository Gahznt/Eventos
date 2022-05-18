<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\UserAssociation;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserAssociation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserAssociation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserAssociation[]    findAll()
 * @method UserAssociation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserAssociationRepository extends Repository
{
    /**
     * UserAssociationRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAssociation::class);
        $this->setAlias('ua');
    }
}
