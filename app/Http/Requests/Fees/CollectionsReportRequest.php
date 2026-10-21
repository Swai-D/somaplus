<?php

namespace App\Http\Requests\Fees;

use Carbon\CarbonImmutable;

/** The dates chosen on the collections report (query string; no personal data). At most a year at a time. */
class CollectionsReportRequest extends FeesRequest
{
    public const MAX_DAYS = 366;

    public function authorize(): bool
    {
        return (bool) $this->user()?->can('fees.view');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from', function (string $attribute, mixed $value, \Closure $fail): void {
                $from = CarbonImmutable::createFromFormat('!Y-m-d', (string) $this->input('from'));
                $to = CarbonImmutable::createFromFormat('!Y-m-d', (string) $value);
                if ($from !== null && $to !== null && $from->diffInDays($to) >= self::MAX_DAYS) {
                    $fail(__('fees.report.errors.range', ['days' => self::MAX_DAYS]));
                }
            }],
        ];
    }

    public function from(): CarbonImmutable
    {
        return CarbonImmutable::createFromFormat('!Y-m-d', (string) $this->validated('from', $this->validated('to', now()->toDateString()))) ?? CarbonImmutable::today();
    }

    public function to(): CarbonImmutable
    {
        return CarbonImmutable::createFromFormat('!Y-m-d', (string) $this->validated('to', $this->from()->toDateString())) ?? CarbonImmutable::today();
    }
}
