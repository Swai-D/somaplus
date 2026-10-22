<?php

namespace App\Domain\Communication\Jobs;

use App\Domain\Communication\PhoneNumber;
use App\Domain\Communication\ReminderMessage;
use App\Domain\Communication\Sms\SchoolSms;
use App\Domain\Communication\Sms\SmsFailedException;
use App\Domain\Fees\InvoiceBalance;
use App\Models\SmsMessage;
use App\Tenancy\TenantAwareJob;
use App\Tenancy\TenantContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Sends one queued reminder inside its school. The balance and the guardian's number are worked out now: a reminder is
 * cancelled when the invoice has meanwhile been paid, and fails (without retrying) when the number is no longer valid
 * or SMS has been switched off. A temporary provider failure is retried, then recorded as failed. The phone number,
 * the text and any provider message with long digit runs are never stored or logged.
 */
class SendSmsMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, TenantAwareJob;

    public int $tries = 3;

    public function __construct(public readonly int $messageId) {}

    public function backoff(): int
    {
        return 60;
    }

    public function handle(SchoolSms $sms): void
    {
        $message = SmsMessage::query()->with(['invoice.student', 'guardian'])->find($this->messageId);
        if ($message === null || $message->status !== SmsMessage::QUEUED) {
            return;
        }

        $invoice = $message->invoice;
        if (InvoiceBalance::of($invoice)->balance() <= 0) {
            $this->finish($message, SmsMessage::CANCELLED, 'paid');

            return;
        }

        $gateway = $sms->gateway();
        if ($gateway === null) {
            $this->finish($message, SmsMessage::FAILED, 'sms_off');

            return;
        }

        $to = PhoneNumber::normalize($message->guardian->phone);
        if ($to === null) {
            $this->finish($message, SmsMessage::FAILED, 'bad_phone');

            return;
        }

        $school = TenantContext::instance()->current();
        $text = ReminderMessage::text($invoice, $school, $message->locale);

        try {
            $result = $gateway->send($to, $text, 'somaplus-sms-'.$message->id);
        } catch (SmsFailedException $e) {
            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff());

                return;
            }
            $this->finish($message, SmsMessage::FAILED, 'unreachable');

            return;
        }

        if (! $result->accepted) {
            $this->finish($message, SmsMessage::FAILED, preg_replace('/\d{6,}/', '***', (string) $result->error) ?: 'rejected');

            return;
        }

        $message->forceFill(['status' => SmsMessage::SENT, 'provider_reference' => $result->reference !== null ? mb_substr($result->reference, 0, 100) : null, 'sent_at' => now(), 'error' => null])->save();
    }

    private function finish(SmsMessage $message, string $status, string $reason): void
    {
        $message->forceFill(['status' => $status, 'error' => mb_substr($reason, 0, 250)])->save();
    }
}
