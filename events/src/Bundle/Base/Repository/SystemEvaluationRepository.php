<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\SystemEvaluation;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserArticles;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SystemEvaluation|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemEvaluation|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemEvaluation[]    findAll()
 * @method SystemEvaluation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemEvaluationRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemEvaluation::class);
        $this->setAlias('se');
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
        $queryBuilder = $this->createQueryBuilder($this->getAlias());
        $queryBuilder->leftJoin(UserArticles::class, 'ua', Join::WITH, 'ua.id=se.userArticles');

        if (isset($filters['division'])) {
            $queryBuilder->andWhere('ua.divisionId = :division');
            $queryBuilder->setParameter(':division', $filters['division']);
        }

        if (isset($filters['search'])) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('ua.title', $queryBuilder->expr()->literal('%' . $filters['search'] . '%')));
        }

        if (isset($filters['status'])) {
            $queryBuilder->andWhere('ua.status = :status');
            $queryBuilder->setParameter(':status', $filters['status']);
        }

        if (isset($filters['userThemes'])) {
            $queryBuilder->andWhere('ua.userThemes = :userThemes');
            $queryBuilder->setParameter(':userThemes', $filters['userThemes']);
        }

//        if (isset($filters['type'])) {
//            $queryBuilder->andWhere('se.type', ':type');
//            $queryBuilder->setParameter(':type', $filters['type']);
//        }

        $dbquery = $queryBuilder
            ->getQuery();
  //->setFetchMode(UserArticles::class, 'uea', ClassMetadata::FETCH_EAGER);

        return $dbquery->execute();
    }

    public function findByTeste(array $filters = [])
    {
        $queryBuilder = $this->createQueryBuilder($this->getAlias());
        $queryBuilder->setMaxResults(2);
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('se.deletedAt'));

        $queryBuilder->leftJoin(UserArticles::class, 'ua', Join::WITH, 'ua.id=se.userArticles');
        $queryBuilder->leftJoin(Division::class, 'd', Join::WITH, 'd.id=ua.divisionId');

        if (isset($filters['userArticles']) && ! is_null($filters['userArticles'])) {
            $queryBuilder->andWhere('ua.id = :article');
            $queryBuilder->setParameter(':article', $filters['userArticles']);
        }

        if (isset($filters['division']) && ! is_null($filters['division'])) {
            $queryBuilder->andWhere('d.id = :division');
            $queryBuilder->setParameter(':division', $filters['division']);
        }

        $queryBuilder->orderBy('se.createdAt', Criteria::ASC);

        $dbquery = $queryBuilder
            ->getQuery();
 // // ->setFetchMode(UserArticles::class, 'uea', ClassMetadata::FETCH_EAGER) // ->setFetchMode(Division::class, 'd', ClassMetadata::FETCH_EAGER);

        return $dbquery->execute();
    }

    /**
     * @param Edition $edition
     * @param User $user
     *
     * @return int|mixed|string|SystemEvaluation[]
     */
    public function findByEditionAndEvaluator(Edition $edition, User $user)
    {
        $qb = $this->createQueryBuilder('se');

        $qb->andWhere($qb->expr()->isNull('se.deletedAt'));
        $qb->andWhere($qb->expr()->eq('se.userOwner', $user->getId()));


        $qb->innerJoin(UserArticles::class, 'ua', Join::WITH, 'ua.id = se.userArticles');
        $qb->andWhere($qb->expr()->isNull('ua.deletedAt'));

        $qb->innerJoin(Edition::class, 'e', Join::WITH, 'e.id = ua.editionId');
        $qb->andWhere($qb->expr()->isNull('e.deletedAt'));

        $qb->andWhere($qb->expr()->eq('e.id', $edition->getId()));

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return int|mixed|string|SystemEvaluation[]
     */
    public function findUniqueEvaluatorsByEdition(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $qb = $this->createQueryBuilder('se');

        $qb->andWhere($qb->expr()->isNull('se.deletedAt'));


        $qb->innerJoin(UserArticles::class, 'ua', Join::WITH, 'ua.id = se.userArticles');
        $qb->andWhere($qb->expr()->isNull('ua.deletedAt'));

        $qb->innerJoin(Edition::class, 'e', Join::WITH, 'e.id = ua.editionId');
        $qb->andWhere($qb->expr()->isNull('e.deletedAt'));

        $qb->andWhere($qb->expr()->eq('e.id', $edition->getId()));

        $qb->addGroupBy('se.userOwner');

        if (null !== $firstResult) {
            $qb->setFirstResult($firstResult);
        }

        if (null !== $maxResults) {
            $qb->setMaxResults($maxResults);
        }

        return $qb->getQuery()->getResult();
    }
}
