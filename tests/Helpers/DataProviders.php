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
            'is' => ['method' => 'is', 'parameters' => ['key' => 'animal', 'value' => 'cat']],
            'not' => ['method' => 'not', 'parameters' => ['key' => 'animal', 'value' => 'dog']],

            'empty' => ['method' => 'empty', 'parameters' => ['key' => 'name']],
            'notEmpty' => ['method' => 'notEmpty', 'parameters' => ['key' => 'name']],

            'in' => ['method' => 'in', 'parameters' => ['key' => 'animal', 'in' => ['dog']]],
            'in-multi' => ['method' => 'in', 'parameters' => ['key' => 'animal', 'in' => ['dog', 'cat']]],

            'notIn' => ['method' => 'notIn', 'parameters' => ['key' => 'animal', 'notIn' => ['dog']]],
            'notIn-multi' => ['method' => 'notIn', 'parameters' => ['key' => 'animal', 'notIn' => ['dog', 'cat']]],

            'like' => ['method' => 'like', 'parameters' => ['key' => 'animal', 'like' => '%dog%']],
            'notLike' => ['method' => 'notLike', 'parameters' => ['key' => 'animal', 'notLike' => '%dog%']],

            'less-int' => ['method' => 'less', 'parameters' => ['key' => 'height', 'less' => 10]],
            'less-float' => ['method' => 'less', 'parameters' => ['key' => 'height', 'less' => 10.5]],
            'less-date' => ['method' => 'less', 'parameters' => ['key' => 'age', 'less' => new DateTime()]],

            'greater-int' => ['method' => 'greater', 'parameters' => ['key' => 'height', 'greater' => 10]],
            'greater-float' => ['method' => 'greater', 'parameters' => ['key' => 'height', 'greater' => 10.5]],
            'greater-date' => ['method' => 'greater', 'parameters' => ['key' => 'age', 'greater' => new DateTime()]],

            'lessOrEqual-int' => ['method' => 'lessOrEqual', 'parameters' => ['key' => 'height', 'lessOrEqual' => 10]],
            'lessOrEqual-float' => ['method' => 'lessOrEqual', 'parameters' => ['key' => 'height', 'lessOrEqual' => 10.5]],
            'lessOrEqual-date' => ['method' => 'lessOrEqual', 'parameters' => ['key' => 'age', 'lessOrEqual' => new DateTime()]],

            'greaterOrEqual-int' => ['method' => 'greaterOrEqual', 'parameters' => ['key' => 'height', 'greaterOrEqual' => 10]],
            'greaterOrEqual-float' => ['method' => 'greaterOrEqual', 'parameters' => ['key' => 'height', 'greaterOrEqual' => 10.5]],
            'greaterOrEqual-date' => ['method' => 'greaterOrEqual', 'parameters' => ['key' => 'age', 'greaterOrEqual' => new DateTime()]],

            'between' => ['method' => 'between', 'parameters' => ['key' => 'age', 'low' => 1, 'high' => 1]],
            'between-mixed-1' => ['method' => 'between', 'parameters' => ['key' => 'age', 'low' => 1, 'high' => 1.5]],
            'between-mixed-2' => ['method' => 'between', 'parameters' => ['key' => 'age', 'low' => 1.1, 'high' => 2]],

            'notBetween' => ['method' => 'notBetween', 'parameters' => ['key' => 'age', 'low' => 1, 'high' => 1]],
            'notBetween-mixed-1' => ['method' => 'notBetween', 'parameters' => ['key' => 'age', 'low' => 1, 'high' => 1.5]],
            'notBetween-mixed-2' => ['method' => 'notBetween', 'parameters' => ['key' => 'age', 'low' => 1.1, 'high' => 2]],

            'dateBetween' => ['method' => 'dateBetween', 'parameters' => ['key' => 'age', 'low' => new DateTime(), 'high' => new DateTime()]],
            'dateNotBetween' => ['method' => 'dateNotBetween', 'parameters' => ['key' => 'age', 'low' => new DateTime(), 'high' => new DateTime()]],

            'filter-object' => ['method' => 'filter', 'parameters' => ['filter' => new OwnerFilter()]],

            'limit' => ['method' => 'limit', 'parameters' => ['limit' => 100]],
            'offset' => ['method' => 'offset', 'parameters' => ['offset' => 50]],

            'asc' => ['method' => 'asc', 'parameters' => ['key' => 'animal']],
            'desc' => ['method' => 'desc', 'parameters' => ['key' => 'animal']],
        ];
    }

    public function providesFilterWithQueryTestcases(): array
    {
        return array_merge_recursive(
            $this->providesFilterTestcases(),
            [
                'is' => ['query' => '`animal` = ?'],
                'not' => ['query' => '`animal` != ?'],
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
