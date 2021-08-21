<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Facades;

use Andriichuk\Laracash\Concerns\CurrencyResolver;
use Andriichuk\Laracash\MoneyManagerInterface;
use Illuminate\Support\Facades\Facade;
use Andriichuk\Laracash\Concerns\Factory;
use Andriichuk\Laracash\Concerns\Formatter;
use Andriichuk\Laracash\Concerns\Parser;

/**
 * Class Laracash
 *
 * @method static Factory factory()
 * @method static Formatter formatter()
 * @method static Parser parser()
 * @method static CurrencyResolver currency()
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class Laracash extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MoneyManagerInterface::class;
    }
}
