<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\Certificate;
use App\Bundle\Base\Entity\Edition;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Certificate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Certificate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Certificate[]    findAll()
 * @method Certificate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CertificateRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Certificate::class);
        $this->setAlias('c');
    }

    /**
     * @param Edition|null $edition
     * @param array|null $criteria
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function list(?Edition $edition = null, ?array $criteria = null)
    {
        $qb = $this->createQueryBuilder('c');

        $qb->andWhere($qb->expr()->isNull('c.deletedAt'));

        if (! empty($edition)) {
            $qb->andWhere($qb->expr()->eq('c.edition', $edition->getId()));
        }

        $qb->innerJoin('c.user', 'u');
        $qb->andWhere($qb->expr()->isNull('u.deletedAt'));

        // many to many join - manytomany join
        $qb->leftJoin('c.userArticles', 'ua');
        $qb->andWhere($qb->expr()->isNull('ua.deletedAt'));

        if (! empty($criteria['user'])) {
            $qb->andWhere($qb->expr()->eq('c.user', $criteria['user']->getId()));
        }

        if (! empty($criteria['type'])) {
            $qb->andWhere($qb->expr()->eq('c.type', $criteria['type']));
        }

        if (! empty($criteria['userArticles'])) {
            $arr = [];
            foreach ($criteria['userArticles'] as $userArticle) {
                $arr[] = $userArticle->getId();
            }

            $qb->andWhere($qb->expr()->in('ua.id', $arr));
        }

        if (! empty($criteria['isActive'])) {
            $qb->andWhere($qb->expr()->eq('c.isActive', $criteria['isActive']));
        }

        if (isset($criteria['q']) && trim($criteria['q']) !== '') {
            $qb->andWhere($qb->expr()->orX(
                'c.id = :iq',
                'u.id = :iq',
                'ua.id = :iq',

                'u.name LIKE :q',
                'u.identifier LIKE :q',

                'ua.title LIKE :q',
            ));

            $qb->setParameter('iq', (int)$criteria['q']);
            $qb->setParameter('q', '%' . trim($criteria['q']) . '%');
        }

        $qb->addGroupBy('c.id');

        $qb->addOrderBy('c.type', 'ASC');
        $qb->addOrderBy('u.name', 'ASC');

        $qb->addOrderBy('c.createdAt', 'DESC');

        return $qb;
    }

    /**
     * @param array $criteria
     *
     * @return int|mixed|string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByCriteria(array $criteria)
    {
        $qb = $this->createQueryBuilder('c');

        $qb->andWhere($qb->expr()->isNull('c.deletedAt'));

        if (! empty($criteria['edition'])) {
            $qb->andWhere($qb->expr()->eq('c.edition', $criteria['edition']->getId()));
        }

        if (! empty($criteria['user'])) {
            $qb->andWhere($qb->expr()->eq('c.user', $criteria['user']->getId()));
        }

        if (! empty($criteria['type'])) {
            $qb->andWhere($qb->expr()->eq('c.type', $criteria['type']));
        }

        if (! empty($criteria['userArticles'])) {
            $arr = [];
            foreach ($criteria['userArticles'] as $userArticle) {
                $arr[] = $userArticle->getId();
            }

            // many to many join - manytomany join
            $qb->innerJoin('c.userArticles', 'ua');
            $qb->andWhere($qb->expr()->in('ua.id', $arr));
            $qb->andWhere($qb->expr()->isNull('ua.deletedAt'));
        }

        if (! empty($criteria['userThemes'])) {
            $arr = [];
            foreach ($criteria['userThemes'] as $userTheme) {
                $arr[] = $userTheme->getId();
            }

            // many to many join - manytomany join
            $qb->innerJoin('c.userThemes', 'ut');
            $qb->andWhere($qb->expr()->in('ut.id', $arr));
            $qb->andWhere($qb->expr()->isNull('ut.deletedAt'));
        }

        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
