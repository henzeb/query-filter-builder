# Query Filter Builder
[![Build Status](https://github.com/henzeb/query-filter-builder/workflows/tests/badge.svg)](https://github.com/henzeb/query-filter-builder/actions)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/henzeb/query-filter-builder.svg)](https://packagist.org/packages/henzeb/query-filter-builder)
[![Total Downloads](https://img.shields.io/packagist/dt/henzeb/query-filter-builder.svg)](https://packagist.org/packages/henzeb/query-filter-builder)
[![Test Coverage](https://api.codeclimate.com/v1/badges/03335803f33d12bc45bd/test_coverage)](https://codeclimate.com/github/henzeb/query-filter-builder/test_coverage)

Whenever you need filters on your API's endpoints, this package gives you 
a nice and simple interface that allows you to add filters without the 
need of a thousand parameters passed to your methods or writing SQL queries 
inside your controllers.

This comes with support for Laravel. If you'd  like to contribute
for other frameworks, see [Contributing](CONTRIBUTING.md).

## Installation
You can install the package via composer:

```bash
composer require henzeb/query-filter-builder
```

## Usage
See [here](doc/LARAVEL.md) for Laravel specific usage.

In your controller you may build up something like this, based on parameters
given by the user of your application.

```php
use Henzeb\Query\Filters\Query;

$filter = (new Query())
    ->nest(
        (new Query)
            ->nest(
                (new Query)
                    ->is('animal', 'cat')
                    ->less('age', 10)
            )->or()
            ->nest(
                (new Query)
                    ->is('animal', 'dog')
                    ->between('age', 5, 7)
            )
    )->in('disease', 'diabetes', 'diarrhea')
    ->limit(50)
    ->offset(50);
```
Building the query using Laravel's query builder, can be done as such:
```php
use DB;
use Henzeb\Query\Illuminate\Builders\Builder;

$query = DB::table('patients')
    ->where('vet_id', 1);
    
$filter->build(new Builder($query));
```
This would result in the following query:
```sql
select *
from `patients`
where `vet_id` = ?
  and (
          (`animal` = ? and `age` < ?)
          or 
          (`animal` = ? and `age` between ? and ?)
      )
  and `disease` in (?, ?)
limit 50 offset 50
```

Note: a query filter can never start with `or`. This prevents data-leak
situations where one could get for example all records of it's own or the 
dogs from another veterinarian:
```sql
select * from `patients` where `vet_id` = ? or `animal` = ?
```
Where one could get all records of it's own or the dogs from another 
veterinarian.

### Custom filters
You can also create your own filters in case you need something specific.

When building with Laravel, your custom filter could look like this:
```php
use Henzeb\Query\Illuminate\Filters\Contracts\Filter;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as IlluminateBuilder;

class OwnerCountFilter implements Filter
{
    public function __construct(private int $count)
    {
    }

    public function build(EloquentBuilder|IlluminateBuilder $builder): void
    {
        $builder->whereRaw(
            '(
                select count(1) 
                from `owners_patients` 
                where `owners_patients`.`patient_id` = `patients`.`id`
            ) = ?',
            [$this->count]
        );
    }
}
```
You can then call it like this:
```php
use Henzeb\Query\Filters\Query;
use App\Filters\YourCustomFilter;

$filter = (new Query)->filter(OwnerCountFilter(1));
```
Which would result in a query like this:
```sql
select *
from `patients`
where `vet_id` = ?
  and (
            (
                select count(1) 
                from `owners_patients` 
                where `owners_patients`.`patient_id` = `patients`.`id`
            ) = ?
      )
```

## Creating your own builder.
Simply implement the `Henzeb\Query\Builders\Contracts\QueryBuilder` interface.

### Custom filters
The custom filters approach might feel a bit strange. You must create your 
own custom filter interface, as the default 
`Henzeb\Query\Illuminate\Filters\Contracts\Filter` interface does not have any
methods. 

See `Henzeb\Query\Illuminate\Builders\Builder` for an example on proxying in 
order to enable your IDE's typehinting.

If you have a better approach, please let me know or submit a pull-request.

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email henzeberkheij@gmail.com instead of using the issue tracker.

## Credits

- [Henze Berkheij](https://github.com/henzeb)

## License

The GNU AGPLv. Please see [License File](LICENSE.md) for more information.
