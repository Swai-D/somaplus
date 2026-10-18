<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class)->in('Feature');

/**
 * A throwaway table for Tests\Support\TenantNote, a model that uses
 * BelongsToTenant, so tenancy behaviour can be tested before any real
 * tenant-owned table exists.
 */
function createTenantNotesTable(): void
{
    Schema::dropIfExists('tenant_notes');

    Schema::create('tenant_notes', function (Blueprint $table) {
        $table->id();
        $table->foreignUlid('tenant_id')->constrained('tenants');
        $table->string('body');
        $table->timestamps();
    });
}
