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

/**
 * Class MoneyCast
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class MoneyCast implements CastsAttributes
{
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

        if ($this->hasCurrencyColumn($model, $key)) {
            /** @psalm-var non-empty-string $currencyColumn */
            $currencyColumn = $model->getCurrencyColumnFor($key);

            return [
                $key => $money->getAmount(),
                $currencyColumn => $money->getCurrency()->getCode(),
            ];
        }

        return $money->getAmount();
    }

    private function resolveCurrencyColumn(Model $model, string $key, array $attributes): ?Currency
    {
        if (!$this->hasCurrencyColumn($model, $key)) {
            return null;
        }

        $default = $model instanceof HasCurrencyInterface
            ? $model->getDefaultCurrencyFor($key)
            : null;

        /** @psalm-var non-empty-string $currencyColumn */
        $currencyColumn = $model->getCurrencyColumnFor($key);
        /** @var Currency|null $currency */
        $currency = $attributes[$currencyColumn] ?? $default;

        return Laracash::currencyResolver()->from($currency);
    }

    private function hasCurrencyColumn(Model $model, string $key): bool
    {
        /** @psalm-var non-empty-string $key */
        return $model instanceof HasMoneyWithCurrencyInterface && $model->getCurrencyColumnFor($key);
    }
}
