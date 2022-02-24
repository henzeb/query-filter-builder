<?php

namespace Henzeb\Query\Illuminate\Validation\Decorators;

use Henzeb\Query\Illuminate\Validation\Contracts\RuleSet;
use Henzeb\Query\Illuminate\Validation\Rules\SortingAllowed;
use Henzeb\Query\Illuminate\Validation\Contracts\RuleSetDecorator;
use function config;

class SortingValidation implements RuleSetDecorator
{

    public function __construct(
        private RuleSet $rules,
        private array $allowedSorting = []
    )
    {
    }

    public function getRules(): array
    {
        return $this->rules->getRules() + [
                config('filter.sorting.key') => [
                    'bail',
                    'regex:/^-{0,1}[0-9a-zA-Z_]+(,-{0,1}[0-9a-zA-Z_]+)*$/',
                    new SortingAllowed($this->allowedSorting)
                ]
            ];
    }
}
