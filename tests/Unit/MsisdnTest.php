<?php

use App\Domain\Integrations\Payments\AzamPay\Msisdn;

it('normalizes Tanzanian mobile numbers to 255 and nine digits', function (string $input, ?string $expected) {
    expect(Msisdn::normalize($input))->toBe($expected);
})->with([
    'local' => ['0712345678', '255712345678'],
    'spaces' => ['0712 345 678', '255712345678'],
    'international' => ['255712345678', '255712345678'],
    'plus' => ['+255 712-345-678', '255712345678'],
    'too short' => ['071234567', null],
    'too long' => ['07123456789', null],
    'letters' => ['07123abc78', null],
    'foreign' => ['254712345678', null],
    'empty' => ['', null],
]);
