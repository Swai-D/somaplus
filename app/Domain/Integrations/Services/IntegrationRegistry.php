<?php

namespace App\Domain\Integrations\Services;

use App\Domain\Integrations\Contracts\Driver;

/** Knows which channels exist and which drivers each one offers (from config/integrations.php). */
class IntegrationRegistry
{
    /** @return list<string> channels that offer at least one driver */
    public function channels(): array
    {
        $channels = [];
        foreach (array_keys((array) config('integrations.channels')) as $channel) {
            if ($this->drivers((string) $channel) !== []) {
                $channels[] = (string) $channel;
            }
        }

        return $channels;
    }

    /** @return array<string, class-string<Driver>> driver key => class */
    public function drivers(string $channel): array
    {
        $drivers = [];

        foreach ((array) config("integrations.channels.{$channel}.drivers", []) as $class) {
            $drivers[$class::key()] = $class;
        }

        return $drivers;
    }

    /** @return class-string<Driver>|null */
    public function driver(string $channel, string $key): ?string
    {
        return $this->drivers($channel)[$key] ?? null;
    }
}
