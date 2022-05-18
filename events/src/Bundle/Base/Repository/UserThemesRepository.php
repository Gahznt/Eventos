<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\ThemeSubmissionConfig;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesDetails;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserThemes|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserThemes|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserThemes[]    findAll()
 * @method UserThemes[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 */
class UserThemesRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserThemes::class);
        $this->setAlias('ut');
    }

    /**
     * Finds entities by a set of criteria.
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return array The objects.
     */
    public function findBy(array $criteria, $orderBy = ['position' => 'asc'], $limit = null, $offset = null)
    {
        return parent::findBy($criteria, $orderBy, $limit, $offset);
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
        $sortableFields = ['id', 'status'];
        $direction = strtoupper($direction) == "DESC" ? strtoupper($direction) : "ASC";
        $sortBy = in_array($sortBy, $sortableFields) ? $sortBy : 'position';
        $queryBuilder = $this->createQueryBuilder($this->getAlias());

        if (isset($filters['id']) && $filters['id']) {
            $queryBuilder->andWhere('ut.id = :id');
            $queryBuilder->setParameter('id', $filters['id']);
        }

        if (isset($filters['divisionId']) && $filters['divisionId']) {
            $queryBuilder->andWhere('ut.division = :divisionId');
            $queryBuilder->setParameter('divisionId', $filters['divisionId']);
        }

        if (isset($filters['status']) && $filters['status']) {
            $queryBuilder->andWhere('ut.status = :status');
            $queryBuilder->setParameter('status', $filters['status']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('utd.portugueseTitle',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('utd.englishTitle',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('utd.spanishTitle',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('utd.portugueseKeywords',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('utd.englishKeywords',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('utd.spanishKeywords',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%'))
            ));
        }

        $dbquery = $queryBuilder
            ->join(UserThemesDetails::class, 'utd', 'WITH', 'ut.id = utd.userThemes')
            ->orderBy('ut.' . $sortBy, $direction)
            ->getQuery();

        return $dbquery->execute();
    }


    /**
     * @return mixed
     */
    public function sumDashboard(?ThemeSubmissionConfig $config = null)
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->select('identity(ut.division) as id, d.initials, count(ut.id) as qtd');
        $qb->innerJoin('ut.division', 'd');

        if ($config instanceof ThemeSubmissionConfig) {
            $qb->andWhere(
                $qb->expr()->eq('ut.themeSubmissionConfig', $config->getId())
            );
        }

        $qb->groupBy('ut.division');
        $qb->orderBy('d.id');
        $query = $qb->getQuery();
        return $query->execute();
    }

    /**
     * @return UserThemes[]
     */
    public function getUserSubmissions(int $userId, ?int $status = null)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->innerJoin($this->replaceFieldAlias('userThemesResearchers'), 'utr');

        $qb->andWhere(
            $qb->expr()->eq('utr.researcher', $userId)
        );

        if (null !== $status) {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('status'), $status));
        }

        $qb->groupBy($this->replaceFieldAlias('id'));

        $qb->orderBy($this->replaceFieldAlias('position'), 'ASC');

        return $qb->getQuery()
            ->getResult();
    }

    public function queryAllByConfig(ThemeSubmissionConfig $themeSubmissionConfig, ?array $criteria = []): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('ut');
        $queryBuilder->andWhere(
            $queryBuilder->expr()->eq('ut.themeSubmissionConfig', $themeSubmissionConfig->getId())
        );

        if (isset($criteria['division']) && '' !== trim($criteria['division'])) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq('ut.division', (int)trim($criteria['division']))
            );
        }

        if (isset($criteria['status']) && '' !== trim($criteria['status'])) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq('ut.status', (int)trim($criteria['status']))
            );
        }

        $queryBuilder->addOrderBy('ut.division', 'ASC');
        $queryBuilder->addOrderBy('ut.status', 'ASC');
        $queryBuilder->addOrderBy('ut.position', 'ASC');
        return $queryBuilder;
    }
}
