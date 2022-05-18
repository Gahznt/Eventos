<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\Edition;
use App\Bundle\Base\Entity\EditionDiscount;
use App\Bundle\Base\Entity\EditionPaymentMode;
use App\Bundle\Base\Entity\EditionSignup;
use App\Bundle\Base\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EditionSignup|null find($id, $lockMode = null, $lockVersion = null)
 * @method EditionSignup|null findOneBy(array $criteria, array $orderBy = null)
 * @method EditionSignup[]    findAll()
 * @method EditionSignup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditionSignupRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditionSignup::class);
        $this->setAlias('esup');
    }

    /**
     * @param int $userId
     *
     * @return int|mixed|string|EditionSignup[]
     */
    public function getUserEditions(int $userId)
    {
        $qb = $this->createQueryBuilder($this->getAlias())
            ->leftJoin(Edition::class, 'e', 'WITH', 'esup.edition = e.id');

        $qb->andWhere('esup.joined = :userId')->setParameter(':userId', $userId);
        $qb->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));

        $qb->addOrderBy('esup.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $edition
     * @param array $criteria
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findByEdition(int $edition, array $criteria = [])
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->select('esup');
        if (isset($criteria['status']) && trim($criteria['status']) !== '') {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('statusPay'), (int)$criteria['status']));
        }

        if (isset($criteria['mode']) && trim($criteria['mode']) !== '') {
            $qb->leftJoin(EditionPaymentMode::class, 'epm', 'WITH', 'epm.id=esup.paymentMode');
            $qb->andWhere($qb->expr()->eq('epm.initials', $qb->expr()->literal($criteria['mode'])));
        }

        if (isset($criteria['q']) && trim($criteria['q']) !== '') {
            $qb->leftJoin(User::class, 'u', 'WITH', 'u.id=esup.joined');

            $qb->andWhere($qb->expr()->orX(
                'u.id = :iq',
                'esup.id = :iq',
                //'esup.edition = :iq',
                // "CONCAT(esup.edition, CONCAT('-', esup.id)) LIKE :sq",
                'u.name LIKE :q',
                'u.identifier LIKE :q',
            ));

            $qb->setParameter('iq', (int)$criteria['q']);
            // $qb->setParameter('sq', (string)$criteria['q']);
            $qb->setParameter('q', '%' . trim($criteria['q']) . '%');
        }

        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('edition'), $edition));
        $qb->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));
        $qb->addOrderBy($this->replaceFieldAlias('id'), 'DESC');

        $qb->addGroupBy($this->replaceFieldAlias('id'));

        $qb->addOrderBy('esup.createdAt', 'DESC');

        return $qb;
    }

    /**
     * @param Edition $edition
     * @param User $user
     *
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isUserSignedUp(Edition $edition, User $user): bool
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->select('count(esup.id) as count');

        if (! empty($options['edition'])) {
            $qb->andWhere($qb->expr()->eq('esup.edition', $options['edition']->getId()));
        }

        $qb->andWhere($qb->expr()->eq('esup.statusPay', EditionSignup::EDITION_SIGNUP_STATUS_PAID));

        $qb->andWhere($qb->expr()->isNull('esup.deletedAt'));

        $qb->andWhere($qb->expr()->eq('esup.edition', $edition->getId()));
        $qb->andWhere($qb->expr()->eq('esup.joined', $user->getId()));

        $qb->addGroupBy('esup.id');

        $query = $qb->getQuery();

        $result = $query->getOneOrNullResult();

        if (null !== $result) {
            return (int)$result['count'] > 0;
        }

        return false;
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return QueryBuilder
     */
    public function findUserSignedUpAndNotListener(Edition $edition, ?int $firstResult = null, ?int $maxResults = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        // $qb->select('count(esup.id) as count');

        $qb->leftJoin(EditionPaymentMode::class, 'epm', 'WITH', 'epm.id=esup.paymentMode');

        $qb->andWhere($qb->expr()->eq('esup.statusPay', EditionSignup::EDITION_SIGNUP_STATUS_PAID));
        $qb->andWhere($qb->expr()->neq('epm.initials', $qb->expr()->literal(EditionPaymentMode::INITIALS['Ouvinte'])));

        $qb->andWhere($qb->expr()->isNull('esup.deletedAt'));

        $qb->andWhere($qb->expr()->eq('esup.edition', $edition->getId()));
        // $qb->andWhere($qb->expr()->eq('esup.joined', $user->getId()));

        $qb->addGroupBy('esup.id');

        if (null !== $firstResult) {
            $qb->setFirstResult($firstResult);
        }

        if (null !== $maxResults) {
            $qb->setMaxResults($maxResults);
        }

        return $qb;
    }

    /**
     * @param Edition $edition
     * @param User $user
     *
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isUserSignedUpAndNotListener(Edition $edition, User $user): bool
    {
        $qb = $this->findUserSignedUpAndNotListener($edition);
        $qb->select('count(esup.id) as count');
        $qb->andWhere($qb->expr()->eq('esup.joined', $user->getId()));

        $query = $qb->getQuery();

        $result = $query->getOneOrNullResult();

        if (null !== $result) {
            return (int)$result['count'] > 0;
        }

        return false;
    }

    /**
     * @param Edition $edition
     * @param int|null $firstResult
     * @param int|null $maxResults
     *
     * @return int|mixed|string
     */
    public function findUniqueUserSignedUpAndIsVoluntary(Edition $edition, ?int $firstResult = null, ?int $maxResults = null)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->innerJoin('esup.editionDiscount', 'ef');

        $qb->andWhere($qb->expr()->eq('esup.statusPay', EditionSignup::EDITION_SIGNUP_STATUS_PAID));
        $qb->andWhere($qb->expr()->eq('ef.type', $qb->expr()->literal(EditionDiscount::TYPE_VOLUNTARY)));

        $qb->andWhere($qb->expr()->isNull('esup.deletedAt'));

        $qb->andWhere($qb->expr()->eq('esup.edition', $edition->getId()));
        // $qb->andWhere($qb->expr()->eq('esup.joined', $user->getId()));

        $qb->addGroupBy('esup.joined');

        if (null !== $firstResult) {
            $qb->setFirstResult($firstResult);
        }

        if (null !== $maxResults) {
            $qb->setMaxResults($maxResults);
        }

        return $qb->getQuery()->getResult();
    }
}
