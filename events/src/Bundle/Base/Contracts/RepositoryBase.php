<?php

namespace App\Bundle\Base\Contracts;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Class RepositoryBase
 * @package App\Bundle\Base\Constracts
 */
abstract class RepositoryBase extends ServiceEntityRepository
{
    const JOIN_PARAMETER = ':_';
    const PASS_PARAMETER = '_';

    /**
     * @var
     */
    private $alias;

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param String $alias
     * @return RepositoryBase
     */
    public function setAlias(String $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @param array|null $select
     * @param array|null $andWhere
     * @param Int|null $limit
     * @param array|null $orderBy
     * @param array|null $in
     * @return array
     */
    public function findCustom(?Array $select = null, ?Array $andWhere = null, ?Int $limit = null, ?Array $orderBy = null, ?Array $in = null): array
    {

        $query = $this->createQueryBuilder($this->getAlias());

        if (!is_null($select) && !empty($select)) {
            $query->select($this->replaceFieldAlias($select));
        }

        if (!is_null($limit)) {
            $query->setMaxResults($limit);
        }

        if (!is_null($andWhere) && !empty($andWhere)) {
            array_walk($andWhere, function ($value, $key) use (&$query){
                $query->andWhere(
                    $query->expr()->eq($this->replaceFieldAlias($key),self::JOIN_PARAMETER.$key)
                )->setParameter(self::PASS_PARAMETER.$key, $value);
            });
        }

        if (!is_null($in) && !empty($in)) {
            array_walk($in, function ($value, $key) use (&$query){
                $query->andWhere(
                    $query->expr()->in($this->replaceFieldAlias($key),self::JOIN_PARAMETER.'array')
                )->setParameter(self::PASS_PARAMETER.'array', $value);
            });
        }

        if (!is_null($orderBy) && !empty($orderBy)) {
            array_walk($orderBy, function ($value, $key) use (&$query) {
                $query->orderBy($this->replaceFieldAlias($key), $value);
            });
        }

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string|array $values
     * @param null|String $replace
     * @return mixed
     */
    public function replaceFieldAlias($values, ?String $replace = null)
    {
        if (is_null($replace)) {
            $replace = $this->getAlias();
        }

        if (is_array($values)) {
            return array_map(function ($value) use ($replace) {
                return "$replace.$value";
            }, $values);
        }

        return "$replace.$values";
    }
}