<?php

namespace App\Domain\Communication;

use App\Domain\Communication\Jobs\SendSmsMessage;
use App\Domain\Communication\Sms\SchoolSms;
use App\Domain\Fees\InvoiceBalance;
use App\Domain\Settings\RecordAudit;
use App\Models\Invoice;
use App\Models\SmsMessage;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;

/**
 * Queues one fee reminder text for every guardian with a valid mobile number of each chosen invoice that still owes
 * money. Needs the school's SMS to be switched on. One reminder per invoice and guardian per day (a second press the
 * same day adds nothing). Each text is sent by SendSmsMessage, which works out the balance again, so a payment made
 * meanwhile cancels the reminder. Every queued message is written to the audit log (ids only).
 */
final class QueueFeeReminders
{
    public function __construct(private readonly SchoolSms $sms, private readonly RecordAudit $audit) {}

    /**
     * @param  list<int>  $invoiceIds
     * @return array{queued: int, no_phone: int, already: int, paid: int}
     */
    public function handle(array $invoiceIds, string $locale, ?User $user): array
    {
        if (! $this->sms->isActive()) {
            throw ValidationException::withMessages(['invoices' => __('communication.errors.sms_off')]);
        }
        if (! in_array($locale, (array) config('app.available_locales', ['en', 'sw']), true)) {
            throw ValidationException::withMessages(['locale' => __('communication.errors.locale')]);
        }
        if (count($invoiceIds) === 0 || count($invoiceIds) > (int) config('sms.max_batch')) {
            throw ValidationException::withMessages(['invoices' => __('communication.errors.batch', ['max' => config('sms.max_batch')])]);
        }

        $result = ['queued' => 0, 'no_phone' => 0, 'already' => 0, 'paid' => 0];
        $today = CarbonImmutable::today();

        $invoices = Invoice::query()->with('student.guardians')->whereIn('id', $invoiceIds)->get();
        foreach ($invoices as $invoice) {
            if (InvoiceBalance::of($invoice)->balance() <= 0) {
                $result['paid']++;

                continue;
            }

            $guardians = $invoice->student->guardians->filter(fn ($guardian): bool => PhoneNumber::normalize($guardian->phone) !== null);
            if ($guardians->isEmpty()) {
                $result['no_phone']++;

                continue;
            }

            foreach ($guardians as $guardian) {
                $message = new SmsMessage;
                $message->forceFill([
                    'invoice_id' => $invoice->id, 'guardian_id' => $guardian->id, 'sent_by' => $user?->id,
                    'kind' => SmsMessage::KIND_REMINDER, 'locale' => $locale, 'day' => $today->toDateString(), 'status' => SmsMessage::QUEUED,
                ]);

                try {
                    $message->save();
                } catch (UniqueConstraintViolationException) {
                    $result['already']++;

                    continue;
                }

                $this->audit->handle('reminder.queued', $message);
                dispatch((new SendSmsMessage($message->id))->forCurrentTenant());
                $result['queued']++;
            }
        }

        return $result;
    }
}
