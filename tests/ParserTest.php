<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests;

use Andriichuk\Laracash\Config;
use Andriichuk\Laracash\Parser;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * Class ParserTest
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class ParserTest extends TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new Parser(new Config());
    }

    public function testParseIntlCurrency(): void
    {
        $this->assertEquals(
            new Money(100, new Currency('USD')),
            $this->parser->parseIntlCurrency('$1.00','en_US', 'USD')
        );
    }

    public function testParseIntlLocalizedDecimal(): void
    {
        $this->assertEquals(
            new Money(100, new Currency('USD')),
            $this->parser->parseIntlLocalizedDecimal('1.000,00','en_US', 'USD')
        );
    }

    public function testParseDecimal(): void
    {
        $this->assertEquals(
            new Money(1000, new Currency('USD')),
            $this->parser->parseDecimal('10', 'USD')
        );
    }

    public function testParseBitcoin(): void
    {
        $this->assertEquals(
            new Money(10000000, new Currency('XBT')),
            $this->parser->parseBitcoin('Ƀ100000')
        );
    }
}
