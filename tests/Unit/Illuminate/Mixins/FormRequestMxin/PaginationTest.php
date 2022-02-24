<?php

namespace Henzeb\Query\Tests\Unit\Illuminate\Mixins\FormRequestMixin;

use Config;
use Henzeb\Query\Filters\Query;
use Orchestra\Testbench\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Illuminate\Validation\ValidationException;
use Henzeb\Query\Tests\Unit\Illuminate\Mixins\Concerns\Mocks;

class PaginationTest extends TestCase
{

    use Mocks;


    public function providesPaginationInputBags()
    {
        return [
            'different-default-limit' => [
                'inputBag' => ['page' => []],
                'expectedQuery' => (new Query())->limit(100),
                'path' => 'page',
                'limitPath' => 'number',
                'offsetPath' => 'size',
                'defaultLimit' => 100,
            ],
            'no-default-limit' => [
                'inputBag' => ['page' => []],
                'expectedQuery' => (new Query()),
                'path' => 'page',
                'limitPath' => 'number',
                'offsetPath' => 'size',
                'defaultLimit' => null,
            ],
            'just-size' => [
                'inputBag' => ['page' => ['size' => 1]],
                'expectedQuery' => (new Query())->limit(1)
            ],
            'just-number' => [
                'inputBag' => ['page' => ['number' => 1]],
                'expectedQuery' => (new Query())->limit(50)->offset(1)
            ],
            'both' => [
                'inputBag' => ['page' => ['number' => 1, 'size' => 20]],
                'expectedQuery' => (new Query())->limit(20)->offset(1)
            ],
            'empty-page-path' => [
                'inputBag' => ['number' => 1, 'size' => 20],
                'expectedQuery' => (new Query())->limit(20)->offset(1),
                'path' => '',
            ],
            'any-page-path' => [
                'inputBag' => ['random' => ['number' => 1, 'size' => 20]],
                'expectedQuery' => (new Query())->limit(20)->offset(1),
                'path' => 'random',
            ],
            'size-offset-path' => [
                'inputBag' => ['page' => ['myOffset' => 1, 'mylimit' => 20]],
                'expectedQuery' => (new Query())->limit(20)->offset(1),
                'path' => 'page',
                'limitPath' => 'mylimit',
                'offsetPath' => 'myOffset'
            ]
        ];
    }

    /**
     * @param array $inputBag
     * @param Query $expected
     * @param string $path
     * @param string $limitPath
     * @param string $offsetPath
     * @param int|null $defaultLimit
     * @return void
     *
     * @dataProvider providesPaginationInputBags
     */
    public function testShouldGetQueryInstanceWithPagination(
        array  $inputBag,
        Query  $expected,
        string $path = 'page',
        string $limitPath = 'size',
        string $offsetPath = 'number',
        ?int   $defaultLimit = 50
    )
    {
        Config::set('filter.pagination.key', $path);
        Config::set('filter.pagination.limit', $limitPath);
        Config::set('filter.pagination.offset', $offsetPath);
        Config::set('filter.pagination.defaults.limit', $defaultLimit);

        $formRequest = $this->getFormRequest();
        $formRequest->query = new InputBag($inputBag);

        $this->assertEquals(
            $expected,
            $formRequest->getFilter()
        );
    }

    public function testShouldNotParsePaginationWhenTurnedOff()
    {
        Config::set('filter.pagination.auto', false);

        $formRequest = $this->getFormRequest();

        $formRequest->query = new InputBag(['page' => ['offset' => 1]]);

        $this->assertEquals(
            (new Query()),
            $formRequest->getFilter()
        );
    }

    public function testShouldAllowFormRequestToManipulateLimitSize()
    {
        $formRequest = $this->getFormRequest();

        $formRequest->defaultLimit = 150;
        $formRequest->query = new InputBag([]);

        $this->assertEquals(
            (new Query())->limit(150),
            $formRequest->getFilter()
        );

    }

    public function testShouldAllowFormRequestOverrideManuallyAddedLimit()
    {
        $formRequest = $this->getFormRequest();

        $formRequest->defaultLimit = 150;
        $formRequest->query = new InputBag(['page' => ['size' => 30]]);

        $this->assertEquals(
            (new Query())->limit(30),
            $formRequest->getFilter()
        );
    }

    protected function providesValidationFailures()
    {
        return [
            'size-as-string' => [
                'input' => ['page' => ['size' => 'NOT A NUMBER']]
            ],
            'size-with-maximum' => [
                'input' => ['page' => ['size' => 101]]
            ],

            'size-with-maximum-set-in-request' => [
                'input' => ['page' => ['size' => 55]],
                'config' => ['max-size' => 50]
            ],

            'offset-as-string' => [
                'input' => ['page' => ['number' => 'NOT A NUMBER']]
            ],

            'size-as-string-alt-path' => [
                'input' => ['myPage' => ['mySize' => 'NOT A NUMBER']],
                'config' => ['member' => 'myPage', 'limit' => 'mySize']
            ],
            'size-with-maximum-alt-path' => [
                'input' => ['myPage' => ['mySize' => 101]],
                'config' => ['member' => 'myPage', 'limit' => 'mySize'],
            ],
            'offset-as-string-alt-path' => [
                'input' => ['myPage' => ['myOffset' => 'NOT A NUMBER']],
                'config' => ['member' => 'myPage', 'offset' => 'myOffset']
            ],

            'size-as-string-root-path' => [
                'input' => ['mySize' => 'NOT A NUMBER'],
                'config' => ['member' => '', 'limit' => 'mySize']
            ],
            'size-with-maximum-root-path' => [
                'input' => ['mySize' => 101],
                'config' => ['member' => '', 'limit' => 'mySize'],
            ],
            'offset-as-string-root-path' => [
                'input' => ['myOffset' => 'NOT A NUMBER'],
                'config' => ['member' => '', 'offset' => 'myOffset']
            ],

        ];
    }

    /**
     * @return void
     *
     * @dataProvider providesValidationFailures
     */
    public function testShouldValidateInputForPage(array $input, array $config = [])
    {
        Config::set('filter.pagination.key', $config['member'] ?? 'page');
        Config::set('filter.pagination.limit', $config['limit'] ?? 'size');
        Config::set('filter.pagination.offset', $config['offset'] ?? 'number');

        $formRequest = $this->getFormRequest();
        $formRequest->query = new InputBag($input);

        if (isset($config['max-size'])) {
            $formRequest->maxLimit = $config['max-size'];
        }

        $formRequest->expects('failedValidation')
            ->once();

        $this->expectException(ValidationException::class);

        $formRequest->getFilter();
    }

    public function testShouldIgnoreGetInstanceWithPagination()
    {
        Config::set('filter.pagination.auto', false);
        $formRequest = $this->getFormRequest();
        $formRequest->query = new InputBag(['page' => ['size' => 12]]);

        $this->assertEquals(
            (new Query()),
            $formRequest->getFilter()
        );
        Config::set('filter.pagination.auto', true);
    }

    public function providesEnablePaginationTestcases()
    {
        return [
            'config-disabled-fq-enabled' => [false, true,(new Query())->limit(12)],
            'config-enabled-fq-disabled' => [true, false, (new Query())->limit(12)],
            'both-disabled' => [false, false, (new Query())],
        ];
    }

    /**
     * @return void
     *
     * @dataProvider providesEnablePaginationTestcases
     */
    public function testShouldIgnoreSortingInFormRequest(
        bool $config, bool $enablePagination, Query $expected
    )
    {
        Config::set('filter.pagination.auto', $config);
        $formRequest = $this->getFormRequest();
        $formRequest->query = new InputBag(['page' => ['size' => 12]]);

        $formRequest->enablePagination = $enablePagination;

        $this->assertEquals(
            $expected,
            $formRequest->getFilter()
        );
    }
}
