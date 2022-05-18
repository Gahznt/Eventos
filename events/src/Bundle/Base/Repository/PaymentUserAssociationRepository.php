<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\PaymentUserAssociation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaymentUserAssociation|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentUserAssociation|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentUserAssociation[]    findAll()
 * @method PaymentUserAssociation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentUserAssociationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentUserAssociation::class);
    }

    // /**
    //  * @return PaymentUserAssociation[] Returns an array of PaymentUserAssociation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PaymentUserAssociation
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
