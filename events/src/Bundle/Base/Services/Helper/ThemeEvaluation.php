<?php

namespace App\Bundle\Base\Services\Helper;

use App\Bundle\Base\Entity\UserThemes;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class ThemeEvaluation
 * @package App\Bundle\Base\Services
 */
class ThemeEvaluation extends ServiceBase implements ServiceInterface
{
    private $entity;

    /**
     * ThemeEvaluation constructor.
     * @param UserThemes $userThemes
     */
    public function __construct(UserThemes $userThemes)
    {
        $this->entity = $userThemes;
    }

    /**
     * @param $value
     * @return array
     */
    public static function getStatus($value)
    {
        $const = UserThemes::THEME_EVALUATION_STATUS;
        $const = array_flip($const);


        if (isset($const[$value])) {
            return [ $value => $const[$value] ];
        }

        return [ 0 => $const[0] ];
    }

    /**
     * @param int status
     * @return string statusText
     */
    public static function getStatusText($status) {
        return preg_replace('/^THEME_EVALUATION_STATUS_/', '', self::getStatus($status)[$status]);
    }
}