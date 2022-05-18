<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\Institution;
use App\Bundle\Base\Entity\Program;
use App\Bundle\Base\Entity\ThemeSubmissionConfig;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserInstitutionsPrograms;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesResearchers;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method UserThemesResearchers|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserThemesResearchers|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserThemesResearchers[]    findAll()
 * @method UserThemesResearchers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserThemesResearchers[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 */
class UserThemesResearchersRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserThemesResearchers::class);
        $this->setAlias('utres');
    }


    /**
     * @param array $filters
     * @param string|null $sortBy
     * @param string|null $direction
     *
     * @return mixed
     */
    public function findByFilters(array $filters, ?string $sortBy = null, ?string $direction = null)
    {
        $sortableFields = [
            'id' => 'uip.user',
            'name' => 'u.name',
        ];
        $direction = strtoupper($direction) == "DESC" ? strtoupper($direction) : "ASC";
        $sortBy = $sortableFields[$sortBy] ?? 'u.name';

        $expr = $this->_em->getExpressionBuilder();
        $queryBuilder = $this->_em->createQueryBuilder()
            ->select('identity(uip.user) as id, u.name, i1.name as inst1, p1.name as prog1, p1.paid as paid1, i2.name as inst2, p2.name as prog2, p2.paid as paid2')
            ->from(UserInstitutionsPrograms::class, 'uip')
            ->innerJoin(User::class, 'u', 'WITH', 'u.id=uip.user')
            ->innerJoin(Institution::class, 'i1', 'WITH', 'i1.id=uip.institutionFirstId')
            ->innerJoin(Program::class, 'p1', 'WITH', 'p1.id=uip.programFirstId')
            ->leftJoin(Institution::class, 'i2', 'WITH', 'i2.id=uip.institutionSecondId')
            ->leftJoin(Program::class, 'p2', 'WITH', 'p2.id=uip.programSecondId')
            ->where($expr->in
            ('uip.user', $this->_em->createQueryBuilder()
                ->select('identity(utr.researcher)')
                ->from(UserThemesResearchers::class, 'utr')
                ->getDQL()
            )
            );

        if (isset($filters['divisionId']) && $filters['divisionId']) {
            $queryBuilder->andWhere(
                $expr->in
                (":divisionId",
                    $this->_em->createQueryBuilder()
                        ->select('d.id')
                        ->from(Division::class, 'd')
                        ->innerJoin(UserThemes::class, 'ut', 'WITH', 'ut.division=d.id')
                        ->innerJoin(UserThemesResearchers::class, 'utr2', 'WITH', 'utr2.userThemes=ut.id')
                        ->where('utr2.researcher=uip.user')
                        ->getDQL()
                )
            );
            $queryBuilder->setParameter('divisionId', $filters['divisionId']);
        }

        if (isset($filters['search']) && $filters['search']) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('u.name',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('i1.name',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('i2.name',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%'))
            ));
        }

        $dbquery = $queryBuilder
            ->orderBy($sortBy, $direction)
            ->getQuery();

        return $dbquery->execute();
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return int|mixed|string|UserThemesResearchers[]
     */
    public function findUniqueResearchersByEdition(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $qb = $this->createQueryBuilder('utres');

        $qb->innerJoin('utres.userThemes', 'ut');
        $qb->andWhere($qb->expr()->eq('ut.status', UserThemes::THEME_EVALUATION_APPROVED));

        if (count($edition->getEvent()->getDivisions()) > 0) {
            $ids = [];
            foreach ($edition->getEvent()->getDivisions() as $item) {
                $ids[] = $item->getId();
            }

            $qb->andWhere($qb->expr()->in('ut.division', $ids));
        }

        $qb->addGroupBy('utres.researcher');

        $qb->addOrderBy('ut.position', 'ASC');

        if (null !== $firstResult) {
            $qb->setFirstResult($firstResult);
        }

        if (null !== $maxResults) {
            $qb->setMaxResults($maxResults);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Edition $edition
     * @param User $user
     *
     * @return int|mixed|string|UserThemesResearchers[]
     */
    public function findByEditionAndResearcher(Edition $edition, User $user)
    {
        $qb = $this->createQueryBuilder('utres');

        $qb->andWhere($qb->expr()->eq('utres.researcher', $user->getId()));

        $qb->innerJoin('utres.userThemes', 'ut');
        $qb->andWhere($qb->expr()->eq('ut.status', UserThemes::THEME_EVALUATION_APPROVED));

        if (count($edition->getEvent()->getDivisions()) > 0) {
            $ids = [];
            foreach ($edition->getEvent()->getDivisions() as $item) {
                $ids[] = $item->getId();
            }

            $qb->andWhere($qb->expr()->in('ut.division', $ids));
        }

        $qb->addOrderBy('ut.position', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findOneResearcherByIdAndSubmissionConfig(User $researcher, ThemeSubmissionConfig $submissionConfig): ?UserThemesResearchers
    {
        $qb = $this->createQueryBuilder('r');
        $qb->innerJoin('r.researcher', 'u');
        $qb->innerJoin('r.userThemes', 'ut');
        $qb->innerJoin('ut.themeSubmissionConfig', 'sc');
        $qb->andWhere($qb->expr()->eq('u.id', $researcher->getId()));
        $qb->andWhere($qb->expr()->eq('sc.id', $submissionConfig->getId()));
        $qb->andWhere($qb->expr()->neq('ut.status', UserThemes::THEME_EVALUATION_STATUS_CANCELED));
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
