<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Casts;

use Andriichuk\Laracash\Model\HasCurrencyInterface;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Andriichuk\Laracash\Facades\Laracash;
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
     * @param mixed $value
     * @param array $attributes
     *
     * @return Money
     */
    public function get($model, $key, $value, $attributes)
    {
        $currency = $model instanceof HasCurrencyInterface
            ? $attributes[$model->getCurrencyColumn()] ?? null
            : null;

        return Laracash::factory()->make($value, $currency);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param array $value
     * @param array $attributes
     *
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        if ($value instanceof Money) {
            return (string) $value->getAmount();
        }

        return (string) $value;
    }
}
