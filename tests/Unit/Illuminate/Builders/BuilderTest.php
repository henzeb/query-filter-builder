<?php

namespace Henzeb\Query\Tests\Unit\Illuminate\Builders;

use Henzeb\Query\Filters\Contracts\Filter;
use Henzeb\Query\Filters\Query;
use Henzeb\Query\Illuminate\Builders\Builder;
use Henzeb\Query\Tests\Helpers\DataProviders;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase;
use Illuminate\Database\Query\Builder as IlluminateBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Henzeb\Query\Illuminate\Filters\Contracts\Filter as IlluminateFilter;


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

    public function testShouldBuildFilter() {
        $laravelBuilder = DB::query()->from('animals')->where('animal', 'dog');;

        $myFilter = new class implements IlluminateFilter {
            public function build(EloquentBuilder|IlluminateBuilder $builder): void
            {
                $builder->join('owners_animals', 'animal_id', 'id')
                    ->where('owner_id', 5);
            }
        };

        (new Builder($laravelBuilder))->filter($myFilter);

        $this->assertEquals(
            'select * from `animals` inner join `owners_animals` on `animal_id` = `id` where `animal` = ? and (`owner_id` = ?)',
            $laravelBuilder->toSql()
        );
    }

    public function testShouldBuildOrFilter() {
        $laravelBuilder = DB::query()->from('animals')->where('animal', 'dog');

        $myFilter = new class implements IlluminateFilter {
            public function build(EloquentBuilder|IlluminateBuilder $builder): void
            {
                $builder->joinSub(function(IlluminateBuilder $builder){
                    $builder->from('owners')->where('country', 'NL');
                }, 'owner', 'animal_id', 'id')->where('owner_id', 5);
            }
        };

        (new Builder($laravelBuilder))->orFilter($myFilter);

        $this->assertEquals(
            'select * from `animals` inner join (select * from `owners` where `country` = ?) as `owner` on `animal_id` = `id` where `animal` = ? or (`owner_id` = ?)',
            $laravelBuilder->toSql()
        );
        $this->assertEquals(
            ['NL','dog', 5],
            $laravelBuilder->getBindings()
        );
    }
}
