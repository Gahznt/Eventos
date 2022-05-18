<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\SystemEnsalementRooms;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SystemEnsalementRooms|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemEnsalementRooms|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemEnsalementRooms[]    findAll()
 * @method SystemEnsalementRooms[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemEnsalementRoomsRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemEnsalementRooms::class);
        $this->setAlias('ser');
    }

    /**
     * @param int $edition
     * @return QueryBuilder
     */
    public function list(int $edition): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('edition'), $edition))
            ->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')))
            ->addOrderBy('ser.name', 'ASC')
            ->addOrderBy('ser.local', 'ASC');

        return $qb;
    }
}
