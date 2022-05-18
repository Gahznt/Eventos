<?php

namespace App\Bundle\Base\Services\Helper;

use App\Bundle\Base\Entity\Panel;
use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class PanelEvaluation
 * @package App\Bundle\Base\Services
 */
class PanelEvaluation extends ServiceBase implements ServiceInterface
{
    /**
     * @param $value
     * @return array
     */
    public static function getStatus($value)
    {
        $const = Panel::PANEL_EVALUATION_STATUS;
        $const = array_flip($const);


        if (isset($const[$value])) {
            return [ $value => $const[$value] ];
        }

        return [ 0 => $const[0] ];
    }

    /**
     * @param int statusEvaluation
     * @return string statusText
     */
    public static function getStatusText($status) {
        return preg_replace('/^PANEL_EVALUATION_STATUS_/', '', self::getStatus($status)[$status]);
    }
}