<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests\Feature;

use Andriichuk\Laracash\Casts\CurrencyCast;
use Andriichuk\Laracash\Model\HasCurrency;
use Andriichuk\Laracash\Model\HasCurrencyInterface;
use Money\Currency;
use Illuminate\Database\Eloquent\Model;

class ModelWithCurrencyCastsMethodTest extends ModelWithCurrencyTest
{
    protected function setUp(): void
    {
        if (!method_exists(Model::class, 'casts')) {
            $this->markTestSkipped('The casts() method requires Laravel 11 or later.');
        }

        parent::setUp();
    }

    public function provideModelInstance(): Model
    {
        return new class() extends Model implements HasCurrencyInterface {
            use HasCurrency;

            protected $table = 'rates';
            public $timestamps = false;
            protected $fillable = ['name', 'currency', 'native_currency'];

            protected function casts(): array
            {
                return [
                    'currency' => CurrencyCast::class,
                    'native_currency' => CurrencyCast::class,
                ];
            }

            public function getDefaultCurrencyFor(string $field): Currency
            {
                return new Currency($field === 'native_currency' ? 'UAH' : 'USD');
            }
        };
    }
}
