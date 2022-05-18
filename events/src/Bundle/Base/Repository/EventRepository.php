<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\Event;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\UserArticles;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
        $this->setAlias('e');
    }

    /**
     * @return QueryBuilder
     */
    public function list(): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        return $qb->select($this->replaceFieldAlias([
            'id',
            'namePortuguese',
            'titlePortuguese',
            'status',
            'isHomolog',
        ]))
            ->where($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')))
            ->orderBy($this->replaceFieldAlias('namePortuguese'), 'ASC');
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function withEditionArticles(array $filters = [])
    {
        $queryBuilder = $this->createQueryBuilder($this->getAlias());
        $queryBuilder->andWhere($queryBuilder->expr()->isNull($this->replaceFieldAlias('deletedAt')));
        $queryBuilder->leftJoin(Edition::class, 'ed', Join::WITH, 'ed.event=e.id');
        $queryBuilder->leftJoin(UserArticles::class, 'ua', Join::WITH, 'ua.editionId=ed.id');


        $dbquery = $queryBuilder
            ->getQuery();
 // // ->setFetchMode(Edition::class, 'ed', ClassMetadata::FETCH_EAGER) // ->setFetchMode(UserArticles::class, 'ua', ClassMetadata::FETCH_EAGER);

        return $dbquery->execute();
    }

}
