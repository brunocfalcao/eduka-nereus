<?php

namespace Eduka\Nereus\Facades;

use Illuminate\Support\Facades\Facade;

class Nereus extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'eduka-nereus';
    }
}
