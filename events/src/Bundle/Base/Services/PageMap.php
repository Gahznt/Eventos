<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class PageMap
 * @package App\Bundle\Base\Services
 */
class PageMap extends ServiceBase implements ServiceInterface
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $subTitle;

    public function __construct()
    {
        $this->title = "Titulo da Página";
        $this->subTitle = "Subtítulo do Site";
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return PageMap
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSubTitle(): ?string
    {
        return $this->subTitle;
    }

    /**
     * @param null|string $SubTitle
     * @return PageMap
     */
    public function setSubTitle(?string $SubTitle): PageMap
    {
        $this->subTitle = $SubTitle;

        return $this;
    }

}