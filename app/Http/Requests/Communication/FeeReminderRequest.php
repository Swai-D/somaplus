<?php

namespace App\Http\Requests\Communication;

use App\Http\Requests\Fees\FeesRequest;
use App\Models\SmsMessage;
use Illuminate\Validation\Rule;

/** The invoices chosen on the reminders page and the language of the text. */
class FeeReminderRequest extends FeesRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('send', SmsMessage::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'invoices' => ['required', 'array', 'min:1', 'max:'.(int) config('sms.max_batch')],
            'invoices.*' => ['integer', 'distinct', $this->inSchool('invoices')],
            'locale' => ['required', 'string', Rule::in((array) config('app.available_locales', ['en', 'sw']))],
        ];
    }
}
