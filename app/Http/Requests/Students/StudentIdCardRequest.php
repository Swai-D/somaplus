<?php

namespace App\Http\Requests\Students;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;

class StudentIdCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('printIdCard', Student::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1', 'max:'.(int) config('students.id_card_max')],
            'ids.*' => ['integer', 'distinct'],
        ];
    }

    /** @return list<int> */
    public function studentIds(): array
    {
        return array_values(array_map('intval', (array) $this->validated('ids')));
    }
}
