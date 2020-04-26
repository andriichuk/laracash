<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests\Feature;

use Andriichuk\Laracash\Casts\CurrencyCast;
use Andriichuk\Laracash\Model\HasCurrency;
use Andriichuk\Laracash\Model\HasCurrencyInterface;
use Andriichuk\Laracash\Tests\BaseTestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Money\Currency;

/**
 * Class ModelWithCurrencyTest
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class ModelWithCurrencyTest extends BaseTestCase
{
    /**
     * @var Model
     */
    private $modelInstance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createCurrenciesTable();

        $this->modelInstance = $this->provideModelInstance();
    }

    public function testCurrencyCasts(): void
    {
        $model = $this->modelInstance::create([
            'name' => 'US Dollar',
            'currency' => 'USD',
            'native_currency' => 'EUR',
        ]);

        $this->assertEquals(new Currency('USD'), $model->currency);
        $this->assertEquals(new Currency('EUR'), $model->native_currency);
    }

    public function testDefaultCurrencyCasts(): void
    {
        $model = $this->modelInstance::create([
            'name' => 'Test rate',
        ]);

        $this->assertEquals(new Currency('USD'), $model->currency);
        $this->assertEquals(new Currency('UAH'), $model->native_currency);
    }

    public function provideModelInstance(): Model
    {
        return new class() extends Model implements HasCurrencyInterface {
            use HasCurrency;

            protected $table = 'rates';

            public $timestamps = false;

            protected $fillable = ['name', 'currency', 'native_currency'];

            protected $casts = [
                'currency' => CurrencyCast::class,
                'native_currency' => CurrencyCast::class,
            ];

            public function getDefaultCurrencyFor(string $field): Currency
            {
                return new Currency(
                    $field === 'native_currency' ? 'UAH' : 'USD'
                );
            }
        };
    }

    private function createCurrenciesTable()
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('rates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->char('currency', 3);
            $table->char('native_currency', 3);
        });
    }
}
