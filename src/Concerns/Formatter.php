<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Concerns;

use Andriichuk\Laracash\Config;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\BitcoinMoneyFormatter;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money;
use NumberFormatter;

/**
 * Class Formatter
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class Formatter
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

    public function formatIntlWithStyle(Money $money, string $locale, int $style): string
    {
        return $this->getIntlFormatter($locale, $style)->format($money);
    }

    public function formatAsIntlCurrency(Money $money, string $locale = null): string
    {
        $locale = $locale ?: $this->config->get('locale');

        return $this->getIntlFormatter($locale, NumberFormatter::CURRENCY)->format($money);
    }

    public function formatAsIntlDecimal(Money $money, string $locale = null): string
    {
        $locale = $locale ?: $this->config->get('locale');

        return $this->getIntlFormatter($locale, NumberFormatter::DECIMAL)->format($money);
    }

    private function getIntlFormatter(string $locale, int $style): IntlMoneyFormatter
    {
        $numberFormatter = new NumberFormatter($locale, $style);

        return new IntlMoneyFormatter($numberFormatter, $this->isoCurrencies);
    }

    public function formatAsDecimal(Money $money): string
    {
        $moneyFormatter = new DecimalMoneyFormatter($this->isoCurrencies);

        return $moneyFormatter->format($money);
    }

    public function formatBitcoin(Money $money, int $fractionDigits = 2): string
    {
        $formatter = new BitcoinMoneyFormatter($fractionDigits, $this->bitcoinCurrencies);

        return $formatter->format($money);
    }

    public function formatBitcoinAsDecimal(Money $money): string
    {
        $moneyFormatter = new DecimalMoneyFormatter($this->bitcoinCurrencies);

        return $moneyFormatter->format($money);
    }
}
