<?php

namespace Henzeb\Query\Illuminate\Facades;

use Illuminate\Support\Facades\Facade;

class Filter extends Facade
{
    protected static function getFacadeAccessor(): string {
        return 'filter.query';
    }
}
