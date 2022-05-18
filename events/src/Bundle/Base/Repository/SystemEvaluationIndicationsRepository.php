<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\SystemEvaluationIndications;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Entity\UserEvaluationArticles;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SystemEvaluationIndications|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemEvaluationIndications|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemEvaluationIndications[]    findAll()
 * @method SystemEvaluationIndications[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemEvaluationIndicationsRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemEvaluationIndications::class);
        $this->setAlias('sei');
    }

    public function findByFilters(array $filters = [])
    {
        $queryBuilder = $this->createQueryBuilder($this->getAlias());
        $queryBuilder->leftJoin(UserArticles::class, 'ua', Join::WITH, 'ua.id=sei.userArticles');

        if (isset($filters['division']) && !is_null($filters['division'])) {
            $queryBuilder->andWhere('ua.divisionId = :division');
            $queryBuilder->setParameter(':division', $filters['division']);
        }

        if (isset($filters['userEvaluator']) && !is_null($filters['userEvaluator'])) {
            $queryBuilder->andWhere('sei.userEvaluator = :userEvaluator');
            $queryBuilder->setParameter(':userEvaluator', $filters['userEvaluator']);
        }

        if (isset($filters['search']) && !is_null($filters['search'])) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('ua.title',
                $queryBuilder->expr()->literal('%' . $filters['search'] . '%')));
        }

        if (isset($filters['userThemes']) && !is_null($filters['userThemes'])) {
            $queryBuilder->andWhere('ua.userThemes = :userThemes');
            $queryBuilder->setParameter(':userThemes', $filters['userThemes']);
        }

        $dbquery = $queryBuilder
            ->getQuery();
 // // ->setFetchMode(UserArticles::class, 'ua', ClassMetadata::FETCH_EAGER);

        return $dbquery->execute();
    }

    /**
     * @param Edition $edition
     * @param User $user
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCountByEdition(Edition $edition, User $user)
    {
        $queryBuilder = $this->createQueryBuilder($this->getAlias());
        $queryBuilder->select('count(sei.id)');
        $queryBuilder->leftJoin(UserArticles::class, 'ua', Join::WITH, 'ua.id=sei.userArticles');
        $queryBuilder->andWhere('ua.editionId = :edition');
        $queryBuilder->setParameter(':edition', $edition);
        $queryBuilder->andWhere('sei.userEvaluator = :userEvaluator');
        $queryBuilder->setParameter(':userEvaluator', $user);

        return $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }
}
