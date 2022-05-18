<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\EditionDiscount;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EditionDiscount|null find($id, $lockMode = null, $lockVersion = null)
 * @method EditionDiscount|null findOneBy(array $criteria, array $orderBy = null)
 * @method EditionDiscount[]    findAll()
 * @method EditionDiscount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditionDiscountRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditionDiscount::class);
        $this->setAlias('ef');
    }

    /**
     * @param int $editionId
     *
     * @return QueryBuilder
     */
    public function list(int $editionId): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        return $qb->select($this->replaceFieldAlias([
            'id',
            'userIdentifier',
            'percentage',
            'type',
        ]))
            ->andWhere($qb->expr()->eq($this->replaceFieldAlias('edition'), $editionId))
            // ->andWhere($qb->expr()->eq($this->replaceFieldAlias('isActive'), 1))
            ->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')))
            ->addOrderBy($this->replaceFieldAlias('id'), 'DESC');
    }
}
