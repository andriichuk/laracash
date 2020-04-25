<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Casts;

use Andriichuk\Laracash\Model\HasCurrencyInterface;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Andriichuk\Laracash\Facades\Laracash;
use Money\Currency;
use Money\Money;

/**
 * Class CurrencyCast
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class CurrencyCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param \Illuminate\Contracts\Database\Eloquent\Model $model
     * @param string $key
     * @param string $value
     * @param array $attributes
     *
     * @return Currency
     */
    public function get($model, $key, $value, $attributes)
    {
        $default = $model instanceof HasCurrencyInterface
            ? $model->getDefaultCurrency()
            : Laracash::currency()->default();

        return Laracash::currency()->from($value ?? $default);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param Currency|string $value
     * @param array $attributes
     *
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return Laracash::currency()->from($value)->getCode();
    }
}
