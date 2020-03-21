<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Andriichuk\Laracash\Config;
use Andriichuk\Laracash\LaracashCompose;

/**
 * Class LaracashServiceProvider
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class LaracashServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the package.
     */
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__) . '/Config/laracash.php' => config_path('laracash.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(LaracashCompose::class, function () {
            return new LaracashCompose(new Config());
        });

        $this->app->alias(LaracashCompose::class, 'laracash');
    }

    /**
     * Facades Binding
     */
    private function facadeBindings()
    {
        $this->app->singleton(LaracashCompose::class, function () {
            return new LaracashCompose(new Config());
        });

        $this->app->alias(LaracashCompose::class, 'laracash');
    }
}
