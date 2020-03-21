<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests;

use InvalidArgumentException;
use Andriichuk\Laracash\Config;
use Andriichuk\Laracash\Creator;
use Money\Currency;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class CreatorTest
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class CreatorTest extends TestCase
{
    /**
     * @var Creator
     */
    private $creator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->creator = new Creator(new Config());
    }

    /**
     * @dataProvider moneyProvider
     */
    public function testCreateFrom(array $input, array $expected): void
    {
        $money = $this->creator->createFrom($input['amount'], $input['currency']);

        $this->assertSame($money->getAmount(), $expected['amount']);
        $this->assertSame($money->getCurrency()->getCode(), $expected['currency']);
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
    public function testThrowException($amount, $currency)
    {
        $this->expectException(InvalidArgumentException::class);

        $this->creator->createFrom($amount, $currency);
    }

    public function exceptionDataProvider(): array
    {
        return [
            'empty string currency' => [1, ''],
            'numeric currency' => [1, 100],
            'simple object currency' => [1, new stdClass()],
        ];
    }
}
