<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Panel;
use App\Bundle\Base\Entity\PanelsPanelist;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Panel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Panel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Panel[]    findAll()
 * @method Panel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PanelRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Panel::class);
        $this->setAlias('p');
    }


    /**
     * @param array $filters
     * @param string|null $sortBy
     * @param string|null $direction
     *
     * @return mixed
     */
    public function findByFilters(array $filters, ?string $sortBy = null, ?string $direction = null)
    {
        $sortableFields = ['id', 'statusEvaluation'];
        $direction = strtoupper($direction) == "DESC" ? strtoupper($direction) : "ASC";
        $sortBy = in_array($sortBy, $sortableFields) ? $sortBy : 'id';
        $queryBuilder = $this->createQueryBuilder($this->getAlias());

        if (isset($filters['id']) && $filters['id']) {
            $queryBuilder->andWhere('p.id = :id');
            $queryBuilder->setParameter('id', $filters['id']);
        }

        if (isset($filters['divisionId']) && $filters['divisionId']) {
            $queryBuilder->andWhere('p.divisionId = :divisionId');
            $queryBuilder->setParameter('divisionId', $filters['divisionId']);
        }

        if (isset($filters['statusEvaluation']) && $filters['statusEvaluation']) {
            $queryBuilder->andWhere('p.statusEvaluation = :statusEvaluation');
            $queryBuilder->setParameter('statusEvaluation', $filters['statusEvaluation']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->like('p.title',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%'))
            );
        }

        $dbquery = $queryBuilder
            ->orderBy('p.' . $sortBy, $direction)
            ->getQuery();

        return $dbquery->execute();
    }

    /**
     * @return int|mixed|string
     */
    public function sumDashboard()
    {
        return $this->createQueryBuilder($this->getAlias())
            ->select('identity(p.divisionId) as id, d.initials, count(p.id) as qtd')
            ->innerJoin(Division::class, 'd', 'WITH', 'd.id = p.divisionId')
            ->groupBy('p.divisionId')
            ->orderBy('d.initials')
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $edition
     * @param int $userId
     *
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNumberOfPanelsPanelistByEdition(int $edition, int $userId)
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->leftJoin(PanelsPanelist::class, 'pp', 'WITH', 'pp.panelId=p.id');

        $qb->select($qb->expr()->count($this->replaceFieldAlias('id')));

        $qb->andWhere('p.editionId = :edition');
        $qb->andWhere($qb->expr()->orX(
            'p.proponentId = :userId',
            'pp.panelistId = :userId'
        ));

        $qb->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));

        $qb->setParameter('edition', $edition);
        $qb->setParameter('userId', $userId);
        $query = $qb->getQuery();
        return (int)$query->getSingleScalarResult();
    }

    /**
     * @param int $userId
     *
     * @return int|mixed|string|Panel[]
     */
    public function getUserPanels(int $userId)
    {
        $qb = $this->createQueryBuilder($this->getAlias())
            ->innerJoin(PanelsPanelist::class, 'pp', 'WITH', 'pp.panelId = p.id');

        $qb->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));
        $qb->andWhere('p.proponentId = :userId OR pp.panelistId = :userId')->setParameter(':userId', $userId);

        $qb->addOrderBy('p.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $criteria
     *
     * @return QueryBuilder
     */
    public function list(array $criteria = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->select('p');
        $qb->where($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));

        if (isset($criteria['status']) && trim($criteria['status']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('statusEvaluation'), (int)$criteria['status']));
        }

        if (isset($criteria['division']) && trim($criteria['division']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('divisionId'), (int)$criteria['division']));
        }

        if (isset($criteria['q']) && trim($criteria['q']) !== '') {
            $qb->andWhere($qb->expr()->orX(
                'p.title LIKE :q',
                'p.justification LIKE :q',
                'p.suggestion LIKE :q',
            ));

            $qb->setParameter('q', '%' . trim($criteria['q']) . '%');
        }

        $qb->orderBy($this->replaceFieldAlias('title'), 'ASC');

        return $qb;
    }
}
