<?php

namespace App\Bundle\Base\Repository;

use App\Bundle\Base\Contracts\RepositoryBase as Repository;
use App\Bundle\Base\Entity\Division;
use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Entity\UserThemesDetails;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserThemes|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserThemes|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserThemes[]    findAll()
 * @method UserThemes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserThemes[]    findCustom(array $select = [], array $andWhere = [], $limit = null, $orderBy = [])
 */
class UserThemesDetailsRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserThemesDetails::class);
        $this->setAlias('utd');
    }

    /**
     * @param int $themeId
     * @param int $lang ['Português' => 1, 'English' => 2, 'Spanish' => 3];
     *
     * @return mixed
     */
    public function keywordsByThemeLang(int $themeId, int $lang)
    {
        $langfield = [1 => 'utd.portugueseKeywords', 2 => 'utd.englishKeywords', 3 => 'utd.spanishKeywords'];

        $qb = $this->createQueryBuilder($this->getAlias());

        return $qb
            ->select($langfield[$lang] . ' as keywords')
            ->andWhere($qb->expr()->eq($this->replaceFieldAlias('userThemes'), $themeId))
            ->andWhere($qb->expr()->isNull('utd.deletedAt'))
            ->distinct()
            ->orderBy('keywords')
            ->getQuery()
            ->execute();
    }

    /**
     * @param int $lang ['Português' => 1, 'English' => 2, 'Spanish' => 3];
     *
     * @return mixed
     */
    public function keywordsByLang(int $lang)
    {
        $langfieldTitle = [1 => 'utd.portugueseTitle', 2 => 'utd.englishTitle', 3 => 'utd.spanishTitle'];
        $langfieldKeywords = [1 => 'utd.portugueseKeywords', 2 => 'utd.englishKeywords', 3 => 'utd.spanishKeywords'];

        $qb = $this->createQueryBuilder($this->getAlias());

        return $qb
            ->select('d.initials as division, ' . $langfieldTitle[$lang] . ' as title, ' . $langfieldKeywords[$lang] . ' as keywords, ut.position as ut_position')
            ->innerJoin(UserThemes::class, 'ut', 'WITH', 'ut.id=utd.userThemes')
            ->innerJoin(Division::class, 'd', 'WITH', 'd.id=ut.division')
            ->andWhere($qb->expr()->isNull('utd.deletedAt'))
            ->distinct()
            ->addOrderBy('division')
            ->addOrderBy('ut_position')
            ->addOrderBy('keywords')
            ->getQuery()
            ->execute();
    }

    /**
     * @return mixed
     */
    public function getAllKeywords()
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        return $qb
            ->select('utd.portugueseKeywords, utd.englishKeywords, utd.spanishKeywords')
            ->andWhere($qb->expr()->isNull('utd.deletedAt'))
            ->getQuery()
            ->execute();
    }

    /**
     * @param $division
     *
     * @return int|mixed|string|UserThemesDetails[]
     */
    public function getByDivision($division)
    {
        $qb = $this->createQueryBuilder($this->getAlias());

        return $qb
            //->select('identity(utd.userThemes) as id, utd.portugueseTitle as name')
            ->join(UserThemes::class, 'ut', 'WITH', 'ut.id = utd.userThemes')
            ->andWhere($qb->expr()->eq('ut.status', 2))
            ->andWhere($qb->expr()->eq('ut.division', $division))
            ->andWhere($qb->expr()->isNull('utd.deletedAt'))
            ->addGroupBy('utd.userThemes')
            //->addOrderBy('ut.position', 'ASC')
            ->addOrderBy('ut.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
