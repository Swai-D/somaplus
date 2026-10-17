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
