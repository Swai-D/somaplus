<?php

namespace App\Http\Requests\Settings;

use App\Domain\Integrations\Services\IntegrationRegistry;
use App\Models\TenantIntegration;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Saves one channel's provider choice and configuration; the rules come from the chosen driver's own field list. */
class IntegrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage', TenantIntegration::class);
    }

    public function channel(): string
    {
        $channel = (string) $this->route('channel');

        abort_unless(in_array($channel, app(IntegrationRegistry::class)->channels(), true), 404);

        return $channel;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $drivers = app(IntegrationRegistry::class)->drivers($this->channel());

        $rules = [
            'enabled' => ['required', 'boolean'],
            'driver' => [Rule::requiredIf(fn (): bool => $this->boolean('enabled')), 'nullable', 'string', Rule::in(array_keys($drivers))],
        ];

        $class = $drivers[(string) $this->input('driver')] ?? null;
        if ($class === null) {
            return $rules;
        }

        foreach ($class::fields() as $field) {
            $rules['config.'.$field['name']] = [
                'nullable',
                ...match ($field['type']) {
                    'select' => ['string', Rule::in(array_keys($field['options'] ?? []))],
                    default => ['string', 'min:'.($field['min'] ?? 1), 'max:'.($field['max'] ?? 255)],
                },
            ];
        }

        return $rules;
    }
}
