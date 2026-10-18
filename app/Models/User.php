<?php

namespace App\Models;

use App\Models\Scopes\UserScope;
use App\Notifications\ResetPasswordNotification;
use App\Tenancy\TenancyViolation;
use App\Tenancy\TenantContext;
use BackedEnum;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * A person who signs in. Users are the one tenant-owned model that does not use
 * BelongsToTenant: a school's staff, parents and students belong to that school,
 * while a super-admin belongs to the platform and has no school (tenant_id null).
 *
 * @property int $id
 * @property string|null $tenant_id
 * @property bool $is_super_admin
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    use HasRoles {
        assignRole as private assignRoleUnchecked;
    }

    /**
     * `tenant_id` and `is_super_admin` are deliberately not mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);

        static::creating(function (self $user): void {
            $context = app(TenantContext::class);
            $currentId = $context->currentId();

            if ($user->is_super_admin) {
                if ($user->tenant_id !== null || $currentId !== null) {
                    throw TenancyViolation::superAdminInTenant();
                }

                return;
            }

            if ($user->tenant_id === null) {
                if ($currentId === null) {
                    throw TenancyViolation::noTenant(self::class);
                }

                $user->tenant_id = $currentId;

                return;
            }

            if ($currentId !== null && $user->tenant_id !== $currentId && ! $context->isSystem()) {
                throw TenancyViolation::otherTenant(self::class);
            }
        });

        static::updating(function (self $user): void {
            if ($user->isDirty('tenant_id')) {
                throw TenancyViolation::tenantChanged(self::class);
            }

            if ($user->isDirty('is_super_admin')) {
                throw TenancyViolation::superAdminChanged();
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * The key under which this person's password reset token is stored.
     *
     * The email address alone is not unique (the same address can belong to a
     * person at two schools), so the key is a hash of the school and the address.
     * That keeps the reset tokens of the schools apart. The address itself is
     * still what the person types and what the reset link carries.
     */
    public function getEmailForPasswordReset(): string
    {
        return hash('sha256', ($this->tenant_id ?? '').'|'.mb_strtolower($this->email));
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin === true;
    }

    /**
     * Assign roles of the current school only.
     *
     * Roles given by name or id are already looked up inside the current school.
     * A Role object could belong to another school, which the package does not
     * check, so it is checked here.
     *
     * @param  string|int|array<mixed>|Role|Collection<int, mixed>|BackedEnum  ...$roles
     * @return $this
     */
    public function assignRole(...$roles): static
    {
        $currentId = TenantContext::instance()->currentId();

        foreach (collect($roles)->flatten() as $role) {
            if ($role instanceof Role && $role->getAttribute('tenant_id') !== $currentId) {
                throw TenancyViolation::otherTenant(Role::class);
            }
        }

        return $this->assignRoleUnchecked(...$roles);
    }
}
