<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Model;

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

    public function getCurrencyColumn(): string
    {
        return $this->currencyColumn;
    }
}
