<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests\Feature;

use Andriichuk\Laracash\Casts\CurrencyCast;
use Andriichuk\Laracash\Casts\MoneyCast;
use Andriichuk\Laracash\Model\HasCurrency;
use Andriichuk\Laracash\Model\HasCurrencyInterface;
use Andriichuk\Laracash\Model\HasMoneyWithCurrency;
use Andriichuk\Laracash\Model\HasMoneyWithCurrencyInterface;
use Andriichuk\Laracash\Tests\BaseTestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Money\Money;

/**
 * Class ModelWithOneMoneyToOneCurrencyColumnTest
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class ModelWithOneMoneyToOneCurrencyColumnTest extends BaseTestCase
{
    /**
     * @var Model
     */
    private $modelInstance;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createProductsTable();

        $this->modelInstance = $this->provideModelInstance();
    }

    /**
     * @dataProvider moneyCasesProvider
     *
     * @param mixed $price
     */
    public function testMoneyCasts(array $options, Money $expected): void
    {
        $model = $this->modelInstance::create([
            'name' => 'Product Name',
            'price' => $options['price'],
            'currency' => $options['currency'],
        ]);

        $this->assertEquals($expected, $model->price);
    }

    public function moneyCasesProvider(): array
    {
        return [
            'from scalar' => [
                [
                    'price' => 1000,
                    'currency' => 'USD',
                ],
                Money::USD(1000),
            ],
            'from native Money object' => [
                [
                    'price' => Money::USD(1000),
                    'currency' => 'UAH',
                ],
                Money::USD(1000),
            ]
        ];
    }

    public function testMoneyFormatting(): void
    {
        $model = $this->modelInstance::create([
            'name' => 'Product Name',
            'price' => 1099,
        ]);

        $this->assertEquals(Money::USD(1099), $model->price);
        $this->assertSame('$10.99', $model->price_as_currency);
    }

    public function provideModelInstance(): Model
    {
        return new class() extends Model implements HasMoneyWithCurrencyInterface, HasCurrencyInterface {
            use HasMoneyWithCurrency;
            use HasCurrency;

            protected $table = 'products';

            public $timestamps = false;

            protected $fillable = ['name', 'price', 'currency'];

            protected $casts = [
                'price' => MoneyCast::class,
                'currency' => CurrencyCast::class
            ];
        };
    }

    public function createProductsTable(): void
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('price');
            $table->char('currency', 3);
        });
    }
}
