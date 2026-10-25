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
        // Laravel 13 may initialize this trait before merging the casts() method.
        $casts = method_exists($this, 'casts')
            ? array_merge($this->getCasts(), $this->casts())
            : $this->getCasts();

        foreach ($casts as $field => $cast) {
            if (explode(':', $cast, 2)[0] !== MoneyCast::class || $this->hasAccessor($field . '_as_decimal')) {
                continue;
            }

            $this->addAccessor($field);
        }
    }

    protected function addAccessor(string $field): void
    {
        $this->moneyCasts[$field . '_as_currency'] = function () use ($field) {
            $money = $this->{$field};

            return $money === null ? null : formatMoneyAsCurrency($money);
        };

        $this->moneyCasts[$field . '_as_decimal'] = function () use ($field) {
            $money = $this->{$field};

            return $money === null ? null : formatMoneyAsDecimal($money);
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
