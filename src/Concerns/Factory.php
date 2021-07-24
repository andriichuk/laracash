<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Concerns;

use InvalidArgumentException;
use Money\Currency;
use Money\Money;

/**
 * Class Factory
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class Factory
{
    private CurrencyResolver $currencyResolver;

    public function __construct(CurrencyResolver $currencyResolver)
    {
        $this->currencyResolver = $currencyResolver;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function make(int|string $amount, Currency|string|null $currency = null): Money
    {
        return new Money($amount, $this->currencyResolver->from($currency));
    }

    public function makeBitcoin(int|string $amount): Money
    {
        return Money::XBT($amount);
    }
}
