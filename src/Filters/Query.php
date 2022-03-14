<?php

namespace Henzeb\Query\Filters;


use DateTime;
use Error;
use Henzeb\Query\Builders\Contracts\QueryBuilder;
use Henzeb\Query\Filters\Contracts\Filter;
use Henzeb\Query\Filters\Contracts\QueryFilter;

class Query implements QueryFilter
{
    private array $filters = [];

    private array $operators = [
        'and',
        'or',
    ];

    protected function addFilter(string $action, array $parameters = []): QueryFilter
    {
        if ($this->shouldNotApply($action)) {
            throw new Error('Cannot apply \'' . $action . '\' in current state');
        }

        if ($this->shouldApplyAnd($action)) {
            $this->and();
        }

        $this->filters[] = ['action' => $action, 'parameters' => $parameters];

        return $this;
    }

    private function shouldNotApply(string $action): bool
    {
        if (!in_array($action, $this->operators)) {
            return false;
        }

        return empty($this->filters);
    }

    private function isOperator(string $action): bool
    {
        return in_array(strtolower($action), $this->operators);
    }

    private function shouldApplyAnd(string $action): bool
    {
        if ($this->isOperator($action)) {
            return false;
        }

        $previousFilter = end($this->filters);

        if (!$previousFilter) {
            return false;
        }

        if ($this->isOperator($previousFilter['action'])) {
            return false;
        }

        return true;
    }

    public function is(string $key, float|bool|int|string|null $value): QueryFilter
    {
        if(null===$value) {
            return $this->empty($key);
        }

        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function not(string $key, float|bool|int|string|null $value): QueryFilter
    {
        if(null===$value) {
            return $this->notEmpty($key);
        }

        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function empty(string $key): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function notEmpty(string $key): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function in(string $key, string|bool|int|float $in, string|bool|int|float ...$moreIn): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, ['key' => $key, 'in' => [$in, ...$moreIn]]);
    }

    public function notIn(string $key, string|bool|int|float $notIn, string|bool|int|float ...$moreNotIn): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, ['key' => $key, 'notIn' => [$notIn, ...$moreNotIn]]);
    }

    public function like(string $key, string $like): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function notLike(string $key, string $notLike): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function less(string $key, float|DateTime|int $less): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function greater(string $key, float|DateTime|int $greater): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function lessOrEqual(string $key, float|DateTime|int $lessOrEqual): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function greaterOrEqual(string $key, float|DateTime|int $greaterOrEqual): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function between(string $key, float|int $low, float|int $high): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function notBetween(string $key, float|int $low, float|int $high): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function dateBetween(string $key, DateTime $low, DateTime $high): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function dateNotBetween(string $key, DateTime $low, DateTime $high): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function filter(Filter $filter): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function limit(int $limit): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function offset(int $offset): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function asc(string $key): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function desc(string $key): QueryFilter
    {
        return $this->addFilter(__FUNCTION__, get_defined_vars());
    }

    public function and(): QueryFilter
    {
        return $this->addFilter(__FUNCTION__);
    }

    public function or(): QueryFilter
    {
        return $this->addFilter(__FUNCTION__);
    }

    public function nest(QueryFilter $query = null): QueryFilter
    {
        $newQuery = $query ?? new self;

        $this->addFilter(__FUNCTION__, [$newQuery]);

        return $query ? $this : $newQuery;
    }

    public function build(QueryBuilder $builder): void
    {
        foreach ($this->filters as $filter) {

            if (empty($queryType) && $queryType = $this->isOperator($filter['action']) ? $filter['action'] : null) {
                continue;
            }

            $parameters = $this->flattenParameters($filter['parameters']);
            $action = $this->parseMethodName($builder, $filter['action'], $queryType);

            $builder->$action(...$parameters);

            $queryType = null;
        }
    }

    private function flattenParameters(array $parameters): array
    {
        $return = array();

        array_walk_recursive(
            $parameters,
            function ($a) use (&$return) {
                $return[] = $a;
            }
        );

        return array_values($return);
    }

    private function parseMethodName(QueryBuilder $builder, string $action, ?string $queryType): string
    {
        if (!$queryType) {
            return $action;
        }

        $queryTypedMethod = strtolower($queryType) . ucfirst($action);

        if (method_exists($builder, $queryTypedMethod)) {
            return $queryTypedMethod;
        }

        return $action;
    }
}
