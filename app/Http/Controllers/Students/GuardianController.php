<?php

namespace App\Http\Controllers\Students;

use App\Domain\Students\AddGuardian;
use App\Domain\Students\RemoveGuardian;
use App\Http\Controllers\Controller;
use App\Http\Requests\Students\GuardianRequest;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class GuardianController extends Controller
{
    public function store(GuardianRequest $request, Student $student, AddGuardian $add): RedirectResponse
    {
        /** @var array{name: string, phone: string, email?: string|null, relationship: string, is_primary?: bool} $data */
        $data = $request->validated();
        $add->handle($student, $data);

        return redirect()->route('students.show', $student)->with('status', __('students.saved'));
    }

    public function destroy(Student $student, Guardian $guardian, RemoveGuardian $remove): RedirectResponse
    {
        Gate::authorize('update', $student);
        abort_unless($student->guardians()->whereKey($guardian->id)->exists(), 404);

        $remove->handle($student, $guardian);

        return redirect()->route('students.show', $student)->with('status', __('students.deleted'));
    }
}
