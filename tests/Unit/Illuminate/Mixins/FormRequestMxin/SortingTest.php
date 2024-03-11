<?php

namespace Henzeb\Query\Tests\Unit\Illuminate\Mixins\FormRequestMixin;

use Config;
use Henzeb\Query\Filters\Query;
use Orchestra\Testbench\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Illuminate\Validation\ValidationException;
use Henzeb\Query\Tests\Unit\Illuminate\Mixins\Concerns\Mocks;

class SortingTest extends TestCase
{
    use Mocks;

    public static function providesSortingSituations(): array
    {
        return [
            'asc' => ['input' => ['sort' => 'animal'], 'expected' => (new Query())->limit(50)->asc('animal')],
            'asc-other-member' => [
                'input' => ['anotherSortField' => 'animal'],
                'expected' => (new Query())->limit(50)->asc('animal'),
                'member' => 'anotherSortField'
            ],
            'asc-twice' => [
                'input' => ['sort' => 'animal,age'],
                'expected' => (new Query())->limit(50)->asc('animal')->asc('age')
            ],
            'desc' => ['input' => ['sort' => '-animal'], 'expected' => (new Query())->limit(50)->desc('animal')],
            'desc-twice' => [
                'input' => ['sort' => '-animal,-age'],
                'expected' => (new Query())->limit(50)->desc('animal')->desc('age')
            ],
            'desc-asc-mixed' => [
                'input' => ['sort' => '-animal,age'],
                'expected' => (new Query())->limit(50)->desc('animal')->asc('age')
            ],
            'asc-desc-mixed' => [
                'input' => ['sort' => 'animal,-age'],
                'expected' => (new Query())->limit(50)->asc('animal')->desc('age')
            ],

        ];
    }

    /**
     * @return void
     *
     * @dataProvider providesSortingSituations
     */
    public function testShouldGetInstanceWithSorting(array $input, Query $expected, string $member = 'sort')
    {
        Config::set('filter.sorting.key', $member);
        $formRequest = $this->getFormRequest();
        $formRequest->query = new InputBag($input);

        $formRequest->allowedSorting = [
            'age',
            'animal',
            '-age',
            '-animal'
        ];

        $this->assertEquals(
            $expected,
            $formRequest->getFilter()
        );
    }

    public static function provideSortingValidationFailures(): array
    {
        return [
            'boolean' => [true],
            'boolean-member' => [true, 'sorting'],
            'comma-ended' => ['animal,'],
            'comma-started' => [',animal'],
            'not-even-trying' => ['-,-'],
            'starts-weird' => ['-,animal'],
            'ends-weird' => ['animal,-'],
        ];
    }

    /**
     * @param array $input
     * @return void
     *
     * @dataProvider provideSortingValidationFailures
     */
    public function testShouldFailWithIncorrectValues(mixed $input, string $member = 'sort')
    {
        Config::set('filter.sorting.key', $member);
        $formRequest = $this->getFormRequest()
            ->shouldAllowMockingProtectedMethods();
        $formRequest->makePartial();

        $formRequest->expects('failedValidation')
            ->once();

        $formRequest->query = new InputBag([$member => $input]);

        $this->expectException(ValidationException::class);

        $formRequest->getFilter();
    }

    public static function providesEnablePaginationTestcases(): array
    {
        return [
            'config-disabled-fq-enabled' =>
                [false, true, (new Query())->limit(50)->asc('animal')],
            'config-enabled-fq-disabled' =>
                [true, false, (new Query())->limit(50)],
            'both-enabled' =>
                [true, true, (new Query())->limit(50)->asc('animal')],
            'both-disabled' =>
                [false, false, (new Query())->limit(50)],
        ];
    }

    /**
     * @return void
     *
     * @dataProvider providesEnablePaginationTestcases
     */
    public function testShouldListenToConfigurationOptions(
        bool $config, bool $enableSorting, Query $expected
    )
    {
        Config::set('filter.sorting.auto', $config);
        $formRequest = $this->getFormRequest();
        $formRequest->query = new InputBag(['sort' => 'animal']);

        $formRequest->enableSorting = $enableSorting;
        $formRequest->allowedSorting = ['animal'];

        $this->assertEquals(
            $expected,
            $formRequest->getFilter()
        );
    }

    public function testShouldThrowValidationExceptionForNotAllowedFields()
    {
        $formRequest = $this->getFormRequest();
        $formRequest->expects('failedValidation')->once();

        $formRequest->query = new InputBag(['sort' => 'animal']);

        $this->expectException(ValidationException::class);

        $formRequest->getFilter();
    }

    public function testShouldAllowDefaultSortingInFormRequest()
    {
        $formRequest = $this->getFormRequest();
        $formRequest->enablePagination = false;
        $formRequest->defaultSort = 'id';


        $this->assertEquals(
            (new Query())->asc('id'),
            $formRequest->getFilter()
        );
    }

    public function testShouldAllowDefaultSortingAsArrayInFormRequest()
    {
        $formRequest = $this->getFormRequest();
        $formRequest->enablePagination = false;
        $formRequest->defaultSort = ['-id'];


        $this->assertEquals(
            (new Query())->desc('id'),
            $formRequest->getFilter()
        );
    }

    public function testShouldNotUseDefaultWhenSortIsGiven()
    {
        $formRequest = $this->getFormRequest();

        $formRequest->enablePagination = false;
        $formRequest->allowedSorting = ['animal'];
        $formRequest->defaultSort = ['-id'];

        $formRequest->query = new InputBag(['sort' => 'animal']);

        $this->assertEquals(
            (new Query())->asc('animal'),
            $formRequest->getFilter()
        );
    }
}
