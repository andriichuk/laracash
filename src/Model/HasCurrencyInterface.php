<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Model;

use \Money\Currency;

/**
 * Interface HasMoneyWithCurrencyInterface
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface HasCurrencyInterface
{
    public function getDefaultCurrencyFor(string $field): Currency;
}
