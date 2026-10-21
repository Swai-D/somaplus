<?php

namespace App\Domain\Integrations\Payments\AzamPay;

/** Tanzanian mobile numbers in the form AzamPay expects: 255 followed by nine digits. */
final class Msisdn
{
    public static function normalize(string $input): ?string
    {
        $digits = preg_replace('/[\s\-().]/', '', trim($input)) ?? '';
        $digits = ltrim($digits, '+');

        if (preg_match('/^0(\d{9})$/', $digits, $m) === 1) {
            return '255'.$m[1];
        }

        return preg_match('/^255\d{9}$/', $digits) === 1 ? $digits : null;
    }
}
