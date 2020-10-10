<?php

namespace Andriichuk\Laracash\Tests\Models;

use Andriichuk\Laracash\Casts\CurrencyCast;
use Andriichuk\Laracash\Casts\MoneyCast;
use Andriichuk\Laracash\Model\HasCurrency;
use Andriichuk\Laracash\Model\HasCurrencyInterface;
use Andriichuk\Laracash\Model\HasMoneyWithCurrency;
use Andriichuk\Laracash\Model\HasMoneyWithCurrencyInterface;
use Illuminate\Database\Eloquent\Model;

class Order extends Model implements HasMoneyWithCurrencyInterface, HasCurrencyInterface
{
    use HasMoneyWithCurrency;
    use HasCurrency;

    protected $table = 'orders';

    public $timestamps = false;

    protected $fillable = ['total_price', 'currency'];

    protected $casts = [
        'total_price' => MoneyCast::class,
        'currency' => CurrencyCast::class
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
