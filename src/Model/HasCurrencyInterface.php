<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Model;

use Money\Currency;

/**
 * Interface HasCurrencyInterface
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface HasCurrencyInterface
{
    public function getCurrencyColumn(): string;

    public function isStrictCurrencyMode(): bool;

    public function getDefaultCurrency(): Currency;
}
