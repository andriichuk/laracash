<?php

declare(strict_types=1);

namespace Andriichuk\Laracash;

/**
 * Class LaracashCompose
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
final class LaracashCompose
{
    /**
     * @var Creator
     */
    private $creator;

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
        $this->creator = new Creator($config);
        $this->formatter = new Formatter($config);
        $this->parser = new Parser($config);
    }

    public function creator(): Creator
    {
        return $this->creator;
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
