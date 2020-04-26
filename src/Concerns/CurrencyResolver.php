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
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param string|Currency|null $currency
     *
     * @throws InvalidArgumentException
     */
    public function from($currency = null): Currency
    {
        if ($currency === null) {
            return $this->default();
        } elseif (is_string($currency) && trim($currency) !== '') {
            return new Currency(mb_strtoupper($currency));
        } elseif ($currency instanceof Currency) {
            return $currency;
        }

        throw new InvalidArgumentException('Invalid type');
    }

    public function default(): Currency
    {
        return new Currency($this->config->get('currency'));
    }
}
