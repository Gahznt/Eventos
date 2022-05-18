<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\Institution;
use App\Bundle\Base\Entity\Program;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Program|null find($id, $lockMode = null, $lockVersion = null)
 * @method Program|null findOneBy(array $criteria, array $orderBy = null)
 * @method Program[]    findAll()
 * @method Program[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgramRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Program::class);
        $this->setAlias('p');
    }

    /**
     * @param int $eventId
     *
     * @return QueryBuilder
     */
    public function list(int $eventId): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        return $qb->select('p')
            ->where($this->replaceFieldAlias('institution = :institutionId'))
            ->setParameter('institutionId', $eventId)/**/
            ->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')))
            ->orderBy($this->replaceFieldAlias('sortPosition'), 'ASC');
    }

    /**
     * @param array $filters
     *
     * @return QueryBuilder
     */
    public function listByFilters(array $filters)
    {
        $queryBuilder = $this->createQueryBuilder($this->getAlias());

        if (isset($filters['search'])) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('p.name', $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('p.email', $queryBuilder->expr()->literal('%' . $filters['search'] . '%'))
            ));
        }

        $queryBuilder->getQuery();
 // // ->setFetchMode(Institution::class, 'i', ClassMetadata::FETCH_EAGER) // ->setFetchMode(Program::class, 'p', ClassMetadata::FETCH_EAGER);

        return $queryBuilder;
    }

    /**
     * @param array $filters
     *
     * @return mixed
     */
    public function findByFilters(array $filters)
    {
        $queryBuilder = $this->listByFilters($filters);

        $dbquery = $queryBuilder->getQuery();

        return $dbquery->execute();
    }
}
