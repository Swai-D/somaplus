<?php

namespace App\Domain\Exams;

/**
 * Marks are handled as whole hundredths (45.5 is 4550), so comparing a mark with a
 * grade boundary is exact integer arithmetic and never floating point.
 */
final class Score
{
    /**
     * Reads a typed mark: digits with an optional decimal point or comma and at most two decimals.
     * Returns null for anything else (letters, signs, exponents, three decimals, spaces inside).
     */
    public static function parse(string $input): ?int
    {
        $input = str_replace(',', '.', trim($input));

        if (preg_match('/^(\d{1,4})(?:\.(\d{1,2}))?$/', $input, $m) !== 1) {
            return null;
        }

        return ((int) $m[1]) * 100 + (int) str_pad($m[2] ?? '0', 2, '0');
    }

    /** Reads a value taken from a decimal column, such as "45.50". */
    public static function fromDecimal(string|int|float $value): int
    {
        return (int) round(((float) $value) * 100);
    }

    /** The value for a decimal column: 4550 becomes "45.50". */
    public static function toDecimal(int $hundredths): string
    {
        return intdiv($hundredths, 100).'.'.str_pad((string) ($hundredths % 100), 2, '0', STR_PAD_LEFT);
    }

    /** For people: 4550 becomes "45.5", 4500 becomes "45". */
    public static function format(int $hundredths): string
    {
        return rtrim(rtrim(self::toDecimal($hundredths), '0'), '.');
    }
}
