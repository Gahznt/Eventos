<?php

namespace App\Bundle\Base\Services;

use App\Bundle\Base\Contracts\ServiceBase;
use App\Bundle\Base\Contracts\ServiceInterface;

/**
 * Class Util
 * @package App\Bundle\Base\Services
 */
class Util extends ServiceBase implements ServiceInterface
{
    public function onlyIntInputs(array $whiteList, array &$data)
    {
        if (!empty($whiteList)) {
            foreach ($whiteList as $value) {
                if (isset($data[$value]) && !empty($data[$value])) {
                    $data[$value] = preg_replace('/[^\d]/', '', $data[$value]);
                }
            }
        }
    }
}