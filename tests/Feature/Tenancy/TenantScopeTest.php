<?php

use App\Models\Tenant;
use App\Tenancy\TenancyViolation;
use App\Tenancy\TenantContext;
use Tests\Support\TenantNote;

beforeEach(function () {
    createTenantNotesTable();

    $this->schoolA = Tenant::factory()->create();
    $this->schoolB = Tenant::factory()->create();

    TenantContext::run($this->schoolA, fn () => TenantNote::create(['body' => 'for A']));
    TenantContext::run($this->schoolB, fn () => TenantNote::create(['body' => 'for B']));
});

it('only returns rows of the current tenant', function () {
    $bodies = TenantContext::run($this->schoolA, fn () => TenantNote::pluck('body')->all());

    expect($bodies)->toBe(['for A']);
});

it('cannot find another tenant\'s row by id', function () {
    $idOfB = TenantContext::runAsSystem(
        fn () => TenantNote::where('body', 'for B')->value('id'),
    );

    $found = TenantContext::run($this->schoolA, fn () => TenantNote::find($idOfB));

    expect($found)->toBeNull();
});

it('returns nothing when no tenant is set (fails closed)', function () {
    expect(TenantNote::count())->toBe(0);
});

it('sees every tenant only inside runAsSystem', function () {
    $all = TenantContext::runAsSystem(fn () => TenantNote::pluck('body')->sort()->values()->all());

    expect($all)->toBe(['for A', 'for B'])
        ->and(TenantContext::instance()->isSystem())->toBeFalse()
        ->and(TenantNote::count())->toBe(0);
});

it('gives new rows the current tenant', function () {
    $note = TenantContext::run($this->schoolA, fn () => TenantNote::create(['body' => 'another']));

    expect($note->tenant_id)->toBe($this->schoolA->id);
});

it('refuses to create a row without a tenant', function () {
    TenantNote::create(['body' => 'orphan']);
})->throws(TenancyViolation::class, 'requires a current tenant');

it('refuses to create a row for another tenant', function () {
    TenantContext::run($this->schoolA, function () {
        $note = new TenantNote(['body' => 'sneaky']);
        $note->tenant_id = $this->schoolB->id;
        $note->save();
    });
})->throws(TenancyViolation::class, 'other than the current one');

it('lets system operations create a row for a given tenant', function () {
    $note = TenantContext::runAsSystem(function () {
        $note = new TenantNote(['body' => 'by system']);
        $note->tenant_id = $this->schoolB->id;
        $note->save();

        return $note;
    });

    expect($note->tenant_id)->toBe($this->schoolB->id);
});

it('refuses to move a row to another tenant', function () {
    TenantContext::run($this->schoolA, function () {
        $note = TenantNote::firstOrFail();
        $note->tenant_id = $this->schoolB->id;
        $note->save();
    });
})->throws(TenancyViolation::class, 'cannot be moved');

it('restores the previous tenant after run, including nested runs', function () {
    TenantContext::run($this->schoolA, function () {
        TenantContext::run($this->schoolB, function () {
            expect(TenantContext::instance()->currentId())->toBe($this->schoolB->id);
        });

        expect(TenantContext::instance()->currentId())->toBe($this->schoolA->id);
    });

    expect(TenantContext::instance()->current())->toBeNull();
});

it('restores the previous tenant even when the work throws', function () {
    try {
        TenantContext::run($this->schoolA, fn () => throw new RuntimeException('boom'));
    } catch (RuntimeException) {
        // expected
    }

    expect(TenantContext::instance()->current())->toBeNull();
});

it('does not keep the tenant once scoped instances are discarded', function () {
    TenantContext::instance()->set($this->schoolA);

    app()->forgetScopedInstances();

    expect(TenantContext::instance()->current())->toBeNull();
});
