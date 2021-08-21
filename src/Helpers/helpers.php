<?php

declare(strict_types=1);

use Andriichuk\Laracash\Facades\Laracash;
use Money\Currency;
use Money\Currencies\BitcoinCurrencies;
use Money\Money;

if (!function_exists('makeMoney')) {
    /**
     * @psalm-param int|numeric-string $amount
     * @psalm-param Currency|non-empty-string|null $currency
     *
     * @throws InvalidArgumentException
     */
    function makeMoney(int|string $amount, Currency|string|null $currency = null): Money
    {
        return Laracash::factory()->make($amount, $currency);
    }
}

if (!function_exists('makeBitcoin')) {
    /**
     * @psalm-param int|numeric-string $amount
     */
    function makeBitcoin(int|string $amount): Money
    {
        return Laracash::factory()->makeBitcoin($amount);
    }
}

if (!function_exists('formatMoneyAsCurrency')) {
    function formatMoneyAsCurrency(Money $money, string $locale = null): string
    {
        if ($money->getCurrency()->getCode() === BitcoinCurrencies::CODE) {
            return Laracash::formatter()->formatBitcoin($money);
        }

        return Laracash::formatter()->formatAsIntlCurrency($money, $locale);
    }
}

if (!function_exists('formatMoneyAsDecimal')) {
    function formatMoneyAsDecimal(Money $money): string
    {
        if ($money->getCurrency()->getCode() === BitcoinCurrencies::CODE) {
            return Laracash::formatter()->formatBitcoinAsDecimal($money);
        }

        return Laracash::formatter()->formatAsDecimal($money);
    }
}

if (!function_exists('parseMoneyDecimal')) {
    function parseMoneyDecimal(string $amount, string $currency = null): Money
    {
        return Laracash::parser()->parseDecimal($amount, $currency);
    }
}

