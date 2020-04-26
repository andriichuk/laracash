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
    /**
     * @var CurrencyResolver
     */
    private $currencyResolver;

    public function __construct(CurrencyResolver $currencyResolver)
    {
        $this->currencyResolver = $currencyResolver;
    }

    /**
     * @param int|string $amount
     * @param string|Currency|null $currency
     *
     * @throws InvalidArgumentException
     */
    public function make($amount, $currency = null): Money
    {
        return new Money($amount, $this->currencyResolver->from($currency));
    }

    /**
     * @param int|string $amount
     */
    public function makeBitcoin($amount): Money
    {
        return Money::XBT($amount);
    }
}
