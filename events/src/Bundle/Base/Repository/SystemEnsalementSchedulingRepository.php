<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\Activity;
use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\Panel;
use App\Bundle\Base\Entity\SystemEnsalementRooms;
use App\Bundle\Base\Entity\SystemEnsalementScheduling;
use App\Bundle\Base\Entity\SystemEnsalementSchedulingArticles;
use App\Bundle\Base\Entity\SystemEnsalementSessions;
use App\Bundle\Base\Entity\SystemEnsalementSlots;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Entity\UserArticlesAuthors;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesDetails;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SystemEnsalementScheduling|null find($id, $lockMode = null, $lockVersion = null)
 * @method SystemEnsalementScheduling|null findOneBy(array $criteria, array $orderBy = null)
 * @method SystemEnsalementScheduling[]    findAll()
 * @method SystemEnsalementScheduling[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemEnsalementSchedulingRepository extends Repository
{
    /**
     * SystemEnsalementSchedulingRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SystemEnsalementScheduling::class);
        $this->setAlias('sesss');
    }

    /**
     * @param QueryBuilder $qb
     * @param int $edition
     * @param bool $isPriority
     * @param array $criteria
     *
     * @return QueryBuilder
     */
    public function buildListQuery(
        QueryBuilder $qb,
        int $edition,
        bool $isPriority = false,
        array $criteria = []
    ): QueryBuilder
    {
        $qb->innerJoin(SystemEnsalementSlots::class, 'ses', 'WITH', 'ses.id=sesss.systemEnsalementSlots');
        $qb->innerJoin(SystemEnsalementRooms::class, 'ser', 'WITH', 'ser.id=ses.systemEnsalementRooms');
        $qb->innerJoin(SystemEnsalementSessions::class, 'sess', 'WITH', 'sess.id=ses.systemEnsalementSessions');

        $qb->leftJoin(Division::class, 'd', 'WITH', 'd.id=sesss.division');
        $qb->leftJoin(Panel::class, 'p', 'WITH', 'p.id=sesss.panel AND p.statusEvaluation=2');
        $qb->leftJoin(Activity::class, 'a', 'WITH', 'a.id=sesss.activity');

        $qb->leftJoin(UserThemes::class, 'ut', 'WITH', 'ut.id=sesss.userThemes');
        $qb->leftJoin(UserThemesDetails::class, 'utd', 'WITH', 'utd.userThemes=ut.id');

        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('edition'), $edition));
        $qb->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));

        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('priority'), true === $isPriority ? 1 : 0));

        if (isset($criteria['division']) && trim($criteria['division']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('division'), (int)$criteria['division']));
        }

        if (isset($criteria['theme']) && trim($criteria['theme']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('userThemes'), (int)$criteria['theme']));
        }

        if (isset($criteria['section']) && trim($criteria['section']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('id'), (int)$criteria['section']));
        }

        if (isset($criteria['user']) && trim($criteria['user']) !== '') {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->eq($this->replaceFieldAlias('coordinatorDebater1'), (int)$criteria['user']),
                $qb->expr()->eq($this->replaceFieldAlias('coordinatorDebater2'), (int)$criteria['user'])
            ));
        }

        if (isset($criteria['date']) && trim($criteria['date']) !== '') {
            $qb->andWhere('sess.date = :date')
                ->setParameter('date', trim($criteria['date']), Types::STRING);
        }

        if (isset($criteria['time']) && trim($criteria['time']) !== '') {
            $qb->andWhere($qb->expr()->eq('sess.id', (int)$criteria['time']));
        }

        if (isset($criteria['article']) && trim($criteria['article']) !== '') {
            $qb->innerJoin(SystemEnsalementSchedulingArticles::class, 'sesa2', 'WITH',
                'sesa2.systemEnsalementSheduling=sesss.id');
            $qb->andWhere($qb->expr()->eq('sesa2.id', (int)$criteria['article']));
        }

        if (isset($criteria['theme']) && trim($criteria['theme']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('userThemes'), (int)$criteria['theme']));
        }

        if (isset($criteria['q']) && trim($criteria['q']) !== '') {
            $qb->leftJoin(SystemEnsalementSchedulingArticles::class, 'sesa', 'WITH',
                'sesa.systemEnsalementSheduling=sesss.id');
            $qb->leftJoin(UserArticles::class, 'ua', 'WITH', 'ua.id=sesa.userArticles');

            $qb->leftJoin(User::class, 'u', 'WITH', 'u.id=sesss.coordinatorDebater1');
            $qb->leftJoin(User::class, 'u2', 'WITH', 'u2.id=sesss.coordinatorDebater2');

            $qb->andWhere($qb->expr()->orX(
                'd.portuguese LIKE :q',
                'utd.portugueseTitle LIKE :q',
                'sesss.title LIKE :q',
                'ua.title LIKE :q',
                'u.name LIKE :q',
                'u2.name LIKE :q'
            ));
            $qb->setParameter('q', '%' . trim($criteria['q']) . '%');
        }

        $qb->addGroupBy($this->replaceFieldAlias('id'));

        $qb->addOrderBy('sess.date', 'ASC')
            ->addOrderBy('sess.start', 'ASC')
            ->addOrderBy('ser.name', 'ASC')
            ->addOrderBy('sess.type', 'ASC')
            ->addOrderBy('ut.position', 'ASC');
        //->addOrderBy('ser.local', 'ASC');

        return $qb;
    }

    /**
     * @param int $edition
     * @param bool $isPriority
     * @param array $criteria
     *
     * @return QueryBuilder|SystemEnsalementScheduling[]
     */
    public function list(int $edition, bool $isPriority = false, array $criteria = []): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        return $this->buildListQuery($qb, $edition, $isPriority, $criteria);
    }

    /**
     * @param Edition $edition
     * @param User $author
     * @param SystemEnsalementSessions $session
     * @param SystemEnsalementRooms $room
     * @param SystemEnsalementScheduling|null $scheduling
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countByAuthorAndSession(Edition $edition, User $author, SystemEnsalementSessions $session, SystemEnsalementRooms $room, ?SystemEnsalementScheduling $scheduling)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->select('count(uaa.userAuthor) as count');

        $qb->innerJoin(SystemEnsalementSlots::class, 'ses', 'WITH', 'ses.id=sesss.systemEnsalementSlots');
        $qb->innerJoin(SystemEnsalementRooms::class, 'ser', 'WITH', 'ser.id=ses.systemEnsalementRooms');
        $qb->innerJoin(SystemEnsalementSessions::class, 'sess', 'WITH', 'sess.id=ses.systemEnsalementSessions');

        $qb->innerJoin(SystemEnsalementSchedulingArticles::class, 'sesa', 'WITH', 'sesa.systemEnsalementSheduling=sesss.id');
        $qb->innerJoin(UserArticles::class, 'ua', 'WITH', 'ua.id=sesa.userArticles');
        $qb->innerJoin(UserArticlesAuthors::class, 'uaa', 'WITH', 'uaa.userArticles=sesa.userArticles');

        //$qb->innerJoin(EditionSignup::class, 'esup', 'WITH', 'esup.joined=u.id');

        $qb->andWhere($qb->expr()->isNull('sesss.deletedAt'));
        $qb->andWhere($qb->expr()->isNull('ses.deletedAt'));
        $qb->andWhere($qb->expr()->isNull('ser.deletedAt'));
        $qb->andWhere($qb->expr()->isNull('sess.deletedAt'));
        $qb->andWhere($qb->expr()->isNull('sesa.deletedAt'));
        $qb->andWhere($qb->expr()->isNull('ua.deletedAt'));
        //$qb->andWhere($qb->expr()->isNull('esup.deletedAt'));

        $qb->andWhere($qb->expr()->eq('sesss.edition', $edition->getId()));
        $qb->andWhere($qb->expr()->eq('ses.systemEnsalementSessions', $session->getId()));
        $qb->andWhere($qb->expr()->eq('uaa.userAuthor', $author->getId()));
        //$qb->andWhere($qb->expr()->eq('esup.statusPay', EditionSignup::EDITION_SIGNUP_STATUS_PAID));

        $qb->andWhere($qb->expr()->neq('ser.id', $room->getId()));

        if ($scheduling && $scheduling->getId()) {
            $qb->andWhere($qb->expr()->neq('sesss.id', $scheduling->getId()));
        }

        $qb->addGroupBy('uaa.userAuthor');

        $query = $qb->getQuery();

        $result = $query->getOneOrNullResult();

        if (null !== $result) {
            return (int)$result['count'];
        }

        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->select('count(sesss.id) as count');

        $qb->innerJoin(SystemEnsalementSlots::class, 'ses', 'WITH', 'ses.id=sesss.systemEnsalementSlots');
        $qb->innerJoin(SystemEnsalementRooms::class, 'ser', 'WITH', 'ser.id=ses.systemEnsalementRooms');
        $qb->innerJoin(SystemEnsalementSessions::class, 'sess', 'WITH', 'sess.id=ses.systemEnsalementSessions');

        $qb->andWhere($qb->expr()->isNull('sesss.deletedAt'));
        $qb->andWhere($qb->expr()->isNull('ses.deletedAt'));
        $qb->andWhere($qb->expr()->isNull('ser.deletedAt'));
        $qb->andWhere($qb->expr()->isNull('sess.deletedAt'));

        $qb->andWhere($qb->expr()->eq('sesss.edition', $edition->getId()));
        $qb->andWhere($qb->expr()->eq('ses.systemEnsalementSessions', $session->getId()));
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('sesss.coordinatorDebater1', $author->getId()),
            $qb->expr()->eq('sesss.coordinatorDebater2', $author->getId())
        ));

        $qb->andWhere($qb->expr()->neq('ser.id', $room->getId()));

        if ($scheduling && $scheduling->getId()) {
            $qb->andWhere($qb->expr()->neq('sesss.id', $scheduling->getId()));
        }

        $qb->addGroupBy('sesss.id');

        $query = $qb->getQuery();

        $result = $query->getOneOrNullResult();

        if (null !== $result) {
            return (int)$result['count'];
        }

        return 0;
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return int|mixed|string|SystemEnsalementScheduling[]
     */
    public function findCoordinatorsByEdition(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->andWhere($qb->expr()->isNull('sesss.deletedAt'));

        $qb->andWhere($qb->expr()->eq('sesss.edition', $edition->getId()));

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('sesss.coordinatorDebater1Type', SystemEnsalementScheduling::TYPE_COORDINATOR),
            $qb->expr()->eq('sesss.coordinatorDebater2Type', SystemEnsalementScheduling::TYPE_COORDINATOR)
        ));

        $qb->addGroupBy('sesss.coordinatorDebater1');
        $qb->addGroupBy('sesss.coordinatorDebater2');

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
     * @param User $coordinator
     *
     * @return int|mixed|string|SystemEnsalementScheduling[]
     */
    public function findByEditionAndCoordinator(Edition $edition, User $coordinator)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->andWhere($qb->expr()->isNull('sesss.deletedAt'));

        $qb->andWhere($qb->expr()->eq('sesss.edition', $edition->getId()));

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->andX(
                $qb->expr()->eq('sesss.coordinatorDebater1Type', SystemEnsalementScheduling::TYPE_COORDINATOR),
                $qb->expr()->eq('sesss.coordinatorDebater1', $coordinator->getId())
            ),
            $qb->expr()->andX(
                $qb->expr()->eq('sesss.coordinatorDebater2Type', SystemEnsalementScheduling::TYPE_COORDINATOR),
                $qb->expr()->eq('sesss.coordinatorDebater2', $coordinator->getId())
            )
        ));

        $qb->addOrderBy('sesss.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return int|mixed|string|SystemEnsalementScheduling[]
     */
    public function findDebatersByEdition(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->andWhere($qb->expr()->isNull('sesss.deletedAt'));

        $qb->andWhere($qb->expr()->eq('sesss.edition', $edition->getId()));

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('sesss.coordinatorDebater1Type', SystemEnsalementScheduling::TYPE_DEBATER),
            $qb->expr()->eq('sesss.coordinatorDebater2Type', SystemEnsalementScheduling::TYPE_DEBATER)
        ));

        $qb->addGroupBy('sesss.coordinatorDebater1');
        $qb->addGroupBy('sesss.coordinatorDebater2');

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
     * @param User $debater
     *
     * @return int|mixed|string|SystemEnsalementScheduling[]
     */
    public function findByEditionAndDebater(Edition $edition, User $debater)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->andWhere($qb->expr()->isNull('sesss.deletedAt'));

        $qb->andWhere($qb->expr()->eq('sesss.edition', $edition->getId()));

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->andX(
                $qb->expr()->eq('sesss.coordinatorDebater1Type', SystemEnsalementScheduling::TYPE_DEBATER),
                $qb->expr()->eq('sesss.coordinatorDebater1', $debater->getId())
            ),
            $qb->expr()->andX(
                $qb->expr()->eq('sesss.coordinatorDebater2Type', SystemEnsalementScheduling::TYPE_DEBATER),
                $qb->expr()->eq('sesss.coordinatorDebater2', $debater->getId())
            )
        ));

        $qb->addOrderBy('sesss.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return int|mixed|string|SystemEnsalementScheduling[]
     */
    public function findCoordinatorDebatersByEdition(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->andWhere($qb->expr()->isNull('sesss.deletedAt'));

        $qb->andWhere($qb->expr()->eq('sesss.edition', $edition->getId()));

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('sesss.coordinatorDebater1Type', SystemEnsalementScheduling::TYPE_COORDINATOR_DEBATER),
            $qb->expr()->eq('sesss.coordinatorDebater2Type', SystemEnsalementScheduling::TYPE_COORDINATOR_DEBATER)
        ));

        $qb->addGroupBy('sesss.coordinatorDebater1');
        $qb->addGroupBy('sesss.coordinatorDebater2');

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
     * @param User $coordinatorDebater
     *
     * @return int|mixed|string|SystemEnsalementScheduling[]
     */
    public function findByEditionAndCoordinatorDebater(Edition $edition, User $coordinatorDebater)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->andWhere($qb->expr()->isNull('sesss.deletedAt'));

        $qb->andWhere($qb->expr()->eq('sesss.edition', $edition->getId()));

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->andX(
                $qb->expr()->eq('sesss.coordinatorDebater1Type', SystemEnsalementScheduling::TYPE_COORDINATOR_DEBATER),
                $qb->expr()->eq('sesss.coordinatorDebater1', $coordinatorDebater->getId())
            ),
            $qb->expr()->andX(
                $qb->expr()->eq('sesss.coordinatorDebater2Type', SystemEnsalementScheduling::TYPE_COORDINATOR_DEBATER),
                $qb->expr()->eq('sesss.coordinatorDebater2', $coordinatorDebater->getId())
            )
        ));

        $qb->addOrderBy('sesss.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param User $user
     *
     * @return int|mixed|string|SystemEnsalementScheduling[]
     */
    public function findByUser(User $user)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        // $qb->select('count(uaa.userAuthor) as count');

        /*$qb->addSelect('CASE
            WHEN sesss.coordinatorDebater1 =:userId THEN IDENTITY(sesss.coordinatorDebater1)
            WHEN sesss.coordinatorDebater2 =:userId THEN IDENTITY(sesss.coordinatorDebater2)
            WHEN uaa.userAuthor =:userId THEN IDENTITY(uaa.userAuthor)
            ELSE 0
        END AS userId');
        $qb->setParameter(':userId', $user->getId());*/

        $qb->innerJoin('sesss.systemEnsalementSlots', 'ses');
        $qb->innerJoin('ses.systemEnsalementRooms', 'ser');
        $qb->innerJoin('ses.systemEnsalementSessions', 'sess');

        $qb->leftJoin('sesss.articles', 'sesa');
        $qb->leftJoin('sesa.userArticles', 'ua');
        $qb->leftJoin('ua.userArticlesAuthors', 'uaa');

        // $qb->andWhere($qb->expr()->eq('sesss.edition', $edition->getId()));

        /*$now = new \DateTime();

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->gt('sess.date', $qb->expr()->literal($now->format('Y-m-d'))), // data > hoje
            $qb->expr()->andX(                                                         // data == hoje e hora final >= agora
                $qb->expr()->eq('sess.date', $qb->expr()->literal($now->format('Y-m-d'))),
                $qb->expr()->gte('sess.end', $qb->expr()->literal($now->format('H:i:s')))
            )
        ));*/

        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('sesss.coordinatorDebater1', $user->getId()),
            $qb->expr()->eq('sesss.coordinatorDebater2', $user->getId()),
            $qb->expr()->eq('uaa.userAuthor', $user->getId())
        ));

        $qb->addGroupBy('sesss.id');

        $qb->addOrderBy('sess.date', 'ASC')
            ->addOrderBy('sess.start', 'ASC')
            ->addOrderBy('ser.name', 'ASC');
        //->addOrderBy('ut.position', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
