<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\ServiceProviders;

use Illuminate\Support\ServiceProvider;
use Andriichuk\Laracash\Config;
use Andriichuk\Laracash\LaracashService;

/**
 * Class LaracashServiceProvider
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class LaracashServiceProvider extends ServiceProvider
{
    /**
     * Boot the package.
     */
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__) . '/Config/laracash.php' => config_path('laracash.php'),
        ], 'config');

        $this->registerHelpers();
    }

    private function registerHelpers(): void
    {
        require_once dirname(__DIR__) . '/Helpers/helpers.php';
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('laracash', function () {
            return new LaracashService(new Config());
        });
        
        $this->mergeConfigFrom(  dirname(__DIR__) . '/Config/laracash.php' , 'laracash');
    }
}
