<?php
namespace Henzeb\Query\Illuminate\Filters\Contracts;

use Henzeb\Query\Filters\Contracts\Filter as BaseFilter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as IlluminateBuilder;

interface Filter extends BaseFilter
{
    public function build(IlluminateBuilder|EloquentBuilder $builder): void;
}
