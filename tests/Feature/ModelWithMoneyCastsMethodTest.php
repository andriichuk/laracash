<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests\Feature;

use Andriichuk\Laracash\Casts\MoneyCast;
use Andriichuk\Laracash\Model\HasMoney;
use Illuminate\Database\Eloquent\Model;

class ModelWithMoneyCastsMethodTest extends ModelWithMoneyTest
{
    protected function setUp(): void
    {
        if (!method_exists(Model::class, 'casts')) {
            $this->markTestSkipped('The casts() method requires Laravel 11 or later.');
        }

        parent::setUp();
    }

    public function provideModelInstance(): Model
    {
        return new class() extends Model {
            use HasMoney;

            protected $table = 'products';
            public $timestamps = false;
            protected $fillable = ['name', 'price'];

            protected function casts(): array
            {
                return ['price' => MoneyCast::class];
            }
        };
    }
}
