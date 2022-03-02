<?php

namespace Henzeb\Query\Illuminate\Builders;

use DB;
use DateTime;
use TypeError;
use Henzeb\Query\Filters\Contracts\Filter;
use Henzeb\Query\Filters\Contracts\QueryFilter;
use Henzeb\Query\Builders\Contracts\QueryBuilder;
use Illuminate\Database\Query\Builder as IlluminateBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Henzeb\Query\Illuminate\Filters\Contracts\Filter as IlluminateFilter;

class Builder implements QueryBuilder
{
    public function __construct(private IlluminateBuilder|EloquentBuilder $builder)
    {
    }

    protected function getBuilder(): IlluminateBuilder|EloquentBuilder
    {
        return $this->builder;
    }

    public function is(string $key, float|bool|int|string $value): void
    {
        $this->getBuilder()->where($key, $value);
    }

    public function orIs(string $key, float|bool|int|string $value): void
    {
        $this->getBuilder()->orWhere($key, $value);
    }

    public function not(string $key, float|bool|int|string $value): void
    {
        $this->getBuilder()->where($key, '!=', $value);
    }

    public function orNot(string $key, float|bool|int|string $value): void
    {
        $this->getBuilder()->orWhere($key, '!=', $value);
    }

    public function empty(string $key): void
    {
        $this->builder->whereNull($key);
    }

    public function orEmpty(string $key): void
    {
        $this->builder->orWhereNull($key);
    }

    public function notEmpty(string $key): void
    {
        $this->builder->whereNotNull($key);
    }

    public function orNotEmpty(string $key): void
    {
        $this->builder->orWhereNotNull($key);
    }

    public function in(string $key, string|float|int|bool $in, string|float|int|bool ...$moreIn): void
    {
        $this->getBuilder()->whereIn($key, [$in, ...$moreIn]);
    }

    public function orIn(string $key, string|float|int|bool $in, string|float|int|bool ...$moreIn): void
    {
        $this->getBuilder()->orWhereIn($key, [$in, ...$moreIn]);
    }

    public function notIn(string $key, string|float|int|bool $notIn, string|float|int|bool ...$moreNotIn): void
    {
        $this->getBuilder()->whereNotIn($key, [$notIn, ...$moreNotIn]);
    }

    public function orNotIn(string $key, string|float|int|bool $notIn, string|float|int|bool ...$moreNotIn): void
    {
        $this->getBuilder()->orWhereNotIn($key, [$notIn, ...$moreNotIn]);
    }

    public function like(string $key, string $like): void
    {
        $this->getBuilder()->where($key, 'like', $like);
    }

    public function orLike(string $key, string $like): void
    {
        $this->getBuilder()->orWhere($key, 'like', $like);
    }

    public function notLike(string $key, string $notLike): void
    {
        $this->getBuilder()->where($key, 'not like', $notLike);
    }

    public function orNotLike(string $key, string $notLike): void
    {
        $this->getBuilder()->orWhere($key, 'not like', $notLike);
    }

    public function less(string $key, float|DateTime|int $less): void
    {
        $this->getBuilder()->where($key, '<', $less);
    }

    public function orLess(string $key, float|DateTime|int $less): void
    {
        $this->getBuilder()->orWhere($key, '<', $less);
    }

    public function greater(string $key, float|DateTime|int $greater): void
    {
        $this->getBuilder()->where($key, '>', $greater);
    }

    public function orGreater(string $key, float|DateTime|int $greater): void
    {
        $this->getBuilder()->orWhere($key, '>', $greater);
    }

    public function lessOrEqual(string $key, float|DateTime|int $lessOrEqual): void
    {
        $this->getBuilder()->where($key, '<=', $lessOrEqual);
    }

    public function orLessOrEqual(string $key, float|DateTime|int $lessOrEqual): void
    {
        $this->getBuilder()->orWhere($key, '<=', $lessOrEqual);
    }

    public function greaterOrEqual(string $key, float|DateTime|int $greaterOrEqual): void
    {
        $this->getBuilder()->where($key, '>=', $greaterOrEqual);
    }

    public function orGreaterOrEqual(string $key, float|DateTime|int $greaterOrEqual): void
    {
        $this->getBuilder()->orWhere($key, '>=', $greaterOrEqual);
    }

    public function between(string $key, float|int $low, float|int $high): void
    {
        $this->getBuilder()->whereBetween($key, [$low, $high]);
    }

    public function orBetween(string $key, float|int $low, float|int $high): void
    {
        $this->getBuilder()->orWhereBetween($key, [$low, $high]);
    }

    public function notBetween(string $key, float|int $low, float|int $high): void
    {
        $this->getBuilder()->whereNotBetween($key, [$low, $high]);
    }

    public function orNotBetween(string $key, float|int $low, float|int $high): void
    {
        $this->getBuilder()->orWhereNotBetween($key, [$low, $high]);
    }

    public function dateBetween(string $key, DateTime $low, DateTime $high): void
    {
        $this->getBuilder()->whereBetween($key, [$low, $high]);
    }

    public function orDateBetween(string $key, DateTime $low, DateTime $high): void
    {
        $this->getBuilder()->orWhereBetween($key, [$low, $high]);
    }

    public function dateNotBetween(string $key, DateTime $low, DateTime $high): void
    {
        $this->getBuilder()->whereNotBetween($key, [$low, $high]);
    }

    public function orDateNotBetween(string $key, DateTime $low, DateTime $high): void
    {
        $this->getBuilder()->orWhereNotBetween($key, [$low, $high]);
    }

    public function limit(int $limit): void
    {
        $this->getBuilder()->limit($limit);
    }

    public function offset(int $offset): void
    {
        $this->getBuilder()->offset($offset);
    }

    public function asc(string $key): void
    {
        $this->getBuilder()->orderBy($key);
    }

    public function desc(string $key): void
    {
        $this->getBuilder()->orderByDesc($key);
    }

    public function nest(QueryFilter $query): void
    {
        $this->getBuilder()->where(
            $this->getNestingClosure($query)
        );
    }

    public function orNest(QueryFilter $query): void
    {
        $this->getBuilder()->orWhere(
            $this->getNestingClosure($query)
        );
    }

    public function filter(Filter $filter): void
    {
        $this->globalFilters($filter);

        $this->getBuilder()->where(
            $this->getNestingClosure(
                $this->filterProxy($filter)
            )
        );
    }

    public function orFilter(Filter $filter): void
    {
        $this->globalFilters($filter);

        $this->getBuilder()->orWhere(
            $this->getNestingClosure(
                $this->filterProxy($filter)
            )
        );
    }

    private function getNestingClosure(QueryFilter|IlluminateFilter $query): callable
    {
        return function (IlluminateBuilder|EloquentBuilder $queryBuilder) use ($query) {
            $query->build($query instanceof QueryFilter ? new self($queryBuilder) : $queryBuilder);
            return $queryBuilder;
        };
    }

    public function filterProxy(Filter $filter): IlluminateFilter
    {
        if ($filter instanceof IlluminateFilter) {
            return $filter;
        }

        throw new TypeError(
            '`' . self::class . ' expects `' . IlluminateFilter::class . '` but got `' . $filter::class . '`'
        );
    }

    private function globalFilters(Filter $filter)
    {
        $query = DB::query();
        $this->filterProxy($filter)->build($query);

        if ($query->joins) {

            $this->getBuilder()->joins = array_merge(
                $this->getBuilder()->joins ?? [],
                $query->joins ?? []
            );

            $this->getBuilder()->bindings['join'] = array_merge(
                $this->getBuilder()->bindings['join'],
                $query->bindings['join']
            );
        }
    }
}
