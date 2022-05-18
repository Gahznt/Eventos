<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\Course;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Course|null find($id, $lockMode = null, $lockVersion = null)
 * @method Course|null findOneBy(array $criteria, array $orderBy = null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
        $this->setAlias('c');
    }
}
