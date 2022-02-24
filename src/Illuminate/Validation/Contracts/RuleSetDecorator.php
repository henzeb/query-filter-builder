<?php

namespace Henzeb\Query\Illuminate\Validation\Contracts;

interface RuleSetDecorator extends RuleSet
{
    public function __construct(RuleSet $rules);
}
