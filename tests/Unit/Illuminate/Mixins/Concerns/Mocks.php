<?php

namespace Henzeb\Query\Tests\Unit\Illuminate\Mixins\Concerns;

use Mockery;
use Mockery\MockInterface;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\InputBag;
use Henzeb\Query\Illuminate\Providers\QueryFilterServiceProvider;
use Symfony\Component\HttpFoundation\ServerBag;

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

        $partial->server = new ServerBag();
        $partial->files = new FileBag();
        $partial->headers = new HeaderBag();

        return $partial;
    }
}
