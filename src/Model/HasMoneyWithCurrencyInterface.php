<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Model;

/**
 * Interface HasMoneyWithCurrencyInterface
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
interface HasMoneyWithCurrencyInterface
{
    /**
     * @psalm-param non-empty-string $field
     *
     * @psalm-return non-empty-string
     */
    public function getCurrencyColumnFor(string $field): string;
}
