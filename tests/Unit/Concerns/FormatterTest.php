<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests\Unit\Concerns;

use Andriichuk\Laracash\Config;
use Andriichuk\Laracash\Concerns\Formatter;
use Andriichuk\Laracash\Tests\BaseTestCase;
use Money\Currency;
use Money\Money;
use NumberFormatter;

/**
 * Class FormatterTest
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class FormatterTest extends BaseTestCase
{
    /**
     * @var \Andriichuk\Laracash\Concerns\Formatter
     */
    private $formatter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatter = new Formatter(new Config());
    }

    public function testFormatIntlWithStyle(): void
    {
        $this->assertSame(
            'one',
            $this->formatter->formatIntlWithStyle(
                new Money(100, new Currency('USD')),
                'en_US',
                NumberFormatter::SPELLOUT
            )
        );
    }

    public function testFormatAsIntlCurrency(): void
    {
        $this->assertSame(
            '$1.00',
            $this->formatter->formatAsIntlCurrency(
                new Money(100, new Currency('USD'))
            )
        );
    }

    public function testFormatAsIntlDecimal(): void
    {
        $this->assertSame(
            '1,000',
            $this->formatter->formatAsIntlDecimal(
                new Money(100000, new Currency('USD'))
            )
        );
    }

    public function testFormatAsDecimal(): void
    {
        $this->assertSame(
            '1000.00',
            $this->formatter->formatAsDecimal(
                new Money(100000, new Currency('USD'))
            )
        );
    }

    public function testFormatAsBitcoin(): void
    {
        $this->assertSame(
            'Ƀ10.00',
            $this->formatter->formatBitcoin(
                new Money(1000000000, new Currency('XBT'))
            )
        );
    }
}
