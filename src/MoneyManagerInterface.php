<?php

declare(strict_types=1);

namespace Andriichuk\Laracash;

use Andriichuk\Laracash\Concerns\CurrencyResolver;
use Andriichuk\Laracash\Concerns\Factory;
use Andriichuk\Laracash\Concerns\Formatter;
use Andriichuk\Laracash\Concerns\Parser;

interface MoneyManagerInterface
{
    public function factory(): Factory;

    public function formatter(): Formatter;

    public function parser(): Parser;

    public function currencyResolver(): CurrencyResolver;
}
