<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Entity\UserInstitutionsPrograms;
use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserInstitutionsPrograms|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInstitutionsPrograms|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInstitutionsPrograms[]    findAll()
 * @method UserInstitutionsPrograms[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInstitutionsProgramsRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInstitutionsPrograms::class);
        $this->setAlias('u');
    }
}
