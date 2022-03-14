<?php

namespace Unit\Illuminate\Mixins\FormRequestMxin;

use Config;
use Henzeb\Query\Filters\Query;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\InputBag;
use Henzeb\Query\Tests\Unit\Illuminate\Mixins\Concerns\Mocks;

class FormRequestMixinTest extends TestCase
{
    use Mocks;

    public function testShouldUseFiltersMethod()
    {
        $request = new class extends FormRequest {
            private function filters(Query $query): void
            {
                $query->in('animal', 'cat', 'dog');
            }
        };

        $this->assertEquals(
            (new Query)->limit(50)->in('animal', 'cat', 'dog'),
            $request->getFilter()
        );
    }

    public function providesTestCasesForFilter(): array
    {
        return [
            'basic' => [['filter' => ['animal' => 'dog']], 'animal', 'dog'],
            'boolean' => [['filter' => ['alive' => true]], 'alive', true],
            'integer' => [['filter' => ['age' => 100]], 'age', 100],
            'comma-separated-string' => [['filter' => ['animal' => 'dog,cat']], 'animal', 'dog,cat'],
            'nested-array' => [['filter' => ['animal' => ['type' => 'mammal']]], 'animal.type', 'mammal'],
            'default' => [['filter' => []], 'animal', 'cat', 'cat'],
        ];
    }


    public function providesTestCasesForFilterArray(): array
    {
        return [
            'basic' => [['filter' => ['animal' => 'dog']], 'animal', 'dog'],
            'boolean' => [['filter' => ['alive' => true]], 'alive', true],
            'integer' => [['filter' => ['age' => 100]], 'age', 100],
            'array' => [['filter' => ['animal' => ['dog','cat'] ]], 'animal', ['dog','cat']],
            'comma-separated-string' => [['filter' => ['animal' => 'dog,cat']], 'animal', ['dog','cat']],
            'comma-separated-string-with-spaces' => [['filter' => ['animal' => 'dog, cat']], 'animal', ['dog','cat']],
            'random-comma' => [['filter' => ['animal' => ',']], 'animal', []],
            'just-some-commas' => [['filter' => ['animal' => ', , ,']], 'animal', []],
            'nested-array' => [['filter' => ['animal' => ['type' => 'mammal']]], 'animal.type', 'mammal'],
            'default' => [['filter' => []], 'animal', 'cat', 'cat'],
        ];
    }

    public function providesTestCasesForHasFilter(): array
    {
        return [
            'basic' => [['filter' => ['animal' => 'dog']], 'animal', true],
            'basic-fail' => [['filter' => []], 'animal', false],
            'nested-array' => [['filter' => ['animal' => ['type' => 'mammal']]], 'animal', true],
            'nested-array-fail' => [['filter' => []], 'animal.type', false],
        ];
    }

    /**
     * @param array $input
     * @param string $filter
     * @param mixed $expected
     * @param mixed|null $default
     * @return void
     *
     * @dataProvider providesTestCasesForFilter
     */
    public function testFilterShouldReturnValue(array $input, string $filter, mixed $expected, mixed $default = null)
    {
        $mock = $this->getFormRequest();
        $mock->query = new InputBag($input);

        $this->assertEquals(
            $expected,
            $mock->filter($filter, $default)
        );
    }

    /**
     * @param array $input
     * @param string $filter
     * @param mixed $expected
     * @param mixed|null $default
     * @return void
     *
     * @dataProvider providesTestCasesForFilterArray
     */
    public function testFilterArrayShouldReturnValueAsArray(array $input, string $filter, mixed $expected, mixed $default = null)
    {
        $mock = $this->getFormRequest();
        $mock->query = new InputBag($input);

        if(!is_array($expected)) {
            $expected = [$expected];
        }

        $this->assertEquals(
            $expected,
            $mock->filterArray($filter, $default)
        );
    }

    /**
     * @param array $input
     * @param string $filter
     * @param mixed $expected
     * @return void
     *
     * @dataProvider providesTestCasesForHasFilter
     */
    public function testHasFilterShouldReturnBoolean(array $input, string $filter, bool $expected)
    {
        $mock = $this->getFormRequest();
        $mock->query = new InputBag($input);

        $this->assertEquals(
            $expected,
            $mock->hasFilter($filter)
        );
    }

    public function testFilterShouldUseConfiguredBaseName() {
        Config::set('filter.key', 'myOwn');
        $mock = $this->getFormRequest();
        $mock->query = new InputBag(['myOwn'=>['animal'=>'dog']]);

        $this->assertEquals(
            'dog',
            $mock->filter('animal')
        );
    }

    public function testFilterShouldUseConfiguredEmptyBaseName() {
        Config::set('filter.key', null);
        $mock = $this->getFormRequest();
        $mock->query = new InputBag(['animal'=>'dog']);

        $this->assertEquals(
            'dog',
            $mock->filter('animal')
        );
    }

    public function testHasFilterShouldUseConfiguredBaseName() {
        Config::set('filter.key', 'myOwn');
        $mock = $this->getFormRequest();
        $mock->query = new InputBag(['myOwn'=>['animal'=>'dog']]);

        $this->assertEquals(
            true,
            $mock->hasFilter('animal')
        );
    }

    public function testHasFilterShouldUseConfiguredEmptyBaseName() {
        Config::set('filter.key', '');
        $mock = $this->getFormRequest();
        $mock->query = new InputBag(['animal'=>'dog']);

        $this->assertEquals(
            true,
            $mock->hasFilter('animal')
        );
    }
}
