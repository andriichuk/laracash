<?php

declare(strict_types=1);

namespace Andriichuk\Laracash;

use Andriichuk\Laracash\Concerns\CurrencyResolver;
use Andriichuk\Laracash\Concerns\Factory;
use Andriichuk\Laracash\Concerns\Formatter;
use Andriichuk\Laracash\Concerns\Parser;

/**
 * Class LaracashService
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class LaracashService
{
    private CurrencyResolver $currencyResolver;
    private Factory $factory;
    private Formatter $formatter;
    private Parser $parser;

    public function __construct(Config $config)
    {
        $this->currencyResolver = new CurrencyResolver($config);
        $this->factory = new Factory($this->currencyResolver);
        $this->formatter = new Formatter($config);
        $this->parser = new Parser($config);
    }

    public function factory(): Factory
    {
        return $this->factory;
    }

    public function formatter(): Formatter
    {
        return $this->formatter;
    }

    public function parser(): Parser
    {
        return $this->parser;
    }

    public function currency(): CurrencyResolver
    {
        return $this->currencyResolver;
    }
}
