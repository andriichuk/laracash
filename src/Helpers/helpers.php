<?php

declare(strict_types=1);

use Andriichuk\Laracash\Facades\Laracash;
use Money\Currency;
use Money\Money;

if (!function_exists('makeMoney')) {
    /**
     * @param int|string $amount
     * @param string|Currency|null $currency
     *
     * @throws InvalidArgumentException
     */
    function makeMoney($amount, $currency = null): Money
    {
        return Laracash::factory()->make($amount, $currency);
    }
}

if (!function_exists('makeBitcoin')) {
    /**
     * @param int|string $amount
     */
    function makeBitcoin($amount): Money
    {
        return Laracash::factory()->makeBitcoin($amount);
    }
}

if (!function_exists('formatMoneyAsCurrency')) {
    function formatMoneyAsCurrency(Money $money, string $locale = null): string
    {
        return Laracash::formatter()->formatAsIntlCurrency($money, $locale);
    }
}

if (!function_exists('formatMoneyAsDecimal')) {
    function formatMoneyAsDecimal(Money $money): string
    {
        return Laracash::formatter()->formatAsDecimal($money);
    }
}

if (!function_exists('parseMoneyDecimal')) {
    function parseMoneyDecimal(string $amount, string $currency = null): Money
    {
        return Laracash::parser()->parseDecimal($amount, $currency);
    }
}

