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
 * Class ModelWithManyMoneyToManyCurrencyColumnTest
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class ModelWithManyMoneyToManyCurrencyColumnTest extends BaseTestCase
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
     */
    public function testMoneyCasts(array $options, array $expected): void
    {
        $model = $this->modelInstance::create([
            'name' => 'Product Name',

            'price' => $options['price'],
            'currency' => $options['currency'],

            'book_price' => $options['book_price'],
            'native_currency' => $options['native_currency'],
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
                    'currency' => 'USD',

                    'book_price' => '25000',
                    'native_currency' => 'UAH',
                ],
                [
                    'price' => Money::USD(1000),
                    'book_price' => Money::UAH(25000),
                ]
            ],
            'from native object' => [
                [
                    'price' => Money::USD(1000),
                    'currency' => 'USD',

                    'book_price' => Money::UAH(25000),
                    'native_currency' => 'UAH',
                ],
                [
                    'price' => Money::USD(1000),
                    'book_price' => Money::UAH(25000),
                ],
            ],
            'from native object without currency' => [
                [
                    'price' => Money::USD(1000),
                    'currency' => null,

                    'book_price' => Money::UAH(25000),
                    'native_currency' => null,
                ],
                [
                    'price' => Money::USD(1000),
                    'book_price' => Money::UAH(25000),
                ],
            ]
        ];
    }

    public function testMoneyFormatting(): void
    {
        $model = $this->modelInstance::create([
            'name' => 'Product Name',
            'price' => 2099,
            'currency' => 'USD',

            'book_price' => 56967,
            'native_currency' => 'UAH'
        ]);

        $this->assertSame('$20.99', $model->price_as_currency);
        $this->assertSame('UAH 569.67', $model->book_price_as_currency);
    }

    public function provideModelInstance(): Model
    {
        return new class() extends Model implements HasMoneyWithCurrencyInterface {
            use HasMoneyWithCurrency;
            use HasCurrency;

            protected $table = 'products';

            public $timestamps = false;

            protected $fillable = ['name', 'price', 'currency', 'book_price', 'native_currency'];

            protected $casts = [
                'price' => MoneyCast::class,
                'currency' => CurrencyCast::class,

                'book_price' => MoneyCast::class,
                'native_currency' => CurrencyCast::class
            ];

            public function getCurrencyColumnFor(string $field): string
            {
                return [
                        'price' => 'currency',
                        'book_price' => 'native_currency',
                    ][$field] ?? '';
            }
        };
    }

    public function createProductsTable(): void
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->string('price');
            $table->char('currency', 3);

            $table->string('book_price');
            $table->char('native_currency', 3);
        });
    }
}
