<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Facades;

use Illuminate\Support\Facades\Facade;
use Andriichuk\Laracash\Factory;
use Andriichuk\Laracash\Formatter;
use Andriichuk\Laracash\Parser;

/**
 * Class Laracash
 *
 * @method static Factory factory()
 * @method static Formatter formatter()
 * @method static Parser parser()
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class Laracash extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laracash';
    }
}
