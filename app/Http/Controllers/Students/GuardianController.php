<?php

namespace App\Http\Controllers\Students;

use App\Domain\Settings\RecordAudit;
use App\Domain\Students\AddGuardian;
use App\Domain\Students\CreateGuardian;
use App\Domain\Students\CurrentPlacements;
use App\Domain\Students\RemoveGuardian;
use App\Domain\Students\SaveGuardian;
use App\Http\Controllers\Controller;
use App\Http\Requests\Students\GuardianDetailsRequest;
use App\Http\Requests\Students\GuardianRequest;
use App\Http\Requests\Students\GuardianStudentRequest;
use App\Models\AcademicYear;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The guardians of a school: their list, their page, adding one, changing one and the pupils
 * they are the parent or guardian of. A guardian is also linked from a pupil's page, which is
 * what the two actions at the bottom do.
 */
class GuardianController extends Controller
{
    public function index(CurrentPlacements $placements): View
    {
        Gate::authorize('viewAny', Guardian::class);

        $guardians = Guardian::query()
            ->with('students')
            ->orderBy('name')
            ->limit(200)
            ->get();

        $year = AcademicYear::query()->where('is_current', true)->first();

        return view('students.guardians.index', [
            'guardians' => $guardians,
            'placements' => $placements->for($guardians->flatMap(fn (Guardian $g) => $g->students), $year),
            'year' => $year,
        ]);
    }

    /**
     * Opens the list with the pop-up of a new guardian ready, so the menu can lead to it.
     */
    public function add(): RedirectResponse
    {
        Gate::authorize('create', Guardian::class);

        return redirect()->route('guardians.index', ['add' => 1]);
    }

    public function store(GuardianDetailsRequest $request, CreateGuardian $create): RedirectResponse
    {
        /** @var array{name: string, phone: string, email?: string|null} $data */
        $data = $request->validated();

        $guardian = $create->handle($data);

        if (! $guardian->wasRecentlyCreated) {
            return redirect()->route('guardians.show', $guardian)
                ->with('status', __('students.guardians.already_there'));
        }

        return redirect()->route('guardians.show', $guardian)->with('status', __('students.guardians.saved'));
    }

    public function show(Guardian $guardian, CurrentPlacements $placements): View
    {
        Gate::authorize('view', $guardian);

        $students = $guardian->students()->orderBy('first_name')->orderBy('last_name')->get();
        $year = AcademicYear::query()->where('is_current', true)->first();

        return view('students.guardians.show', [
            'guardian' => $guardian,
            'students' => $students,
            'placements' => $placements->for($students, $year),
            'year' => $year,
            'candidates' => Student::query()
                ->where('status', Student::ACTIVE)
                ->whereDoesntHave('guardians', fn ($q) => $q->where('guardians.id', $guardian->id))
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
        ]);
    }

    /**
     * Changes what the school knows about a guardian. The form is the pop-up of the list, so
     * the page comes back to the list.
     */
    public function update(GuardianDetailsRequest $request, Guardian $guardian, SaveGuardian $save): RedirectResponse
    {
        Gate::authorize('update', $guardian);

        /** @var array{name: string, phone: string, email?: string|null} $data */
        $data = $request->validated();
        $save->handle($guardian, $data);

        return redirect()->route('guardians.show', $guardian)->with('status', __('students.guardians.saved'));
    }

    public function destroy(Guardian $guardian): RedirectResponse
    {
        Gate::authorize('delete', $guardian);

        if ($guardian->hasPortalAccess()) {
            return redirect()->route('guardians.show', $guardian)->with('delete', __('family.errors.revoke_first'));
        }

        // A guardian who is still the parent of a pupil is not deleted; the link is removed first.
        if ($guardian->students()->exists()) {
            return redirect()->route('guardians.show', $guardian)
                ->with('delete', __('students.guardians.has_students'));
        }

        $guardian->delete();

        return redirect()->route('guardians.index')->with('status', __('students.guardians.deleted'));
    }

    /** Links a pupil to this guardian. */
    public function addStudent(GuardianStudentRequest $request, Guardian $guardian, AddGuardian $add, RecordAudit $audit): RedirectResponse
    {
        Gate::authorize('update', $guardian);

        $student = $request->student();
        abort_if($student === null, 422);

        if ($guardian->students()->whereKey($student->id)->exists()) {
            return redirect()->route('guardians.show', $guardian)
                ->with('delete', __('students.guardians.already_linked'));
        }

        $add->handle($student, [
            'name' => $guardian->name,
            'phone' => $guardian->phone,
            'email' => $guardian->email,
            'relationship' => $request->validated('relationship'),
            'is_primary' => (bool) $request->validated('is_primary', false),
        ]);

        $audit->handle('guardian.linked', $student);

        return redirect()->route('guardians.show', $guardian)->with('status', __('students.guardians.linked'));
    }

    /** Takes a pupil off this guardian. */
    public function removeStudent(Guardian $guardian, Student $student, RemoveGuardian $remove): RedirectResponse
    {
        Gate::authorize('update', $guardian);
        abort_unless($guardian->students()->whereKey($student->id)->exists(), 404);

        $remove->handle($student, $guardian);

        return redirect()->route('guardians.show', $guardian)->with('status', __('students.guardians.unlinked'));
    }

    /**
     * The guardian of a pupil, from the pupil's page.
     */
    public function linkToStudent(GuardianRequest $request, Student $student, AddGuardian $add): RedirectResponse
    {
        /** @var array{name: string, phone: string, email?: string|null, relationship: string, is_primary?: bool} $data */
        $data = $request->validated();
        $add->handle($student, $data);

        return redirect()->route('students.show', $student)->with('status', __('students.saved'));
    }

    /**
     * Takes a guardian off a pupil, from the pupil's page.
     */
    public function unlinkFromStudent(Student $student, Guardian $guardian, RemoveGuardian $remove): RedirectResponse
    {
        Gate::authorize('update', $student);
        abort_unless($student->guardians()->whereKey($guardian->id)->exists(), 404);

        $remove->handle($student, $guardian);

        return redirect()->route('students.show', $student)->with('status', __('students.deleted'));
    }
}
