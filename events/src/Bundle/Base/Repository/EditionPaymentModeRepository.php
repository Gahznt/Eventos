<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\EditionPaymentMode;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EditionPaymentMode|null find($id, $lockMode = null, $lockVersion = null)
 * @method EditionPaymentMode|null findOneBy(array $criteria, array $orderBy = null)
 * @method EditionPaymentMode[]    findAll()
 * @method EditionPaymentMode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditionPaymentModeRepository extends Repository
{
    /**
     * EditionPaymentModeRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditionPaymentMode::class);
        $this->setAlias('em');
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
            'name',
            'value',
            'type',
            'initials',
        ]))
            ->andWhere($qb->expr()->eq($this->replaceFieldAlias('edition'), $editionId))
            ->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')))
            ->addOrderBy($this->replaceFieldAlias('id'), 'DESC');
    }
}
