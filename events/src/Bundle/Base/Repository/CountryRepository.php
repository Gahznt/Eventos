<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\Country;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findAll()
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Country[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 * @method Country[]    executeSql(string $sql)
 */
class CountryRepository extends Repository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
        $this->setAlias('c');
    }
}
