<?php

namespace Unit\Illuminate\Facades;

use Henzeb\Query\Filters\Query as expectedQuery;
use Orchestra\Testbench\TestCase;
use Henzeb\Query\Illuminate\Facades\Filter as FilterWithPath;
use Henzeb\Query\Illuminate\Providers\QueryFilterServiceProvider;

class QueryTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [QueryFilterServiceProvider::class];
    }

    public function testShouldLoad()
    {
        $this->assertEquals(
            (new expectedQuery())->limit(1),
            FilterWithPath::limit(1)
        );
    }

    public function testShouldLoadWithAlias()
    {
        $this->assertEquals(
            (new expectedQuery())->limit(1), \Filter::limit(1)
        );
    }
}
