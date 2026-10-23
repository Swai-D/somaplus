<?php

namespace App\Http\Requests\Communication;

use App\Models\Notice;
use Illuminate\Foundation\Http\FormRequest;

/** A notice on the school's notice board: the day it speaks from and what it says. */
class NoticeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $notice = $this->route('notice');

        return $notice instanceof Notice
            ? (bool) $this->user()?->can('update', $notice)
            : (bool) $this->user()?->can('create', Notice::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'day' => ['required', 'date'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
