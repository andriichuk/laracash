<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests\Feature;

use Andriichuk\Laracash\Casts\CurrencyCast;
use Andriichuk\Laracash\Casts\MoneyCast;
use Andriichuk\Laracash\Model\HasCurrency;
use Andriichuk\Laracash\Model\HasMoneyWithCurrency;
use Andriichuk\Laracash\Model\HasMoneyWithCurrencyInterface;
use Andriichuk\Laracash\Tests\BaseTestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Money\Money;

/**
 * Class ModelWithManyMoneyToOneCurrencyColumnTest
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class ModelWithManyMoneyToOneCurrencyColumnTest extends BaseTestCase
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
    public function testMoneyCasts(array $options, array $expected): void
    {
        $model = $this->modelInstance::create([
            'name' => 'Product Name',
            'price' => $options['price'],
            'book_price' => $options['book_price'],
            'currency' => $options['currency'],
        ]);

        $this->assertEquals($expected['price'], $model->price);
        $this->assertEquals($expected['book_price'], $model->book_price);
    }

    public function moneyCasesProvider(): array
    {
        return [
            'from scalar' => [
                [
                    'price' => 1000,
                    'book_price' => '2000',
                    'currency' => 'USD',
                ],
                [
                    'price' => Money::USD(1000),
                    'book_price' => Money::USD(2000),
                ]
            ],
            'from native Money object' => [
                [
                    'price' => Money::UAH(10000),
                    'book_price' => Money::UAH(10000),
                    'currency' => 'UAH',
                ],
                [
                    'price' => Money::UAH(10000),
                    'book_price' => Money::UAH(10000),
                ],
            ]
        ];
    }

    public function testMoneyFormatting(): void
    {
        $model = $this->modelInstance::create([
            'name' => 'Product Name',
            'price' => 1099,
            'book_price' => 100099,
            'currency' => 'USD',
        ]);

        $this->assertSame('$10.99', $model->price_as_currency);
        $this->assertSame('$1,000.99', $model->book_price_as_currency);
    }

    public function provideModelInstance(): Model
    {
        return new class() extends Model implements HasMoneyWithCurrencyInterface {
            use HasMoneyWithCurrency;
            use HasCurrency;

            protected $table = 'products';

            public $timestamps = false;

            protected $fillable = ['name', 'price', 'book_price', 'currency_code'];

            protected $casts = [
                'price' => MoneyCast::class,
                'book_price' => MoneyCast::class,
                'currency_code' => CurrencyCast::class,
            ];

            public function getCurrencyColumnFor(string $field): string
            {
                return 'currency_code';
            }
        };
    }

    public function createProductsTable(): void
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('price');
            $table->string('book_price');
            $table->char('currency_code', 3);
        });
    }
}
