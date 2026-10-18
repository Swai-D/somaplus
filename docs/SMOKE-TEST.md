# Smoke test

Manual checks to run after a deploy or a significant merge. Each PR that adds a feature adds its steps here.

## Skeleton

1. `docker compose up -d` starts PostgreSQL, Redis and Mailpit; `docker compose ps` shows all three running.
2. `php artisan migrate --seed` completes without errors.
3. `php artisan serve` starts, and the home page loads showing "Somaplus" and its tagline, with no broken styles.
4. A template asset loads, for example `/assets/css/somaplus.css` returns 200.
5. `vendor/bin/pest`, `vendor/bin/pint --test`, `vendor/bin/phpstan analyse --level=5`, `composer audit` and `npm run build` all pass.
6. The template is unchanged: `git diff -M --summary f622034 e11ca8c | grep -v '(100%)' | grep -c rename` prints `0` (all 456 template files were moved as 100% renames).

## Tenancy and plans

Create two schools and a plan from `php artisan tinker`:

```php
$plan = App\Models\SubscriptionPlan::where('key', 'basic')->first();
App\Models\Tenant::create(['name' => 'Acme', 'slug' => 'acme', 'subscription_plan_id' => $plan->id]);
App\Models\Tenant::create(['name' => 'Beta', 'slug' => 'beta', 'subscription_plan_id' => $plan->id]);
```

1. `php artisan migrate --seed` creates three plans: basic, medium and enterprise (`SubscriptionPlan::count()` is 3). Running the seeder again does not duplicate them.
2. `http://localhost:8000` (central domain) shows the home page. `http://acme.localhost:8000` shows it too. `http://nobody.localhost:8000` answers 404.
3. Sending `X-Tenant: acme` to the central domain does not change the tenant.
4. Set `subscription_status` of Acme to `read_only`: pages still load.

## Users and roles

From `php artisan tinker`, with the two schools Acme and Beta from the previous section:

```php
$acme = App\Models\Tenant::where('slug', 'acme')->first();
$id = App\Tenancy\TenantContext::run($acme, function () {
    $user = App\Models\User::factory()->create();
    $user->assignRole('teacher');
    dump($user->tenant_id, $user->hasRole('teacher'));

    return $user->id;
});
```

1. A new school has five roles: `Spatie\Permission\Models\Role::where('tenant_id', $acme->id)->pluck('name')` lists owner, head_teacher, teacher, accountant and parent_student.
2. The snippet above prints Acme's id and `true`.
3. Outside any school, `App\Models\User::count()` counts only super-admins, not the user just created.
4. Inside Beta (`TenantContext::run($beta, ...)`), `App\Models\User::count()` does not include Acme's user, and `hasRole('teacher')` on that user, loaded with `App\Tenancy\TenantContext::runAsSystem(fn () => App\Models\User::find($id))`, is `false`.
5. `php artisan tinker` shows the same email can be used at Acme and at Beta, but twice at Acme is refused.
