<?php

namespace Henzeb\Query\Tests\Filters;

use Henzeb\Query\Builders\Contracts\QueryBuilder;
use Henzeb\Query\Filters\Query;
use Henzeb\Query\Tests\Helpers\DataProviders;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;

class QueryTest extends MockeryTestCase
{
    use DataProviders;

    public function getMock(bool $withGetFilters = true): Query|Mock
    {
        /**
         * @method array getFilters()
         */
        $mock = Mockery::mock(Query::class)
            ->makePartial();

        if ($withGetFilters) {
            $mock->expects('getFilters')
                ->once()
                ->andReturnUsing(
                    (function () {
                        return $this->filters;
                    })->bindTo($mock, Query::class));
        }

        return $mock;
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return void
     *
     * @dataProvider providesFilterTestcases
     */
    public function testShouldAddFilter(string $method, array $parameters, string $expectedMethod = null): void
    {
        $queryFilter = $this->getMock();
        $expectedParameters = $parameters;

        $parameters = $this->flattenArray($parameters);

        $queryFilter->$method(...array_values($parameters));

        $this->assertEquals(
            [
                ['action' => $expectedMethod ?? $method, 'parameters' => array_filter($expectedParameters)]
            ],
            $queryFilter->getFilters()
        );
    }

    public function testShouldAutomaticallyAddAnd(): void
    {
        $mock = $this->getMock()
            ->is('animal', 'cat')
            ->is('age', 5);

        $this->assertEquals(
            [
                [
                    'action' => 'is',
                    'parameters' => [
                        'key' => 'animal',
                        'value' => 'cat',
                    ],
                ],
                [
                    'action' => 'and',
                    'parameters' => [],
                ],
                [
                    'action' => 'is',
                    'parameters' => [
                        'key' => 'age',
                        'value' => 5,
                    ]
                ],
            ],
            $mock->getFilters()
        );
    }

    /**
     * @param string $method
     * @return void
     *
     * @dataProvider providesOperators
     */
    public function testShouldReplaceAnd(string $method): void
    {
        $mock = $this->getMock()
            ->is('animal', 'cat')
            ->$method()
            ->is('age', 5);

        $this->assertEquals(
            [
                [
                    'action' => 'is',
                    'parameters' => [
                        'key' => 'animal',
                        'value' => 'cat',
                    ],
                ],
                [
                    'action' => $method,
                    'parameters' => [],
                ],
                [
                    'action' => 'is',
                    'parameters' => [
                        'key' => 'age',
                        'value' => 5,
                    ]
                ],
            ],
            $mock->getFilters()
        );
    }

    /**
     * @param string $method
     * @return void
     *
     * @dataProvider providesOperators
     */
    public function testShouldNotBeAbleToStart(string $method): void
    {
        $query = $this->getMock(false);

        $this->expectError();

        $query->$method();
    }

    public function testShouldBeAbleToNest(): void
    {
        $mock = $this->getMock();
        $mock->nest()->is('animal', 'cat')->is('age', 5);
        $mock->or()->nest()->is('animal', 'dog')->is('age', 6);


        $this->assertEquals([
            [
                'action' => 'nest',
                'parameters' => [(new Query())->is('animal', 'cat')->is('age', 5)],
            ],
            [
                'action' => 'or',
                'parameters' => [],
            ],
            [
                'action' => 'nest',
                'parameters' => [(new Query())->is('animal', 'dog')->is('age', 6)],
            ]
        ], $mock->getFilters());
    }

    public function testShouldBeAbleToNestByPassingNewQuery(): void
    {
        $mock = $this->getMock();
        $mock->nest((new Query())->is('animal', 'cat')->is('age', 5));
        $mock->or()->nest((new Query())->is('animal', 'dog')->is('age', 6));


        $this->assertEquals([
            [
                'action' => 'nest',
                'parameters' => [(new Query())->is('animal', 'cat')->is('age', 5)],
            ],
            [
                'action' => 'or',
                'parameters' => [],
            ],
            [
                'action' => 'nest',
                'parameters' => [(new Query())->is('animal', 'dog')->is('age', 6)],
            ]
        ], $mock->getFilters());
    }

    public function testChainingOfGroup(): void
    {
        $query = new Query();

        $this->assertEquals($query::class, $query->nest()::class);
        $this->assertNotEquals($query, $query->nest());
    }

    public function testChainingOfGroupWhenPassing(): void
    {
        $query = new Query();
        $nest = new Query();

        $this->assertEquals($query, $query->nest($nest));
    }

    /**
     * @return void
     * @dataProvider providesFilterTestcases
     */
    public function testShouldBuildSimple(string $method, array $parameters, string $expectedMethod = null): void
    {
        $query = $this->getMock(false);

        $parameters = $this->flattenArray($parameters);

        $query->$method(...array_values($parameters));

        $builder = Mockery::mock(QueryBuilder::class.'[empty]');


        $builder->expects($expectedMethod ?? $method)->withArgs(array_filter(array_values($parameters)));

        $query->build($builder);
    }

    /**
     * @param string $method
     * @param $parameters
     * @return void
     * @dataProvider providesFilterTestcases
     */
    public function testShouldBuildOr(string $method, array $parameters, string $expectedMethod = null): void
    {
        $queryFilter = $this->getMock(false);
        $queryFilter->is('is', 'test');
        $queryFilter->or();
        $queryFilter->$method(...array_values($this->flattenArray($parameters)));

        $builder = Mockery::mock(QueryBuilder::class.'[empty]');
        $builder->expects('is')->once();

        $orMethod = 'or' . ucfirst($expectedMethod ?? $method);
        if (method_exists($builder, $orMethod)) {
            $method = $orMethod;
        }

        $builder->expects($method)->once();

        $queryFilter->build($builder);
    }

    public function testShouldAllowExtendingQuery(): void
    {
        $extendedQuery = $this->getMock();

        $extendedQuery->expects('test')
            ->once()
            ->andReturnUsing((function () {

                $this->addFilter('testMethod', ['parameter' => 'test']);

            })->bindTo($extendedQuery));

        $extendedQuery->test();

        $this->assertEquals(
            [
                [
                    'action' => 'testMethod',
                    'parameters' => ['parameter' => 'test']
                ]
            ],
            $extendedQuery->getFilters()
        );
    }
}
