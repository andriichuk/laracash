<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Casts;

use Andriichuk\Laracash\Exceptions\CurrencyMismatchException;
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
     * @param \Illuminate\Contracts\Database\Eloquent\Model $model
     * @param string $key
     * @param Money|string|int $value
     * @param array $attributes
     *
     * @return Money
     */
    public function get($model, $key, $value, $attributes)
    {
        return Laracash::factory()->make($value, $this->resolveCurrencyColumn($model, $key, $attributes));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param Money|string|int $value
     * @param array $attributes
     *
     * @return array|string
     */
    public function set($model, $key, $value, $attributes)
    {
        $money = $value instanceof Money
            ? $value
            : Laracash::factory()->make($value, $this->resolveCurrencyColumn($model, $key, $attributes));

        if ($this->hasCurrencyColumn($model)) {
            return [
                $key => $money->getAmount(),
                $model->getCurrencyColumnFor($key) => $money->getCurrency()->getCode(),
            ];
        }

        return (string) $money->getAmount();
    }

    private function resolveCurrencyColumn(Model $model, string $key, $attributes): ?Currency
    {
        if (!$this->hasCurrencyColumn($model)) {
            return null;
        }

        $default = $model instanceof HasCurrencyInterface
            ? $model->getDefaultCurrencyFor($key)
            : null;

        return Laracash::currency()->from(
            $attributes[$model->getCurrencyColumnFor($key)] ?? $default
        );
    }

    private function hasCurrencyColumn(Model $model): bool
    {
        return $model instanceof HasMoneyWithCurrencyInterface;
    }
}
