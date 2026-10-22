<?php

namespace App\Http\Requests\Settings;

use App\Models\SchoolProfile;
use Illuminate\Foundation\Http\FormRequest;

class SchoolProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage', SchoolProfile::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9][0-9 ()\/-]{4,}$/'],
            'email' => ['nullable', 'email:rfc', 'max:190'],
            'address' => ['nullable', 'string', 'max:250'],
        ];
    }
}
