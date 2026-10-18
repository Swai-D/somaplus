<?php

namespace App\Tenancy;

use App\Models\Tenant;
use Closure;

/**
 * Holds the tenant (school) the current request or job is working for.
 *
 * It is bound as a scoped instance, so the container discards it between
 * requests and between queued jobs: nothing is retained.
 */
final class TenantContext
{
    private ?Tenant $tenant = null;

    private int $systemDepth = 0;

    public static function instance(): self
    {
        return app(self::class);
    }

    public function current(): ?Tenant
    {
        return $this->tenant;
    }

    public function currentId(): ?string
    {
        return $this->tenant?->id;
    }

    public function set(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function clear(): void
    {
        $this->tenant = null;
    }

    /**
     * True while inside runAsSystem(): tenant scoping is switched off.
     */
    public function isSystem(): bool
    {
        return $this->systemDepth > 0;
    }

    /**
     * Run work for one tenant, then restore whatever context was active before.
     *
     * @template TReturn
     *
     * @param  Closure(): TReturn  $callback
     * @return TReturn
     */
    public static function run(Tenant $tenant, Closure $callback): mixed
    {
        $context = self::instance();
        $previous = $context->tenant;
        $context->tenant = $tenant;

        try {
            return $callback();
        } finally {
            $context->tenant = $previous;
        }
    }

    /**
     * Run work that is allowed to see every tenant's data.
     *
     * Only for reviewed super-admin and system operations. Every call site must
     * be justified in code review; prefer run() with a specific tenant.
     *
     * @template TReturn
     *
     * @param  Closure(): TReturn  $callback
     * @return TReturn
     */
    public static function runAsSystem(Closure $callback): mixed
    {
        $context = self::instance();
        $context->systemDepth++;

        try {
            return $callback();
        } finally {
            $context->systemDepth--;
        }
    }
}
