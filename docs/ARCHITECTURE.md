# Somaplus architecture

Somaplus is a school management system for East African schools, built by Odessa Lab Technologies. One codebase and one database serve many schools; each school is a tenant. Rules for contributors live in `CLAUDE.md`; this file explains the structure behind them and is updated whenever the structure changes.

## Repository layout

| Path | Purpose |
| --- | --- |
| `public/assets/` | Static files of the purchased UI template (CSS, JS, plugins, fonts, images). Served as-is. |
| `resources/template-original/` | Original template source: the 62 static HTML pages, the template `README.md` and the `tools/` scripts that produced the pages. Reference only; never served and never edited. |
| `resources/views/` | Blade wrappers built on top of the template. `layouts/base.blade.php` loads the template's Bootstrap, icon and `somaplus.css` assets; the full application layout (header, sidebar from `config/menu.php`) is added with navigation. |
| `lang/en`, `lang/sw` | Translations. English is the fallback; every key must exist in both languages (enforced by `tests/Unit/TranslationParityTest.php`). |
| `app/Support/` | Shared helpers. `Money` holds amounts as integer minor units (TZS by default; TZS, KES, UGX, USD, EUR supported). |
| `app/Domain/` | Business behaviour as Actions/Services, grouped by domain. Folders are created when the first feature needs them. |
| `tests/` | Pest tests: `Unit/` for plain PHP, `Feature/` for HTTP and database tests (SQLite in memory). |
| `docs/` | This file, `SMOKE-TEST.md` and other project documentation. |

## Template boundary

The template is a purchased asset and stays exactly as supplied.

- Files under `public/assets/` and `resources/template-original/` are never edited, renamed, minified or modernized. The move into these folders was a pure rename, verified with a sha256 manifest of all 456 files.
- Product behaviour is added by Blade wrappers that include the template's assets and copy its markup. If something needs different styling, it goes in a new stylesheet, not in a template file.
- The UI stays on the template's Bootstrap and jQuery. Tailwind is not used (the Laravel default was removed); Vite only builds `resources/js/app.js`.
- `resources/template-original/tools/*.py` are the template author's build scripts. They are kept for reference and are not part of the application.

## Tenancy

One school is one tenant, in a single database. Tenant ids are ULIDs.

- **Resolving the tenant.** `App\Http\Middleware\ResolveTenant` runs first in the `web` group and picks the tenant from the request host (`config/tenancy.php`): a host in `central_domains` has no tenant (the super-admin area); a tenant's own `domain` matches exactly; otherwise `<slug>.<base_domain>` matches the school with that slug. Any other host is a 404. There is deliberately no header or session override for choosing a tenant.
- **Context.** `App\Tenancy\TenantContext` holds the current tenant. It is bound as a scoped instance, so nothing survives between requests or queued jobs. Use `TenantContext::run($tenant, fn () => ...)` for work on behalf of one school; the previous context is restored afterwards, even on an exception.
- **Tenant-owned models.** Add the `App\Models\Concerns\BelongsToTenant` trait and a `tenant_id` ULID column (`foreignUlid('tenant_id')->constrained('tenants')`, no cascading delete: a school's data is never removed because of a subscription change). The trait applies `TenantScope` to every query, fills `tenant_id` on create, and refuses to write a row for another tenant or to move a row between tenants (`TenancyViolation`).
- **Fails closed.** With no current tenant, a scoped query returns nothing and creating a row throws. The only way to see every school's rows is `TenantContext::runAsSystem(fn () => ...)`, reserved for reviewed super-admin and system operations; every call needs a reason in code review.
- **Queued jobs.** A job that touches tenant data uses the `App\Tenancy\TenantAwareJob` trait: dispatch it with `->forCurrentTenant()`; only the tenant id is serialized, and `RestoreTenantContext` job middleware reloads the tenant and runs the job inside it. A job without a tenant id fails.
- **Not yet tenant-aware:** `users` and Spatie permissions (teams mode keyed by tenant) arrive with roles and permissions.

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
