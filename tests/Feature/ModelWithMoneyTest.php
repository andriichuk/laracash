<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests\Feature;

use Andriichuk\Laracash\Casts\MoneyCast;
use Andriichuk\Laracash\Model\HasMoney;
use Andriichuk\Laracash\Tests\BaseTestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Money\Money;

/**
 * Class ModelWithMoneyTest
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class ModelWithMoneyTest extends BaseTestCase
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
    public function testMoneyCasts($price, string $expected): void
    {
        $model = $this->modelInstance::create([
            'name' => 'Product Name',
            'price' => $price,
        ]);

        $this->assertSame($expected, $model->price->getAmount());
    }

    public function moneyCasesProvider(): array
    {
        return [
            'from string' => [
                '1000',
                '1000',
            ],
            'from integer' => [
                100000,
                '100000',
            ],
            'from native Money object' => [
                Money::USD(1000),
                '1000'
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
        $this->assertSame('10.99', $model->price_as_decimal);
    }

    public function provideModelInstance(): Model
    {
        return new class() extends Model {
            use HasMoney;

            protected $table = 'products';

            public $timestamps = false;

            protected $fillable = ['name', 'price'];

            protected $casts = [
                'price' => MoneyCast::class,
            ];
        };
    }

    public function createProductsTable(): void
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('price');
        });
    }
}
