<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Model;

use Andriichuk\Laracash\Casts\CurrencyCast;
use Andriichuk\Laracash\Facades\Laracash;
use Illuminate\Database\Eloquent\Model;
use Money\Currency;

/**
 * Trait HasCurrency
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
trait HasCurrency
{
    private array $currencyFields = [];

    protected function initializeHasCurrency(): void
    {
        $this->grabCurrencyFields();
        $this->attachCurrencyFieldsEvents();
    }

    private function grabCurrencyFields(): void
    {
        /**
         * @var string $field
         * @var string $cast
         */
        foreach ($this->casts as $field => $cast) {
            if ($cast !== CurrencyCast::class) {
                continue;
            }

            $this->currencyFields[] = $field;
        }
    }

    private function attachCurrencyFieldsEvents(): void
    {
        static::creating(function (Model $model) {
            foreach ($this->currencyFields as $currencyField) {
                if ($model->{$currencyField} === null) {
                    $model->{$currencyField} = $this->getDefaultCurrencyFor($currencyField);
                }
            }
        });
    }

    public function getDefaultCurrencyFor(string $field): Currency
    {
        return $this->getDefaultCurrency();
    }

    private function getDefaultCurrency(): Currency
    {
        return Laracash::currency()->default();
    }
}
