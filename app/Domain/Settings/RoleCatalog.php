<?php

namespace App\Domain\Settings;

use App\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * What a school's roles are made of: the five default roles (config/roles.php, never changed here),
 * the custom roles the school made, and the permissions a custom role may be given.
 */
final class RoleCatalog
{
    /** @return list<string> */
    public static function defaultNames(): array
    {
        return array_values(array_map('strval', (array) config('roles.roles', [])));
    }

    public static function isDefault(string $name): bool
    {
        return in_array($name, self::defaultNames(), true);
    }

    /** A default role has a translated label; a custom role is shown under the name its school gave it. */
    public static function label(string $name): string
    {
        return self::isDefault($name) ? (string) __('roles.'.$name) : $name;
    }

    /**
     * Names a custom role may not take: the default role keys and their English and Swahili labels.
     *
     * @return list<string>
     */
    public static function reservedNames(): array
    {
        $names = [];
        foreach (self::defaultNames() as $key) {
            $names[] = mb_strtolower($key);
            $names[] = mb_strtolower((string) __('roles.'.$key, [], 'en'));
            $names[] = mb_strtolower((string) __('roles.'.$key, [], 'sw'));
        }

        return array_values(array_unique($names));
    }

    /**
     * The permissions a custom role may be given, in config order.
     *
     * @return list<string>
     */
    public static function assignable(): array
    {
        return array_values(array_diff(
            array_map('strval', array_keys((array) config('roles.permissions', []))),
            (array) config('roles.custom_excluded', []),
        ));
    }

    /**
     * The assignable permissions by area: ['students' => ['students.view', 'students.manage'], ...].
     *
     * @return array<string, list<string>>
     */
    public static function groups(): array
    {
        $groups = [];
        foreach (self::assignable() as $permission) {
            $groups[explode('.', $permission, 2)[0]][] = $permission;
        }

        return $groups;
    }

    /**
     * Keeps only assignable permissions and adds the ones they need to work, in config order.
     *
     * @param  array<int, mixed>  $permissions
     * @return list<string>
     */
    public static function withImplied(array $permissions): array
    {
        $allowed = self::assignable();
        $set = array_values(array_intersect(array_map('strval', $permissions), $allowed));
        $implies = (array) config('roles.implies', []);

        do {
            $before = count($set);
            foreach ($set as $permission) {
                foreach ((array) ($implies[$permission] ?? []) as $needed) {
                    if (in_array($needed, $allowed, true) && ! in_array($needed, $set, true)) {
                        $set[] = $needed;
                    }
                }
            }
        } while (count($set) !== $before);

        return array_values(array_intersect($allowed, $set));
    }

    /** @return Builder<Role> */
    public static function query(): Builder
    {
        return Role::query()->where('guard_name', 'web')->where('tenant_id', TenantContext::instance()->currentId());
    }

    /**
     * Every role of the current school: default roles first, in config order, then custom roles by name.
     *
     * @return Collection<int, Role>
     */
    public static function all(): Collection
    {
        $defaults = self::defaultNames();

        return self::query()->with('permissions:id,name')->get()->sortBy(function (Role $role) use ($defaults): string {
            $position = array_search($role->name, $defaults, true);

            return $position === false ? '1'.mb_strtolower($role->name) : '0'.str_pad((string) $position, 3, '0', STR_PAD_LEFT);
        })->values();
    }

    /** The custom role with this id in the current school, or null (default roles are never returned). */
    public static function findCustom(int|string $id): ?Role
    {
        return self::query()->whereNotIn('name', self::defaultNames())->find($id);
    }

    /**
     * How many people hold each role of the current school, by role id.
     *
     * @return array<int, int>
     */
    public static function holders(): array
    {
        /** @var array<int, int> $counts */
        $counts = DB::table((string) config('permission.table_names.model_has_roles'))
            ->where('tenant_id', TenantContext::instance()->currentId())
            ->selectRaw('role_id, count(*) as total')->groupBy('role_id')->pluck('total', 'role_id')->map(fn ($n): int => (int) $n)->all();

        return $counts;
    }
}
