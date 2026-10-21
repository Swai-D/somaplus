<?php

namespace App\Domain\Integrations\Payments;

/**
 * The signature of a payment webhook: hex HMAC-SHA256 of "<timestamp>.<raw body>" with the shared secret, sent in
 * the X-Somaplus-Signature header together with X-Somaplus-Timestamp (unix seconds). A notice older or newer than
 * five minutes is refused, so a captured request cannot be replayed later.
 */
final class WebhookSignature
{
    public const TOLERANCE_SECONDS = 300;

    public static function sign(string $secret, int $timestamp, string $body): string
    {
        return hash_hmac('sha256', $timestamp.'.'.$body, $secret);
    }

    public static function valid(string $secret, ?string $timestamp, ?string $signature, string $body, ?int $now = null): bool
    {
        if ($secret === '' || $timestamp === null || $signature === null || preg_match('/^\d{9,12}$/', $timestamp) !== 1) {
            return false;
        }

        if (abs(($now ?? time()) - (int) $timestamp) > self::TOLERANCE_SECONDS) {
            return false;
        }

        return hash_equals(self::sign($secret, (int) $timestamp, $body), strtolower($signature));
    }
}
