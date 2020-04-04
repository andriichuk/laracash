## PHP Laravel Money Package

![Logo](./docs/logo.png)

[![Latest Stable Version](https://poser.pugx.org/andriichuk/laracash/v/stable?format=flat)](https://packagist.org/packages/andriichuk/laracash)
[![Total Downloads](https://poser.pugx.org/andriichuk/laracash/downloads?format=flat)](https://packagist.org/packages/andriichuk/laracash)
[![License](https://poser.pugx.org/andriichuk/laracash/license?format=flat)](https://packagist.org/packages/andriichuk/laracash)

* Laravel wrapper over [MoneyPHP](https://github.com/moneyphp/money) library
* Provides a convenient way to work with the money column as a Value Object
* Uses the [Custom Casts](https://laravel.com/docs/7.x/eloquent-mutators#custom-casts) Laravel 7.x feature 

### Requirements

* PHP >= 7.2.5
* Laravel v7.x
* JSON PHP Extension (`ext-json`)

Suggest

* BCMath (`ext-bcmath`) and GMP (`ext-gmp`) PHP Extensions for calculations with large integers
* Intl PHP Extension (`ext-intl`) for formatting

### Installation

Require package

```shell script
composer require andriichuk/laracash
```

Publish vendor settings

```shell script
php artisan vendor:publish --provider="Andriichuk\Laracash\ServiceProviders\LaracashServiceProvider" --tag="config"
```

Default settings

* `currency` - USD
* `locale` - en_US

### Usage

Add Money cast to your Eloquent column

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

With currency column

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

### Display money data in the form input field

Assign model

```php
use App\Product;
use Illuminate\Support\Facades\Route;

Route::view('/', 'productForm', ['product' => Product::find(1)]);
```

Present money object as a decimal value

```blade
<input type="number" name="price" value="{{ formatMoneyAsDecimal($product->price) }}">
```

### Get money from request

```php

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('products/{product}', function (Product $product, Request $request) {
    $product->price = parseMoneyDecimal($request->get('price')); // 55.99 => Money::USD(5599)
});
```

### Serialization

Define model resource

```php
use App\Product;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductResource
 *
 * @mixin Product
 */
final class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'price_as_currency' => formatMoneyAsCurrency($this->price),
        ];
    }
}
```

Apply resource to the model

```php
use App\Product;
use App\Http\Resources\ProductResource;

Route::get('products/{product}', function (Product $product) {
    return new ProductResource($product);
});
```

Output

```json
{
  "data": {
    "id": 1,
    "name": "Product name",
    "price": {
      "amount": "1000",
      "currency": "USD"
    },
    "price_as_currency": "$10.00"
  }
}
```

### Model Creation

Using scalar values (int|string)

```php
use App\Product;

Product::create([
    'name' => 'The First Product',
    'price' => 100,
]);
```

Using `Money\Money` object:

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
    'name' => 'The Fourth Product',
    'price' => Laracash::factory()->make(100)
]);
```

Using helper function:

```php
use App\Product;

Product::create([
    'name' => 'The Third Product',
    'price' => makeMoney(100)
]);
```

Bitcoin creation:

```php
use Andriichuk\Laracash\Facades\Laracash;
use Money\Money;

// Using Facade
Laracash::factory()->makeBitcoin(1000000000);

// Using helper
makeBitcoin(1000000000);

// Using native library factory call
Money::XBT(1000000000);
```

### Retrieving data

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

### Operations

Check original library [docs](http://moneyphp.org/en/stable/features/operation.html) for more information

```php
use Andriichuk\Laracash\Facades\Laracash;
use App\Product;

$product = Product::find(1);

$product->price = $product->price->add(Laracash::factory()->make(2000));

$product->save();
```

### API

#### Creation

Money instance creation using `Laracash` facade.

*If you do not pass the second argument `currency`, then it will take from `config` file

```php
use \Andriichuk\Laracash\Facades\Laracash;

Laracash::factory()->make(1000);
Laracash::factory()->make('10000000000000');
```

Specify currency

```php
use \Andriichuk\Laracash\Facades\Laracash;
use \Money\Currency;

Laracash::factory()->make(1000, 'USD');
Laracash::factory()->make(1000, new Currency('USD'));

// Or use native method Money::USD(100)
```

```text
Money\Money {#403 ▼
  -amount: "1000"
  -currency: Money\Currency {#404 ▼
    -code: "USD"
  }
}
```

#### Formatting

Money instance formatting. [More info](http://moneyphp.org/en/stable/features/formatting.html)

Decimal

```php
use \Andriichuk\Laracash\Facades\Laracash;
use Money\Money;

Laracash::formatter()->formatAsDecimal(Money::USD(100)); // "1.00"
formatMoneyAsDecimal(Money::USD(100)); // "1.00"
```

Using `Intl` extension

```php
use \Andriichuk\Laracash\Facades\Laracash;
use Money\Money;

Laracash::formatter()->formatAsIntlDecimal(Money::USD(100)); // "1"
Laracash::formatter()->formatAsIntlDecimal(Money::USD(100), 'uk_UA'); // "1"
```

`Intl` currency

```php
use \Andriichuk\Laracash\Facades\Laracash;
use Money\Money;

Laracash::formatter()->formatAsIntlCurrency(Money::USD(100)); // "$1.00"
Laracash::formatter()->formatAsIntlCurrency(Money::USD(100), 'uk_UA'); // "1,00 USD"
formatMoneyAsCurrency(Money::USD(100)); // "$1.00"
```

Specify custom `Intl` formatting style

```php
use \Andriichuk\Laracash\Facades\Laracash;
use Money\Money;
use NumberFormatter;

Laracash::formatter()->formatIntlWithStyle(Money::USD(100), 'en_US', NumberFormatter::SPELLOUT); // "one"
Laracash::formatter()->formatIntlWithStyle(Money::USD(100), 'en_US', NumberFormatter::SCIENTIFIC); // "1E0"
```

Bitcoin

```php
use \Andriichuk\Laracash\Facades\Laracash;
use Money\Money;

Laracash::formatter()->formatBitcoin(Money::XBT(1000000000)); // "Ƀ10.00"
```

#### Parsing
 
[More info](http://moneyphp.org/en/stable/features/parsing.html)

Intl parse money string with currency

```php
use Andriichuk\Laracash\Facades\Laracash;

Laracash::parser()->parseIntlCurrency('$1.00');

```

Result

```text
Money\Money {#369 ▼
    -amount: "100"
    -currency: Money\Currency {#368 ▼
      -code: "USD"
    }
  }
```

Parse decimal

```php
use Andriichuk\Laracash\Facades\Laracash;

Laracash::parser()->parseDecimal('1.30');
parseMoneyDecimal('1.30');
```

Result

```text
Money\Money {#368 ▼
  -amount: "130"
  -currency: Money\Currency {#367 ▼
    -code: "USD"
  }
}
```
