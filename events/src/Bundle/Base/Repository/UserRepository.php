<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\DivisionCoordinator;
use App\Bundle\Base\Entity\Permission;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserAcademics;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Entity\UserArticlesAuthors;
use App\Bundle\Base\Entity\UserAssociation;
use App\Bundle\Base\Entity\UserCommittee;
use App\Bundle\Base\Entity\UserEvaluationArticles;
use App\Bundle\Base\Entity\UserThemesResearchers;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends Repository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
        $this->setAlias('u');
    }

    /**
     * @param UserInterface $user
     * @param string $newEncodedPassword
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (! $user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param array $filters
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getEvaluators(array $filters = [])
    {
        $queryBuilder = $this->createQueryBuilder($this->getAlias());

        //$queryBuilder->select('u', 'GROUP_CONCAT(DISTINCT ua2.level)');
        //$queryBuilder->addSelect();
        $queryBuilder->innerJoin(UserEvaluationArticles::class, 'uea', Join::WITH, 'uea.user=u.id');

        $queryBuilder->innerJoin(UserArticles::class, 'ua', Join::WITH, 'ua.userId=u.id');
        $queryBuilder->innerJoin(UserArticlesAuthors::class, 'uaa', Join::WITH, 'uaa.userAuthor=u.id AND uaa.userArticles=ua.id');

        $queryBuilder->leftJoin(UserAcademics::class, 'ua2', Join::WITH, 'ua2.user=u.id');

        $queryBuilder->andWhere($queryBuilder->expr()->eq('uea.wantEvaluate', true));
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('u.deletedAt'));
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('ua.deletedAt'));

        if (! empty($filters['edition'])) {
            $queryBuilder->andWhere('ua.editionId = :edition');
            $queryBuilder->setParameter(':edition', $filters['edition']);
        }

        /*if (! empty($filters['users'])) {
            $queryBuilder->andWhere('u.id = :users');
            $queryBuilder->setParameter(':users', $filters['users']);
        }*/

        if (! empty($filters['search'])) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('u.name', $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('u.phone', $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('u.email', $queryBuilder->expr()->literal('%' . $filters['search'] . '%'))
            ));
        }
        $queryBuilder->addGroupBy('u.id');

        return $queryBuilder;
    }

    public function findByTeste(array $filters = [])
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->andWhere($qb->expr()->isNull('u.deletedAt'));

        // Apenas quem marcou para ser avaliador
        $qb->innerJoin(UserEvaluationArticles::class, 'uea', Join::WITH, 'uea.user=u.id');
        $qb->andWhere($qb->expr()->eq("uea.wantEvaluate", true));

        if (! empty($filters['ignoreUser'])) {
            if (! is_array($filters['ignoreUser'])) {
                $filters['ignoreUser'] = [
                    $filters['ignoreUser'],
                ];
            }
            $qb->andWhere($qb->expr()->notIn("u.id", $filters['ignoreUser']));
        }

        if (! empty($filters['division'])) {
            if (! is_array($filters['division'])) {
                $filters['division'] = [
                    $filters['division'],
                ];
            }

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in("uea.divisionFirstId", $filters['division']),
                $qb->expr()->in("uea.divisionSecondId", $filters['division'])
            ));
        }

        if (! empty($filters['theme'])) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->eq("uea.themeFirstId", $qb->expr()->literal($filters['theme'])),
                $qb->expr()->eq("uea.themeSecondId", $qb->expr()->literal($filters['theme']))
            ));
        }

        if (! empty($filters['level'])) {
            $qb->innerJoin(UserAcademics::class, 'ua2', Join::WITH, 'ua2.user=u.id');
            $qb->andWhere($qb->expr()->eq('ua2.level', $filters['level']));
        }

        if (! empty($filters['search'])) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->eq("u.identifier", $qb->expr()->literal($filters['search'])),
                $qb->expr()->eq("u.name", $qb->expr()->literal($filters['search'])),
                $qb->expr()->eq("u.phone", $qb->expr()->literal(intval($filters['search']) ?: -1)),
                $qb->expr()->eq("u.email", $qb->expr()->literal($filters['search'])),
            ));
        }

        $qb->addGroupBy('u.id');

        $dbquery = $qb
            ->getQuery();

        return $dbquery->execute();
    }

    /**
     * @param array $filters
     * @param string|null $sortBy
     * @param string|null $direction
     *
     * @return QueryBuilder
     */
    public function list(array $filters, ?string $sortBy = null, ?string $direction = null)
    {
        $sortableFields = ['name', 'identifier', 'id', 'since', 'thru', 'level', 'payment'];
        $direction = strtoupper($direction) == "DESC" ? strtoupper($direction) : "ASC";
        $sortBy = in_array($sortBy, $sortableFields) ? $sortBy : 'id';
        $queryBuilder = $this->createQueryBuilder($this->getAlias());

        if (isset($filters['permission']) && $filters['permission'] == Permission::ROLE_LEADER) {
            $sub = $this->createQueryBuilder('utr1');
            $sub->select("utx");
            $sub->from(UserThemesResearchers::class, "utx");
            $sub->andWhere('utx.researcher = u.id');
            $queryBuilder->andWhere($queryBuilder->expr()->exists($sub->getDQL()));
        }

        if (isset($filters['permission']) && $filters['permission'] == Permission::ROLE_DIVISION_COORDINATOR) {
            $sub = $this->createQueryBuilder('utr2');
            $sub->select("utw");
            $sub->from(DivisionCoordinator::class, "utw");
            $sub->andWhere('utw.coordinator = u.id');
            $queryBuilder->andWhere($queryBuilder->expr()->exists($sub->getDQL()));
        }

        if (isset($filters['permission']) && (
                $filters['permission'] == Permission::ROLE_ADMIN ||
                $filters['permission'] == Permission::ROLE_ADMIN_OPERATIONAL ||
                $filters['permission'] == Permission::ROLE_DIRECTOR ||
                $filters['permission'] == Permission::ROLE_USER_GUEST
            )) {
            $queryBuilder->where("JSON_CONTAINS(u.roles, :roles) = 1");
            $queryBuilder->setParameter(':roles', '["' . $filters['permission'] . '"]');
        }


        if (isset($filters['payment']) || isset($filters['paymentDays']) || isset($filters['levels'])) {
            $queryBuilder->leftJoin(UserAssociation::class, 'ua', Join::WITH, 'ua.user=u.id');
        }

        if (isset($filters['since']) && isset($filters['thru'])) {
            $queryBuilder->orWhere('u.updatedAt BETWEEN :since AND :thru')
                ->setParameter('since', $filters['since']->format('Y-m-d 00:00:00'))
                ->setParameter('thru', $filters['thru']->format('Y-m-d 23:59:59'));
        }

        if (isset($filters['payment'])) {

            if (intval($filters['payment']) == 1) {
                $queryBuilder->andWhere('ua.statusPay = 1');
                $sub = $this->createQueryBuilder('uas');
                $sub->select("uax");
                $sub->from(UserAssociation::class, "uax");
                $sub->andWhere('uax.user = u.id');
                $sub->andWhere('uax.statusPay = 1');
                $queryBuilder->andWhere($queryBuilder->expr()->exists($sub->getDQL()));
            }

            if (intval($filters['payment']) == 0) {
                $sub = $this->createQueryBuilder('uas');
                $sub->select("uax");
                $sub->from(UserAssociation::class, "uax");
                $sub->andWhere('uax.user = u.id');
                $queryBuilder->andWhere($queryBuilder->expr()->not($queryBuilder->expr()->exists($sub->getDQL())));
            }
        }

        if (isset($filters['paymentDays'])) {
            $beginDate = new \DateTime();
            $endDate = new \DateTime();
            $endDate = $endDate->modify("- {$filters['paymentDays']} days");

            $queryBuilder->andWhere('ua.lastPay BETWEEN :endPay AND :beginPay ')
                ->setParameter('endPay', $endDate->format('Y-m-d 00:00:00'))
                ->setParameter('beginPay', $beginDate->format('Y-m-d 23:59:59'));
        }

        if (isset($filters['levels'])) {

            if (intval($filters['levels']) == UserAssociation::USER_ASSOCIATIONS_LEVEL['USER_ASSOCIATIONS_LEVEL_UNDEF']) {
                $sub = $this->createQueryBuilder('uaq');
                $sub->select("uaw");
                $sub->from(UserAssociation::class, "uaw");
                $sub->andWhere('uaw.user = u.id');
                $queryBuilder->andWhere($queryBuilder->expr()->not($queryBuilder->expr()->exists($sub->getDQL())));
            } else {
                $sub = $this->createQueryBuilder('uaq');
                $sub->select("uaw");
                $sub->from(UserAssociation::class, "uaw");
                $sub->andWhere('uaw.user = u.id');
                $sub->andWhere('uaw.level = :level');
                $queryBuilder->setParameter('level', $filters['levels']);
                $queryBuilder->andWhere($queryBuilder->expr()->exists($sub->getDQL()));
            }
        }

        if (isset($filters['search'])) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('u.id', (int)$filters['search']),
                $queryBuilder->expr()->like('u.identifier', $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('u.name', $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('u.email', $queryBuilder->expr()->literal('%' . $filters['search'] . '%'))
            ));
        }

        $queryBuilder->orderBy('u.' . $sortBy, $direction);

        return $queryBuilder;
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
        $queryBuilder = $this->list($filters, $sortBy, $direction);

        $dbquery = $queryBuilder->getQuery();

        return $dbquery->execute();
    }

    public function findExtra()
    {
        $queryBuilder = $this->createQueryBuilder($this->getAlias());

        $queryBuilder->leftJoin(UserThemesResearchers::class, 'utr', Join::WITH, 'utr.researcher=u.id');

        $dbquery = $queryBuilder
            ->getQuery();

        return $dbquery->execute();
    }

    public function findByCountry($countryId, $cpf)
    {
        return $this->createQueryBuilder($this->getAlias())
            ->select($this->replaceFieldAlias(['id', 'name', 'createdAt']))
            ->join("{$this->getAlias()}.city", 'city')
            ->join("city.country", 'country')
            ->where('country.id = :countryId')
            ->andWhere($this->getAlias() . '.identifier = :cpf')
            ->setParameter('countryId', $countryId)
            ->setParameter('cpf', $cpf)
            ->setMaxResults(1)
            ->orderBy($this->replaceFieldAlias('createdAt'), 'ASC')
            ->getQuery()->getResult();
    }

    public function findByIdentifier(string $identifier, string $value): ?array
    {
        if ($value === '') {
            return [];
        }

        $qb = $this->createQueryBuilder('u');

        $qb->select(['u.id', 'u.name']);

        if ($identifier === 'email') {
            $qb->andWhere(
                $qb->expr()->eq('u.email', $qb->expr()->literal($value))
            );

            $qb->setMaxResults(2);
        } else {
            $qb->andWhere(
                $qb->expr()->eq('u.identifier', $qb->expr()->literal($value))
            );

            if ($identifier === 'cpf') {
                $qb->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->eq('u.recordType', User::USER_RECORD_TYPE_BRAZILIAN),
                        $qb->expr()->andX(
                            $qb->expr()->eq('u.recordType', User::USER_RECORD_TYPE_FOREIGN),
                            $qb->expr()->eq('u.isForeignUseCpf', User::USER_FOREIGN_USE_CPF_YES)
                        )
                    )
                );
            } else {
                $qb->andWhere(
                    $qb->expr()->andX(
                        $qb->expr()->eq('u.recordType', User::USER_RECORD_TYPE_FOREIGN),
                        $qb->expr()->eq('u.isForeignUsePassport', User::USER_FOREIGN_USE_PASSPORT_YES)
                    )
                );
            }

            $qb->setMaxResults(1);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $filters
     * @param string|null $sortBy
     * @param string|null $direction
     *
     * @return mixed
     */
    public function findCoordinators(array $filters, ?string $sortBy = null, ?string $direction = null)
    {
        $sortableFields = ['name', 'identifier', 'id', 'created_at'];
        $direction = strtoupper($direction) == "DESC" ? strtoupper($direction) : "ASC";
        $sortBy = in_array($sortBy, $sortableFields) ? $sortBy : 'id';
        $queryBuilder = $this->createQueryBuilder($this->getAlias());

        if (isset($filters['total']) && $filters['total'] === true) {
            $sub1 = $this->createQueryBuilder('utr1');
            $sub1->select("utx");
            $sub1->from(UserThemesResearchers::class, "utx");
            $sub1->andWhere('utx.researcher = u.id');

            $sub2 = $this->createQueryBuilder('utr2');
            $sub2->select("utw");
            $sub2->from(DivisionCoordinator::class, "utw");
            $sub2->andWhere('utw.coordinator = u.id');

            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->exists($sub1->getDQL()),
                $queryBuilder->expr()->exists($sub2->getDQL())
            ));
        }

        if (isset($filters['type'])) {
            if ($filters['type'] == Permission::ROLE_LEADER) {
                $sub1 = $this->createQueryBuilder('utr1');
                $sub1->select("utx");
                $sub1->from(UserThemesResearchers::class, "utx");
                $sub1->andWhere('utx.researcher = u.id');
                $queryBuilder->andWhere($queryBuilder->expr()->exists($sub1->getDQL()));
            }

            if ($filters['type'] == Permission::ROLE_COMMITTEE) {
                $sub3 = $this->createQueryBuilder('utr3');
                $sub3->select("utx3");
                $sub3->from(UserCommittee::class, "utx3");
                $sub3->andWhere('utx3.user = u.id');
                $queryBuilder->andWhere($queryBuilder->expr()->exists($sub3->getDQL()));
            }

            if ($filters['type'] == Permission::ROLE_DIVISION_COORDINATOR) {
                $sub4 = $this->createQueryBuilder('utr4');
                $sub4->select("utx4");
                $sub4->from(DivisionCoordinator::class, "utx4");
                $sub4->andWhere('utx4.coordinator = u.id');
                $queryBuilder->andWhere($queryBuilder->expr()->exists($sub4->getDQL()));
            }
        }

        if (isset($filters['theme'])) {
            $queryBuilder->innerJoin(UserThemesResearchers::class, 'ut', Join::WITH, 'ut.researcher=u.id');
            $queryBuilder->andWhere("ut.userThemes = {$filters['theme']->getId()}");
        }

        if (isset($filters['division'])) {
            $queryBuilder->innerJoin(DivisionCoordinator::class, 'dc', Join::WITH, 'dc.coordinator=u.id');
            $queryBuilder->andWhere("dc.division = {$filters['division']->getId()}");
        }

        if (isset($filters['search'])) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('u.identifier',
                    $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('u.name', $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('u.email', $queryBuilder->expr()->literal('%' . $filters['search'] . '%'))
            ));
        }

        $queryBuilder
            ->orderBy('u.' . $sortBy, $direction);

        return $queryBuilder;
    }

    /**
     * @return User[]
     */
    public function findByDocument(string $identifier): array
    {
        $result = [];

        $identifier = trim($identifier);
        $length = mb_strlen($identifier);

        if ($length < 4) {
            return $result;
        }

        $intIdentifier = preg_replace('/[^0-9]*/', '', $identifier);
        $isCpf = 11 === mb_strlen($intIdentifier);

        $cleanIdentifier = preg_replace('/[^a-zA-Z0-9]*/', '', $identifier);
        $isPassport = preg_match('/^(?!^0+$)[a-zA-Z0-9]{3,20}$/', $cleanIdentifier) !== false;

        $qb = $this->createQueryBuilder('u');

        // cpf
        if ($isCpf) {
            $qb->andWhere($qb->expr()->andX(
                $qb->expr()->orX(
                    $qb->expr()->eq('u.recordType', User::USER_RECORD_TYPE_BRAZILIAN),
                    $qb->expr()->andX(
                        $qb->expr()->eq('u.recordType', User::USER_RECORD_TYPE_FOREIGN),
                        $qb->expr()->orX(
                            $qb->expr()->eq('u.isForeignUseCpf', User::USER_FOREIGN_USE_CPF_YES),
                            $qb->expr()->isNull('u.isForeignUseCpf'),
                        )
                    )
                ),
                $qb->expr()->orX(
                    'u.identifier =:identifier',
                    'u.identifier =:intIdentifier'
                )
            ));
            $qb->setParameter('identifier', $identifier);
            $qb->setParameter('intIdentifier', $intIdentifier);
        } elseif ($isPassport) { // passaporte
            $qb->andWhere($qb->expr()->andX(
                $qb->expr()->eq('u.recordType', User::USER_RECORD_TYPE_FOREIGN),
                $qb->expr()->orX(
                    'u.identifier =:identifier',
                    'u.identifier =:cleanIdentifier'
                )
            ));
            $qb->setParameter('identifier', $identifier);
            $qb->setParameter('cleanIdentifier', $cleanIdentifier);
        } else {
            $qb->andWhere(
                $qb->expr()->orX(
                    'u.identifier =:identifier',
                )
            );
            $qb->setParameter('identifier', $identifier);
        }

        // força o limite para 2, permitindo verificar
        // se a busca retornou mais que 1 único resultado
        // e não tornar a busca "pesada"
        $qb->setMaxResults(2);

        try {
            $result = [$qb->getQuery()->getOneOrNullResult()];
        } catch (NonUniqueResultException $e) {
        }

        return $result;
    }
}
