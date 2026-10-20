<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * An amount of money held as an integer number of minor units.
 *
 * Amounts are never stored or calculated as floats. The default currency is TZS.
 */
final class Money
{
    public const DEFAULT_CURRENCY = 'TZS';

    /**
     * Supported currencies and how many decimal places their minor unit has.
     *
     * @var array<string, int>
     */
    public const CURRENCIES = [
        'TZS' => 2,
        'KES' => 2,
        'UGX' => 0,
        'USD' => 2,
        'EUR' => 2,
    ];

    private function __construct(
        private readonly int $minor,
        private readonly string $currency,
    ) {}

    public static function of(int $minor, string $currency = self::DEFAULT_CURRENCY): self
    {
        $currency = strtoupper($currency);

        if (! array_key_exists($currency, self::CURRENCIES)) {
            throw new InvalidArgumentException("Unsupported currency [{$currency}].");
        }

        return new self($minor, $currency);
    }

    /**
     * Reads a typed amount into minor units: digits with an optional point or comma and no more decimals
     * than the currency has (TZS 1500.50 is 150050 minor units; UGX takes none). Returns null for anything
     * else (letters, signs, exponents, spaces, too many decimals, more than ten whole digits).
     */
    public static function parse(string $input, string $currency = self::DEFAULT_CURRENCY): ?int
    {
        $currency = strtoupper($currency);
        if (! array_key_exists($currency, self::CURRENCIES)) {
            return null;
        }

        $decimals = self::CURRENCIES[$currency];
        $pattern = $decimals > 0
            ? '/^(\d{1,10})(?:[.,](\d{1,'.$decimals.'}))?$/'
            : '/^(\d{1,10})$/';

        if (preg_match($pattern, trim($input), $m) !== 1) {
            return null;
        }

        return ((int) $m[1]) * (int) (10 ** $decimals) + (int) str_pad($m[2] ?? '', $decimals, '0');
    }

    /** An amount for an input field: 150050 becomes "1500.5" (no thousands separators, no trailing zeros). */
    public static function toInput(int $minor, string $currency = self::DEFAULT_CURRENCY): string
    {
        $decimals = self::CURRENCIES[strtoupper($currency)] ?? 2;
        if ($decimals === 0) {
            return (string) $minor;
        }
        $divisor = (int) (10 ** $decimals);

        return rtrim(rtrim(intdiv($minor, $divisor).'.'.str_pad((string) ($minor % $divisor), $decimals, '0', STR_PAD_LEFT), '0'), '.');
    }

    public static function zero(string $currency = self::DEFAULT_CURRENCY): self
    {
        return self::of(0, $currency);
    }

    public function minor(): int
    {
        return $this->minor;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function plus(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->minor + $other->minor, $this->currency);
    }

    public function minus(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->minor - $other->minor, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->currency === $other->currency && $this->minor === $other->minor;
    }

    public function isNegative(): bool
    {
        return $this->minor < 0;
    }

    /**
     * Format as "TZS 1,500.50" using integer arithmetic only.
     */
    public function format(): string
    {
        $decimals = self::CURRENCIES[$this->currency];
        $absolute = abs($this->minor);
        $divisor = (int) (10 ** $decimals);

        $whole = number_format(intdiv($absolute, $divisor));
        $fraction = $decimals > 0
            ? '.'.str_pad((string) ($absolute % $divisor), $decimals, '0', STR_PAD_LEFT)
            : '';

        return ($this->minor < 0 ? '-' : '')."{$this->currency} {$whole}{$fraction}";
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException(
                "Currency mismatch: [{$this->currency}] and [{$other->currency}].",
            );
        }
    }
}
