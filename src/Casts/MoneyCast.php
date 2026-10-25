<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Casts;

use Andriichuk\Laracash\Model\HasCurrencyInterface;
use Andriichuk\Laracash\Model\HasMoneyWithCurrencyInterface;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Andriichuk\Laracash\Facades\Laracash;
use Illuminate\Database\Eloquent\Model;
use Money\Currency;
use Money\Money;
use InvalidArgumentException;

/**
 * Class MoneyCast
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class MoneyCast implements CastsAttributes
{
    private ?string $currencyColumn;
    private ?Currency $fixedCurrency;

    /**
     * @psalm-param non-empty-string|null $currency
     */
    public function __construct(?string $currencyColumn = null, ?string $currency = null)
    {
        $this->currencyColumn = $currencyColumn === null || $currencyColumn === '' ? null : self::normalizeColumn($currencyColumn);
        $this->fixedCurrency = $currency === null ? null : new Currency(self::normalizeCurrency($currency));

        if ($this->currencyColumn !== null && $this->fixedCurrency !== null) {
            throw new InvalidArgumentException('Configure either a currency column or a fixed currency, not both.');
        }
    }

    /**
     * @psalm-param non-empty-string $currency
     */
    public static function usingCurrency(string $currency): string
    {
        return self::class . ':,' . self::normalizeCurrency($currency);
    }

    public static function usingCurrencyColumn(string $column): string
    {
        return self::class . ':' . self::normalizeColumn($column);
    }

    /** @psalm-return non-empty-string */
    private static function normalizeColumn(string $column): string
    {
        if ($column === '' || trim($column) !== $column || strpbrk($column, ':,') !== false) {
            throw new InvalidArgumentException('The currency column must be a non-empty attribute name without cast separators.');
        }

        return $column;
    }

    /** @psalm-return non-empty-string */
    private static function normalizeCurrency(string $currency): string
    {
        if ($currency === '' || trim($currency) !== $currency || strpbrk($currency, ':,') !== false) {
            throw new InvalidArgumentException('The fixed currency must be a non-empty currency code without cast separators.');
        }

        return strtoupper($currency);
    }

    /**
     * Cast the given value.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     *
     * @return \Money\Money|null
     */
    public function get($model, $key, $value, $attributes)
    {
        if ($value === null) {
            return null;
        }

        /** @psalm-var int|numeric-string $value */
        return Laracash::factory()->make($value, $this->resolveCurrencyColumn($model, $key, $attributes));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     *
     * @return array|string|null
     */
    public function set($model, $key, $value, $attributes)
    {
        /** @psalm-var non-empty-string $key */
        /** @psalm-var Money|int|numeric-string|null $value */

        if ($value === null) {
            return null;
        }

        $money = $value instanceof Money
            ? $value
            : Laracash::factory()->make($value, $this->resolveCurrencyColumn($model, $key, $attributes));

        if ($this->fixedCurrency !== null && !$money->getCurrency()->equals($this->fixedCurrency)) {
            throw new InvalidArgumentException('The Money currency must match the fixed currency configured for ' . $key . '.');
        }

        $currencyColumn = $this->getCurrencyColumn($model, $key);

        if ($currencyColumn !== '') {
            // Clear any cached currency cast before Eloquent merges cached values on save.
            $model->setAttribute($currencyColumn, $money->getCurrency()->getCode());

            return [
                $key => $money->getAmount(),
                $currencyColumn => $money->getCurrency()->getCode(),
            ];
        }

        return $money->getAmount();
    }

    private function resolveCurrencyColumn(Model $model, string $key, array $attributes): ?Currency
    {
        if ($this->fixedCurrency !== null) {
            return $this->fixedCurrency;
        }

        $currencyColumn = $this->getCurrencyColumn($model, $key);

        if ($currencyColumn === '') {
            return null;
        }

        $default = $model instanceof HasCurrencyInterface
            ? $model->getDefaultCurrencyFor($key)
            : null;

        /** @psalm-var Currency|non-empty-string|null $currency */
        $currency = $attributes[$currencyColumn] ?? $default;

        return Laracash::currencyResolver()->from($currency);
    }

    private function getCurrencyColumn(Model $model, string $key): string
    {
        if ($this->fixedCurrency !== null) {
            return '';
        }

        /** @psalm-var non-empty-string $key */
        $column = $this->currencyColumn ?? ($model instanceof HasMoneyWithCurrencyInterface ? $model->getCurrencyColumnFor($key) : '');

        if ($column === $key) {
            throw new InvalidArgumentException('The money and currency attributes must use different columns.');
        }

        return $column;
    }
}
