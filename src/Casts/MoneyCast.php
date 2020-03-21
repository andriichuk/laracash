<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Andriichuk\Laracash\Facades\Laracash;
use Andriichuk\Laracash\Model\HasCurrency;

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
     * @param  \Illuminate\Contracts\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     *
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        $currency = $model instanceof HasCurrency
            ? $attributes[$model->getCurrencyColumn()] ?? null
            : null;

        return Laracash::creator()->createFrom($value, $currency);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     *
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return (string) $value;
    }
}
