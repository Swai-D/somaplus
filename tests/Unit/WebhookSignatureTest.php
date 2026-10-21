<?php

use App\Domain\Integrations\Payments\WebhookSignature;

it('signs the timestamp and raw body with the secret', function () {
    $sig = WebhookSignature::sign('s3cret-s3cret-s3cret', 1700000000, '{"a":1}');

    expect($sig)->toBe(hash_hmac('sha256', '1700000000.{"a":1}', 's3cret-s3cret-s3cret'))
        ->and(WebhookSignature::valid('s3cret-s3cret-s3cret', '1700000000', $sig, '{"a":1}', 1700000000))->toBeTrue();
});

it('refuses a wrong secret, a changed body, a stale or missing timestamp and a missing signature', function () {
    $secret = 's3cret-s3cret-s3cret';
    $sig = WebhookSignature::sign($secret, 1700000000, 'body');

    expect(WebhookSignature::valid('other-other-other-x', '1700000000', $sig, 'body', 1700000000))->toBeFalse()
        ->and(WebhookSignature::valid($secret, '1700000000', $sig, 'body!', 1700000000))->toBeFalse()
        ->and(WebhookSignature::valid($secret, '1700000000', $sig, 'body', 1700000000 + 301))->toBeFalse()
        ->and(WebhookSignature::valid($secret, '1700000000', $sig, 'body', 1700000000 - 301))->toBeFalse()
        ->and(WebhookSignature::valid($secret, '1700000000', $sig, 'body', 1700000000 + 300))->toBeTrue()
        ->and(WebhookSignature::valid($secret, 'abc', $sig, 'body', 1700000000))->toBeFalse()
        ->and(WebhookSignature::valid($secret, null, $sig, 'body', 1700000000))->toBeFalse()
        ->and(WebhookSignature::valid($secret, '1700000000', null, 'body', 1700000000))->toBeFalse()
        ->and(WebhookSignature::valid('', '1700000000', WebhookSignature::sign('', 1700000000, 'body'), 'body', 1700000000))->toBeFalse();
});
