<?php

namespace Henzeb\Query\Tests\Unit\Illuminate\Mixins\Concerns;

use Mockery;
use Mockery\MockInterface;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\InputBag;
use Henzeb\Query\Illuminate\Providers\QueryFilterServiceProvider;

trait Mocks
{
    protected function getPackageProviders($app)
    {
        return [QueryFilterServiceProvider::class];
    }

    public function getFormRequest(): FormRequest|MockInterface
    {
        $partial = Mockery::mock(FormRequest::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $partial->query = new InputBag();
        $partial->request = new InputBag();

        $partial->server = new InputBag();
        $partial->files = new InputBag();
        $partial->headers = new InputBag();

        return $partial;
    }
}
