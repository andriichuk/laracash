<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests\Feature;

use Andriichuk\Laracash\Config;
use Andriichuk\Laracash\Facades\Laracash;
use Andriichuk\Laracash\MoneyManagerInterface;
use Andriichuk\Laracash\ServiceProviders\LaracashServiceProvider;
use Andriichuk\Laracash\Tests\BaseTestCase;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Money\Money;

class ConfigurationTest extends BaseTestCase
{
    public function testRuntimeConfigurationChangesReachAnAlreadyResolvedManager(): void
    {
        $manager = $this->app->make(MoneyManagerInterface::class);
        $this->assertEquals(Money::USD(100), $manager->factory()->make(100));

        config(['laracash.currency' => 'EUR', 'laracash.locale' => 'de_DE']);

        $this->assertEquals(Money::EUR(100), $manager->factory()->make(100));
        $this->assertSame('12,34', $manager->formatter()->formatAsIntlDecimal(Money::EUR(1234)));
        $this->assertEquals(Money::EUR(100), Laracash::parser()->parseDecimal('1.00'));
    }

    public function testInjectedConfigurationUsesTheEffectiveLaravelConfiguration(): void
    {
        $repository = new Repository(['laracash' => ['currency' => 'GBP', 'locale' => 'en_GB']]);
        $config = new Config($repository);

        $this->assertSame('GBP', $config->get('currency'));
        $repository->set('laracash.currency', 'EUR');
        $this->assertSame('EUR', $config->get('currency'));
        $this->assertNull($config->get('missing'));
    }

    public function testDefaultsAreAvailableDuringProviderRegistration(): void
    {
        $this->app['config']->set('laracash', ['currency' => 'GBP']);
        (new LaracashServiceProvider($this->app))->register();

        $this->assertSame('GBP', config('laracash.currency'));
        $this->assertSame('en_US', config('laracash.locale'));
    }

    public function testStandaloneConfigurationFallsBackToPackageDefaults(): void
    {
        $application = Container::getInstance();

        try {
            Container::setInstance(new Container());
            $config = new Config();

            $this->assertSame('USD', $config->get('currency'));
            $this->assertSame('en_US', $config->get('locale'));
        } finally {
            Container::setInstance($application);
        }
    }
}
