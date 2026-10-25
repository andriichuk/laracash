<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Rules;

use Illuminate\Contracts\Validation\Rule;
use Money\Currencies;
use Money\Currencies\AggregateCurrencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;

final class CurrencyCode implements Rule
{
    private array $allowedCurrencies;
    private Currencies $currencies;

    /**
     * @param list<string> $allowedCurrencies
     */
    public function __construct(array $allowedCurrencies = [], ?Currencies $currencies = null)
    {
        $this->allowedCurrencies = $allowedCurrencies;
        $this->currencies = $currencies ?? new AggregateCurrencies([new ISOCurrencies(), new BitcoinCurrencies()]);
    }

    public function passes($attribute, $value): bool
    {
        if (!is_string($value) || $value === '' || strtoupper($value) !== $value) {
            return false;
        }

        return $this->currencies->contains(new Currency($value))
            && ($this->allowedCurrencies === [] || in_array($value, $this->allowedCurrencies, true));
    }

    public function message(): string
    {
        return (string) trans('laracash::validation.currency_code');
    }
}
