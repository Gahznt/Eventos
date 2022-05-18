<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\SystemEnsalementSessions;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SystemEnsalementSessions|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemEnsalementSessions|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemEnsalementSessions[]    findAll()
 * @method SystemEnsalementSessions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemEnsalementSessionsRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemEnsalementSessions::class);
        $this->setAlias('sess');
    }

    /**
     * @param int $editionId
     *
     * @return QueryBuilder|SystemEnsalementSessions[]
     */
    public function list(int $edition): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('edition'), $edition))
            ->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')))
            ->addOrderBy('sess.date', 'ASC')
            ->addOrderBy('sess.start', 'ASC')
            ->addOrderBy('sess.type', 'ASC');

        return $qb;
    }
}
