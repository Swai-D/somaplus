# Somaplus architecture

Somaplus is a school management system for East African schools, built by Odessa Lab Technologies. One codebase and one database serve many schools; each school is a tenant. Rules for contributors live in `CLAUDE.md`; this file explains the structure behind them and is updated whenever the structure changes.

## Repository layout

| Path | Purpose |
| --- | --- |
| `public/assets/` | Static files of the purchased UI template (CSS, JS, plugins, fonts, images). Served as-is. |
| `resources/template-original/` | The template as supplied, except `assets/`: its 64 static HTML pages (62 pages plus login and register), its `README.md`, the `tools/` scripts that produced the pages, and `laravel/`, the Blade version of the same pages (layout, header, sidebar, one view per page, `routes/somaplus.php`). Reference only; never served and never edited. |
| `resources/views/` | Blade wrappers built on top of the template. `layouts/base.blade.php` loads the template's Bootstrap, icon and `somaplus.css` assets; the full application layout (header, sidebar from `config/menu.php`) is added with navigation. |
| `lang/en`, `lang/sw` | Translations. English is the fallback; every key must exist in both languages (enforced by `tests/Unit/TranslationParityTest.php`). |
| `app/Support/` | Shared helpers. `Money` holds amounts as integer minor units (TZS by default; TZS, KES, UGX, USD, EUR supported). |
| `app/Domain/` | Business behaviour as Actions/Services, grouped by domain. Folders are created when the first feature needs them. |
| `tests/` | Pest tests: `Unit/` for plain PHP, `Feature/` for HTTP and database tests (SQLite in memory). |
| `docs/` | This file, `SMOKE-TEST.md` and other project documentation. |

## Template boundary

The template is a purchased asset and stays exactly as supplied.

- Files under `public/assets/` and `resources/template-original/` are never edited, renamed, minified or modernized. Both folders are an exact copy of the `somaplus/` folder of `Swai-D/merge-temp` at commit `b716aa1` (the final template: 537 files), verified file by file with sha256. To take a newer version of the template, replace both folders with the new supplied files and verify again the same way.
- Product behaviour is added by Blade wrappers that include the template's assets and copy its markup. If something needs different styling, it goes in a new stylesheet, not in a template file.
- The UI stays on the template's Bootstrap and jQuery. Tailwind is not used (the Laravel default was removed); Vite only builds `resources/js/app.js`.
- `resources/template-original/tools/*.py` are the template author's build scripts. They are kept for reference and are not part of the application.
- The supplied `laravel/` package is a UI-only mock: every page is static markup with made-up data, mounted with `Route::view`, with no login, tenant or permission checks. It is not copied into `resources/views/` or routed as it is, because school users would see fake students and fees. Our own wrappers in `resources/views/` reuse its markup, and a page gets a route only together with the feature that gives it real data.

## Tenancy

One school is one tenant, in a single database. Tenant ids are ULIDs.

- **Resolving the tenant.** `App\Http\Middleware\ResolveTenant` runs first in the `web` group and picks the tenant from the request host (`config/tenancy.php`): a host in `central_domains` has no tenant (the super-admin area); a tenant's own `domain` matches exactly; otherwise `<slug>.<base_domain>` matches the school with that slug. Any other host is a 404. There is deliberately no header or session override for choosing a tenant.
- **Context.** `App\Tenancy\TenantContext` holds the current tenant. It is bound as a scoped instance, so nothing survives between requests or queued jobs. Use `TenantContext::run($tenant, fn () => ...)` for work on behalf of one school; the previous context is restored afterwards, even on an exception.
- **Tenant-owned models.** Add the `App\Models\Concerns\BelongsToTenant` trait and a `tenant_id` ULID column (`foreignUlid('tenant_id')->constrained('tenants')`, no cascading delete: a school's data is never removed because of a subscription change). The trait applies `TenantScope` to every query, fills `tenant_id` on create, and refuses to write a row for another tenant or to move a row between tenants (`TenancyViolation`).
- **Fails closed.** With no current tenant, a scoped query returns nothing and creating a row throws. The only way to see every school's rows is `TenantContext::runAsSystem(fn () => ...)`, reserved for reviewed super-admin and system operations; every call needs a reason in code review.
- **Queued jobs.** A job that touches tenant data uses the `App\Tenancy\TenantAwareJob` trait: dispatch it with `->forCurrentTenant()`; only the tenant id is serialized, and `RestoreTenantContext` job middleware reloads the tenant and runs the job inside it. A job without a tenant id fails.
- **Users** are the one tenant-owned model that does not use `BelongsToTenant`; see below.

## Users, roles and permissions

- **Users.** `users.tenant_id` points at the school; a super-admin (`is_super_admin`) belongs to the platform and has no school. `UserScope` shows a school only its own users, the central domain only super-admins, and everything only inside `TenantContext::runAsSystem()`. A normal user always has a school (taken from the current tenant when created); a super-admin cannot be created inside or for a school; a user cannot be moved to another school or promoted to super-admin afterwards. `tenant_id` and `is_super_admin` are not mass assignable. The email address is unique per school, not globally, so one person can sign in at two schools. Password reset tokens are still keyed by email only and must be made per school with the sign-in work.
- **Roles.** `spatie/laravel-permission` runs in teams mode, where the team is the school (`permission.team_foreign_key` is `tenant_id`, a ULID that cannot be null). `TenantTeamResolver` always answers with the current tenant and refuses to be set to anything else, so a role check can never be answered for another school. Roles and role assignments belong to a school; permission names are shared rows.
- **The five roles** are in `config/roles.php` (`owner`, `head_teacher`, `teacher`, `accountant`, `parent_student`), with English and Swahili labels in `lang/*/roles.php`. Every new school gets all five (`Tenant::created` runs `SyncTenantRoles`). Syncing is idempotent and only adds; it never revokes.
- **Permissions** are added to `config/roles.php` only together with the feature that checks them (`'students.view' => ['owner', 'teacher']`). Existing schools receive a new permission by running `SyncTenantRoles` for each school (a data migration), because the config is seed data and not the source of truth.
- **Assigning roles.** Assign by name or id (`$user->assignRole('teacher')`), inside the school's context. `User::assignRole()` refuses a `Role` object of another school, which the package does not check by itself. Do not assign roles outside a tenant context.
- Super-admin permissions, sign-in and password reset arrive with authentication.

## Plans and subscriptions

- Plans live in the `subscription_plans` table (`key`, `name`, `features` JSON). `config/plans.php` only seeds missing plans through `PlanSeeder` (`php artisan migrate --seed`); the seeder never overwrites an existing plan, so changing a plan's features needs a data migration.
- Feature keys are added to a plan only when a feature that checks the key exists. For now the three plans (basic, medium, enterprise) have no features.
- A tenant has one plan (`subscription_plan_id`) and a `subscription_status`: `active`, `grace` or `read_only`. A lapsed subscription moves from grace to read-only and never deletes data. Nothing changes the status automatically yet.
- Gating: `plan:<feature>[,<feature>]` route middleware (403 unless the current school's plan has every feature), `Plans::current()->allows('<feature>')` for code outside requests, and the `feature` key in `config/menu.php` (with navigation). With no tenant, nothing is allowed.
- `tenant.writable` route middleware answers 403 to changes (anything but GET/HEAD/OPTIONS) from a read-only school. Routes that change data must use it; login and logout must not.

## Environments

- Local supporting services (PostgreSQL 16, Redis, Mailpit) run from `docker-compose.yml`. PHP and Node run on the host; Laravel Sail is not used.
- Tests run on SQLite in memory.
- CI (`.github/workflows/ci.yml`) is the source of truth for quality gates: Pint, PHPStan level 5, Pest, `composer audit` and `npm run build`.
