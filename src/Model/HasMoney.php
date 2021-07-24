<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Model;

use Andriichuk\Laracash\Casts\MoneyCast;

/**
 * Trait HasMoney
 *
 * @author Serhii Andriichuk <andriichuk29@gmail.com>
 */
trait HasMoney
{
    private array $moneyCasts = [];

    protected function initializeHasMoney(): void
    {
        $this->initializeAccessors();
    }

    protected function initializeAccessors(): void
    {
        foreach ($this->casts as $field => $cast) {
            if ($cast !== MoneyCast::class || $this->hasAccessor($field)) {
                continue;
            }

            $this->addAccessor($field);
        }
    }

    protected function addAccessor(string $field): void
    {
        $this->moneyCasts[$field . '_as_currency'] = function () use ($field) {
            return formatMoneyAsCurrency($this->{$field});
        };

        $this->moneyCasts[$field . '_as_decimal'] = function () use ($field) {
            return formatMoneyAsDecimal($this->{$field});
        };
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if (!$this->hasAccessor($key)) {
            return parent::__get($key);
        }

        return $this->moneyCasts[$key]();
    }

    protected function hasAccessor(string $field): bool
    {
        return isset($this->moneyCasts[$field]);
    }
}
