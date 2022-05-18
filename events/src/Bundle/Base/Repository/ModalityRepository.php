<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\Modality;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Modality|null find($id, $lockMode = null, $lockVersion = null)
 * @method Modality|null findOneBy(array $criteria, array $orderBy = null)
 * @method Modality[]    findAll()
 * @method Modality[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModalityRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Modality::class);
        $this->setAlias('mo');
    }
}
