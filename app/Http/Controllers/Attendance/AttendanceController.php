<?php

namespace App\Http\Controllers\Attendance;

use App\Domain\Attendance\AttendanceSummary;
use App\Domain\Attendance\ClassRoll;
use App\Domain\Attendance\RecordAttendance;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\SaveAttendanceRequest;
use App\Http\Requests\Attendance\ViewAttendanceRequest;
use App\Models\AttendanceRecord;
use App\Models\SchoolClass;
use App\Models\Stream;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(ViewAttendanceRequest $request, ClassRoll $roll): View
    {
        $class = $this->classFrom($request);
        $stream = $this->streamFrom($request);
        $date = (string) ($request->validated('date') ?? now()->toDateString());
        $year = $roll->yearOn($date);

        $students = collect();
        $records = collect();
        if ($class !== null && $year !== null) {
            $students = $roll->students($class, $stream, $year);
            $records = AttendanceRecord::query()
                ->whereIn('student_id', $students->modelKeys())
                ->whereDate('attended_on', $date)
                ->get()->keyBy('student_id');
        }

        return view('attendance.index', $this->choices() + [
            'class' => $class, 'stream' => $stream, 'date' => $date, 'year' => $year,
            'students' => $students, 'records' => $records,
        ]);
    }

    public function store(SaveAttendanceRequest $request, RecordAttendance $record): RedirectResponse
    {
        $class = SchoolClass::query()->findOrFail($request->integer('school_class_id'));
        $stream = $request->filled('stream_id') ? Stream::query()->findOrFail($request->integer('stream_id')) : null;

        /** @var array<int|string, array{status: string, note?: string|null}> $rows */
        $rows = $request->validated('rows');
        $record->handle($class, $stream, (string) $request->validated('date'), $rows);

        return redirect()->route('attendance.index', array_filter([
            'school_class_id' => $class->id, 'stream_id' => $stream?->id, 'date' => $request->validated('date'),
        ]))->with('status', __('attendance.saved'));
    }

    public function summary(ViewAttendanceRequest $request, AttendanceSummary $summary): View
    {
        $class = $this->classFrom($request);
        $stream = $this->streamFrom($request);
        $to = (string) ($request->validated('to') ?? now()->toDateString());
        $from = (string) ($request->validated('from') ?? now()->startOfMonth()->toDateString());

        return view('attendance.summary', $this->choices() + [
            'class' => $class, 'stream' => $stream, 'from' => $from, 'to' => $to,
            'rows' => $class === null ? [] : $summary->handle($class, $stream, $from, $to),
        ]);
    }

    private function classFrom(ViewAttendanceRequest $request): ?SchoolClass
    {
        return $request->filled('school_class_id') ? SchoolClass::query()->find($request->integer('school_class_id')) : null;
    }

    private function streamFrom(ViewAttendanceRequest $request): ?Stream
    {
        return $request->filled('stream_id') ? Stream::query()->find($request->integer('stream_id')) : null;
    }

    /**
     * @return array{classes: Collection<int, SchoolClass>, streams: Collection<int, Stream>}
     */
    private function choices(): array
    {
        return [
            'classes' => SchoolClass::query()->orderBy('sort_order')->orderBy('name')->get(),
            'streams' => Stream::query()->with('schoolClass')->orderBy('school_class_id')->orderBy('name')->get(),
        ];
    }
}
