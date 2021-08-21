<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\ServiceProviders;

use Andriichuk\Laracash\MoneyManagerInterface;
use Illuminate\Support\ServiceProvider;
use Andriichuk\Laracash\Config;
use Andriichuk\Laracash\LaracashMoneyManager;

/**
 * Class LaracashServiceProvider
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class LaracashServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->singleton(MoneyManagerInterface::class, function () {
            return new LaracashMoneyManager(new Config());
        });
    }

    /**
     * Boot the package.
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__) . '/Config/laracash.php' , 'laracash');

        $this->publishes([
            dirname(__DIR__) . '/Config/laracash.php' => config_path('laracash.php'),
        ], 'config');

        $this->registerHelpers();
    }

    private function registerHelpers(): void
    {
        require_once dirname(__DIR__) . '/Helpers/helpers.php';
    }
}
