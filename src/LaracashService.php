<?php

declare(strict_types=1);

namespace Andriichuk\Laracash;

/**
 * Class LaracashService
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class LaracashService
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Config $config)
    {
        $this->factory = new Factory($config);
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
}
