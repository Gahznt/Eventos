<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\Speaker;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Speaker|null find($id, $lockMode = null, $lockVersion = null)
 * @method Speaker|null findOneBy(array $criteria, array $orderBy = null)
 * @method Speaker[]    findAll()
 * @method Speaker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpeakerRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Speaker::class);
        $this->setAlias('s');
    }

    /**
     * @param int $editionId
     * @return QueryBuilder
     */
    public function list(int $editionId): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        return $qb->select($this->replaceFieldAlias([
            'id',
            'type',
            'position',
            'namePortuguese',
            'status',
            'isHomolog',
        ]))
            ->where($this->replaceFieldAlias('edition = :editionId'))
            ->setParameter('editionId', $editionId)
            ->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')))
            ->orderBy($this->replaceFieldAlias('id'), 'DESC');
    }
}
