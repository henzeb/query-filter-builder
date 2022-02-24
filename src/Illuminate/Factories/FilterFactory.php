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
        bool $enableSorting,
        bool $enablePagination,
        int      $defaultLimit = null
    ): Query
    {
        $query = new Query;

        $filterFactory = new self($query, $inputBag->all());

        if ($enablePagination || config('filter.pagination.auto')) {
            $filterFactory->parsePagination($defaultLimit);
        }

        if ($enableSorting || config('filter.sorting.auto')) {
            $filterFactory->parseSorting();
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

    private function parseSorting(): void
    {
        $key = config('filter.sorting.key', 'sort');

        if (Arr::has($this->parameters, $key)) {
            $sorts = explode(',', $this->parameters[$key]);

            foreach ($sorts as $sort) {

                if (str_starts_with($sort, '-')) {
                    $this->query->desc(ltrim($sort, '-'));
                    continue;
                }
                $this->query->asc($sort);
            }

        }
    }

}
