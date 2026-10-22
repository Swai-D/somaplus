<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\GuardianFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * A parent or guardian, one record per phone number in a school so brothers and
 * sisters share it.
 *
 * @property int $id
 * @property string $tenant_id
 * @property string $name
 * @property string $phone
 * @property string|null $email
 * @property int|null $user_id
 */
class Guardian extends Model
{
    /** @use HasFactory<GuardianFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = ['name', 'phone', 'email'];

    /**
     * The account this guardian signs in with, when the school has given access to the family pages.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasPortalAccess(): bool
    {
        return $this->user_id !== null;
    }

    /**
     * @return BelongsToMany<Student, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)->withPivot(['relationship', 'is_primary'])->withTimestamps();
    }
}
