<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\SystemEnsalementRooms;
use App\Bundle\Base\Entity\SystemEnsalementSessions;
use App\Bundle\Base\Entity\SystemEnsalementSlots;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SystemEnsalementSlots|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemEnsalementSlots|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemEnsalementSlots[]    findAll()
 * @method SystemEnsalementSlots[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemEnsalementSlotsRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemEnsalementSlots::class);
        $this->setAlias('ses');
    }

    /**
     * @param int $edition
     * @return QueryBuilder
     */
    public function list(int $edition): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->innerJoin(SystemEnsalementRooms::class, 'ser', 'WITH', 'ser.id=ses.systemEnsalementRooms');
        $qb->innerJoin(SystemEnsalementSessions::class, 'sess', 'WITH', 'sess.id=ses.systemEnsalementSessions');

        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('edition'), $edition))
            ->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')))
            ->addOrderBy('sess.date', 'ASC')
            ->addOrderBy('sess.start', 'ASC')
            ->addOrderBy('sess.type', 'ASC')
            ->addOrderBy('ser.name', 'ASC')
            ->addOrderBy('ser.local', 'ASC');

        return $qb;
    }
}
