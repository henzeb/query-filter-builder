# Laravel

## Usage
you can use this package easily with laravel FormRequests. Out of the box 
the package will parse and validate query-parameters for sorting and 
paginating based on the 
[JSON:API specification](https://jsonapi.org/format/1.1/). 

### configuration
The configuration file can be published using `php artisan vendor:publish`
```php
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
```
In your FormRequest, you also have some control.
```php
class YourFormRequest extends FormRequest {
    protected bool $enablePagination = false; /** disable or enable pagination */
    
    protected bool $enableSorting = false; /** disable or enable sorting */
    protected array $allowedSorting = []; /** fields that are allowed for sorting */
    protected array|string $defaultSort = []; /** default sort, can be a string or an array */
    protected int $defaultLimit = 10; /** The default limit */
    protected int $maxLimit = 50; /** The maximum allowed limit */    
}
```
By default, the package does not allow any fields for sorting.
You have to add the fields you want to allow into the `$allowedSorting` property. 
The format is the same as specified in the
[JSON:API specification: Sorting](https://jsonapi.org/format/1.1/#fetching-sorting), 
except it's listed as an array:

```php
protected array $allowSorting = [
    'animal', /** ascending */
    '-animal' /** descending */
];
```

`defaultSort` works the same way, except it is allowed to be a string.

### Add your filters
To add your own filters, simply add the following method in your FormRequest. 
You can use `filter`, `filterArray` and `hasFilter` methods as shortcut to the filter 
query parameter family as specified in [JSON:API specification: Filtering](https://jsonapi.org/format/1.1/#fetching-filtering)

The `filterArray` method makes sure the result is always returned as an array, so you can pass a comma separated 
string in your `filter['animals]` as per recommendation or an array.
```php
private function filters(Query $query): void
{
    if($this->hasFilter('animal')) {
        $query->is('animal_field', $this->filter('animal'));
    }
    
    if($this->hasFilter('animals')) {
        $query->in('animal_field', $this->filterArray('animals'));
    }
    
}
```
Note: You need to add your own validations in your rules method.




