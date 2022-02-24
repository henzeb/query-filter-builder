<?php

namespace Henzeb\Query\Illuminate\Validation\Decorators;

use Henzeb\Query\Illuminate\Validation\Contracts\RuleSet;
use Henzeb\Query\Illuminate\Validation\Contracts\RuleSetDecorator;
use function config;

class PaginationValidation implements RuleSetDecorator
{

    public function __construct(
        private RuleSet $rules,
        private ?int $maxSize = null
    )
    {
    }

    public function getRules(): array
    {
        $key = config('filter.pagination.key', 'page');
        $limit = join('.', array_filter([$key, config('filter.pagination.limit', 'size')]));
        $offset = join('.', array_filter([$key, config('filter.pagination.offset', 'number')]));

        $rules = [
            $limit => 'integer|max:' . ($this->maxSize ?? config('filter.pagination.defaults.max_limit')),
            $offset => 'integer',
        ];

        return $this->rules->getRules() + $rules;
    }
}
