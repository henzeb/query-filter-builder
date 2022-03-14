<?php

namespace Henzeb\Query\Tests\Helpers;

use DateTime;
use Henzeb\Query\Tests\Fixtures\OwnerFilter;

trait DataProviders
{
    public function providesOperators(): array
    {
        return [
            'and' => ['and'],
            'or' => ['or'],
        ];
    }

    public function providesFilterTestcases(): array
    {
        return [
            'is' => ['method' => 'is', 'parameters' => ['key' => 'animal', 'value' => 'cat'], null],
            'is-null' => ['method' => 'is', 'parameters' => ['key' => 'animal', 'value' => null], 'empty'],
            'not' => ['method' => 'not', 'parameters' => ['key' => 'animal', 'value' => 'dog'], null],
            'not-null' => ['method' => 'not', 'parameters' => ['key' => 'animal', 'value' => null], 'notEmpty'],

            'empty' => ['method' => 'empty', 'parameters' => ['key' => 'name'], null],
            'notEmpty' => ['method' => 'notEmpty', 'parameters' => ['key' => 'name'], null],

            'in' => ['method' => 'in', 'parameters' => ['key' => 'animal', 'in' => ['dog']], null],
            'in-multi' => ['method' => 'in', 'parameters' => ['key' => 'animal', 'in' => ['dog', 'cat']], null],

            'notIn' => ['method' => 'notIn', 'parameters' => ['key' => 'animal', 'notIn' => ['dog']], null],
            'notIn-multi' => ['method' => 'notIn', 'parameters' => ['key' => 'animal', 'notIn' => ['dog', 'cat']], null],

            'like' => ['method' => 'like', 'parameters' => ['key' => 'animal', 'like' => '%dog%'], null],
            'notLike' => ['method' => 'notLike', 'parameters' => ['key' => 'animal', 'notLike' => '%dog%'], null],

            'less-int' => ['method' => 'less', 'parameters' => ['key' => 'height', 'less' => 10], null],
            'less-float' => ['method' => 'less', 'parameters' => ['key' => 'height', 'less' => 10.5], null],
            'less-date' => ['method' => 'less', 'parameters' => ['key' => 'age', 'less' => new DateTime()], null],

            'greater-int' => ['method' => 'greater', 'parameters' => ['key' => 'height', 'greater' => 10], null],
            'greater-float' => ['method' => 'greater', 'parameters' => ['key' => 'height', 'greater' => 10.5], null],
            'greater-date' => ['method' => 'greater', 'parameters' => ['key' => 'age', 'greater' => new DateTime()], null],

            'lessOrEqual-int' => ['method' => 'lessOrEqual', 'parameters' => ['key' => 'height', 'lessOrEqual' => 10], null],
            'lessOrEqual-float' => ['method' => 'lessOrEqual', 'parameters' => ['key' => 'height', 'lessOrEqual' => 10.5], null],
            'lessOrEqual-date' => ['method' => 'lessOrEqual', 'parameters' => ['key' => 'age', 'lessOrEqual' => new DateTime()], null],

            'greaterOrEqual-int' => ['method' => 'greaterOrEqual', 'parameters' => ['key' => 'height', 'greaterOrEqual' => 10], null],
            'greaterOrEqual-float' => ['method' => 'greaterOrEqual', 'parameters' => ['key' => 'height', 'greaterOrEqual' => 10.5], null],
            'greaterOrEqual-date' => ['method' => 'greaterOrEqual', 'parameters' => ['key' => 'age', 'greaterOrEqual' => new DateTime()], null],

            'between' => ['method' => 'between', 'parameters' => ['key' => 'age', 'low' => 1, 'high' => 1], null],
            'between-mixed-1' => ['method' => 'between', 'parameters' => ['key' => 'age', 'low' => 1, 'high' => 1.5], null],
            'between-mixed-2' => ['method' => 'between', 'parameters' => ['key' => 'age', 'low' => 1.1, 'high' => 2], null],

            'notBetween' => ['method' => 'notBetween', 'parameters' => ['key' => 'age', 'low' => 1, 'high' => 1], null],
            'notBetween-mixed-1' => ['method' => 'notBetween', 'parameters' => ['key' => 'age', 'low' => 1, 'high' => 1.5], null],
            'notBetween-mixed-2' => ['method' => 'notBetween', 'parameters' => ['key' => 'age', 'low' => 1.1, 'high' => 2], null],

            'dateBetween' => ['method' => 'dateBetween', 'parameters' => ['key' => 'age', 'low' => new DateTime(), 'high' => new DateTime()], null],
            'dateNotBetween' => ['method' => 'dateNotBetween', 'parameters' => ['key' => 'age', 'low' => new DateTime(), 'high' => new DateTime()], null],

            'filter-object' => ['method' => 'filter', 'parameters' => ['filter' => new OwnerFilter()], null],

            'limit' => ['method' => 'limit', 'parameters' => ['limit' => 100], null],
            'offset' => ['method' => 'offset', 'parameters' => ['offset' => 50], null],

            'asc' => ['method' => 'asc', 'parameters' => ['key' => 'animal'], null],
            'desc' => ['method' => 'desc', 'parameters' => ['key' => 'animal'], null],
        ];
    }

    public function providesFilterWithQueryTestcases(): array
    {
        return array_merge_recursive(
            $this->providesFilterTestcases(),
            [
                'is' => ['query' => '`animal` = ?'],
                'is-null' => ['query' => '`animal` is null'],
                'not' => ['query' => '`animal` != ?'],
                'not-null' => ['query' => '`animal` is not null'],
                'empty' => ['query' => '`name` is null'],
                'notEmpty' => ['query' => '`name` is not null'],
                'in' => ['query' => '`animal` in (?)'],
                'in-multi' => ['query' => '`animal` in (?, ?)'],
                'notIn' => ['query' => '`animal` not in (?)'],
                'notIn-multi' => ['query' => '`animal` not in (?, ?)'],
                'like' => ['query' => '`animal` like ?'],
                'notLike' => ['query' => '`animal` not like ?'],

                'less-int' => ['query' => '`height` < ?'],
                'less-float' => ['query' => '`height` < ?'],
                'less-date' => ['query' => '`age` < ?'],

                'greater-int' => ['query' => '`height` > ?'],
                'greater-float' => ['query' => '`height` > ?'],
                'greater-date' => ['query' => '`age` > ?'],

                'lessOrEqual-int' => ['query' => '`height` <= ?'],
                'lessOrEqual-float' => ['query' => '`height` <= ?'],
                'lessOrEqual-date' => ['query' => '`age` <= ?'],

                'greaterOrEqual-int' => ['query' => '`height` >= ?'],
                'greaterOrEqual-float' => ['query' => '`height` >= ?'],
                'greaterOrEqual-date' => ['query' => '`age` >= ?'],

                'between' => ['query' => '`age` between ? and ?'],
                'between-mixed-1' => ['query' => '`age` between ? and ?'],
                'between-mixed-2' => ['query' => '`age` between ? and ?'],

                'notBetween' => ['query' => '`age` not between ? and ?'],
                'notBetween-mixed-1' => ['query' => '`age` not between ? and ?'],
                'notBetween-mixed-2' => ['query' => '`age` not between ? and ?'],

                'dateBetween' => ['query' => '`age` between ? and ?'],
                'dateNotBetween' => ['query' => '`age` not between ? and ?'],

                'filter-object' => ['query' => ['query' => '(`owner` = ?)', 'parameters' => ['Jason']]],

                'asc' => ['query' => 'order by `animal` asc', 'noParameters' => true],
                'desc' => ['query' => 'order by `animal` desc', 'noParameters' => true],

                'limit' => ['query' => 'limit 100', 'noParameters' => true],
                'offset' => ['query' => 'offset 50', 'noParameters' => true],
            ]
        );
    }

    /**
     * @param array $parameters
     * @return array
     */
    protected function flattenArray(array $parameters): array
    {
        $return = [];

        array_walk_recursive(
            $parameters,
            function ($a) use (&$return) {
                $return[] = $a;
            }
        );

        return $return;
    }
}
