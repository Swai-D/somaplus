<?php

use App\Support\Money;

it('defaults to TZS', function () {
    expect(Money::of(1500)->currency())->toBe('TZS');
});

it('normalizes the currency code', function () {
    expect(Money::of(100, 'kes')->currency())->toBe('KES');
});

it('rejects an unsupported currency', function () {
    Money::of(100, 'XYZ');
})->throws(InvalidArgumentException::class, 'Unsupported currency [XYZ].');

it('formats minor units with integer arithmetic', function (int $minor, string $currency, string $expected) {
    expect(Money::of($minor, $currency)->format())->toBe($expected);
})->with([
    'whole amount' => [150000, 'TZS', 'TZS 1,500.00'],
    'with minor units' => [150050, 'TZS', 'TZS 1,500.50'],
    'less than one unit' => [5, 'KES', 'KES 0.05'],
    'currency without decimals' => [2500000, 'UGX', 'UGX 2,500,000'],
    'negative amount' => [-12345, 'USD', '-USD 123.45'],
    'zero' => [0, 'EUR', 'EUR 0.00'],
]);

it('adds and subtracts amounts in the same currency', function () {
    $total = Money::of(1000)->plus(Money::of(250));

    expect($total->minor())->toBe(1250)
        ->and($total->minus(Money::of(1500))->isNegative())->toBeTrue();
});

it('refuses to mix currencies', function () {
    Money::of(100, 'TZS')->plus(Money::of(100, 'USD'));
})->throws(InvalidArgumentException::class, 'Currency mismatch');

it('compares by amount and currency', function () {
    expect(Money::of(100)->equals(Money::of(100)))->toBeTrue()
        ->and(Money::of(100)->equals(Money::of(100, 'KES')))->toBeFalse()
        ->and(Money::zero()->equals(Money::of(0)))->toBeTrue();
});

it('reads typed amounts into minor units exactly', function (string $input, string $currency, ?int $minor) {
    expect(Money::parse($input, $currency))->toBe($minor);
})->with([
    'whole TZS' => ['150000', 'TZS', 15000000],
    'two decimals' => ['1500.50', 'TZS', 150050],
    'one decimal' => ['1500.5', 'TZS', 150050],
    'comma decimal' => ['1500,5', 'TZS', 150050],
    'zero' => ['0', 'TZS', 0],
    'surrounding spaces are trimmed' => [' 20 ', 'TZS', 2000],
    'UGX has no decimals' => ['1500', 'UGX', 1500],
    'UGX refuses a decimal' => ['1500.5', 'UGX', null],
    'three decimals' => ['1.234', 'TZS', null],
    'empty' => ['', 'TZS', null],
    'letters' => ['abc', 'TZS', null],
    'negative' => ['-5', 'TZS', null],
    'plus sign' => ['+5', 'TZS', null],
    'exponent' => ['1e3', 'TZS', null],
    'space inside' => ['1 000', 'TZS', null],
    'thousands separator' => ['1,000.00', 'TZS', null],
    'more than ten whole digits' => ['12345678901', 'TZS', null],
    'ten whole digits' => ['9999999999', 'TZS', 999999999900],
    'unsupported currency' => ['10', 'XYZ', null],
]);

it('writes minor units back for an input field', function (int $minor, string $currency, string $expected) {
    expect(Money::toInput($minor, $currency))->toBe($expected);
})->with([
    [15000000, 'TZS', '150000'],
    [150050, 'TZS', '1500.5'],
    [150005, 'TZS', '1500.05'],
    [5, 'TZS', '0.05'],
    [1500, 'UGX', '1500'],
]);

it('round trips every typed amount', function () {
    foreach (['0', '0.01', '12.3', '99999.99', '1500'] as $typed) {
        expect(Money::toInput(Money::parse($typed, 'TZS') ?? -1, 'TZS'))->toBe(rtrim(rtrim(number_format((float) $typed, 2, '.', ''), '0'), '.') ?: '0');
    }
});
