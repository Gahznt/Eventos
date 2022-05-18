<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\Event;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Edition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Edition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Edition[]    findAll()
 * @method Edition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditionRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Edition::class);
        $this->setAlias('s');
    }

    /**
     * @param $event
     *
     * @return mixed
     */
    public function getByEvent($event)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        return $qb
            ->select('s.id as id, s.namePortuguese as name')
            ->andWhere('s.event = :event')
            ->setParameter(':event', $event)
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $eventId
     *
     * @return QueryBuilder
     */
    public function list(int $eventId): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        return $qb->select($this->replaceFieldAlias([
            'id',
            'position',
            'namePortuguese',
            'dateStart',
            'dateEnd',
            'place',
            'status',
            'isHomolog',
        ]))
            ->andWhere($this->replaceFieldAlias('event = :eventId'))->setParameter('eventId', $eventId)
            ->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')))
            ->addOrderBy($this->replaceFieldAlias('id'), 'DESC');
    }

    /**
     * @return int|mixed|string|Edition[]
     */
    public function findNext()
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->innerJoin(Event::class, 'e', 'WITH', 'e.id = s.event');

        $qb->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));
        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('status'), 1));
        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('isShowHome'), true));
        $qb->andWhere($qb->expr()->gt($this->replaceFieldAlias('homePosition'), 0));

        $qb->andWhere($qb->expr()->isNull('e.deletedAt'));
        $qb->andWhere($qb->expr()->eq('e.status', 1));

        $qb->addGroupBy($this->replaceFieldAlias('event'));

        $qb->addOrderBy($this->replaceFieldAlias('homePosition'), 'ASC');
        $qb->addOrderBy($this->replaceFieldAlias('status'), 'ASC');
        $qb->addOrderBy($this->replaceFieldAlias('dateStart'), 'ASC');

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @return int|mixed|string|Edition[]
     */
    public function findPrevious()
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->innerJoin(Event::class, 'e', 'WITH', 'e.id = s.event');

        $qb->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));
        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('status'), 1));

        $qb->andWhere($qb->expr()->isNull('e.deletedAt'));
        $qb->andWhere($qb->expr()->eq('e.status', 1));
        $qb->andWhere($qb->expr()->eq('e.isShowPreviousEventsHome', true));

        $qb->addGroupBy($this->replaceFieldAlias('event'));

        $qb->addOrderBy($this->replaceFieldAlias('dateStart'), 'ASC');

        return $qb->getQuery()
            ->getResult();
    }
}
