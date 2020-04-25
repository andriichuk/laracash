<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests;

use Andriichuk\Laracash\CurrencyResolver;
use InvalidArgumentException;
use Andriichuk\Laracash\Config;
use Andriichuk\Laracash\Factory;
use Money\Currency;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class FactoryTest
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class FactoryTest extends TestCase
{
    /**
     * @var Factory
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

    /**
     * @dataProvider exceptionDataProvider
     */
    public function testThrowException($amount, $currency): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->make($amount, $currency);
    }

    public function exceptionDataProvider(): array
    {
        return [
            'empty string currency' => [1, ''],
            'numeric currency' => [1, 100],
            'simple object currency' => [1, new stdClass()],
        ];
    }

    public function testBitcoinCreation(): void
    {
        $money = $this->factory->makeBitcoin('1000000000');

        $this->assertSame('1000000000', $money->getAmount());
        $this->assertSame('XBT', $money->getCurrency()->getCode());
    }
}
