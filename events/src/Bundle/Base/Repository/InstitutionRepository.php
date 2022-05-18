<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\Institution;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Institution|null find($id, $lockMode = null, $lockVersion = null)
 * @method Institution|null findOneBy(array $criteria, array $orderBy = null)
 * @method Institution[]    findAll()
 * @method Institution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InstitutionRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Institution::class);
        $this->setAlias('i');
    }

    /**
     * @param array $criteria
     *
     * @return QueryBuilder
     */
    public function list(array $criteria = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->select('i');
        $qb->where($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));

        if (isset($criteria['status']) && trim($criteria['status']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('status'), (int)$criteria['status']));
        }

        if (isset($criteria['q']) && trim($criteria['q']) !== '') {
            $qb->andWhere($qb->expr()->orX(
                'i.name LIKE :q',
                'i.initials LIKE :q',
            ));

            $qb->setParameter('q', '%' . trim($criteria['q']) . '%');
        }

        $qb->orderBy($this->replaceFieldAlias('name'), 'ASC');

        return $qb;
    }
}
