<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Model;

use Andriichuk\Laracash\Facades\Laracash;
use Money\Currency;

/**
 * Trait HasCurrency
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
trait HasCurrency
{
    /**
     * @var string
     */
    protected $currencyColumn = 'currency';

    /**
     * @var bool
     */
    protected $strictCurrencyMode = false;

    protected function initializeHasCurrency(): void
    {
        static::creating(function (HasCurrencyInterface $model) {
            if ($model->{$this->getCurrencyColumn()} === null) {
                $model->{$this->getCurrencyColumn()} = $this->getDefaultCurrency();
            }
        });
    }

    public function getCurrencyColumn(): string
    {
        return $this->currencyColumn;
    }

    public function isStrictCurrencyMode(): bool
    {
        return (bool) $this->strictCurrencyMode;
    }

    public function getDefaultCurrency(): Currency
    {
        return Laracash::currency()->default();
    }
}
