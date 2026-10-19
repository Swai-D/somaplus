<?php

namespace App\Http\Controllers\Students;

use App\Domain\Settings\RecordAudit;
use App\Domain\Students\EnrollStudent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Students\EnrollmentRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;

class EnrollmentController extends Controller
{
    public function store(EnrollmentRequest $request, Student $student, EnrollStudent $enroll, RecordAudit $audit): RedirectResponse
    {
        /** @var array{academic_year_id: int|string, school_class_id: int|string, stream_id?: int|string|null, enrolled_on: string} $data */
        $data = $request->validated();
        $enroll->handle($student, $data);
        $audit->handle('student.placed', $student);

        return redirect()->route('students.show', $student)->with('status', __('students.saved'));
    }
}
