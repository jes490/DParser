<?php

namespace Jes490\DParser\Facades;

use Illuminate\Support\Facades\Facade;

class DParser extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Jes490\DParser\DParser::class;
    }


}
