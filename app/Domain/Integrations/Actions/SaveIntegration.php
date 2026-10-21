<?php

namespace App\Domain\Integrations\Actions;

use App\Domain\Integrations\Services\IntegrationRegistry;
use App\Domain\Settings\RecordAudit;
use App\Models\TenantIntegration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Saves the provider a school chose for a channel, its settings and secrets, and switches it on or off.
 *
 * - Secrets are stored encrypted; a blank secret keeps the saved one; changing the provider drops the old
 *   provider's secrets.
 * - A secret field marked `generate` is filled in here when left blank and nothing is saved yet. The new value is
 *   returned once for the page to show, and is never stored in plain text or in the session.
 * - Switching a channel on needs a provider and every required field (a saved secret counts).
 * - The audit log gets the channel and the row id, never a setting or a secret.
 */
final class SaveIntegration
{
    public function __construct(private readonly IntegrationRegistry $registry, private readonly RecordAudit $audit) {}

    /**
     * @param  array{enabled: bool|int|string, driver?: string|null, config?: array<string, mixed>|null}  $data
     * @return array<string, string> secrets generated now, by field name (show once)
     */
    public function handle(string $channel, array $data): array
    {
        $enabled = filter_var($data['enabled'], FILTER_VALIDATE_BOOLEAN);
        $class = $this->registry->driver($channel, (string) ($data['driver'] ?? ''));

        if ($enabled && $class === null) {
            throw ValidationException::withMessages(['driver' => __('integrations.errors.driver_required')]);
        }

        return DB::transaction(function () use ($channel, $data, $enabled, $class): array {
            $row = TenantIntegration::query()->where('channel', $channel)->lockForUpdate()->first();
            $generated = [];

            if ($class === null) {
                // Nothing chosen: only switching off is possible, and the saved configuration is kept.
                if ($row !== null) {
                    $row->forceFill(['enabled' => false, 'updated_by' => Auth::id()])->save();
                    $this->audit->handle('integration.saved', $row);
                }

                return [];
            }

            $secrets = $row !== null && $row->driver === $class::key() ? ($row->secrets ?? []) : [];
            $settings = [];

            foreach ($class::fields() as $field) {
                $name = $field['name'];
                $value = $data['config'][$name] ?? null;
                $value = is_string($value) ? trim($value) : $value;

                if ($field['secret'] ?? false) {
                    if ($value !== null && $value !== '') {
                        $secrets[$name] = (string) $value;
                    } elseif (($field['generate'] ?? false) && ! isset($secrets[$name])) {
                        $secrets[$name] = $generated[$name] = bin2hex(random_bytes(24));
                    }
                    if ($enabled && ($field['required'] ?? false) && ! isset($secrets[$name])) {
                        throw ValidationException::withMessages(['config.'.$name => __('integrations.errors.field_required')]);
                    }

                    continue;
                }

                if ($enabled && ($field['required'] ?? false) && ($value === null || $value === '')) {
                    throw ValidationException::withMessages(['config.'.$name => __('integrations.errors.field_required')]);
                }
                $settings[$name] = ($value === null || $value === '') ? ($field['default'] ?? null) : $value;
            }

            $row ??= new TenantIntegration;
            $row->forceFill([
                'channel' => $channel,
                'driver' => $class::key(),
                'enabled' => $enabled,
                'settings' => $settings,
                'secrets' => $secrets === [] ? null : $secrets,
                'updated_by' => Auth::id(),
            ])->save();

            $this->audit->handle('integration.saved', $row);

            return $generated;
        });
    }
}
