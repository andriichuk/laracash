<?php

declare(strict_types=1);

namespace Andriichuk\Laracash;

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
     * @var  Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param int|string $amount
     * @param string|Currency|null $currency
     *
     * @throws InvalidArgumentException
     */
    public function make($amount, $currency = null): Money
    {
        return new Money($amount, $this->resolveCurrency($currency));
    }

    /**
     * @param string|Currency|null $currency
     *
     * @throws InvalidArgumentException
     */
    private function resolveCurrency($currency = null): Currency
    {
        if ($currency === null) {
            return new Currency($this->config->get('currency'));
        } elseif (is_string($currency) && trim($currency) !== '') {
            return new Currency(mb_strtoupper($currency));
        } elseif ($currency instanceof Currency) {
            return $currency;
        }

        throw new InvalidArgumentException('Invalid type');
    }
}
