<?php

namespace Andriichuk\Laracash\Tests\Models;

use Andriichuk\Laracash\Casts\MoneyCast;
use Andriichuk\Laracash\Model\HasCurrency;
use Andriichuk\Laracash\Model\HasCurrencyInterface;
use Andriichuk\Laracash\Model\HasMoneyWithCurrency;
use Andriichuk\Laracash\Model\HasMoneyWithCurrencyInterface;
use Illuminate\Database\Eloquent\Model;
use Money\Currency;

class OrderItem extends Model implements HasMoneyWithCurrencyInterface, HasCurrencyInterface
{
    use HasMoneyWithCurrency;
    use HasCurrency;

    protected $table = 'order_items';

    public $timestamps = false;

    protected $fillable = ['unit_price'];

    protected $casts = [
        'unit_price' => MoneyCast::class,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function getDefaultCurrencyFor(string $field): Currency
    {
        return $this->order->currency;
    }
}
