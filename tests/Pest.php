<?php

use App\Models\AcademicYear;
use App\Models\GradingScale;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Tenant;
use App\Models\Term;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Model;
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

/**
 * Hosts used by tests that go through ResolveTenant: the central address is
 * http://somaplus.test and a school is reached at http://<slug>.somaplus.test.
 */
function useTestHosts(): void
{
    config()->set('tenancy.base_domain', 'somaplus.test');
    config()->set('tenancy.central_domains', ['somaplus.test']);
}

/**
 * A user of the given school, optionally with a role, created inside that school.
 *
 * @param  array<string, mixed>  $attributes
 */
function makeSchoolUser(Tenant $school, array $attributes = [], ?string $role = null): User
{
    return TenantContext::run($school, function () use ($attributes, $role) {
        $user = User::factory()->create($attributes);

        if ($role !== null) {
            $user->assignRole($role);
        }

        return $user;
    });
}

/**
 * A user of the given school with the given role, loaded fresh the way a request
 * would load it (so no relation cached while the test set things up).
 */
function userWithRole(Tenant $school, string $role): User
{
    $user = makeSchoolUser($school, [], $role);

    return TenantContext::runAsSystem(fn () => User::findOrFail($user->id));
}

/**
 * An academic setup for exam tests, created inside the given school: a current year with two terms,
 * two classes, two streams of the first class, two subjects and a standard grading scale
 * (A from 80, B from 70, C from 60, D from 50, F from 0).
 *
 * @return array<string, Model>
 */
function makeExamWorld(Tenant $school): array
{
    return TenantContext::run($school, function () {
        $year = AcademicYear::factory()->current()->create([
            'name' => 'Y1', 'starts_on' => now()->subYear()->toDateString(), 'ends_on' => now()->addYear()->toDateString(),
        ]);
        $form1 = SchoolClass::factory()->create(['name' => 'Form 1', 'sort_order' => 1]);

        return [
            'year' => $year,
            'term' => Term::factory()->create(['academic_year_id' => $year->id, 'name' => 'Term 1', 'starts_on' => now()->subMonths(6)->toDateString(), 'ends_on' => now()->addMonths(2)->toDateString()]),
            'term2' => Term::factory()->create(['academic_year_id' => $year->id, 'name' => 'Term 2', 'starts_on' => now()->addMonths(3)->toDateString(), 'ends_on' => now()->addMonths(6)->toDateString()]),
            'form1' => $form1,
            'form2' => SchoolClass::factory()->create(['name' => 'Form 2', 'sort_order' => 2]),
            'streamA' => Stream::factory()->create(['school_class_id' => $form1->id, 'name' => 'A']),
            'streamB' => Stream::factory()->create(['school_class_id' => $form1->id, 'name' => 'B']),
            'math' => Subject::factory()->create(['name' => 'Mathematics']),
            'english' => Subject::factory()->create(['name' => 'English']),
            'scale' => GradingScale::factory()->standard()->create(['name' => 'Standard']),
        ];
    });
}
