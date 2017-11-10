<?php

namespace Jes490\DParser\Facade;

use Illuminate\Support\Facades\Facade;

class DParser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Jes490\DParser\DParser::class;
    }

    public static function roll(string $string)
    {
        return new \Jes490\DParser\DParser($string);
    }
}