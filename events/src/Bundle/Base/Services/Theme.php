<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Repository\KeywordRepository;
use App\Bundle\Base\Repository\ThemeRepository;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class Theme
 * @package App\Bundle\Base\Services
 */
class Theme extends ServiceBase implements ServiceInterface
{
    /**
     * @var ThemeRepository
     */
    private $entity;

    private $teste;

    /**
     * Theme constructor.
     * @param ThemeRepository $themeRepository
     * @param KeywordRepository $keywordRepository
     */
    public function __construct(ThemeRepository $themeRepository, KeywordRepository $keywordRepository)
    {
        $this->entity = $themeRepository;
        $this->teste = $keywordRepository;
    }

    public function getDivisions()
    {
        return $this->entity->findCustom(['id', 'portuguese'], null, null, ['name' => 'ASC']);
    }

    public function getThemeByDivision($divisionId)
    {
        return $this->entity->findCustom(['id', 'portuguese as name'], ['divisionId' => $divisionId]);
    }

    /**
     * @param $id
     * @return mixed
     */
    function getThemeById($id)
    {
        return $this->entity->find(['id' => $id]);
    }
}