<?php

namespace App\Domain\Integrations\Services;

use App\Models\TenantIntegration;

/**
 * Answers "is this channel active for the current school, and with which driver, settings and secrets?".
 * Active means: a row exists, it is switched on, and its driver is still offered. Runs inside the school's context.
 */
class IntegrationManager
{
    public function __construct(private readonly IntegrationRegistry $registry) {}

    /** @return array{driver: string, settings: array<string, mixed>, secrets: array<string, mixed>}|null */
    public function active(string $channel): ?array
    {
        $row = TenantIntegration::query()->where('channel', $channel)->first();

        if ($row === null || ! $row->enabled || $this->registry->driver($channel, $row->driver) === null) {
            return null;
        }

        return ['driver' => $row->driver, 'settings' => $row->settings ?? [], 'secrets' => $row->secrets ?? []];
    }

    public function isActive(string $channel, ?string $driver = null): bool
    {
        $active = $this->active($channel);

        return $active !== null && ($driver === null || $active['driver'] === $driver);
    }
}
