<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Concerns;

use Andriichuk\Laracash\Config;
use InvalidArgumentException;
use Money\Currency;

/**
 * Class CurrencyResolver
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class CurrencyResolver
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @psalm-param Currency|non-empty-string|null $currency
     */
    public function from(Currency|string|null $currency = null): Currency
    {
        if ($currency === null) {
            return $this->default();
        }

        if (is_string($currency)) {
            return new Currency($currency);
        }

        return $currency;
    }

    public function default(): Currency
    {
        /** @psalm-var non-empty-string $currency */
        $currency = (string) $this->config->get('currency');

        return new Currency($currency);
    }
}
