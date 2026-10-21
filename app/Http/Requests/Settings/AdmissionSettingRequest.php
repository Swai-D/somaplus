<?php

namespace App\Http\Requests\Settings;

use App\Models\AdmissionSetting;
use Illuminate\Foundation\Http\FormRequest;

class AdmissionSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage', AdmissionSetting::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'mode' => ['required', 'in:manual,auto'],
            'prefix' => ['nullable', 'string', 'max:10', 'regex:/^[A-Za-z0-9]+$/'],
            'separator' => ['required_if:mode,auto', 'nullable', 'in:none,/,-,.,_'],
            'year_format' => ['required_if:mode,auto', 'nullable', 'in:none,YYYY,YY'],
            'digits' => ['required_if:mode,auto', 'nullable', 'integer', 'between:1,8'],
            'start_at' => ['nullable', 'integer', 'min:1', 'max:99999999'],
        ];
    }
}
