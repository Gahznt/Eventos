<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\DivisionCoordinator;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\UserCommittee;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserCommittee|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCommittee|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCommittee[]    findAll()
 * @method UserCommittee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserCommittee[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 * @method UserCommittee[]    executeSql(string $sql)
 */
class UserCommitteeRepository extends Repository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCommittee::class);
        $this->setAlias('ucttee');
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return int|mixed|string|UserCommittee[]
     */
    public function findByEdition(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $qb = $this->createQueryBuilder('ucttee');

        $qb->andWhere($qb->expr()->isNull('ucttee.deletedAt'));

        $qb->andWhere($qb->expr()->eq('ucttee.edition', $edition->getId()));

        if (null !== $firstResult) {
            $qb->setFirstResult($firstResult);
        }

        if (null !== $maxResults) {
            $qb->setMaxResults($maxResults);
        }

        return $qb->getQuery()->getResult();
    }
}
