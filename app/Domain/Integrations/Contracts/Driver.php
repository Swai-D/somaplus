<?php

namespace App\Domain\Integrations\Contracts;

/**
 * A pluggable provider for one integration channel. The driver describes the settings it needs and
 * Settings > Integrations renders and validates the form from that description.
 */
interface Driver
{
    /** Stable identifier stored with the school's configuration, for example "signed_webhook". */
    public static function key(): string;

    /** Translation key of the name shown in the provider picker. */
    public static function label(): string;

    /**
     * The configuration fields. `secret` fields are stored encrypted and never shown again; a blank secret
     * field keeps what is saved. A secret field with `generate` is filled in by Somaplus when left blank the
     * first time, and shown once.
     *
     * @return list<array{name: string, label: string, type: 'text'|'password'|'textarea'|'select', required?: bool, secret?: bool, generate?: bool, options?: array<string, string>, default?: string, min?: int, max?: int}>
     */
    public static function fields(): array;
}
