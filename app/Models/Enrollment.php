<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Database\Factories\EnrollmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One placement of a student in a class (and stream) for an academic year.
 * Rows are only added, never edited: a move or a promotion is a new row.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $student_id
 * @property int $academic_year_id
 * @property int $school_class_id
 * @property int|null $stream_id
 * @property CarbonImmutable $enrolled_on
 */
class Enrollment extends Model
{
    /** @use HasFactory<EnrollmentFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = ['academic_year_id', 'school_class_id', 'stream_id', 'enrolled_on'];

    protected function casts(): array
    {
        return ['enrolled_on' => 'immutable_date'];
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * @return BelongsTo<SchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * @return BelongsTo<Stream, $this>
     */
    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }
}
