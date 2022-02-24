<?php

namespace Henzeb\Query\Tests\Unit\Illuminate\Builders;

use Henzeb\Query\Filters\Contracts\Filter;
use Henzeb\Query\Filters\Query;
use Henzeb\Query\Illuminate\Builders\Builder;
use Henzeb\Query\Tests\Helpers\DataProviders;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase;


class BuilderTest extends TestCase
{
    use DataProviders;

    /**
     * @param string $method
     * @param array $parameters
     * @param string|array $query
     * @param bool $noParameters
     * @return void
     *
     * @dataProvider providesFilterWithQueryTestcases
     */
    public function testShouldBuild(string $method, array $parameters, string|array $query, bool $noParameters = false): void
    {
        $laravelBuilder = DB::query()->from('animals');

        $builder = new Builder($laravelBuilder);

        $flattenedParameters = $this->flattenArray($parameters);

        $builder->$method(...$flattenedParameters);

        array_shift($flattenedParameters);

        if(is_array($query)) {
            $flattenedParameters = $query['parameters'];
            $query = $query['query'];
        }

        $this->assertEquals('select * from `animals` ' . ($noParameters ? '' : 'where ') . $query, $laravelBuilder->toSql());
        $this->assertEquals($laravelBuilder->getBindings(), $noParameters ? [] : array_values($flattenedParameters));
    }

    /**
     * @param string $method
     * @param array $parameters
     * @param array|string $query
     * @param bool $noParameters
     * @return void
     *
     * @dataProvider providesFilterWithQueryTestcases
     */
    public function testShouldBuildWithOr(string $method, array $parameters, array|string $query, bool $noParameters = false): void
    {
        $illuminateBuilder = DB::query()->from('animals')->whereRaw('true');

        $builder = new Builder($illuminateBuilder);

        $flattenedParameters = $this->flattenArray($parameters);
        if (!$noParameters) {
            $method = 'or' . ucfirst($method);
        }
        $builder->$method(...$flattenedParameters);

        array_shift($flattenedParameters);

        if(is_array($query)) {
            $flattenedParameters = $query['parameters'];
            $query = $query['query'];
        }

        $this->assertEquals('select * from `animals` where true ' . ($noParameters ? '' : 'or ') . $query, $illuminateBuilder->toSql());
        $this->assertEquals($illuminateBuilder->getBindings(), $noParameters ? [] : array_values($flattenedParameters));
    }

    /**
     * @param string $method
     * @param array $parameters
     * @param array|string $query
     * @param bool $noParameters
     * @return void
     *
     * @dataProvider providesFilterWithQueryTestcases
     */
    public function testShouldBuildWithGroup(string $method, array $parameters, array|string $query, bool $noParameters = false): void
    {
        $illuminateBuilder = DB::query()->from('animals')->whereRaw('true');
        $builder = new Builder($illuminateBuilder);

        $queryFilter = new Query();
        $parameters = $this->flattenArray($parameters);
        $queryFilter->nest()->$method(...$parameters);
        $queryFilter->build($builder);

        array_shift($parameters);

        if(is_array($query)) {
            $parameters = $query['parameters'];
            $query = $query['query'];
        }

        $this->assertEquals('select * from `animals` where true' . ($noParameters ? '' : ' and (' . $query . ')'), $illuminateBuilder->toSql());
        $this->assertEquals($noParameters ? [] : $parameters, $illuminateBuilder->getBindings());
    }

    /**
     * @param string $method
     * @param array $parameters
     * @param array|string $query
     * @param bool $noParameters
     * @return void
     *
     * @dataProvider providesFilterWithQueryTestcases
     */
    public function testShouldBuildWithGroupOr(string $method, array $parameters, array|string $query, bool $noParameters = false): void
    {
        $laravelBuilder = DB::query()->from('animals');
        $builder = new Builder($laravelBuilder);

        $queryFilter = new Query();
        $parameters = $this->flattenArray($parameters);
        $queryFilter->is('animal', 'horse')->or()->nest()->$method(...$parameters);

        $queryFilter->build($builder);

        array_shift($parameters);

        if(is_array($query)) {
            $parameters = $query['parameters'];
            $query = $query['query'];
        }

        array_unshift($parameters, 'horse');

        $this->assertEquals('select * from `animals` where `animal` = ?' . ($noParameters ? '' : ' or (' . $query . ')'), $laravelBuilder->toSql());
        $this->assertEquals($noParameters ? ['horse'] : $parameters, $laravelBuilder->getBindings());
    }

    public function testAcceptsEloquentBuilderInstance()
    {
        $model = new class extends Model {
            protected $table = 'my_table';
        };

        $query = $model->where('alive', true);

        $builder = new Builder($query);
        $builder->is('animal', 'cat');

        $this->assertEquals(
            'select * from `my_table` where `alive` = ? and `animal` = ?',
            $query->toSql()
        );
    }

    public function testShouldNotAllowNonIlluminateFilters() {

        $laravelBuilder = DB::query()->from('animals');

        $myFilter = new class implements Filter {

        };

        $this->expectError();

        (new Builder($laravelBuilder))->filter($myFilter);
    }

    public function testShouldNotAllowNonIlluminateFiltersWhenOr() {

        $laravelBuilder = DB::query()->from('animals');

        $myFilter = new class implements Filter {

        };

        $this->expectError();

        (new Builder($laravelBuilder))->orFilter($myFilter);
    }
}
