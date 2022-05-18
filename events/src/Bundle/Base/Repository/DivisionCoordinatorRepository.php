<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\DivisionCoordinator;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\SystemEvaluation;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DivisionCoordinator|null find($id, $lockMode = null, $lockVersion = null)
 * @method DivisionCoordinator|null findOneBy(array $criteria, array $orderBy = null)
 * @method DivisionCoordinator[]    findAll()
 * @method DivisionCoordinator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method DivisionCoordinator[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 * @method DivisionCoordinator[]    executeSql(string $sql)
 */
class DivisionCoordinatorRepository extends Repository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DivisionCoordinator::class);
        $this->setAlias('dc');
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return int|mixed|string|DivisionCoordinator[]
     */
    public function findByEdition(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $qb = $this->createQueryBuilder('dc');

        $qb->andWhere($qb->expr()->isNull('dc.deletedAt'));

        $qb->andWhere($qb->expr()->eq('dc.edition', $edition->getId()));

        if (null !== $firstResult) {
            $qb->setFirstResult($firstResult);
        }

        if (null !== $maxResults) {
            $qb->setMaxResults($maxResults);
        }

        return $qb->getQuery()->getResult();
    }
}
