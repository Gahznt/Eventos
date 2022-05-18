<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Repository\KeywordRepository;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class Keyword
 * @package App\Bundle\Base\Services
 */
class Keyword extends ServiceBase implements ServiceInterface
{
    /**
     * @var KeywordRepository
     */
    private $entity;

    /**
     * State constructor.
     * @param KeywordRepository $keywordRepository$
     */
    public function __construct(KeywordRepository $keywordRepository)
    {
        $this->entity = $keywordRepository;
    }

    public function getKeywords()
    {
        return $this->entity->findCustom(['id', 'portuguese'], null, null, ['name' => 'ASC']);
    }

    public function getKeywordByThemeId($themeId)
    {
        return $this->entity->findCustom(['id', 'portuguese as name'], ['theme' => $themeId]);
    }

    /**
     * @param $id
     * @return \App\Bundle\Base\Entity\Keyword|null
     */
    function getKeywordById($id)
    {
        return $this->entity->find($id);
    }

    function getTeste($id)
    {
        return $this->entity->findBy(['theme' => $id]);
    }

}