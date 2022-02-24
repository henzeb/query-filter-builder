<?php
return [
    /**
     * @see https://jsonapi.org/format/1.1/#fetching-filtering
     */
    'key' => 'filter', /** The key used as query-family for filtering. like filter[]= */
    /**
     * @see https://jsonapi.org/format/1.1/#fetching-pagination
     */
    'pagination' => [
        'auto' => true,         /** automatically adds pagination to your filters if set to true */
        'key' => 'page',        /** The key used as query-family for sorting. like page[]= */
        'limit' => 'size',      /** The key used for query-family-member limit, like: page[size]=1 */
        'offset' => 'number',   /** The key used for query-family-member offset, like: page[number]=1 */
        'defaults' => [
            'limit' => 50,      /** the default limit */
            'max_limit' => 100  /** The maximum allowed limit */
        ]
    ],
    /**
     * @see https://jsonapi.org/format/1.1/#fetching-sorting
     */
    'sorting' => [
        'auto'=> true,          /** automatically adds sorting to your filters if set to true */
        'key'=> 'sort',         /** the key used as query-family for sorting, like sort= */
    ]
];
