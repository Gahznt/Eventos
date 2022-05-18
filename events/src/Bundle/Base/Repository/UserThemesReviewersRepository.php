<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\UserThemesReviewers;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\Division;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Validator\DoctrineLoader;

/**
 * @method UserThemesReviewers|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserThemesReviewers|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserThemesReviewers[]    findAll()
 * @method UserThemesReviewers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserThemesReviewers[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 */
class UserThemesReviewersRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserThemesReviewers::class);
        $this->setAlias('utrev');
    }


    /**
     * @param array $filters
     * @param string|null $sortBy
     * @param string|null $direction
     * @return mixed
     */
    public function findByFilters(array $filters, ?string $sortBy = null, ?string $direction = null)
    {
        $sortableFields = [
            'id' => 'utrev.id',
            'name' => 'utrev.name',
            'division' => 'd.portuguese'
        ];        
        $direction = strtoupper($direction) == "DESC" ? strtoupper($direction) : "ASC";
        $sortBy = $sortableFields[$sortBy] ?? 'utrev.name';

        $queryBuilder = $this->createQueryBuilder($this->getAlias())
            ->select('utrev.id, utrev.name, d.portuguese as division')
            ->innerJoin(UserThemes::class, 'ut', 'WITH', 'ut.id=utrev.userThemes')
            ->innerJoin(Division::class, 'd', 'WITH', 'd.id=ut.division')
        ;

        if (isset($filters['divisionId']) && $filters['divisionId']) {
            $queryBuilder->andWhere('ut.division = :divisionId');
            $queryBuilder->setParameter('divisionId', $filters['divisionId']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('d.portuguese',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('d.english',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('d.spanish',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('d.initials',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('utrev.name',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%'))
            ));
        } 
        
        $dbquery = $queryBuilder
            ->orderBy($sortBy, $direction)
            ->getQuery();

        return $dbquery->execute();
    }
}