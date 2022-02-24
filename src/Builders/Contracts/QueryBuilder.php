<?php

namespace Henzeb\Query\Builders\Contracts;

use DateTime;
use Henzeb\Query\Filters\Contracts\Filter;
use Henzeb\Query\Filters\Contracts\QueryFilter;

interface QueryBuilder
{
    public function is(string $key, string|float|int|bool $value): void;

    public function orIs(string $key, string|float|int|bool $value): void;

    public function not(string $key, string|float|int|bool $value): void;

    public function orNot(string $key, string|float|int|bool $value): void;

    public function empty(string $key): void;

    public function orEmpty(string $key): void;

    public function notEmpty(string $key): void;

    public function orNotEmpty(string $key): void;

    public function in(string $key, string|float|int|bool $in, string|float|int|bool ...$moreIn): void;

    public function orIn(string $key, string|float|int|bool $in, string|float|int|bool ...$moreIn): void;

    public function notIn(string $key, string|float|int|bool $notIn, string|float|int|bool ...$moreNotIn): void;

    public function orNotIn(string $key, string|float|int|bool $notIn, string|float|int|bool ...$moreNotIn): void;

    public function like(string $key, string $like): void;

    public function orLike(string $key, string $like): void;

    public function notLike(string $key, string $notLike): void;

    public function orNotLike(string $key, string $notLike): void;

    public function less(string $key, int|float|DateTime $less): void;

    public function orLess(string $key, int|float|DateTime $less): void;

    public function greater(string $key, int|float|DateTime $greater): void;

    public function orGreater(string $key, int|float|DateTime $greater): void;

    public function lessOrEqual(string $key, int|float|DateTime $lessOrEqual): void;

    public function orLessOrEqual(string $key, int|float|DateTime $lessOrEqual): void;

    public function greaterOrEqual(string $key, int|float|DateTime $greaterOrEqual): void;

    public function orGreaterOrEqual(string $key, int|float|DateTime $greaterOrEqual): void;

    public function between(string $key, int|float $low, int|float $high): void;

    public function orBetween(string $key, int|float $low, int|float $high): void;

    public function notBetween(string $key, int|float $low, int|float $high): void;

    public function orNotBetween(string $key, int|float $low, int|float $high): void;

    public function dateBetween(string $key, DateTime $low, DateTime $high): void;

    public function orDateBetween(string $key, DateTime $low, DateTime $high): void;

    public function dateNotBetween(string $key, DateTime $low, DateTime $high): void;

    public function orDateNotBetween(string $key, DateTime $low, DateTime $high): void;

    public function filter(Filter $filter): void;

    public function orFilter(Filter $filter): void;

    public function nest(QueryFilter $query): void;

    public function orNest(QueryFilter $query): void;

    public function limit(int $limit): void;

    public function offset(int $offset): void;

    public function asc(string $key): void;

    public function desc(string $key): void;
}
