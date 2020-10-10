<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests\Feature;

use Andriichuk\Laracash\Tests\BaseTestCase;
use Andriichuk\Laracash\Tests\Models\Order;
use Illuminate\Database\Schema\Blueprint;
use Money\Money;

/**
 * Class ModelWithOneMoneyToOneCurrencyColumnTest
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
class ModelWithMoneyAndRelationCurrencyTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createOrdersTable();
        $this->createOrderItemsTable();
    }

    /**
     * @dataProvider moneyCasesProvider
     *
     * @param mixed $price
     */
    public function testMoneyCasts(array $options, Money $expected): void
    {
        $orderModel = Order::create([
            'total_price' => $options['price'],
            'currency' => $options['currency'],
        ]);

        $orderModel->items()->create([
            'unit_price' => $options['price']
        ]);

        $this->assertEquals($expected, $orderModel->total_price);
        $this->assertEquals($expected, $orderModel->items()->first()->unit_price);
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

    public function createOrdersTable(): void
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('total_price');
            $table->char('currency', 3);
        });
    }

    public function createOrderItemsTable(): void
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('order_id')->constrained();
            $table->string('unit_price');
        });
    }
}
