<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Exceptions;

use Money\Currency;

/**
 * Class CurrencyMismatchException
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class CurrencyMismatchException extends \RuntimeException
{
    public static function createFrom(Currency $expected, Currency $actual): CurrencyMismatchException
    {
        return new static(
            sprintf('Failed asserting that "%s" matches expected "%s"', $actual->getCode(), $expected->getCode())
        );
    }
}
