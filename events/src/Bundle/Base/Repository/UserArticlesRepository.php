<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\SystemEvaluationIndications;
use App\Bundle\Base\Entity\User;
use App\Bundle\Base\Entity\UserArticles;
use App\Bundle\Base\Entity\UserArticlesAuthors;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesDetails;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserArticles|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserArticles|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserArticles[]    findAll()
 * @method UserArticles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserArticles[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 */
class UserArticlesRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserArticles::class);
        $this->setAlias('ua');
    }

    /**
     * @param array $filters
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findByTesteQb(array $filters = [])
    {
        $queryBuilder = $this->createQueryBuilder($this->getAlias());
        //$queryBuilder->leftJoin(User::class, 'u', Join::WITH, 'u.id=ua.userId');
        $queryBuilder->leftJoin(UserThemesDetails::class, 'utd', Join::WITH, 'utd.userThemes=ua.userThemes');
        $queryBuilder->andWhere($queryBuilder->expr()->isNull('ua.deletedAt'));

        if (isset($filters['id']) && ! is_null($filters['id'])) {
            $queryBuilder->andWhere('ua.id = :id')->setParameter(':id', $filters['id']->getId());
        }

        if (isset($filters['status']) && ! is_null($filters['status'])) {
            $queryBuilder->andWhere('ua.status = :status')->setParameter(':status', $filters['status']);
        }

        if (isset($filters['edition']) && ! is_null($filters['edition'])) {
            $queryBuilder->andWhere('ua.editionId = :edition')->setParameter(':edition', $filters['edition']);
        }

        if (isset($filters['search']) && ! is_null($filters['search'])) {
            $queryBuilder->leftJoin(UserArticlesAuthors::class, 'uaa', 'WITH', 'uaa.userArticles=ua.id');
            $queryBuilder->leftJoin(User::class, 'u2', Join::WITH, 'u2.id=uaa.userAuthor');
            $queryBuilder->addGroupBy('ua.id');

            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('ua.id', $queryBuilder->expr()->literal($filters['search'])),
                $queryBuilder->expr()->like('ua.title', $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('ua.resume', $queryBuilder->expr()->literal('%' . $filters['search'] . '%')),
                $queryBuilder->expr()->like('u2.name', $queryBuilder->expr()->literal('%' . $filters['search'] . '%'))
            ));
        }

        if (isset($filters['division']) && ! is_null($filters['division'])) {
            if (is_array($filters['division'])) {
                $count = 0;
                foreach ($filters['division'] as $division) {
                    $queryBuilder->orWhere("ua.divisionId = :division_$count")->setParameter(":division_$count", $division);
                    $count++;
                }
            } else {
                $queryBuilder->andWhere('ua.divisionId = :division')->setParameter(':division', $filters['division']);
            }
        }

        if (isset($filters['theme']) && ! is_null($filters['theme'])) {
            $queryBuilder->andWhere('ua.userThemes = :theme')->setParameter(':theme', $filters['theme']);
        }

        return $queryBuilder;
    }

    /**
     * @param array $filters
     *
     * @return int|mixed|string
     */
    public function findByTeste(array $filters = [])
    {
        $queryBuilder = $this->findByTesteQb($filters);

        $dbquery = $queryBuilder
            ->getQuery();
 // // ->setFetchMode(User::class, 'u', ClassMetadata::FETCH_EAGER) // ->setFetchMode(UserThemesDetails::class, 'utd', ClassMetadata::FETCH_EAGER);

        return $dbquery->execute();
    }

    /**
     * @param array $filters
     * @param string|null $sortBy
     * @param string|null $direction
     *
     * @return mixed
     */
    public function findByFilters(array $filters = [], ?string $sortBy = null, ?string $direction = null)
    {
        $queryBuilder = $this->createQueryBuilder($this->getAlias());

        $queryBuilder->leftJoin(Division::class, 'd', Join::WITH, 'd.id=ua.divisionId');
        $queryBuilder->leftJoin(UserThemes::class, 't', Join::WITH, 't.id=ua.userThemes');


        $dbquery = $queryBuilder
            ->getQuery();
 // // ->setFetchMode(Division::class, 'd', ClassMetadata::FETCH_EAGER) // ->setFetchMode(UserThemes::class, 't', ClassMetadata::FETCH_EAGER);

        return $dbquery->execute();
    }

    /**
     * @param int $userId
     * @param int|null $status
     *
     * @return int|mixed|string|UserArticles[]
     */
    public function getUserSubmissions(int $userId, ?int $status = null)
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->leftJoin(UserArticlesAuthors::class, 'uaa', 'WITH', 'uaa.userArticles=ua.id');

        $qb->andWhere('uaa.userAuthor = :userId')->setParameter(':userId', $userId)
            ->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));
        if (null !== $status) {
            $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('status'), $status));
        }

        $qb->addOrderBy($this->replaceFieldAlias('createdAt'), 'DESC');

        return $qb->getQuery()
            ->getResult();
    }


    /**
     * @param $division
     *
     * @return mixed
     */
    public function getByDivision($division)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        return $qb
            ->select('ua.id, ua.title as name')
            ->andWhere($qb->expr()->eq('ua.divisionId', $division))
            ->andWhere($qb->expr()->isNull('ua.deletedAt'))
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getDashEvaluationIndicationQuantity(int $edition)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->select([
            'ua.id',
            sprintf('%s AS base_count', $qb->expr()->count('sei.id')),
        ]);

        $qb->leftJoin(SystemEvaluationIndications::class, 'sei', 'WITH', 'sei.userArticles=ua.id');

        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('editionId'), $edition));
        $qb->andWhere($qb->expr()->isNull('ua.deletedAt'));
        $qb->andWhere($qb->expr()->isNull('sei.deleted_at'));

        $qb->addGroupBy('ua.id');

        // subquery gerada com `echo $qb->getQuery()->getSQL();`
        $sql = "SELECT 
               COUNT(*) count,
               SUBQUERY.sclr_1 indication_count
        FROM (
            SELECT u0_.id AS id_0, COUNT(s1_.id) AS sclr_1 FROM user_articles u0_ 
                LEFT JOIN system_evaluation_indications s1_ ON (s1_.user_articles_id = u0_.id) 
            WHERE u0_.edition_id = 1 AND u0_.deleted_at IS NULL AND s1_.deleted_at IS NULL 
            GROUP BY u0_.id
        ) SUBQUERY
        GROUP BY SUBQUERY.sclr_1
        ORDER BY count DESC";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     *
     * @return int|mixed|string
     */
    public function getDashEvaluationByIndicationProgress(int $edition)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->select([
            'sei.valid',
            sprintf('%s AS count', $qb->expr()->count('sei.valid')),
        ]);

        $qb->innerJoin(SystemEvaluationIndications::class, 'sei', 'WITH', 'sei.userArticles=ua.id');

        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('editionId'), $edition));
        $qb->andWhere($qb->expr()->isNull('ua.deletedAt'));
        $qb->andWhere($qb->expr()->isNull('sei.deleted_at'));

        $qb->addGroupBy('sei.valid');

        $qb->orderBy('sei.valid', 'DESC');

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @param int $edition
     * @param int $quantity
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getDashEvaluationIndicationsByQuantity(int $edition, $quantity = 1)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->select([
            'ua.id',
            sprintf('%s AS base_count', $qb->expr()->count('sei.id')),
        ]);

        $qb->leftJoin(SystemEvaluationIndications::class, 'sei', 'WITH', 'sei.userArticles=ua.id');

        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('editionId'), $edition));
        $qb->andWhere($qb->expr()->isNull('ua.deletedAt'));
        $qb->andWhere($qb->expr()->isNull('sei.deleted_at'));


        $qb->addGroupBy('ua.id');

        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->select([
            'ua.id',
            'SUM(sei.valid) as valid_count',
            sprintf('%s AS base_count', $qb->expr()->count('sei.id')),
        ]);

        $qb->leftJoin(SystemEvaluationIndications::class, 'sei', 'WITH', 'sei.userArticles=ua.id');

        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('editionId'), $edition));
        $qb->andWhere($qb->expr()->isNull('ua.deletedAt'));
        $qb->andWhere($qb->expr()->isNull('sei.deleted_at'));

        $qb->addGroupBy('ua.id');

        // subquery gerada com `echo $qb->getQuery()->getSQL();`
        $sql = "SELECT 
               COUNT(*) count,
               SUBQUERY.sclr_1 valid_count,
               (SUBQUERY.sclr_2-SUBQUERY.sclr_1) invalid_count, 
               SUBQUERY.sclr_2 indication_count
        FROM (
            SELECT u0_.id AS id_0, SUM(s1_.valid) AS sclr_1, COUNT(s1_.id) AS sclr_2 
            FROM user_articles u0_ 
                LEFT JOIN system_evaluation_indications s1_ ON (s1_.user_articles_id = u0_.id) 
            WHERE u0_.edition_id = 1 AND u0_.deleted_at IS NULL AND s1_.deleted_at IS NULL 
            GROUP BY u0_.id
        ) SUBQUERY

        WHERE 1=1 
        AND SUBQUERY.sclr_2 =:quantity
        GROUP BY SUBQUERY.sclr_2
        ORDER BY count DESC";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('quantity', $quantity);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     * @param int $edition
     *
     * @return int|mixed|string
     */
    public function getDashEvaluationByIndicationProgressByDivision(int $edition)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->select([
            'd.initials',
            'SUM(CASE
                WHEN sei.valid = 1 THEN 1
                ELSE 0
            END) AS valid',
            'SUM(CASE
                WHEN sei.valid = 0 THEN 1
                ELSE 0
            END) AS invalid',
        ]);

        $qb->innerJoin(SystemEvaluationIndications::class, 'sei', 'WITH', 'sei.userArticles=ua.id');
        $qb->innerJoin(Division::class, 'd', 'WITH', 'd.id = ua.divisionId');

        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('editionId'), $edition));
        $qb->andWhere($qb->expr()->isNull('ua.deletedAt'));
        $qb->andWhere($qb->expr()->isNull('sei.deleted_at'));

        $qb->addGroupBy('ua.divisionId');

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @param int $edition
     *
     * @return int|mixed|string
     */
    public function getDashEvaluationByIndicationThemesByDivision(int $edition)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        $qb->select([
            'd.id as division',
            'd.initials',
            'd.portuguese',
            'ut.id as userThemes',
            'utd.portugueseTitle',
            'SUM(CASE
                WHEN sei.valid = 1 THEN 1
                ELSE 0
            END) AS valid',
            'SUM(CASE
                WHEN sei.valid = 0 THEN 1
                ELSE 0
            END) AS invalid',
        ]);

        $qb->innerJoin(SystemEvaluationIndications::class, 'sei', 'WITH', 'sei.userArticles=ua.id');
        $qb->innerJoin(Division::class, 'd', 'WITH', 'd.id = ua.divisionId');
        $qb->innerJoin(UserThemes::class, 'ut', 'WITH', 'ut.id = ua.userThemes');
        $qb->innerJoin(UserThemesDetails::class, 'utd', 'WITH', 'utd.userThemes = ut.id');

        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('editionId'), $edition));
        $qb->andWhere($qb->expr()->isNull('ua.deletedAt'));
        $qb->andWhere($qb->expr()->isNull('sei.deleted_at'));
        $qb->andWhere($qb->expr()->isNull('utd.deletedAt'));

        $qb->addGroupBy('ua.divisionId');
        $qb->addGroupBy('ua.userThemes');

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReportStatistics(int $edition)
    {
        $sql = "SELECT
                    d.portuguese AS division,
                    GROUP_CONCAT(utd.portuguese_title) AS themes,
                    -- submetidos geral
                    (
                        SELECT
                            COUNT(DISTINCT(sua.id))
                        FROM
                            user_articles sua
                        WHERE
                            1 = 1 
                            AND sua.deleted_at IS NULL 
                            AND sua.edition_id = :edition 
                            AND sua.division_id = d.id
                    ) AS submitted,
                    -- rejeitados pré seleção
                    (
                        SELECT
                            COUNT(DISTINCT(sua.id))
                        FROM
                            system_evaluation sse
                        INNER JOIN user_articles sua ON
                            (sua.id = sse.user_articles_id)
                        WHERE
                            1 = 1 
                            AND sse.deleted_at IS NULL 
                            AND sse.reject_at IS NOT NULL 
                            AND sua.deleted_at IS NULL 
                            AND sua.edition_id = :edition
                            AND sua.division_id = d.id
                        GROUP BY
                            sse.user_articles_id
                    ) AS rp_pre,
                    -- disponíveis para avaliação
                    (
                        SELECT
                            COUNT(DISTINCT(sua.id))
                        FROM
                            user_articles sua
                        WHERE
                            1 = 1 
                            AND sua.deleted_at IS NULL 
                            AND sua.edition_id = :edition
                            AND sua.division_id = d.id 
                            AND sua.status = 1
                    ) AS available,
                    -- com discrepancia
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            (
                            SELECT
                                sseaa.user_articles_id,
                                COUNT(*) AS COUNT
                            FROM
                                system_evaluation_averages_articles AS sseaa
                            INNER JOIN user_articles sua ON
                                (
                                    sua.id = sseaa.user_articles_id
                                )
                            WHERE
                                1 = 1 
                                AND sua.deleted_at IS NULL
                                AND sua.edition_id = :edition
                            GROUP BY
                                sseaa.user_articles_id
                            HAVING
                                COUNT(*) > 1
                        ) AS sub
                    INNER JOIN user_articles sua ON
                        (sua.id = sub.user_articles_id)
                    WHERE
                        1 = 1 
                        AND sua.division_id = d.id 
                        AND sua.deleted_at IS NULL
                        AND sua.edition_id = :edition
                    ) AS discrepancies
                FROM
                    division d
                INNER JOIN user_themes ut ON
                    (ut.division_id = d.id)
                INNER JOIN user_themes_details utd ON
                    (utd.user_themes_id = ut.id)
                WHERE
                    1 = 1
                GROUP BY
                    d.id
                ORDER BY
                    d.portuguese
                LIMIT 100";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('edition', $edition);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReportSysStatistics(int $edition)
    {
        $sql = "SELECT
                    d.portuguese AS division,
                    GROUP_CONCAT(utd.portuguese_title) AS themes,
                    -- submetidos geral
                    (
                        SELECT
                            COUNT(DISTINCT(sua.id))
                        FROM
                            user_articles sua
                        WHERE
                            1 = 1 
                            AND sua.deleted_at IS NULL
                            AND sua.edition_id = :edition
                            AND sua.division_id = d.id
                    ) AS submitted_orig,
                    -- submetidos tema diferente
                    (
                        SELECT
                            COUNT(DISTINCT(sua.id))
                        FROM
                            user_articles sua
                        WHERE
                            1 = 1 
                            AND sua.deleted_at IS NULL
                            AND sua.edition_id = :edition 
                            AND sua.division_id = d.id 
                            AND sua.original_user_themes_id != sua.user_themes_id
                    ) AS submitted_final,
                    -- disponíveis para avaliação
                    (
                        SELECT
                            COUNT(DISTINCT(sua.id))
                        FROM
                            user_articles sua
                        WHERE
                            1 = 1 
                            AND sua.deleted_at IS NULL
                            AND sua.edition_id = :edition 
                            AND sua.division_id = d.id 
                            AND sua.status = 1
                    ) AS available,
                    -- rejeitados formato
                    (
                        SELECT
                            COUNT(DISTINCT(sua.id))
                        FROM
                            system_evaluation sse
                        INNER JOIN user_articles sua ON
                            (sua.id = sse.user_articles_id)
                        WHERE
                            1 = 1 
                            AND sse.deleted_at IS NULL 
                            AND sse.format_error_at IS NOT NULL 
                            AND sua.deleted_at IS NULL
                            AND sua.edition_id = :edition 
                            AND sua.division_id = d.id
                        GROUP BY
                            sse.user_articles_id
                    ) AS rp,
                    -- avaliadores
                    (
                        SELECT
                            COUNT(*) AS COUNT
                        FROM
                            system_evaluation_indications AS ssei
                        INNER JOIN user_articles sua ON
                            (sua.id = ssei.user_articles_id)
                        WHERE
                            1 = 1 
                            AND sua.deleted_at IS NULL
                            AND sua.edition_id = :edition 
                            AND sua.division_id = d.id
                    ) AS evaluators,
                    -- avaliados por 1
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            (
                            SELECT
                                ssei.user_articles_id,
                                COUNT(*) AS COUNT
                            FROM
                                system_evaluation_indications AS ssei
                            INNER JOIN user_articles sua ON
                                (sua.id = ssei.user_articles_id)
                            WHERE
                                1 = 1 
                                AND sua.deleted_at IS NULL
                                AND sua.edition_id = :edition
                            GROUP BY
                                ssei.user_articles_id
                            HAVING
                                COUNT(*) = 1
                        ) AS sub
                    INNER JOIN user_articles sua ON
                        (sua.id = sub.user_articles_id)
                    WHERE
                        1 = 1 
                        AND sua.division_id = d.id 
                        AND sua.deleted_at IS NULL
                        AND sua.edition_id = :edition
                    ) AS evaluator_by1,
                    -- avaliados por 2
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            (
                            SELECT
                                ssei.user_articles_id,
                                COUNT(*) AS COUNT
                            FROM
                                system_evaluation_indications AS ssei
                            INNER JOIN user_articles sua ON
                                (sua.id = ssei.user_articles_id)
                            WHERE
                                1 = 1 
                                AND sua.deleted_at IS NULL
                                AND sua.edition_id = :edition
                            GROUP BY
                                ssei.user_articles_id
                            HAVING
                                COUNT(*) = 2
                        ) AS sub
                    INNER JOIN user_articles sua ON
                        (sua.id = sub.user_articles_id)
                    WHERE
                        1 = 1 
                        AND sua.division_id = d.id 
                        AND sua.deleted_at IS NULL
                        AND sua.edition_id = :edition
                    ) AS evaluator_by2,
                    -- avaliados por 3
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            (
                            SELECT
                                ssei.user_articles_id,
                                COUNT(*) AS COUNT
                            FROM
                                system_evaluation_indications AS ssei
                            INNER JOIN user_articles sua ON
                                (sua.id = ssei.user_articles_id)
                            WHERE
                                1 = 1 
                                AND sua.deleted_at IS NULL
                                AND sua.edition_id = :edition
                            GROUP BY
                                ssei.user_articles_id
                            HAVING
                                COUNT(*) = 3
                        ) AS sub
                    INNER JOIN user_articles sua ON
                        (sua.id = sub.user_articles_id)
                    WHERE
                        1 = 1 
                        AND sua.division_id = d.id 
                        AND sua.deleted_at IS NULL
                        AND sua.edition_id = :edition
                    ) AS evaluator_by3,
                    -- com discrepancia
                    (
                        SELECT
                            COUNT(*)
                        FROM
                            (
                            SELECT
                                sseaa.user_articles_id,
                                COUNT(*) AS COUNT
                            FROM
                                system_evaluation_averages_articles AS sseaa
                            INNER JOIN user_articles sua ON
                                (
                                    sua.id = sseaa.user_articles_id
                                )
                            WHERE
                                1 = 1 
                                AND sua.deleted_at IS NULL
                                AND sua.edition_id = :edition
                            GROUP BY
                                sseaa.user_articles_id
                            HAVING
                                COUNT(*) > 1
                        ) AS sub
                    INNER JOIN user_articles sua ON
                        (sua.id = sub.user_articles_id)
                    WHERE
                        1 = 1 
                        AND sua.division_id = d.id 
                        AND sua.deleted_at IS NULL
                        AND sua.edition_id = :edition
                    ) AS discrepancies,
                    -- submetidos convidados/coordenador da divisão
                    (
                        SELECT
                            COUNT(DISTINCT(sua.id))
                        FROM
                            user_articles sua
                        INNER JOIN division_coordinator sdc ON
                            (
                                sdc.coordinator_id = sua.user_id
                            )
                        WHERE
                            1 = 1 
                            AND sua.deleted_at IS NULL
                            AND sua.edition_id = :edition 
                            AND sua.division_id = d.id 
                            AND sdc.division_id = d.id 
                            AND sua.status = 2
                    ) AS invited,
                    -- submetidos selecionados/aprovados
                    (
                        SELECT
                            COUNT(DISTINCT(sua.id))
                        FROM
                            user_articles sua
                        WHERE
                            1 = 1 
                            AND sua.deleted_at IS NULL
                            AND sua.edition_id = :edition 
                            AND sua.division_id = d.id 
                            AND sua.status = 2
                    ) AS selected
                FROM
                    division d
                INNER JOIN user_themes ut ON
                    (ut.division_id = d.id)
                INNER JOIN user_themes_details utd ON
                    (utd.user_themes_id = ut.id)
                WHERE
                    1 = 1
                GROUP BY
                    d.id
                ORDER BY
                    d.portuguese
                LIMIT 100";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('edition', $edition);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReportPendingAction(int $edition)
    {
        $sql = "SELECT
                    CONCAT(d.initials, ua.id) AS id,
                    utd.portuguese_title AS theme,
                    ua.title AS title,
                    ua.frame AS fit,
                    sse.format_error_at,
                    sse.format_error_justification,
                    sse.reject_at,
                    sse.reject_justification,
                    u.name AS evaluator
                FROM
                    user_articles ua
                INNER JOIN system_evaluation sse ON
                    (sse.user_articles_id = ua.id)
                INNER JOIN user_themes ut ON
                    (ut.id = ua.user_themes_id)
                INNER JOIN user_themes_details utd ON
                    (utd.user_themes_id = ut.id)
                INNER JOIN division d ON
                    (d.id = ua.division_id)
                INNER JOIN user u ON
                    (u.id = sse.user_owner_id)
                WHERE
                    1 = 1 
                    AND sse.deleted_at IS NULL 
                    AND (
                            sse.format_error_at IS NOT NULL 
                            OR sse.reject_at IS NOT NULL
                        ) 
                    AND ua.deleted_at IS NULL
                    AND ua.edition_id = :edition
                GROUP BY
                    sse.user_articles_id";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('edition', $edition);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReportRefusedFormat(int $edition)
    {
        $sql = "SELECT
                    CONCAT(d.initials, ua.id) AS id,
                    utd.portuguese_title AS theme,
                    ua.title AS title,
                    ua.frame AS fit,
                    sse.format_error_justification AS reason
                FROM
                    user_articles ua
                INNER JOIN system_evaluation sse ON
                    (sse.user_articles_id = ua.id)
                INNER JOIN user_themes ut ON
                    (ut.id = ua.user_themes_id)
                INNER JOIN user_themes_details utd ON
                    (utd.user_themes_id = ut.id)
                INNER JOIN division d ON
                    (d.id = ua.division_id)
                WHERE
                    1 = 1 
                    AND sse.deleted_at IS NULL 
                    AND sse.format_error_at IS NOT NULL 
                    AND ua.deleted_at IS NULL
                    AND ua.edition_id = :edition
                GROUP BY
                    sse.user_articles_id";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('edition', $edition);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReportRefusedPre(int $edition)
    {
        $sql = "SELECT
                    CONCAT(d.initials, ua.id) AS id,
                    utd.portuguese_title AS theme,
                    ua.title AS title,
                    ua.frame AS fit,
                    sse.reject_justification AS reason
                FROM
                    user_articles ua
                INNER JOIN system_evaluation sse ON
                    (sse.user_articles_id = ua.id)
                INNER JOIN user_themes ut ON
                    (ut.id = ua.user_themes_id)
                INNER JOIN user_themes_details utd ON
                    (utd.user_themes_id = ut.id)
                INNER JOIN division d ON
                    (d.id = ua.division_id)
                WHERE
                    1 = 1 
                    AND sse.deleted_at IS NULL 
                    AND sse.reject_at IS NOT NULL 
                    AND ua.deleted_at IS NULL
                    AND ua.edition_id = :edition
                GROUP BY
                    sse.user_articles_id";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('edition', $edition);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReportCoordDivision(int $edition)
    {
        $sql = "SELECT
                    CONCAT(d.initials, ua.id) AS id,
                    utd.portuguese_title AS theme,
                    ua.title AS title,
                    ua.frame AS fit,
                    ua.created_at AS date,
                    ua.status AS status
                FROM
                    user_articles ua
                INNER JOIN system_evaluation sse ON
                    (sse.user_articles_id = ua.id)
                INNER JOIN user_themes ut ON
                    (ut.id = ua.user_themes_id)
                INNER JOIN user_themes_details utd ON
                    (utd.user_themes_id = ut.id)
                INNER JOIN division d ON
                    (d.id = ua.division_id)
                INNER JOIN system_evaluation_indications AS sei
                ON
                    (sei.user_articles_id = ua.id)
                INNER JOIN division_coordinator sdc ON
                    (
                        sdc.coordinator_id = sei.user_evaluator_id
                    )
                WHERE
                    1 = 1 
                    AND sse.deleted_at IS NULL 
                    AND ua.deleted_at IS NULL
                    AND ua.edition_id = :edition
                    AND sei.deleted_at IS NULL
                GROUP BY
                    sse.user_articles_id";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('edition', $edition);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReportArticlesTheme(int $edition)
    {
        $sql = "SELECT
                    CONCAT(d.initials, ua.id) AS id,
                    utd.portuguese_title AS theme,
                    ua.title AS title,
                    ua.frame AS fit,
                    utd.portuguese_title AS theme_original,
                    utd2.portuguese_title AS theme_current,
                    ua.created_at AS date
                FROM
                    user_articles ua
                INNER JOIN system_evaluation sse ON
                    (sse.user_articles_id = ua.id)
                INNER JOIN user_themes ut ON
                    (ut.id = ua.user_themes_id)
                INNER JOIN user_themes_details utd ON
                    (utd.user_themes_id = ut.id)
                INNER JOIN user_themes ut2 ON
                    (
                        ut2.id = ua.original_user_themes_id
                    )
                INNER JOIN user_themes_details utd2 ON
                    (utd2.user_themes_id = ut2.id)
                INNER JOIN division d ON
                    (d.id = ua.division_id)
                WHERE
                    1 = 1 
                    AND sse.deleted_at IS NULL 
                    AND ua.deleted_at IS NULL 
                    AND ua.original_user_themes_id != ua.user_themes_id
                GROUP BY
                    sse.user_articles_id";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('edition', $edition);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReportDiscrepancies(int $edition)
    {
        $sql = "SELECT
                    CONCAT(d.initials, ua.id) AS id,
                    ua.title AS title,
                    COUNT(
                        DISTINCT(sei.user_evaluator_id)
                    ) AS designations,
                    ua.frame AS fit,
                    utd.portuguese_title AS theme,
                    ua.created_at AS date,
                    ua.status AS status
                FROM
                    user_articles ua
                INNER JOIN system_evaluation_averages_articles AS sseaa ON
                    (
                        sseaa.user_articles_id = ua.id
                    )
                INNER JOIN system_evaluation_indications AS sei ON 
                    (sei.user_articles_id = ua.id)
                INNER JOIN user_themes ut ON
                    (ut.id = ua.user_themes_id)
                INNER JOIN user_themes_details utd ON
                    (utd.user_themes_id = ut.id)
                INNER JOIN division d ON
                    (d.id = ua.division_id)
                WHERE
                    1 = 1 
                    AND ua.deleted_at IS NULL
                GROUP BY
                    sseaa.user_articles_id
                HAVING
                    COUNT(DISTINCT(sseaa.id)) > 1";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('edition', $edition);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReportInprogress(int $edition)
    {
        $sql = "SELECT
                    CONCAT(d.initials, ua.id) AS id,
                    utd.portuguese_title AS theme,
                    ua.title AS title,
                    ua.frame AS fit,
                    COUNT(DISTINCT(sei.user_evaluator_id)) AS designations,
                    COUNT(DISTINCT(sse.user_owner_id)) AS evaluations,
                    ua.status AS status
                FROM
                    user_articles ua
                INNER JOIN system_evaluation sse ON
                    (sse.user_articles_id = ua.id)
                INNER JOIN system_evaluation_indications sei ON
                	(sei.user_articles_id = ua.id)
                INNER JOIN user_themes ut ON
                    (ut.id = ua.user_themes_id)
                INNER JOIN user_themes_details utd ON
                    (utd.user_themes_id = ut.id)
                INNER JOIN division d ON
                    (d.id = ua.division_id)
                WHERE
                    1 = 1 
                    AND sse.deleted_at IS NULL 
                    AND ua.deleted_at IS NULL 
                    AND sei.deleted_at IS NULL
                GROUP BY
                    sse.user_articles_id";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('edition', $edition);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReportInprogressEvaluators(int $edition)
    {
        $sql = "SELECT
                    u.name,
                    u.email,
                    GROUP_CONCAT(DISTINCT(utd.portuguese_title)) AS themes,
                    COUNT(DISTINCT(sei.user_articles_id)) AS designations,
                    COUNT(DISTINCT(sei2.user_articles_id)) AS evaluations
                FROM
                    system_evaluation sse
                INNER JOIN user u ON
                    (u.id = sse.user_owner_id)
                INNER JOIN user_articles ua ON
                    (ua.id = sse.user_articles_id)
                INNER JOIN system_evaluation_indications sei ON
                	(sei.user_evaluator_id = sse.user_owner_id)
                LEFT JOIN system_evaluation_indications sei2 ON
                	(sei2.user_evaluator_id = sse.user_owner_id AND sei2.valid = 1)
                INNER JOIN user_themes ut ON
                    (ut.id = ua.user_themes_id)
                INNER JOIN user_themes_details utd ON
                    (utd.user_themes_id = ut.id)
                WHERE
                    1 = 1 
                    AND sse.deleted_at IS NULL 
                    AND ua.deleted_at IS NULL 
                    AND sei.deleted_at IS NULL
                    AND sei2.deleted_at IS NULL
                GROUP BY
                    sei.user_evaluator_id";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('edition', $edition);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReportInprogressEvaluations(int $edition)
    {
        $sql = "SELECT
                    CONCAT(d.initials, ua.id) AS id,
                    utd.portuguese_title AS theme,
                    -- ua.title AS title,
                    ua.frame AS fit,
                    sse.created_at AS date,
                    sse.user_owner_id AS evaluator_id,
                    CHAR_LENGTH(sse.justification) AS char_count,
                    sse.justification,
                    sse.author_rate_one,
                    sse.author_rate_two
                FROM
                    user_articles ua
                INNER JOIN system_evaluation sse ON
                    (sse.user_articles_id = ua.id)
                INNER JOIN user_themes ut ON
                    (ut.id = ua.user_themes_id)
                INNER JOIN user_themes_details utd ON
                    (utd.user_themes_id = ut.id)
                INNER JOIN division d ON
                    (d.id = ua.division_id)
                WHERE
                    1 = 1 
                    AND sse.deleted_at IS NULL 
                    AND ua.deleted_at IS NULL 
                    AND ua.edition_id = :edition
                    AND ua.status IN(:approved, :reproved)
                -- GROUP BY
                   -- sse.user_articles_id";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('edition', $edition);
        $stmt->bindValue('approved', UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED);
        $stmt->bindValue('reproved', UserArticles::ARTICLE_EVALUATION_STATUS_REPROVED);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReportArticlesQtd(int $edition)
    {
        $sql = "SELECT
					u.name AS name,
					CASE
						WHEN u.record_type != 2 THEN u.identifier
						ELSE ''
					END AS doc,
					
					CASE
						WHEN u.record_type = 2 THEN u.identifier
						ELSE ''
					END AS passport,
                    COUNT(*) AS qtd,
                    GROUP_CONCAT(DISTINCT(CONCAT(d.initials, ua.id))) AS articles
                FROM
                    user u
                INNER JOIN user_articles ua ON
                    (ua.user_id = u.id)
                INNER JOIN division d ON
                    (d.id = ua.division_id)
                WHERE
                    1 = 1  
                    AND ua.deleted_at IS NULL 
                GROUP BY
                	u.id
                ORDER BY
                    qtd DESC";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('edition', $edition);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReportApprovedArticles(int $edition)
    {
        $sql = "
            SELECT ua.id,
				    ut.position AS ordem,
                    utd.portuguese_title AS theme_title,
                    u.name AS author,
                    u.identifier AS document,
                    u.email,
                    esp.name AS tipo_inscricao,
                    es.status_pay, 
                    d.initials AS divisao
            FROM user_articles ua
                    INNER JOIN user_articles_authors uaa ON(ua.id = uaa.user_articles_id)
                    INNER JOIN user u ON(u.id = uaa.user_author_id)
                    INNER JOIN division d ON(ua.division_id = d.id)
                    LEFT JOIN user_themes ut ON(ua.user_themes_id = ut.id)
                    LEFT JOIN user_themes_details utd ON(utd.user_themes_id = ut.id)
                    LEFT JOIN edition_signup es ON(es.edition_id = ua.edition_id and uaa.user_author_id = es.joined_id)
                    LEFT JOIN edition_signup_articles esa ON(uaa.user_articles_id=esa.user_articles_id and esa.edition_signup_id = es.id)
                    LEFT JOIN edition_payment_mode esp ON(es.edition_payment_mode_id = esp.id)
            WHERE es.deleted_at IS NULL 
            AND ua.status = :status
            AND ua.edition_id = :edition
            ORDER BY ua.id 
        ";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('edition', $edition);
        $stmt->bindValue('status', UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     *
     * @return mixed[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getReportInstitutions(int $edition)
    {
        $sql = "SELECT
                       i.name                               AS institution,
                       p.name                               AS program,
                       COUNT(DISTINCT (ua.id))              AS submitted,
                       COUNT(DISTINCT (ua2.id))             AS selected,
                       COUNT(DISTINCT (uaa.user_author_id)) AS authors
                FROM institution i
                         INNER JOIN user_association uass ON
                    (uass.institution_id = i.id) AND (uass.status_pay IS NULL OR uass.status_pay = 1)
                         INNER JOIN program p ON
                    (p.id = uass.program_id)
                         INNER JOIN user_articles ua ON
                    (ua.user_id = uass.user_id AND ua.edition_id=:edition_id AND ua.deleted_at IS NULL)
                         LEFT JOIN user_articles ua2 ON
                    (ua2.user_id = uass.user_id AND ua2.edition_id=:edition_id AND ua2.status = :status AND ua2.deleted_at IS NULL)
                         INNER JOIN user_articles_authors uaa ON
                    (uaa.user_articles_id = ua.id)
                GROUP BY p.id, i.name, p.name
                ORDER BY i.name, p.name";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->bindValue('edition_id', $edition);
        $stmt->bindValue('status', UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param int $edition
     * @param int $userId
     *
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNumberOfNotCanceledArticlesByEditionAndAuthor(int $edition, int $userId)
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->leftJoin(UserArticlesAuthors::class, 'uaa', 'WITH', 'uaa.userArticles=ua.id');

        $qb->select($qb->expr()->count($this->replaceFieldAlias('id')));
        $qb->andWhere('ua.editionId = :edition');
        $qb->andWhere('uaa.userAuthor = :userId');
        $qb->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));
        $qb->andWhere($qb->expr()->neq($this->replaceFieldAlias('status'), UserArticles::ARTICLE_EVALUATION_STATUS_CANCELED));
        $qb->setParameter('edition', $edition);
        $qb->setParameter('userId', $userId);
        $query = $qb->getQuery();
        return (int)$query->getSingleScalarResult();
    }

    /**
     * @param int $edition
     * @param int $userId
     *
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getNumberOfApprovedArticlesByEditionAndAuthor(int $edition, int $userId)
    {
        $qb = $this->createQueryBuilder($this->getAlias());
        $qb->leftJoin(UserArticlesAuthors::class, 'uaa', 'WITH', 'uaa.userArticles=ua.id');

        $qb->select($qb->expr()->count($this->replaceFieldAlias('id')));
        $qb->andWhere('ua.editionId = :edition');
        $qb->andWhere('uaa.userAuthor = :userId');
        $qb->andWhere($qb->expr()->isNull($this->replaceFieldAlias('deletedAt')));
        $qb->andWhere($qb->expr()->eq($this->replaceFieldAlias('status'), UserArticles::ARTICLE_EVALUATION_STATUS_APPROVED));
        $qb->setParameter('edition', $edition);
        $qb->setParameter('userId', $userId);
        $query = $qb->getQuery();
        return (int)$query->getSingleScalarResult();
    }
}
