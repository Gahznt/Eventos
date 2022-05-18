<?php

namespace App\Repository\Bundle\Base\Entity;

use App\Bundle\Base\Entity\PaymentUserAssociationDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaymentUserAssociationDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentUserAssociationDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentUserAssociationDetails[]    findAll()
 * @method PaymentUserAssociationDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentUserAssociationDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentUserAssociationDetails::class);
    }

    // /**
    //  * @return PaymentUserAssociationDetails[] Returns an array of PaymentUserAssociationDetails objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PaymentUserAssociationDetails
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
