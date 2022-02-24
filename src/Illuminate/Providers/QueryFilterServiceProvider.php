<?php

namespace Henzeb\Query\Illuminate\Providers;

use Henzeb\Query\Filters\Query;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Henzeb\Query\Illuminate\Facades\Filter;
use Illuminate\Foundation\Http\FormRequest;
use Henzeb\Query\Illuminate\Mixins\FormRequestMixin;


class QueryFilterServiceProvider extends ServiceProvider
{
    private string $configFilePath = __DIR__ . '/../Config/filter.php';

    public function register()
    {
        $this->app->bind('filter.query', fn() => new Query());

        $loader = AliasLoader::getInstance();
        $loader->alias('Filter', Filter::class);

        $this->mergeConfigFrom($this->configFilePath, 'filter');

    }

    public function boot()
    {
        $this->publishes([$this->configFilePath => config_path('filter.php')]);

        FormRequest::mixin(new FormRequestMixin());


    }
}
