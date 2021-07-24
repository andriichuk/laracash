<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Concerns;

use Andriichuk\Laracash\Config;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
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
    private Config $config;
    private ISOCurrencies $isoCurrencies;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->isoCurrencies = new ISOCurrencies();
    }

    public function parseIntlCurrency(string $amount, string $locale = null, string $fallbackCurrency = null): Money
    {
        $locale = $locale ?: $this->config->get('locale');

        return $this->parseIntl($amount, $locale, NumberFormatter::CURRENCY, $fallbackCurrency);
    }

    public function parseIntl(string $amount, string $locale, int $style, string $fallbackCurrency = null): Money
    {
        $currency = is_string($fallbackCurrency) ? new Currency($fallbackCurrency) : null;

        return $this->getIntlParser($locale, $style)->parse($amount, $currency);
    }

    private function getIntlParser(string $locale, int $style): IntlMoneyParser
    {
        $numberFormatter = new NumberFormatter($locale, $style);

        return new IntlMoneyParser($numberFormatter, $this->isoCurrencies);
    }

    public function parseIntlLocalizedDecimal(string $amount, string $locale, string $fallbackCurrency = null): Money
    {
        return $this->parseIntlLocalized($amount, $locale, NumberFormatter::DECIMAL, $fallbackCurrency);
    }

    public function parseIntlLocalized(string $amount, string $locale, int $style, string $fallbackCurrency = null): Money
    {
        $currency = is_string($fallbackCurrency) ? new Currency($fallbackCurrency) : null;

        return $this->getIntlLocalizedParser($locale, $style)->parse($amount, $currency);
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

        return $moneyParser->parse($amount, new Currency($currency));
    }

    public function parseBitcoin(string $amount, int $fractionDigits = 2, string $fallbackCurrency = null): Money
    {
        $currency = is_string($fallbackCurrency) ? new Currency($fallbackCurrency) : null;
        $parser = new BitcoinMoneyParser($fractionDigits);

        return $parser->parse($amount, $currency);
    }
}
