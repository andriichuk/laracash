<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Rules;

use Illuminate\Contracts\Validation\Rule;
use InvalidArgumentException;
use Money\Currencies;
use Money\Currencies\AggregateCurrencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

final class MoneyAmount implements Rule
{
    private string $currency;
    private int|string|null $min;
    private int|string|null $max;
    private Currencies $currencies;

    public function __construct(string $currency, int|string|null $min = null, int|string|null $max = null, ?Currencies $currencies = null)
    {
        $this->currency = $currency;
        $this->min = $min;
        $this->max = $max;
        $this->currencies = $currencies ?? new AggregateCurrencies([new ISOCurrencies(), new BitcoinCurrencies()]);
    }

    public function passes($attribute, $value): bool
    {
        $money = $this->parse($value);

        if ($money === null) {
            return false;
        }

        $minimum = $this->bound($this->min);
        $maximum = $this->bound($this->max);

        if ($minimum !== null && $maximum !== null && $minimum->greaterThan($maximum)) {
            throw new InvalidArgumentException('The minimum money amount must not exceed the maximum.');
        }

        return ($minimum === null || !$money->lessThan($minimum))
            && ($maximum === null || !$money->greaterThan($maximum));
    }

    public function message(): string
    {
        return (string) trans('laracash::validation.money_amount');
    }

    private function parse(mixed $value): ?Money
    {
        if ((!is_string($value) && !is_int($value)) || $this->currency === '' || strtoupper($this->currency) !== $this->currency) {
            return null;
        }

        $currency = new Currency($this->currency);

        if (!$this->currencies->contains($currency) || !preg_match('/^-?[0-9]+(?:\.([0-9]+))?$/D', (string) $value, $matches)) {
            return null;
        }

        if (strlen($matches[1] ?? '') > $this->currencies->subunitFor($currency)) {
            return null;
        }

        return (new DecimalMoneyParser($this->currencies))->parse((string) $value, $currency);
    }

    private function bound(int|string|null $value): ?Money
    {
        if ($value === null) {
            return null;
        }

        $money = $this->parse($value);

        if ($money === null) {
            throw new InvalidArgumentException('Money validation bounds must be decimal amounts valid for the configured currency.');
        }

        return $money;
    }
}
