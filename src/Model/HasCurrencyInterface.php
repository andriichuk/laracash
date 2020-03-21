<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Model;

/**
 * Interface HasCurrencyInterface
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface HasCurrencyInterface
{
    public function getCurrencyColumn(): string;
}
