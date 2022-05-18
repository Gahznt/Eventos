<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\SystemEnsalementSchedulingArticles;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SystemEnsalementSchedulingArticles|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemEnsalementSchedulingArticles|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemEnsalementSchedulingArticles[]    findAll()
 * @method SystemEnsalementSchedulingArticles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemEnsalementSchedulingArticlesRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemEnsalementSchedulingArticles::class);
        $this->setAlias('sesa');
    }
}
