## PHP Laravel Money Package

### Installation

Require package

```shell script
composer require andriichuk/laracash
```

Publish vendor settings:

```shell script
php artisan vendor:publish  --provider="\Andriichuk\Laracash\ServiceProviders\LaracashServiceProvider" --tag="config"
```

### Usage

Add Money column `casts`

```php
<?php

namespace App;

use Andriichuk\Laracash\Casts\MoneyCast;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
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
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasCurrency;

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
