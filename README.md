## PHP Laravel Money Package

Laravel wrapper over [MoneyPHP](https://github.com/moneyphp/money) library

### Installation

Require package

```shell script
composer require andriichuk/laracash
```

Publish vendor settings:

```shell script
php artisan vendor:publish --provider="Andriichuk\Laracash\ServiceProviders\LaracashServiceProvider" --tag="config"
```

### Usage

Add Money column `casts`

```php
<?php

namespace App;

use Andriichuk\Laracash\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;
use Money\Money;

/**
 * Class Product
 *
 * @property Money $price
 */
class Product extends Model
{
    protected $fillable = ['name', 'price'];

    protected $casts = [
        'price' => MoneyCast::class,
    ];
}
```

With currency column:

```php
<?php

namespace App;

use Andriichuk\Laracash\Casts\MoneyCast;
use Andriichuk\Laracash\Model\HasCurrency;
use Andriichuk\Laracash\Model\HasCurrencyInterface;
use Illuminate\Database\Eloquent\Model;
use Money\Money;

/**
 * Class Product
 *
 * @property Money $price
 */
class Product extends Model implements HasCurrencyInterface
{
    use HasCurrency;

    protected $fillable = ['name', 'price', 'currency_code'];

    protected $casts = [
        'price' => MoneyCast::class,
    ];

    /**
     * Override default currency column (`currency`)
     *
     * @var string
     */
    protected $currencyColumn = 'currency_code';
}
```

Creation

Scalar value:

```php
use App\Product;

Product::create([
    'name' => 'The First Product',
    'price' => 100 // OR string '100'
]);
```

Using Money object:

```php
use App\Product;
use Money\Money;

Product::create([
    'name' => 'The Second Product',
    'price' => Money::USD(100),
]);
```

Using facade:

```php
use Andriichuk\Laracash\Facades\Laracash;
use App\Product;

Product::create([
    'name' => 'The Third Product',
    'price' => Laracash::factory()->make(100)
]);
```

Retrieving:

```php
use App\Product;

$product = Product::find(1);

dd($product->price);
```

```text
Money\Money {#403 ▼
  -amount: "1000"
  -currency: Money\Currency {#404 ▼
    -code: "USD"
  }
}
```

Operations:

```php
use Andriichuk\Laracash\Facades\Laracash;
use App\Product;

$product = Product::find(1);

$product->price = $product->price->add(Laracash::factory()->make(2000));
```
