<?php

namespace Henzeb\Query\Illuminate\Factories;

use Henzeb\Query\Illuminate\Validation\Decorators\Rules;
use Henzeb\Query\Illuminate\Validation\Contracts\RuleSet;
use Henzeb\Query\Illuminate\Validation\Decorators\SortingValidation;
use Henzeb\Query\Illuminate\Validation\Decorators\PaginationValidation;

class RulesFactory
{
    private function __construct()
    {
    }

    public static function get(
        bool $enableSorting,
        array $allowedSorting,
        bool $enablePagination,
        ?int $maxLimit,
    ): RuleSet
    {
        $factory = new self();
        $rules = new Rules();

        $rules = $factory->decoratePaginationValidation($rules, $maxLimit, $enablePagination);

        return $factory->decorateSortingValidation($rules, $enableSorting, $allowedSorting);
    }

    private function decoratePaginationValidation(
        RuleSet $rules,
        ?int $maxLimit,
        bool $enablePagination,
    ): RuleSet
    {
        if ($enablePagination && config('filter.pagination.auto')) {

            return new PaginationValidation(
                $rules,
                $maxLimit
            );
        }

        return $rules;
    }

    public function decorateSortingValidation(RuleSet $rules, bool $enableSorting, array $allowedSorting): RuleSet
    {
        if ($enableSorting && config('filter.sorting.auto')) {

            return new SortingValidation(
                $rules,
                $allowedSorting
            );
        }

        return $rules;
    }
}
