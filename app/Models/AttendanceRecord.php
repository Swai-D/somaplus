<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Database\Factories\AttendanceRecordFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One student's attendance on one day.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $student_id
 * @property int $academic_year_id
 * @property int $school_class_id
 * @property int|null $stream_id
 * @property CarbonImmutable $attended_on
 * @property string $status
 * @property string|null $note
 * @property int|null $recorded_by
 */
class AttendanceRecord extends Model
{
    /** @use HasFactory<AttendanceRecordFactory> */
    use BelongsToTenant, HasFactory;

    public const PRESENT = 'present';

    public const ABSENT = 'absent';

    public const LATE = 'late';

    public const EXCUSED = 'excused';

    /** @var list<string> */
    public const STATUSES = [self::PRESENT, self::ABSENT, self::LATE, self::EXCUSED];

    protected $fillable = [
        'student_id', 'academic_year_id', 'school_class_id', 'stream_id', 'attended_on', 'status', 'note', 'recorded_by',
    ];

    protected function casts(): array
    {
        return ['attended_on' => 'immutable_date'];
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
