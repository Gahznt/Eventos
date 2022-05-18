<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\EditionFile;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EditionFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method EditionFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method EditionFile[]    findAll()
 * @method EditionFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditionFileRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditionFile::class);
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
            'description',
            'fileName',
            'filePath',
        ]))
            ->where($this->replaceFieldAlias('edition = :editionId'))
            ->setParameter('editionId', $editionId)
            ->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')))
            ->orderBy($this->replaceFieldAlias('id'), 'DESC');
    }
}
