<?php

namespace Henzeb\Query\Illuminate\Validation\Decorators;

use Henzeb\Query\Illuminate\Validation\Contracts\RuleSet;

class Rules implements RuleSet
{
    public function getRules(): array
    {
        return [];
    }
}
