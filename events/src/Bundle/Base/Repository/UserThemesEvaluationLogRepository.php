<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\UserThemesEvaluationLog;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserThemesEvaluationLogRepository|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserThemesEvaluationLogRepository|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserThemesEvaluationLogRepository[]    findAll()
 * @method UserThemesEvaluationLogRepository[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserThemesEvaluationLogRepository[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 */
class UserThemesEvaluationLogRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserThemesEvaluationLog::class);
        $this->setAlias('utel');
    }

    /**
     * @param array $filters
     * @param string|null $sortBy
     * @param string|null $direction
     * @return mixed
     */
    public function findByFilters(array $filters, ?string $sortBy = null, ?string $direction = null)
    {
        $sortableFields = ['id', 'user', 'action', 'createdAt'];
        $direction = $direction ? $direction : "DESC";
        $sortBy = in_array($sortBy, $sortableFields) ? $sortBy : 'createdAt';
        $queryBuilder = $this->createQueryBuilder($this->getAlias());

        if (isset($filters['id']) && $filters['id']) {
            $queryBuilder->andWhere('utel.id = :id');
            $queryBuilder->setParameter('id', $filters['id']);
        }

        if (isset($filters['theme']) && $filters['theme']) {
            $queryBuilder->andWhere('utel.userThemes = :theme');
            $queryBuilder->setParameter('theme', $filters['theme']);
        }

        if (isset($filters['user']) && $filters['user']) {
            $queryBuilder->andWhere('utel.user = :user');
            $queryBuilder->setParameter('user', $filters['user']);
        }

        if (isset($filters['action']) && $filters['action']) {
            $queryBuilder->andWhere('utel.action = :action');
            $queryBuilder->setParameter('action', $filters['action']);
        }

        if (isset($filters['visibleAuthor']) && $filters['visibleAuthor']) {
            $queryBuilder->andWhere('utel.visibleAuthor = :visibleAuthor');
            $queryBuilder->setParameter('visibleAuthor', $filters['visibleAuthor']);
        }

        $dbquery = $queryBuilder
            ->orderBy('utel.' . $sortBy, $direction)
            ->getQuery();

        return $dbquery->execute();
    }
}
