<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests\Feature;

use Andriichuk\Laracash\Casts\CurrencyCast;
use Andriichuk\Laracash\Casts\MoneyCast;
use Andriichuk\Laracash\Model\HasMoney;
use Andriichuk\Laracash\Tests\BaseTestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use InvalidArgumentException;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\Attributes\DataProvider;

class ConfigurableCastsTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['db']->connection()->getSchemaBuilder()->create('configured_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('price')->nullable();
            $table->string('book_price')->nullable();
            $table->string('fixed_price')->nullable();
            $table->char('currency', 3)->nullable();
            $table->char('book_currency', 3)->nullable();
        });
    }

    /** @dataProvider castStyles */
    #[DataProvider('castStyles')]
    public function testColumnAndFixedCurrencyCastsPersistWithoutMappingInterfaces(bool $useMethod): void
    {
        $model = $this->model($useMethod);
        $model->forceFill([
            'currency' => 'UAH',
            'price' => '1234',
            'book_price' => Money::GBP(555),
            'fixed_price' => 999,
        ])->save();
        $stored = $model->fresh();

        $this->assertEquals(Money::UAH(1234), $stored->price);
        $this->assertEquals(Money::GBP(555), $stored->book_price);
        $this->assertEquals(new Currency('GBP'), $stored->book_currency);
        $this->assertEquals(Money::EUR(999), $stored->fixed_price);
        $this->assertSame('12.34', $stored->price_as_decimal);
        $this->assertSame('€9.99', $stored->fixed_price_as_currency);

        $this->assertEquals(new Currency('UAH'), $stored->currency);
        $stored->price = Money::USD(2599);
        $stored->save();

        $this->assertEquals(Money::USD(2599), $stored->fresh()->price);
        $this->assertSame('$25.99', $stored->fresh()->price_as_currency);
    }

    /** @dataProvider castStyles */
    #[DataProvider('castStyles')]
    public function testNullableAndDefaultCurrencyBehavior(bool $useMethod): void
    {
        $model = $this->model($useMethod);
        $model->price = 0;
        $model->save();
        $stored = $model->fresh();

        $this->assertEquals(Money::USD(0), $stored->price);
        $this->assertSame('0.00', $stored->price_as_decimal);
        $this->assertNull($stored->fixed_price);
        $this->assertNull($stored->fixed_price_as_currency);
        $this->assertNull($stored->book_price_as_decimal);
    }

    /** @dataProvider castStyles */
    #[DataProvider('castStyles')]
    public function testFixedCurrencyRejectsAMismatchedMoneyValue(bool $useMethod): void
    {
        $model = $this->model($useMethod);
        $this->expectException(InvalidArgumentException::class);
        $model->fixed_price = Money::USD(100);
    }

    public function testAnExplicitColumnOverridesAnExistingMappingInterface(): void
    {
        $model = new class extends Model implements \Andriichuk\Laracash\Model\HasMoneyWithCurrencyInterface {
            public function getCurrencyColumnFor(string $field): string
            {
                return 'legacy_currency';
            }
        };
        $model->setRawAttributes(['currency' => 'GBP', 'legacy_currency' => 'USD']);

        $money = (new MoneyCast('currency'))->get($model, 'price', '100', $model->getAttributes());

        $this->assertEquals(Money::GBP(100), $money);
    }

    public function testFixedCurrencyAndColumnCannotBeConfiguredTogether(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MoneyCast('currency', 'USD');
    }

    public function testMoneyAndCurrencyCannotUseTheSameColumn(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new MoneyCast('price'))->set(new class extends Model {}, 'price', 100, []);
    }

    /** @dataProvider invalidColumns */
    #[DataProvider('invalidColumns')]
    public function testInvalidCurrencyColumnConfigurationIsRejected(string $column): void
    {
        $this->expectException(InvalidArgumentException::class);
        MoneyCast::usingCurrencyColumn($column);
    }

    /** @dataProvider invalidColumns */
    #[DataProvider('invalidColumns')]
    public function testInvalidFixedCurrencyConfigurationIsRejected(string $currency): void
    {
        $this->expectException(InvalidArgumentException::class);
        MoneyCast::usingCurrency($currency);
    }

    public static function invalidColumns(): array
    {
        return [[''], [' currency'], ['currency '], ['currency,USD'], ['currency:USD']];
    }

    public static function castStyles(): array
    {
        return ['property' => [false], 'method' => [true]];
    }

    private function model(bool $useMethod): Model
    {
        if ($useMethod) {
            if (!method_exists(Model::class, 'casts')) {
                $this->markTestSkipped('The casts() method requires Laravel 11 or later.');
            }

            return new class extends Model {
                use HasMoney;

                protected $table = 'configured_prices';
                public $timestamps = false;

                protected function casts(): array
                {
                    return [
                        'price' => MoneyCast::usingCurrencyColumn('currency'),
                        'currency' => CurrencyCast::class,
                        'book_price' => MoneyCast::usingCurrencyColumn('book_currency'),
                        'book_currency' => CurrencyCast::class,
                        'fixed_price' => MoneyCast::usingCurrency('EUR'),
                    ];
                }
            };
        }

        return new class extends Model {
            use HasMoney;

            protected $table = 'configured_prices';
            public $timestamps = false;

            protected $casts = [
                'price' => MoneyCast::class . ':currency',
                'currency' => CurrencyCast::class,
                'book_price' => MoneyCast::class . ':book_currency',
                'book_currency' => CurrencyCast::class,
                'fixed_price' => MoneyCast::class . ':,EUR',
            ];
        };
    }
}
