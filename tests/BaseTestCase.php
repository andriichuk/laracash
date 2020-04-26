<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests;

use Andriichuk\Laracash\ServiceProviders\LaracashServiceProvider;
use Orchestra\Testbench\TestCase;

/**
 * Class BaseTestCase
 */
class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaracashServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testdb');
        $app['config']->set('database.connections.testdb', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
