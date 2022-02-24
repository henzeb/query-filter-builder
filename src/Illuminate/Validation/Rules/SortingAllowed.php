<?php

namespace Henzeb\Query\Illuminate\Validation\Rules;

use Illuminate\Contracts\Validation\Rule;

class SortingAllowed implements Rule
{
    private array $messages = [];

    public function __construct(private $allowed)
    {
    }

    public function passes($attribute, $value): bool
    {
        $values = explode(',', $value);

        foreach ($values as $value) {
            if (!in_array($value, $this->allowed)) {
                $this->messages[] = trans('filter.pagination.sorting_not_allowed', ['value' => $value]);
            }
        }

        return empty($this->messages);
    }

    public function message(): array
    {
        return $this->messages;
    }
}
