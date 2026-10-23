<?php

namespace App\Http\Requests\Communication;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

/** An event of the school's calendar: when it runs and what it is about. */
class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $event = $this->route('event');

        return $event instanceof Event
            ? (bool) $this->user()?->can('update', $event)
            : (bool) $this->user()?->can('create', Event::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
