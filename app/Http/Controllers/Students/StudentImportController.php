<?php

namespace App\Http\Controllers\Students;

use App\Domain\Students\ImportStudents;
use App\Domain\Students\StudentImportException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Students\StudentImportRequest;
use App\Models\AcademicYear;
use App\Models\AdmissionSetting;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Support\Csv;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Download the CSV template and load many new students (with class and parent) from a filled-in file. */
class StudentImportController extends Controller
{
    public function create(): View
    {
        Gate::authorize('create', Student::class);

        return view('students.import', [
            'columns' => ImportStudents::COLUMNS,
            'required' => ImportStudents::required(),
            'auto' => AdmissionSetting::current()->isAuto(),
            'max' => (int) config('students.import_max_rows'),
            'year' => AcademicYear::query()->where('is_current', true)->first(),
            'classes' => SchoolClass::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function template(): StreamedResponse
    {
        Gate::authorize('create', Student::class);

        // The example rows use the school's own first class when there is one, so the template matches the school.
        $class = SchoolClass::query()->orderBy('sort_order')->orderBy('name')->value('name') ?? 'Form 1';

        // When Somaplus makes the admission numbers the example rows leave that cell empty.
        $auto = AdmissionSetting::current()->isAuto();

        return Csv::download('students-template.csv', ImportStudents::COLUMNS, [
            [$auto ? '' : 'A-001', 'Amina', 'Hassan', 'Juma', 'female', '2012-05-06', '2026-01-10', $class, '', 'Fatuma Juma', '0712345678', 'fatuma@example.com', 'mother'],
            [$auto ? '' : 'A-002', 'Baraka', '', 'Mushi', 'male', '2011-11-20', '2026-01-10', $class, '', 'Joseph Mushi', '0755123456', '', 'father'],
        ]);
    }

    public function store(StudentImportRequest $request, ImportStudents $import): RedirectResponse
    {
        try {
            $result = $import->handle($request->file('file')?->getRealPath() ?: '');
        } catch (StudentImportException $e) {
            return redirect()->route('students.import')->with(['import_problems' => $e->problems, 'import_total' => $e->total]);
        }

        return redirect()->route('students.import')->with('import_result', $result);
    }
}
