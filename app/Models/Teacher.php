<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\CarbonImmutable;
use Database\Factories\TeacherFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * A teacher of the school. The person is a user (they sign in with the teacher
 * role) and this record is what the school knows about them: the staff number,
 * the phone, the subjects they teach and the classes they are in.
 *
 * @property int $id
 * @property string $tenant_id
 * @property int $user_id
 * @property string|null $staff_number
 * @property string $phone
 * @property string|null $gender
 * @property CarbonImmutable|null $date_of_birth
 * @property string|null $address
 * @property string|null $qualification
 * @property string|null $contract_type
 * @property string|null $shift
 * @property CarbonImmutable $joined_on
 * @property string $status
 * @property string|null $notes
 */
class Teacher extends Model
{
    /** @use HasFactory<TeacherFactory> */
    use BelongsToTenant, HasFactory;

    public const ACTIVE = 'active';

    public const INACTIVE = 'inactive';

    protected $fillable = [
        'user_id',
        'staff_number',
        'phone',
        'gender',
        'date_of_birth',
        'address',
        'qualification',
        'contract_type',
        'shift',
        'joined_on',
        'status',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'joined_on' => 'date',
        ];
    }

    /**
     * The account this teacher signs in with.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The subjects this teacher teaches.
     *
     * @return BelongsToMany<Subject, $this>
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject');
    }

    /**
     * The classes this teacher is in.
     *
     * @return BelongsToMany<SchoolClass, $this>
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'teacher_class', 'teacher_id', 'school_class_id');
    }

    /**
     * The name of the person, taken from the account they sign in with.
     */
    public function fullName(): string
    {
        return $this->user->name ?? '';
    }

    public function isActive(): bool
    {
        return $this->status === self::ACTIVE;
    }
}
