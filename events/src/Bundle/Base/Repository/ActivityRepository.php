<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\Activity;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
        $this->setAlias('a');
    }

    /**
     * @param array $criteria
     *
     * @return QueryBuilder
     */
    public function list(array $criteria = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->select('a');
        // $qb->where($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));

        /*if (isset($criteria['status']) && trim($criteria['status']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('statusEvaluation'), (int)$criteria['status']));
        }*/

        if (isset($criteria['type']) && trim($criteria['type']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('activityType'), (int)$criteria['type']));
        }

        if (isset($criteria['division']) && trim($criteria['division']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('division'), (int)$criteria['division']));
        }

        if (isset($criteria['q']) && trim($criteria['q']) !== '') {
            $qb->andWhere($qb->expr()->orX(
                'a.titlePortuguese LIKE :q',
                'a.descriptionPortuguese LIKE :q',

                'a.titleEnglish LIKE :q',
                'a.descriptionEnglish LIKE :q',

                'a.titleSpanish LIKE :q',
                'a.descriptionSpanish LIKE :q',
            ));

            $qb->setParameter('q', '%' . trim($criteria['q']) . '%');
        }

        $qb->orderBy($this->replaceFieldAlias('titlePortuguese'), 'ASC');

        return $qb;
    }
}
