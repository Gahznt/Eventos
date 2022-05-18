<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\Thesis;
use App\Bundle\Base\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Thesis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Thesis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Thesis[]    findAll()
 * @method Thesis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThesisRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Thesis::class);
        $this->setAlias('t');
    }

    /**
     * @param User $user
     *
     * @return int|mixed|string|Thesis[]
     */
    public function getUserSubmissions(User $user)
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->select('t');
        $qb->where($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));

        $qb->where($qb->expr()->eq('t.user', $user->getId()));

        $qb->orderBy($this->replaceFieldAlias('createdAt'), 'DESC');

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
        $qb->select('t');
        $qb->where($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));

        if (isset($criteria['status']) && trim($criteria['status']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('status'), (int)$criteria['status']));
        }

        if (isset($criteria['modality']) && trim($criteria['modality']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('modality'), (int)$criteria['modality']));
        }

        if (isset($criteria['division']) && trim($criteria['division']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('division'), (int)$criteria['division']));
        }

        if (isset($criteria['theme']) && trim($criteria['theme']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('userThemes'), (int)$criteria['theme']));
        }

        if (isset($criteria['q']) && trim($criteria['q']) !== '') {
            $qb->andWhere($qb->expr()->orX(
                't.id LIKE :iq',
                't.title LIKE :q',
                't.advisorName LIKE :q',
            ));

            $qb->setParameter('iq', (int)$criteria['q']);
            $qb->setParameter('q', '%' . trim($criteria['q']) . '%');
        }

        $qb->orderBy($this->replaceFieldAlias('createdAt'), 'DESC');

        return $qb;
    }
}
