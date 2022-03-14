<?php

namespace Henzeb\Query\Illuminate\Mixins;

use Arr;
use Closure;
use Validator;
use Henzeb\Query\Filters\Query;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Henzeb\Query\Illuminate\Factories\RulesFactory;
use Henzeb\Query\Illuminate\Factories\FilterFactory;

class FormRequestMixin
{
    protected function validateFilters(): Closure
    {
        return function () {
            /**
             * @var $this FormRequest
             */
            $rules = RulesFactory::get(
                $this->enableSorting ?? true,
                $this->allowedSorting ?? [],
                $this->enablePagination ?? true,
                $this->maxLimit ?? null,
            );

            $validator = Validator::make(
                $this->query->all(),
                $rules->getRules()
            );

            /** in case the user has some custom logic */
            if ($validator->fails()) {
                $this->failedValidation($validator);
            }

            /** in case failed Validation does not throw exceptions */
            if ($validator->fails()) {
                throw ValidationException::withMessages(
                    $validator->getMessageBag()->toArray()
                );
            }
        };
    }

    public function getFilter(): Closure
    {
        return function (): Query {
            /**
             * @var $this FormRequest
             */
            $this->validateFilters();

            $filters = FilterFactory::get(
                $this->query,
                $this->enableSorting ?? null,
                $this->defaultSort ?? null,
                $this->enablePagination ?? null,
                $this->defaultLimit ?? null,
            );

            if (method_exists($this, 'filters')) {
                $this->filters($filters);
            }

            return $filters;
        };
    }

    public function filter(): Closure
    {
        return function (string $key, mixed $default = null): mixed {
            /**
             * @var $this FormRequest
             */
            $key = join(
                '.',
                array_filter(
                    [
                        config('filter.key'),
                        $key
                    ]
                )
            );

            return Arr::get(
                $this->query->all(),
                $key,
                $default
            );
        };
    }

    public function filterArray(): Closure
    {
        return function (string $key, mixed $default = null): array {
            /**
             * @var $this FormRequest
             */
            $result = $this->filter($key, $default);

            if (is_string($result) && str_contains($result, ',')) {
                return array_filter(
                    array_map('trim',
                        explode(',', $result)
                    )
                );
            }

            if(is_array($result)) {
                return $result;
            }

            return array_filter([$result]);
        };
    }

    public function hasFilter(): Closure
    {
        return function (string $key): bool {
            /**
             * @var $this FormRequest
             */

            $key = join(
                '.',
                array_filter(
                    [
                        config('filter.key'),
                        $key
                    ]
                )
            );

            return Arr::has(
                $this->query->all(),
                $key
            );
        };
    }

}
