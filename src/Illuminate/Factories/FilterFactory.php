<?php

namespace Henzeb\Query\Illuminate\Factories;

use Arr;
use Henzeb\Query\Filters\Query;
use Symfony\Component\HttpFoundation\InputBag;

class FilterFactory
{
    private function __construct(private Query $query, private array $parameters)
    {
    }

    public static function get(
        InputBag $inputBag,
        ?bool    $enableSorting,
        array|string|null $defaultSorting,
        ?bool    $enablePagination,
        ?int      $defaultLimit,
    ): Query
    {
        $query = new Query;

        $filterFactory = new self($query, $inputBag->all());

        if ((null === $enablePagination && config('filter.pagination.auto')) || $enablePagination) {
            $filterFactory->parsePagination($defaultLimit);
        }

        if ((null === $enableSorting && config('filter.sorting.auto')) || $enableSorting) {
            $filterFactory->parseSorting($defaultSorting);
        }

        return $query;
    }

    private function parsePagination(int $defaultLimit = null): void
    {
        $key = config('filter.pagination.key');

        $limit = $this->createConfigKey([$key, config('filter.pagination.limit')]);
        $defaultLimit = $defaultLimit ?? config('filter.pagination.defaults.limit');

        $offset = $this->createConfigKey([$key, config('filter.pagination.offset')]);

        if (Arr::has($this->parameters, $limit) || $defaultLimit) {
            $this->query->limit(Arr::get($this->parameters, $limit, $defaultLimit));
        }

        if (Arr::has($this->parameters, $offset)) {
            $this->query->offset(Arr::get($this->parameters, $offset));
        }
    }

    private function createConfigKey(array $paths): string
    {
        return join('.', array_filter($paths));
    }

    private function parseSorting(array|string|null $defaultSorting): void
    {
        $key = config('filter.sorting.key', 'sort');

        $sorting = $this->stringToArray($defaultSorting);

        if (Arr::has($this->parameters, $key)) {
            $sorting = $this->stringToArray($this->parameters[$key]);
        }

        if ($sorting) {

            foreach ($sorting as $sort) {

                if (str_starts_with($sort, '-')) {
                    $this->query->desc(ltrim($sort, '-'));
                    continue;
                }
                $this->query->asc($sort);
            }

        }
    }

    private function stringToArray(string|array|null $value): ?array
    {
        if(is_array($value)) {
            return $value;
        }

        if(null===$value) {
            return null;
        }

        return explode(',', $value);
    }

}
