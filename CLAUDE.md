# Somaplus Contributor Notes

Somaplus is a school management system for East African schools, by Odessa Lab Technologies (formerly codenamed EduCore SMS; the requirements file may still be called `EDUCORE_SMS_BLUEPRINT.md`). One codebase, one database, many schools (tenants).
Read this file, `README.md` and `docs/` at the start of every session. Update them whenever you change structure or rules.

## Stack

Laravel 12, PHP 8.3, PostgreSQL 16, Vite, Pest 4, Pint, Larastan 3 (level 5) and `spatie/laravel-permission`. Queue, cache, and session drivers are database-backed. Local supporting services are PostgreSQL, Redis, and Mailpit in `docker-compose.yml`; Laravel Sail is not used. Tailwind is not used: the UI is the purchased template (Bootstrap/jQuery), and Vite only builds the small `resources/js/app.js`.
Keep `config.platform.php` in `composer.json` at `8.3.0` so `composer.lock` always resolves on PHP 8.3 (CI and production); a lock built on a newer PHP breaks `composer install`.
Product name everywhere in the UI, emails, SMS sender text and docs: **Somaplus**. Use `APP_NAME="Somaplus"`.

## Template preservation

The purchased UI template must remain exactly as supplied. Keep its static files in `public/assets/` and the original source in `resources/template-original/`; never edit, rename, minify, or modernize them. Build Blade wrappers around them. Do not replace Bootstrap/jQuery with Tailwind. The sidebar's permitted navigation comes from `config/menu.php` (label, icon, route, permission, optional plan feature).

## Tenancy (one school = one tenant)

Every tenant-owned Eloquent model uses `App\Models\Concerns\BelongsToTenant`. Never query across tenants by bypassing that global scope except in explicit, reviewed super-admin/system operations. Resolve tenants by subdomain/custom domain (`ResolveTenant`); there is no header or session override, and none may be added. Use `TenantContext::run()` for scoped work and do not retain context between requests or jobs. With no tenant, scoped queries return nothing; `TenantContext::runAsSystem()` is the only way to see all schools and needs a reason in review. Queued work uses the `TenantAwareJob` trait (`RestoreTenantContext` middleware); serialized queue payloads carry the tenant ID only. Tenant ids are ULIDs. Permissions use Spatie Permission in teams mode keyed by tenant: `TenantTeamResolver` takes the team only from `TenantContext`, every school has the five roles in `config/roles.php` (`SyncTenantRoles`, additive only), and a permission is added to that file only with the feature that checks it. Assign roles by name or id inside the school's context. `User` is the one tenant-owned model without `BelongsToTenant` (it uses `UserScope`: a school sees its own users, the central domain only super-admins); `tenant_id` and `is_super_admin` are never mass assignable.

## Code conventions

- Keep controllers thin. Behaviour lives in Actions/Services (`app/Domain/...`), validation in FormRequests, authorization in Policies.
- Domain folders: `app/Domain/{Academics,Students,Attendance,Exams,Fees,Staff,Communication,Settings,Integrations}` (add more only when needed).
- Money is stored as integer minor units and formatted with `App\Support\Money`. Default currency TZS (TZS, KES, UGX, USD, EUR supported).
- Two languages everywhere: English (fallback) and Swahili. Every label, message, validation text and help article exists in both.
- A shop/school setting or switch must change real behaviour. No decorative features.
- Optional integrations (SMS, mobile money, others) are disabled by default and configured per tenant; their secrets are stored encrypted and never shown again after saving.
- Do not add business workflows until requested.

## Academic and school rules

- A school has academic years, terms, classes (levels), streams and subjects. Students belong to a class and stream per year; history is never overwritten, a promotion or transfer adds a new record.
- Marks, grades and positions are computed server-side from grading scales the school configures; reports must be reproducible for any past term.
- Fees: structures, invoices and payments are separate records. A payment never edits an invoice; balances are derived. Receipts are numbered without gaps. Operations a client may resend (payments, mark entry, invoice generation) must be idempotent.
- Student and guardian data is personal data (often minors). Collect only what is needed, log access to sensitive records in the audit log, and never put it in error tracking, logs or URLs.
- Never delete a school's data because a subscription lapsed or a plan changed: move to grace, then read-only.

## Plans

Plans (Basic/Medium/Enterprise) live in the database (`subscription_plans`). `config/plans.php` only seeds them, so adding a feature to a plan needs a data migration. `PlanSeeder` only creates missing plans and never overwrites one. Gate with the `plan:` route middleware, the `feature` key in `config/menu.php`, and `Plans::current()->allows()` for logic outside requests. Add a feature key to a plan only when a feature that checks it exists. Routes that change data use `tenant.writable` so read-only schools can still read. Degrade quietly instead of refusing work already done (for example a late sync after a downgrade). Test every gated feature on each tier.

## Workflow

- One feature per branch and PR, branched from `origin/main`. Never commit to `main` directly.
- Commits end with the `Co-Authored-By` and `Claude-Session` trailers; PR bodies end with the "Generated with Claude Code" line and the session URL.
- After pushing, wait for CI (about two minutes), read the check runs and annotations, fix failures, then open the PR (use the REST API with `gh`). The owner merges only on green CI; if a PR was merged early with a defect, open a fix PR. Before saying something is merged, check the PR's base branch is `main`.
- Stop-hook warnings about unpushed commits: compare `git ls-remote origin <branch>` with `git rev-parse HEAD`; if equal the warning is wrong.
- Every feature updates tests, `docs/`, `docs/SMOKE-TEST.md` and the EN/SW help articles.
- Claude's cloud sandbox cannot reach Packagist (`repo.packagist.org` is blocked), so it cannot run `composer require` or `composer update`. When a change needs a new or updated PHP package, Claude gives the owner the exact command, the owner runs it locally and pushes `composer.json` and `composer.lock`, and Claude continues from there. `npm` works in the sandbox.
- Because of that, CI is the proof that PHP checks pass. Claude lints PHP with `php -l` but states clearly when PHPStan and Pest have not run locally.
- Pint can still run in the sandbox: `git clone --depth 1 --branch <version locked in composer.lock> https://github.com/laravel/pint <scratch dir>`, then `php <scratch dir>/builds/pint --test` (or without `--test` to fix) from the repo root. CI job logs are on a host the sandbox cannot reach, so use this to see Pint's exact complaints.

## Commands

```sh
docker compose up -d
php artisan migrate --seed
vendor/bin/pest
vendor/bin/pint --test
vendor/bin/phpstan analyse --level=5
npm run build
composer audit
```

PHPUnit/Pest uses SQLite in memory. See `docs/ARCHITECTURE.md` for tenancy and template boundaries.
