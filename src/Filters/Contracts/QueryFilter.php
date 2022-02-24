<?php /** @noinspection ALL */

namespace Henzeb\Query\Filters\Contracts;

use DateTime;
use henzeb\Query\Builders\Contracts\QueryBuilder;


interface QueryFilter
{
    public function is(string $key, string|float|int|bool $value): self;

    public function not(string $key, string|float|int|bool $value): self;

    public function empty(string $key): self;

    public function notEmpty(string $key): self;

    public function in(string $key, string|float|int|bool $in, string|float|int|bool ...$moreIn): self;

    public function notIn(string $key, string|float|int|bool $notIn, string|float|int|bool ...$moreNotIn): self;

    public function like(string $key, string $like): self;

    public function notLike(string $key, string $notLike): self;

    public function less(string $key, int|float|DateTime $less): self;

    public function greater(string $key, int|float|DateTime $greater): self;

    public function lessOrEqual(string $key, int|float|DateTime $lessOrEqual): self;

    public function greaterOrEqual(string $key, int|float|DateTime $greaterOrEqual): self;

    public function between(string $key, int|float $low, int|float $high): self;

    public function notBetween(string $key, int|float $low, int|float $high): self;

    public function dateBetween(string $key, DateTime $low, DateTime $high): self;

    public function dateNotBetween(string $key, DateTime $low, DateTime $high): self;

    public function filter(Filter $filter): self;

    public function limit(int $limit): self;

    public function offset(int $offset): self;

    public function asc(string $key): self;

    public function desc(string $key): self;

    public function and(): self;

    public function or(): self;

    public function nest(self $query = null): self;

    public function build(QueryBuilder $builder): void;
}
