# Smoke test

Manual checks to run after a deploy or a significant merge. Each PR that adds a feature adds its steps here.

## Skeleton

1. `docker compose up -d` starts PostgreSQL, Redis and Mailpit; `docker compose ps` shows all three running.
2. `php artisan migrate --seed` completes without errors.
3. `php artisan serve` starts, and the central address `http://localhost:8000` sends you to the sign-in page showing the Somaplus logo, with no broken styles.
4. A template asset loads, for example `/assets/css/somaplus.css` returns 200.
5. `vendor/bin/pest`, `vendor/bin/pint --test`, `vendor/bin/phpstan analyse --level=5`, `composer audit` and `npm run build` all pass.
6. The template is unchanged. Clone `https://github.com/Swai-D/merge-temp`, check out `b716aa1`, then from the repository root both `diff -r <clone>/somaplus/assets public/assets` and `diff -r <clone>/somaplus resources/template-original -x assets` print nothing.

## Tenancy and plans

Create two schools and a plan from `php artisan tinker`:

```php
$plan = App\Models\SubscriptionPlan::where('key', 'basic')->first();
App\Models\Tenant::create(['name' => 'Acme', 'slug' => 'acme', 'subscription_plan_id' => $plan->id]);
App\Models\Tenant::create(['name' => 'Beta', 'slug' => 'beta', 'subscription_plan_id' => $plan->id]);
```

1. `php artisan migrate --seed` creates three plans: basic, medium and enterprise (`SubscriptionPlan::count()` is 3). Running the seeder again does not duplicate them.
2. `http://localhost:8000` (central domain) and `http://acme.localhost:8000` both show the sign-in page. `http://nobody.localhost:8000` answers 404.
3. Sending `X-Tenant: acme` to the central domain does not change the tenant.
4. Set `subscription_status` of Acme to `read_only`: the sign-in page still loads.

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

## Sign-in, language and layout

Create a user for Acme from `php artisan tinker` (password `password`):

```php
App\Tenancy\TenantContext::run(App\Models\Tenant::where('slug', 'acme')->first(), function () {
    App\Models\User::factory()->create(['name' => 'Asha', 'email' => 'asha@acme.test'])->assignRole('head_teacher');
});
App\Models\User::factory()->superAdmin()->create(['name' => 'Root', 'email' => 'root@somaplus.test']);
```

1. `http://acme.localhost:8000` redirects to `/login`, which names the school. Signing in as `asha@acme.test` opens the dashboard with Acme's name, its plan, the subscription status and the role "Head teacher".
2. The same email and password on `http://localhost:8000/login` or on Beta's address is refused.
3. `root@somaplus.test` signs in only on `http://localhost:8000`; the dashboard shows the number of schools.
4. Five wrong passwords in a row block further attempts for about a minute, even with the right password.
5. The flag menu in the header switches between English and Kiswahili; menu, dashboard and error messages change, and the choice survives signing out.
6. Set Acme to `read_only`: a yellow notice shows on every page, and signing in, signing out and reading still work.
7. The sidebar shows only "Dashboard" and "Help". Other items appear as the features that own them are built.
8. "Log out" in the profile menu returns to `/login`.

## Help

1. "Help" in the sidebar opens `/help` with the articles Getting started, Forgot your password, Roles and Subscription and plan.
2. Opening an article shows its text; "All help articles" returns to the list.
3. After switching to Kiswahili, the list and the articles are in Swahili.
4. `/help/nothing-here` answers 404; without signing in, `/help` redirects to `/login`.

## Password reset

Mail goes to Mailpit (`http://localhost:8025`) with the `MAIL_*` values of `.env.example`.

1. On `http://acme.localhost:8000/login`, "Forgot password?" opens the forgot-password page naming the school.
2. Asking for a link for `asha@acme.test` shows "If that email address belongs to this school…" and an email arrives in Mailpit with a link to `acme.localhost`.
3. Asking for an address that does not exist shows the same message and sends nothing.
4. Opening the link, choosing a password of 8 or more characters (twice) and saving returns to sign-in with a success message; the new password signs in, the old one does not.
5. Using the same link again, or the same link on Beta's address, answers "This reset link is not valid or has expired."
6. After switching to Kiswahili, the pages and the email are in Swahili. Help has the article "Forgot your password".
