<?php

namespace App\Domain\Students;

use App\Domain\Settings\RecordAudit;
use App\Http\Requests\Students\GuardianRequest;
use App\Models\AcademicYear;
use App\Models\AdmissionSetting;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Student;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Adds many new students from a CSV file, all or nothing.
 *
 * Every row is checked first and nothing is written unless the whole file is clean. A row adds one student
 * (through SaveStudent, so the audit line and the first placement are the same as for a student typed by
 * hand), placed in the class and stream named in the row for the school's current academic year, and
 * optionally one parent or guardian (through AddGuardian, so a parent with several children is saved once,
 * by phone number). Existing students are never changed: an admission number that is already in the school
 * is a problem, so sending the same file twice adds nothing. Classes and streams are matched by name and are
 * never created here. The file is read from its temporary path and is not kept; no personal data is logged.
 */
final class ImportStudents
{
    /** Columns a file may contain, in template order. */
    public const COLUMNS = [
        'admission_number', 'first_name', 'middle_name', 'last_name', 'gender', 'date_of_birth', 'admitted_on',
        'class', 'stream', 'guardian_name', 'guardian_phone', 'guardian_email', 'guardian_relationship',
    ];

    public const REQUIRED = ['admission_number', 'first_name', 'last_name', 'gender', 'date_of_birth'];

    private const MAX_PROBLEMS = 50;

    public function __construct(
        private readonly SaveStudent $save,
        private readonly AddGuardian $addGuardian,
        private readonly RecordAudit $audit,
        private readonly AdmissionNumbers $numbers,
    ) {}

    /**
     * The columns every row must fill. When Somaplus makes the admission numbers the column may stay empty:
     * a number typed in a row (from an old register) is kept, an empty one is made.
     *
     * @return list<string>
     */
    public static function required(): array
    {
        return AdmissionSetting::current()->isAuto() ? array_values(array_diff(self::REQUIRED, ['admission_number'])) : self::REQUIRED;
    }

    /** @return array{created: int, guardians: int, placed: int} */
    public function handle(string $path): array
    {
        [$header, $rows] = $this->read($path);

        $year = AcademicYear::query()->where('is_current', true)->first();
        [$clean, $problems] = $this->validate($header, $rows, $year);

        if ($problems !== []) {
            throw new StudentImportException(array_slice($problems, 0, self::MAX_PROBLEMS), count($problems));
        }

        return DB::transaction(fn (): array => $this->apply($clean, $year));
    }

    /**
     * @return array{0: list<string>, 1: array<int, list<string>>} header names and data rows keyed by file line
     */
    private function read(string $path): array
    {
        $handle = $path === '' ? false : fopen($path, 'r');
        if ($handle === false) {
            throw new StudentImportException([__('students.import.unreadable')], 1);
        }

        $first = (string) fgets($handle);
        rewind($handle);
        // Excel in many locales saves CSV with semicolons.
        $delimiter = substr_count($first, ';') > substr_count($first, ',') ? ';' : ',';

        $rows = [];
        $header = null;
        $line = 0;

        while (($cells = fgetcsv($handle, 0, $delimiter, '"', '')) !== false) {
            $line++;

            if ($header === null) {
                $cells[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $cells[0]);
                $header = array_map(fn ($h): string => str_replace(' ', '_', strtolower(trim((string) $h))), $cells);

                continue;
            }

            if (count(array_filter($cells, fn ($c): bool => trim((string) $c) !== '')) === 0) {
                continue;
            }

            $rows[$line] = array_map(fn ($c): string => trim((string) $c), $cells);

            if (count($rows) > (int) config('students.import_max_rows')) {
                fclose($handle);
                throw new StudentImportException([__('students.import.too_many', ['max' => config('students.import_max_rows')])], 1);
            }
        }

        fclose($handle);

        if ($header === null || $rows === []) {
            throw new StudentImportException([__('students.import.empty')], 1);
        }

        $unknown = array_values(array_diff($header, self::COLUMNS));
        if ($unknown !== []) {
            throw new StudentImportException([__('students.import.unknown_columns', ['columns' => implode(', ', $unknown)])], 1);
        }

        $missing = array_values(array_diff(self::required(), $header));
        if ($missing !== []) {
            throw new StudentImportException([__('students.import.missing_columns', ['columns' => implode(', ', $missing)])], 1);
        }

        return [$header, $rows];
    }

    /**
     * Checks each row; returns the cleaned ones keyed by file line, and the problems found.
     *
     * @param  list<string>  $header
     * @param  array<int, list<string>>  $rows
     * @return array{0: array<int, array<string, mixed>>, 1: list<string>}
     */
    private function validate(array $header, array $rows, ?AcademicYear $year): array
    {
        $problems = [];
        $clean = [];
        $seen = [];
        $auto = AdmissionSetting::current()->isAuto();

        $classes = SchoolClass::query()->get()->keyBy(fn (SchoolClass $c): string => mb_strtolower($c->name));
        $streams = Stream::query()->get()->groupBy('school_class_id');

        $numbers = [];
        foreach ($rows as $cells) {
            $position = array_search('admission_number', $header, true);
            $numbers[] = $position === false ? '' : ($cells[$position] ?? '');
        }
        $taken = Student::query()->whereIn('admission_number', array_values(array_filter($numbers)))->pluck('admission_number')->all();

        foreach ($rows as $line => $cells) {
            $row = [];
            foreach ($header as $i => $column) {
                if (($cells[$i] ?? '') !== '') {
                    $row[$column] = $this->normalise($column, $cells[$i]);
                }
            }

            $validator = Validator::make($row, [
                'admission_number' => [$auto ? 'nullable' : 'required', 'string', 'max:30'],
                'first_name' => ['required', 'string', 'max:100'],
                'middle_name' => ['nullable', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'gender' => ['required', 'in:male,female'],
                'date_of_birth' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
                'admitted_on' => ['nullable', 'date_format:Y-m-d'],
                'class' => ['nullable', 'string', 'max:100'],
                'stream' => ['nullable', 'string', 'max:100'],
                'guardian_name' => ['nullable', 'string', 'max:150', 'required_with:guardian_phone'],
                'guardian_phone' => ['nullable', 'string', 'max:30', 'regex:'.GuardianRequest::PHONE_PATTERN, 'required_with:guardian_name'],
                'guardian_email' => ['nullable', 'email', 'max:150'],
                'guardian_relationship' => ['nullable', 'in:father,mother,guardian,other'],
            ], [], array_combine(self::COLUMNS, array_map(fn (string $c): string => __('students.import.col_name.'.$c), self::COLUMNS)));

            foreach ($validator->errors()->all() as $message) {
                $problems[] = $this->problem($line, $message);
            }

            $number = $row['admission_number'] ?? '';
            if ($number !== '') {
                if (in_array($number, $taken, true)) {
                    $problems[] = $this->problem($line, __('students.import.already_exists', ['number' => $number]));
                }
                if (isset($seen[$number])) {
                    $problems[] = $this->problem($line, __('students.import.duplicate_number', ['number' => $number, 'first' => $seen[$number]]));
                }
                $seen[$number] = $line;
            }

            if (isset($row['class'])) {
                $class = $classes->get(mb_strtolower($row['class']));
                if ($year === null) {
                    $problems[] = $this->problem($line, __('students.import.no_year'));
                } elseif ($class === null) {
                    $problems[] = $this->problem($line, __('students.import.unknown_class', ['class' => $row['class']]));
                } else {
                    $row['school_class_id'] = $class->id;
                    if (isset($row['stream'])) {
                        $stream = ($streams->get($class->id) ?? collect())->first(fn (Stream $s): bool => mb_strtolower($s->name) === mb_strtolower($row['stream']));
                        if ($stream === null) {
                            $problems[] = $this->problem($line, __('students.import.unknown_stream', ['stream' => $row['stream'], 'class' => $class->name]));
                        } else {
                            $row['stream_id'] = $stream->id;
                        }
                    }
                }
            } elseif (isset($row['stream'])) {
                $problems[] = $this->problem($line, __('students.import.stream_without_class'));
            }

            $clean[$line] = $row;
        }

        return [$clean, $problems];
    }

    private function problem(int $line, mixed $message): string
    {
        return (string) __('students.import.line', ['line' => $line, 'message' => is_string($message) ? $message : '']);
    }

    /** Makes spreadsheet-style cells readable to the validator: "05/03/2012" -> "2012-03-05", "F" -> "female". */
    private function normalise(string $column, string $value): string
    {
        $lower = mb_strtolower($value);

        return match ($column) {
            'gender' => match (true) {
                in_array($lower, ['male', 'm', 'boy', 'me', 'mwanaume', 'mvulana', 'kiume'], true) => 'male',
                in_array($lower, ['female', 'f', 'girl', 'ke', 'mwanamke', 'msichana', 'kike'], true) => 'female',
                default => $lower,
            },
            'guardian_relationship' => match (true) {
                in_array($lower, ['father', 'baba'], true) => 'father',
                in_array($lower, ['mother', 'mama'], true) => 'mother',
                in_array($lower, ['guardian', 'mlezi'], true) => 'guardian',
                in_array($lower, ['other', 'mwingine'], true) => 'other',
                default => $lower,
            },
            'date_of_birth', 'admitted_on' => $this->date($value),
            // Excel drops the leading zero of a phone number typed as a number: 712345678 -> 0712345678.
            'guardian_phone' => preg_match('/^[67][0-9]{8}$/', $value) === 1 ? '0'.$value : $value,
            default => $value,
        };
    }

    /** Accepts 2012-03-05, 05/03/2012 and 05-03-2012 (day first, as in East Africa); anything else is left for the validator. */
    private function date(string $value): string
    {
        if (preg_match('/^(\d{1,2})[\/.-](\d{1,2})[\/.-](\d{4})$/', $value, $m) === 1 && checkdate((int) $m[2], (int) $m[1], (int) $m[3])) {
            return CarbonImmutable::create((int) $m[3], (int) $m[2], (int) $m[1])->toDateString();
        }

        return $value;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{created: int, guardians: int, placed: int}
     */
    private function apply(array $rows, ?AcademicYear $year): array
    {
        $created = $guardians = $placed = 0;
        $reserved = array_values(array_filter(array_map(fn (array $row): string => (string) ($row['admission_number'] ?? ''), $rows)));

        foreach ($rows as $row) {
            $yearId = $year?->id;
            $withClass = $yearId !== null && isset($row['school_class_id']);

            $admittedOn = (string) ($row['admitted_on'] ?? now()->toDateString());

            $student = $this->save->handle([
                'admission_number' => $row['admission_number'] ?? $this->numbers->generate(CarbonImmutable::parse($admittedOn), $reserved),
                'first_name' => $row['first_name'],
                'middle_name' => $row['middle_name'] ?? null,
                'last_name' => $row['last_name'],
                'gender' => $row['gender'],
                'date_of_birth' => $row['date_of_birth'],
                'admitted_on' => $admittedOn,
                'academic_year_id' => $withClass ? $yearId : null,
                'school_class_id' => $withClass ? $row['school_class_id'] : null,
                'stream_id' => $withClass ? ($row['stream_id'] ?? null) : null,
            ]);
            $created++;
            $placed += $withClass ? 1 : 0;

            if (isset($row['guardian_phone'], $row['guardian_name'])) {
                $this->addGuardian->handle($student, [
                    'name' => $row['guardian_name'],
                    'phone' => $row['guardian_phone'],
                    'email' => $row['guardian_email'] ?? null,
                    'relationship' => $row['guardian_relationship'] ?? 'guardian',
                    'is_primary' => true,
                ]);
                $guardians++;
            }
        }

        $this->audit->handle('student.imported', new Student);

        return ['created' => $created, 'guardians' => $guardians, 'placed' => $placed];
    }
}
