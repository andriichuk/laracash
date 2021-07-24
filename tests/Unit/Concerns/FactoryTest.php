<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests\Unit\Concerns;

use Andriichuk\Laracash\Concerns\CurrencyResolver;
use Andriichuk\Laracash\Tests\BaseTestCase;
use Andriichuk\Laracash\Config;
use Andriichuk\Laracash\Concerns\Factory;
use Money\Currency;
use stdClass;

/**
 * Class FactoryTest
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class FactoryTest extends BaseTestCase
{
    /**
     * @var \Andriichuk\Laracash\Concerns\Factory
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new Factory(
            new CurrencyResolver(new Config())
        );
    }

    /**
     * @dataProvider moneyProvider
     */
    public function testCreateFrom(array $input, array $expected): void
    {
        $money = $this->factory->make($input['amount'], $input['currency']);

        $this->assertSame($expected['amount'], $money->getAmount());
        $this->assertSame($expected['currency'], $money->getCurrency()->getCode());
    }

    public function moneyProvider(): array
    {
        return [
            'scalar values' => [
                [
                    'amount' => 500,
                    'currency' => 'USD',
                ],
                [
                    'amount' => '500',
                    'currency' => 'USD',
                ],
            ],

            'currency object' => [
                [
                    'amount' => 500,
                    'currency' => new Currency('USD'),
                ],
                [
                    'amount' => '500',
                    'currency' => 'USD',
                ],
            ],

            'currency as null' => [
                [
                    'amount' => 500,
                    'currency' => null,
                ],
                [
                    'amount' => '500',
                    'currency' => 'USD',
                ],
            ],
        ];
    }

    public function testBitcoinCreation(): void
    {
        $money = $this->factory->makeBitcoin('1000000000');

        $this->assertSame('1000000000', $money->getAmount());
        $this->assertSame('XBT', $money->getCurrency()->getCode());
    }
}
