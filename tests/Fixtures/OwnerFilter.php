<?php

namespace Henzeb\Query\Tests\Fixtures;

use Henzeb\Query\Illuminate\Filters\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as IlluminateBuilder;

class OwnerFilter implements Filter
{
    public function build(EloquentBuilder|IlluminateBuilder $builder): void
    {
        $builder->where('owner', 'Jason');
    }
}
