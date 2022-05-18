<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\Subsection;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Subsection|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subsection|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subsection[]    findAll()
 * @method Subsection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubsectionRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subsection::class);
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
            'isHighlight',
            'namePortuguese',
            'frontCallPortuguese',
            'status',
            'isHomolog',
        ]))
            ->where($this->replaceFieldAlias('edition = :editionId'))
            ->setParameter('editionId', $editionId)
            ->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')))
            ->orderBy($this->replaceFieldAlias('position'), 'ASC');
    }
}
