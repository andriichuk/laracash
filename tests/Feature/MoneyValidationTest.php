<?php

declare(strict_types=1);

namespace Andriichuk\Laracash\Tests\Feature;

use Andriichuk\Laracash\Rules\CurrencyCode;
use Andriichuk\Laracash\Rules\MoneyAmount;
use Andriichuk\Laracash\Tests\BaseTestCase;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Money\Currencies\CurrencyList;
use PHPUnit\Framework\Attributes\DataProvider;

class MoneyValidationTest extends BaseTestCase
{
    /** @dataProvider amounts */
    #[DataProvider('amounts')]
    public function testAmountsAreValidatedWithoutRoundingOrFloatCoercion(string $currency, mixed $value, bool $expected): void
    {
        $this->assertSame($expected, (new MoneyAmount($currency))->passes('price', $value));
    }

    public static function amounts(): array
    {
        return [
            ['USD', '12.34', true], ['USD', 0, true], ['USD', '-12.34', true],
            ['USD', '00012.30', true], ['USD', '12.345', false],
            ['USD', '12.340', false], ['USD', 12.34, false], ['USD', true, false],
            ['USD', null, false], ['USD', [], false], ['USD', '', false],
            ['USD', '1e3', false], ['USD', '1,000.00', false], ['USD', ' 12.34 ', false],
            ['USD', '+12.34', false], ['USD', '.34', false], ['USD', '12.', false],
            ['USD', '$12.34', false], ['USD', "12.34\n", false],
            ['JPY', '100', true], ['JPY', '100.1', false],
            ['KWD', '1.234', true], ['KWD', '1.2345', false],
            ['XBT', '0.00000001', true], ['XBT', '0.000000001', false],
            ['ZZZ', '1.00', false], ['', '1.00', false], ['usd', '1.00', false],
        ];
    }

    /** @dataProvider ranges */
    #[DataProvider('ranges')]
    public function testBoundsUseInclusiveDecimalAmounts(string $value, string $minimum, string $maximum, bool $expected): void
    {
        $this->assertSame($expected, (new MoneyAmount('USD', $minimum, $maximum))->passes('price', $value));
    }

    public static function ranges(): array
    {
        return [
            ['0.00', '0.00', '100.00', true],
            ['100.00', '0.00', '100.00', true],
            ['100.01', '0.00', '100.00', false],
            ['-0.01', '0.00', '100.00', false],
            ['-10.00', '-10.00', '0.00', true],
            ['9007199254740993.00', '0', '9007199254740993.00', true],
            ['9007199254740993.01', '0', '9007199254740993.00', false],
        ];
    }

    public function testInvalidRangeConfigurationFailsClearly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new MoneyAmount('USD', '2.00', '1.00'))->passes('price', '1.50');
    }

    public function testBoundsCannotSilentlyRound(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new MoneyAmount('USD', '0.001'))->passes('price', '1.00');
    }

    public function testInvalidInputCurrencyDoesNotThrowEvenWithBounds(): void
    {
        $this->assertFalse((new MoneyAmount('invalid', '0.00'))->passes('price', '1.00'));
    }

    public function testCurrencyCodesSupportAnAllowListAndBitcoin(): void
    {
        $rule = new CurrencyCode(['USD', 'EUR']);

        $this->assertTrue($rule->passes('currency', 'USD'));
        $this->assertFalse($rule->passes('currency', 'GBP'));
        $this->assertFalse($rule->passes('currency', 'usd'));
        $this->assertFalse($rule->passes('currency', 'ZZZ'));
        $this->assertFalse($rule->passes('currency', []));
        $this->assertTrue((new CurrencyCode())->passes('currency', 'XBT'));
    }

    public function testCustomCurrencyProvidersAreSupported(): void
    {
        $currencies = new CurrencyList(['TOK' => 4]);

        $this->assertTrue((new CurrencyCode([], $currencies))->passes('currency', 'TOK'));
        $this->assertTrue((new MoneyAmount('TOK', null, null, $currencies))->passes('price', '1.2345'));
        $this->assertFalse((new MoneyAmount('TOK'))->passes('price', '1.2345'));
    }

    public function testLaravelRequiredNullableAndAttributeMessagesWork(): void
    {
        $rules = ['price' => ['required', new MoneyAmount('USD', '0.00', '100.00')]];

        $this->assertFalse(Validator::make([], $rules)->passes());
        $this->assertTrue(Validator::make(['price' => '12.34'], $rules)->passes());
        $this->assertTrue(Validator::make(['price' => null], ['price' => ['nullable', new MoneyAmount('USD')]])->passes());

        $validator = Validator::make(['price' => '12.345'], $rules);
        $this->assertFalse($validator->passes());
        $this->assertStringContainsString('price', $validator->errors()->first('price'));
        $this->assertStringNotContainsString('laracash::', $validator->errors()->first('price'));

        $currency = Validator::make(['currency' => 'ZZZ'], ['currency' => ['required', new CurrencyCode()]]);
        $this->assertFalse($currency->passes());
        $this->assertSame('The currency must be a supported currency code.', $currency->errors()->first('currency'));
    }

    public function testApplicationsCanOverrideValidationTranslations(): void
    {
        $this->app['translator']->addLines(['validation.money_amount' => 'Invalid :attribute amount.'], 'en', 'laracash');
        $validator = Validator::make(['price' => 'bad'], ['price' => [new MoneyAmount('USD')]]);

        $this->assertSame('Invalid price amount.', $validator->errors()->first('price'));
    }
}
