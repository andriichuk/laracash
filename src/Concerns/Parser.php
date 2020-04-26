<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Concerns;

use Andriichuk\Laracash\Config;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\ISOCurrencies;
use Money\Money;
use Money\Parser\BitcoinMoneyParser;
use Money\Parser\DecimalMoneyParser;
use Money\Parser\IntlLocalizedDecimalParser;
use Money\Parser\IntlMoneyParser;
use NumberFormatter;

/**
 * Class Parser
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class Parser
{
    /**
     * @var  Config
     */
    private $config;

    /**
     * @var ISOCurrencies
     */
    private $isoCurrencies;

    /**
     * @var BitcoinCurrencies
     */
    private $bitcoinCurrencies;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->isoCurrencies = new ISOCurrencies();
        $this->bitcoinCurrencies = new BitcoinCurrencies();
    }

    public function parseIntlCurrency(string $amount, string $locale = null, string $forceCurrency = null): Money
    {
        $locale = $locale ?: $this->config->get('locale');

        return $this->parseIntl($amount, $locale, NumberFormatter::CURRENCY, $forceCurrency);
    }

    public function parseIntl(string $amount, string $locale, int $style, string $forceCurrency = null): Money
    {
        return $this->getIntlParser($locale, $style)->parse($amount, $forceCurrency);
    }

    private function getIntlParser(string $locale, int $style): IntlMoneyParser
    {
        $numberFormatter = new NumberFormatter($locale, $style);

        return new IntlMoneyParser($numberFormatter, $this->isoCurrencies);
    }

    public function parseIntlLocalizedDecimal(string $amount, string $locale, string $forceCurrency = null): Money
    {
        return $this->parseIntlLocalized($amount, $locale, NumberFormatter::DECIMAL, $forceCurrency);
    }

    public function parseIntlLocalized(string $amount, string $locale, int $style, string $forceCurrency = null): Money
    {
        return $this->getIntlLocalizedParser($locale, $style)->parse($amount, $forceCurrency);
    }

    private function getIntlLocalizedParser(string $locale, int $style): IntlLocalizedDecimalParser
    {
        $numberFormatter = new NumberFormatter($locale, $style);

        return new IntlLocalizedDecimalParser($numberFormatter, $this->isoCurrencies);
    }

    public function parseDecimal(string $amount, string $currency = null): Money
    {
        $currency = $currency ?: $this->config->get('currency');
        $moneyParser = new DecimalMoneyParser($this->isoCurrencies);

        return $moneyParser->parse($amount, $currency);
    }

    public function parseBitcoin(string $amount, int $fractionDigits = 2, string $forceCurrency = null): Money
    {
        $parser = new BitcoinMoneyParser($fractionDigits);

        return $parser->parse($amount, $forceCurrency);
    }
}
