<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class JsonDecodeExtension extends AbstractExtension
{
    public function getName()
    {
        return 'twig.json_decode';
    }

    public function getFilters()
    {
        return array(
            'json_decode'   => new TwigFilter('json_decode', [$this, 'jsonDecode'])
        );
    }

    public function jsonDecode($string)
    {
        return json_decode($string);
    }
}