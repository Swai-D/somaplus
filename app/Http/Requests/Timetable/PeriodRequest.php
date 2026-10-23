<?php

namespace App\Http\Requests\Timetable;

use App\Models\TimetablePeriod;

class PeriodRequest extends TimetableRequest
{
    public function authorize(): bool
    {
        $period = $this->route('period');

        return $period instanceof TimetablePeriod
            ? (bool) $this->user()?->can('update', $period)
            : (bool) $this->user()?->can('create', TimetablePeriod::class);
    }

    protected function prepareForValidation(): void
    {
        // An unticked box sends nothing.
        $this->merge(['is_break' => $this->boolean('is_break')]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $period = $this->route('period');
        $ignore = $period instanceof TimetablePeriod ? $period->id : null;

        return [
            'name' => ['required', 'string', 'max:50', $this->unique('timetable_periods', 'name', $ignore)],
            'starts_at' => ['required', 'date_format:H:i'],
            'ends_at' => ['required', 'date_format:H:i'],
            'is_break' => ['boolean'],
        ];
    }
}
