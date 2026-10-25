<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests\Feature;

use Andriichuk\Laracash\Casts\MoneyCast;
use Andriichuk\Laracash\Model\HasMoney;
use Andriichuk\Laracash\Tests\BaseTestCase;
use Generator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Money\Money;
use PHPUnit\Framework\Attributes\DataProvider;

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
    #[DataProvider('moneyCasesProvider')]
    public function testMoneyCasts($price, ?Money $expected, string $message): void
    {
        $model = $this->modelInstance::create([
            'name' => 'Product Name',
            'price' => $price,
        ]);

        $this->assertEquals($expected, $model->price, $message);
    }

    public static function moneyCasesProvider(): Generator
    {
        yield [
            'price' => '1000',
            'expected' => Money::USD(1000),
            'message' => 'From numeric string'
        ];

        yield [
            'price' => 100000,
            'expected' => Money::USD(100000),
            'message' => 'From integer'
        ];

        yield [
            'price' => Money::USD(1000),
            'expected' => Money::USD(1000),
            'message' => 'From Money object'
        ];

        yield [
            'price' => null,
            'expected' => null,
            'message' => 'From null'
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

    public function testNullableMoneyFormatting(): void
    {
        $model = $this->modelInstance::create(['name' => 'No price', 'price' => null]);

        $this->assertNull($model->price_as_currency);
        $this->assertNull($model->price_as_decimal);
        $this->assertNull($model->fresh()->price_as_currency);
        $this->assertNull($model->fresh()->price_as_decimal);
    }

    public function testZeroMoneyFormatting(): void
    {
        $model = $this->modelInstance::create(['name' => 'Free', 'price' => 0]);

        $this->assertSame('$0.00', $model->price_as_currency);
        $this->assertSame('0.00', $model->price_as_decimal);
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
            $table->string('price')->nullable();
        });
    }
}
