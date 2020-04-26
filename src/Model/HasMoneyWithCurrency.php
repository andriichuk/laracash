<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Model;

/**
 * Trait HasMoneyWithCurrency
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
trait HasMoneyWithCurrency
{
    use HasMoney;

    /**
     * @var array
     */
    private $moneyCasts = [];

    public function getCurrencyColumnFor(string $field): string
    {
        return $this->getCurrencyColumn();
    }

    public function getCurrencyColumn(): string
    {
        return 'currency';
    }
}
