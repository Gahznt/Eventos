<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\Panel;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Entity\UserArticlesAuthors;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Panel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Panel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Panel[]    findAll()
 * @method Panel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PanelsPanelistRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Panel::class);
        $this->setAlias('pp');
    }
}
