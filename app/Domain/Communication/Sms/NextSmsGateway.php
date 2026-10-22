<?php

namespace App\Domain\Communication\Sms;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * NextSMS (messaging-service.co.tz): basic auth, JSON body {from, to, text, reference}, with the school's own account.
 * A rejected message (bad number, no balance) is a final answer; network errors and 5xx are temporary.
 * Nothing here logs the number, the text or the password.
 */
final class NextSmsGateway implements SmsGateway
{
    public function __construct(private readonly string $username, private readonly string $password, private readonly string $senderId) {}

    public function send(string $to, string $text, ?string $reference = null): SmsResult
    {
        $cfg = (array) config('sms.nextsms');

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->acceptJson()->asJson()->timeout((int) $cfg['timeout'])
                ->post(rtrim((string) $cfg['base_url'], '/').'/'.ltrim((string) $cfg['send_path'], '/'), [
                    'from' => $this->senderId,
                    'to' => $to,
                    'text' => $text,
                    'reference' => $reference ?? uniqid('somaplus_', true),
                ]);
        } catch (ConnectionException $e) {
            throw new SmsFailedException('SMS provider unreachable', 0, $e);
        }

        if ($response->serverError()) {
            throw new SmsFailedException('SMS provider error '.$response->status());
        }

        if ($response->failed()) {
            return new SmsResult(false, $reference, 'HTTP '.$response->status());
        }

        $message = $response->json('messages.0');
        $groupId = is_array($message) ? (int) ($message['status']['groupId'] ?? 0) : 0;

        // Group 1 = pending, 3 = delivered, 2 = undeliverable, 4 = expired, 5 = rejected (NextSMS-specific).
        if (is_array($message) && in_array($groupId, [2, 4, 5, 19], true)) {
            return new SmsResult(false, $reference, mb_substr((string) ($message['status']['description'] ?? 'Rejected'), 0, 100));
        }

        return new SmsResult(true, is_array($message) ? (string) ($message['messageId'] ?? $reference) : $reference);
    }
}
