<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Casts;

use Andriichuk\Laracash\Model\HasCurrencyInterface;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Andriichuk\Laracash\Facades\Laracash;
use Illuminate\Database\Eloquent\Model;
use Money\Currency;

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
     * @return \Money\Currency
     */
    public function get($model, $key, $value, $attributes)
    {
        return $this->resolveCurrency($model, $key, $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param string $key
     * @param \Money\Currency|string $value
     * @param array $attributes
     *
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return $this->resolveCurrency($model, $key, $value)->getCode();
    }

    /**
     * @param \Money\Currency|string $value
     */
    private function resolveCurrency(Model $model, string $key, $value): Currency
    {
        $default = $model instanceof HasCurrencyInterface
            ? $model->getDefaultCurrencyFor($key)
            : Laracash::currency()->default();

        return Laracash::currency()->from($value ?? $default);
    }
}
