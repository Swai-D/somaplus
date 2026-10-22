<?php

namespace App\Http\Controllers\Teachers;

use App\Domain\Settings\RecordAudit;
use App\Domain\Teachers\ChangeTeacherStatus;
use App\Domain\Teachers\RemoveTeacher;
use App\Domain\Teachers\SaveTeacher;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teachers\TeacherRequest;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * The teachers of a school: their list, their page, adding one, changing one and
 * putting one out of duty. A teacher is a person who signs in, so the section also
 * opens the account of the person.
 */
class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Teacher::class);

        $status = $request->query('status');

        $query = Teacher::query()
            ->select('teachers.*')
            ->join('users', 'users.id', '=', 'teachers.user_id')
            ->with(['user', 'subjects', 'classes'])
            ->orderBy('users.name');

        if ($status === Teacher::INACTIVE) {
            $query->where('teachers.status', Teacher::INACTIVE);
        } elseif ($status === Teacher::ACTIVE) {
            $query->where('teachers.status', Teacher::ACTIVE);
        }

        $teachers = $query->limit(200)->get();

        return view('teachers.index', [
            'teachers' => $teachers,
            'status' => $status,
        ] + $this->choices());
    }

    /**
     * Searching is a POST so the name or number typed never ends up in a URL, a log or the browser history.
     */
    public function search(Request $request, RecordAudit $audit): View
    {
        Gate::authorize('viewAny', Teacher::class);

        $term = trim((string) $request->validate(['term' => ['required', 'string', 'min:2', 'max:100']])['term']);
        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], mb_strtolower($term)).'%';

        $teachers = Teacher::query()
            ->select('teachers.*')
            ->join('users', 'users.id', '=', 'teachers.user_id')
            ->with(['user', 'subjects', 'classes'])
            ->where(function ($q) use ($like): void {
                $q->whereRaw('lower(users.name) like ?', [$like])
                    ->orWhereRaw('lower(users.email) like ?', [$like])
                    ->orWhereRaw('lower(teachers.phone) like ?', [$like])
                    ->orWhereRaw('lower(coalesce(teachers.staff_number, \'\')) like ?', [$like]);
            })
            ->orderBy('users.name')
            ->limit(50)
            ->get();

        $audit->handle('teacher.searched', new Teacher);

        return view('teachers.index', [
            'teachers' => $teachers,
            'term' => $term,
            'status' => null,
        ] + $this->choices());
    }

    /**
     * The form of a new teacher is a pop-up of the list, as in the template, so this address
     * only opens the list with the pop-up ready.
     */
    public function create(): RedirectResponse
    {
        Gate::authorize('create', Teacher::class);

        return redirect()->route('teachers.index', ['add' => 'teacher-modal']);
    }

    public function store(TeacherRequest $request, SaveTeacher $save): RedirectResponse
    {
        /** @var array{name: string, email: string, password?: string|null, phone: string, staff_number?: string|null, gender?: string|null, date_of_birth?: string|null, address?: string|null, qualification?: string|null, contract_type?: string|null, shift?: string|null, joined_on: string, notes?: string|null, subjects?: array<int>, classes?: array<int>} $data */
        $data = $request->validated();

        $teacher = $save->handle(null, $data);

        return redirect()->route('teachers.show', $teacher)->with('status', __('teachers.saved'));
    }

    public function show(Teacher $teacher, RecordAudit $audit): View
    {
        Gate::authorize('view', $teacher);

        $audit->handle('teacher.viewed', $teacher);

        return view('teachers.show', [
            'teacher' => $teacher->load(['user', 'subjects', 'classes']),
        ] + $this->choices());
    }

    public function edit(Teacher $teacher): View
    {
        Gate::authorize('update', $teacher);

        return view('teachers.edit', [
            'teacher' => $teacher->load(['user', 'subjects', 'classes']),
        ] + $this->choices());
    }

    public function update(TeacherRequest $request, Teacher $teacher, SaveTeacher $save): RedirectResponse
    {
        Gate::authorize('update', $teacher);

        /** @var array{name: string, email: string, password?: string|null, phone: string, staff_number?: string|null, gender?: string|null, date_of_birth?: string|null, address?: string|null, qualification?: string|null, contract_type?: string|null, shift?: string|null, joined_on: string, notes?: string|null, subjects?: array<int>, classes?: array<int>} $data */
        $data = $request->validated();

        $save->handle($teacher, $data);

        return redirect()->route('teachers.show', $teacher)->with('status', __('teachers.saved'));
    }

    /**
     * Puts a teacher out of duty, or back into it. The record and the marks the person
     * entered stay as they are.
     */
    public function status(Teacher $teacher, ChangeTeacherStatus $change): RedirectResponse
    {
        Gate::authorize('update', $teacher);

        $status = $teacher->isActive() ? Teacher::INACTIVE : Teacher::ACTIVE;

        $change->handle($teacher, $status);

        return redirect()->route('teachers.show', $teacher)
            ->with('status', $status === Teacher::ACTIVE ? __('teachers.activated') : __('teachers.deactivated'));
    }

    /**
     * Takes a teacher out of the school. The account stays, so the marks the person entered
     * keep their name.
     */
    public function destroy(Teacher $teacher, RemoveTeacher $remove): RedirectResponse
    {
        Gate::authorize('delete', $teacher);

        $remove->handle($teacher);

        return redirect()->route('teachers.index')->with('status', __('teachers.deleted'));
    }

    /**
     * The subjects and the classes the school has, for the form of a teacher.
     *
     * @return array<string, mixed>
     */
    private function choices(): array
    {
        return [
            'subjects' => Subject::query()->orderBy('name')->get(),
            'classes' => SchoolClass::query()->orderBy('sort_order')->orderBy('name')->get(),
        ];
    }
}
