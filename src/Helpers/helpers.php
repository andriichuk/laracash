<?php

declare(strict_types=1);

use Andriichuk\Laracash\Facades\Laracash;
use Money\Money;

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

