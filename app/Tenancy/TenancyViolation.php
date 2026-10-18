<?php

namespace App\Tenancy;

use LogicException;

/**
 * Thrown when code tries to read or write tenant data without a valid tenant.
 * It is a programming error, never a user-facing condition.
 */
final class TenancyViolation extends LogicException
{
    public static function noTenant(string $what): self
    {
        return new self("{$what} requires a current tenant, but none is set.");
    }

    public static function otherTenant(string $model): self
    {
        return new self("{$model} cannot be written for a tenant other than the current one.");
    }

    public static function tenantChanged(string $model): self
    {
        return new self("{$model} cannot be moved to another tenant.");
    }

    public static function superAdminInTenant(): self
    {
        return new self('A super-admin belongs to the platform and cannot be created for, or inside, a school.');
    }

    public static function superAdminChanged(): self
    {
        return new self('A user cannot be promoted to or demoted from super-admin after creation.');
    }

    public static function jobWithoutTenant(string $job): self
    {
        return new self("Queued job {$job} carries no tenant id; call forCurrentTenant() when dispatching it.");
    }

    public static function unknownTenant(string $tenantId): self
    {
        return new self("Queued job refers to tenant {$tenantId}, which does not exist.");
    }
}
