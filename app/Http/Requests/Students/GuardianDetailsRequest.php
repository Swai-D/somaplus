<?php

namespace App\Http\Requests\Students;

use App\Domain\Students\AddGuardian;
use App\Models\Guardian;

/**
 * The details of a guardian on their own (not from a pupil's page): a name, a phone number and
 * an optional email address. The phone is checked the way it is stored, so a number that is
 * already in the school is refused.
 */
class GuardianDetailsRequest extends StudentsRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('phone'))) {
            $this->merge(['phone' => AddGuardian::normalizePhone((string) $this->input('phone'))]);
        }
    }

    public function authorize(): bool
    {
        return (bool) $this->user()?->can('create', Guardian::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $guardian = $this->route('guardian');

        return [
            'name' => ['required', 'string', 'max:150'],
            'phone' => [
                'required', 'string', 'max:30', 'regex:'.GuardianRequest::PHONE_PATTERN,
                $this->unique('guardians', 'phone', $guardian instanceof Guardian ? $guardian->id : null),
            ],
            'email' => ['nullable', 'email', 'max:150'],
        ];
    }
}
