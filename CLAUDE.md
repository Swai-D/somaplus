# Somaplus Contributor Notes

Somaplus is a school management system for East African schools, by Odessa Lab Technologies (formerly codenamed EduCore SMS; the requirements file may still be called `EDUCORE_SMS_BLUEPRINT.md`). One codebase, one database, many schools (tenants).
Read this file, `README.md` and `docs/` at the start of every session. Update them whenever you change structure or rules.

## Stack

Laravel 12, PHP 8.3, PostgreSQL 16, Vite, Pest 4, Pint, Larastan 3 (level 5) and `spatie/laravel-permission`. Queue, cache, and session drivers are database-backed. Local supporting services are PostgreSQL, Redis, and Mailpit in `docker-compose.yml`; Laravel Sail is not used. Tailwind is not used: the UI is the purchased template (Bootstrap/jQuery), and Vite only builds the small `resources/js/app.js`.
Keep `config.platform.php` in `composer.json` at `8.3.0` so `composer.lock` always resolves on PHP 8.3 (CI and production); a lock built on a newer PHP breaks `composer install`.
Product name everywhere in the UI, emails, SMS sender text and docs: **Somaplus**. Use `APP_NAME="Somaplus"`.

## Template preservation

The purchased UI template must remain exactly as supplied (the final version is the `somaplus/` folder of `Swai-D/merge-temp`; `resources/template-original/laravel/` is its supplied Blade mock, kept as reference and never routed as it is). Keep its static files in `public/assets/` and the original source in `resources/template-original/`; never edit, rename, minify, or modernize them. Build Blade wrappers around them. Do not replace Bootstrap/jQuery with Tailwind. The sidebar's permitted navigation comes from `config/menu.php` (label, icon, route, optional permission, optional plan feature, scope); an entry is added only together with its route, and `App\Support\Menu` hides what the person may not see. Pages extend `layouts.app` and fill its `page` section. Language is switched on the server (`SetLocale`, session); never load the template's `somaplus-i18n.js` or `somaplus-crud.js`. Sign-in lives in `App\Domain\Auth`; who can sign in where is decided by `UserScope`, not by comparing tenants by hand.

## Tenancy (one school = one tenant)

Every tenant-owned Eloquent model uses `App\Models\Concerns\BelongsToTenant`. Never query across tenants by bypassing that global scope except in explicit, reviewed super-admin/system operations. Resolve tenants by subdomain/custom domain (`ResolveTenant`); there is no header override, and none may be added. The only session-based choice is the Railway path entry (`TENANCY_ALLOW_PATH_ENTRY`, `/t/{slug}`, central hosts only, off by default, applied by `ApplyPathEntryTenant` after the session starts); keep it off once schools have their own domains. Behind Railway's proxy only `X-Forwarded-For/-Port/-Proto` are trusted (`bootstrap/app.php`); never trust `X-Forwarded-Host`, since the host picks the school. Use `TenantContext::run()` for scoped work and do not retain context between requests or jobs. With no tenant, scoped queries return nothing; `TenantContext::runAsSystem()` is the only way to see all schools and needs a reason in review. Queued work uses the `TenantAwareJob` trait (`RestoreTenantContext` middleware); serialized queue payloads carry the tenant ID only. Tenant ids are ULIDs. Permissions use Spatie Permission in teams mode keyed by tenant: `TenantTeamResolver` takes the team only from `TenantContext`, every school has the five roles in `config/roles.php` (`SyncTenantRoles`, additive only), and a permission is added to that file only with the feature that checks it. Assign roles by name or id inside the school's context. `User` is the one tenant-owned model without `BelongsToTenant` (it uses `UserScope`: a school sees its own users, the central domain only super-admins); `tenant_id` and `is_super_admin` are never mass assignable.

## Code conventions

- Keep controllers thin. Behaviour lives in Actions/Services (`app/Domain/...`), validation in FormRequests, authorization in Policies.
- Domain folders: `app/Domain/{Academics,Students,Attendance,Exams,Fees,Staff,Communication,Settings,Integrations,Auth}` (add more only when needed).
- Money is stored as integer minor units and formatted with `App\Support\Money`. Default currency TZS (TZS, KES, UGX, USD, EUR supported).
- Two languages everywhere: English (fallback) and Swahili. Every label, message, validation text and help article exists in both.
- A shop/school setting or switch must change real behaviour. No decorative features.
- Optional integrations (SMS, mobile money, others) are disabled by default and configured per tenant; their secrets are stored encrypted and never shown again after saving.
- Do not add business workflows until requested.

## Academic and school rules

- A school has academic years, terms, classes (levels), streams and subjects. Students belong to a class and stream per year; history is never overwritten, a promotion or transfer adds a new record.
- The academic structure lives in `App\Domain\Academics`. Features that refer to a year, term, class, stream or subject keep restrictive foreign keys and add their own check to `DeleteAcademicRecord`; records are never silently removed or overwritten.
- Marks, grades and positions are computed server-side from grading scales the school configures; reports must be reproducible for any past term. Marks are handled as whole hundredths (`App\Domain\Exams\Score`), never as floats; a grade is never stored or accepted from a client; a grading scale that an exam uses is never changed (copy it); a locked exam accepts no marks. Results, averages and positions are shown only for locked exams, ranked by exact average (ties share a position, no rounding), from the class snapshot stored on each mark; report cards need `results.view` and are audit-logged.
- Fees: structures, invoices and payments are separate records. A payment never edits an invoice; balances are derived. Receipts are numbered without gaps. Operations a client may resend (payments, mark entry, invoice generation) must be idempotent. Document numbers (`App\Domain\Fees\NextNumber`) are taken under a row lock inside the saving transaction. An invoice is a snapshot; a fee structure that has invoices is never changed. Amounts are typed through `Money::parse` and held in the school's currency (`tenants.currency`). Fees need `fees.view` / `fees.manage`; invoice views are audit-logged. Payments (`RecordPayment`) are idempotent (idempotency key and provider reference), take the invoice row lock while checking the balance, issue a gapless receipt in the same transaction, are never edited or deleted (`ReversePayment` adds a reversal record) and never change an invoice; `InvoiceBalance` derives paid/balance/status. A payment typed by a person cannot exceed the balance, one received from an integration is always recorded. Recording needs `payments.record`, reversing `payments.reverse`; receipt views are audit-logged.
- Integrations (Settings > Integrations, `settings.manage`, owner only): `config/integrations.php` lists channels and driver classes (`App\Domain\Integrations`, `Driver` contract with `fields()`); the Settings form is generated from the fields. A school's choice lives in `tenant_integrations` (off by default, secrets `encrypted:array`, never shown again; a `generate` secret is shown once in the save response only). `IntegrationManager::active()` is how features read a school's configuration. Provider notices arrive at `POST /webhooks/payments/{tenant}` (central host only, no CSRF, 404 for anything unknown or off): believed only after `WebhookSignature` (HMAC-SHA256 of `timestamp.body`, 5-minute window), then `ReceivePaymentNotification` calls `RecordPayment` with `source = integration`. Never invent a bank/GePG protocol: a real provider is a new driver built from its documentation. SMS is the `sms` channel (NextSMS, the school's own account): `SchoolSms` reads it, messages go through the queued `SendSmsMessage` job, one reminder per guardian per invoice per day (`sms_messages`, unique), no phone number or text stored, balance re-checked at send time; needs `reminders.send` (owner, accountant).
- Roles: five default roles in `config/roles.php`; custom roles (Enterprise, `custom_roles`) are spatie roles of the school made under Settings > Roles with only the permissions of `config('roles.permissions')` minus `custom_excluded`. Show any role name with `RoleCatalog::label()`, never `__('roles.'.$name)` directly. A new permission goes in `config/roles.php` with its labels in `lang/*/custom_roles.php` (`area` and `perm`) and, when it needs another to work, in `implies`.
- Admission numbers: a school either types them or lets `App\\Domain\\Students\\AdmissionNumbers` make them from its format (`admission_settings`, owner only under Settings). A made number is taken from `NextNumber` inside the saving transaction, is skipped when already used, and is never edited afterwards; a school with no setting types them by hand.
- Family pages (`family/*`, permission `family.portal`, parent role only, excluded from custom roles): a parent sees only the children returned by `FamilyChildren` (pupils linked to a guardian whose `guardians.user_id` is the parent); anything else is 404. Access is given or taken away by the owner (`GrantPortalAccess`, `RevokePortalAccess`); read-only pages, audit-logged, locked exams only.
- School profile (`settings/school`, owner, `school_profiles`): phone, email and address printed under the school name by `partials.letterhead` on every receipt and report card; the school name itself belongs to the platform administrator.
- Student and guardian data is personal data (often minors). Collect only what is needed, log access to sensitive records in the audit log (`App\Domain\Settings\RecordAudit`, ids only; search with POST), and never put it in error tracking, logs or URLs.
- Platform administration (`platform/schools`, central address, `platform.admin` → 404 for anyone else; `CreateSchool`, `UpdateSchool`): opens a school with its first owner (reset link on the school's own address via `SchoolAddress`), changes plan and subscription state; short name and currency are fixed after creation. It has no help article (the Help centre is for a school's people).
- Never delete a school's data because a subscription lapsed or a plan changed: move to grace, then read-only.

## Plans

Plans (Basic/Medium/Enterprise) live in the database (`subscription_plans`). `config/plans.php` only seeds them, so adding a feature to a plan needs a data migration. `PlanSeeder` only creates missing plans and never overwrites one. Gate with the `plan:` route middleware, the `feature` key in `config/menu.php`, and `Plans::current()->allows()` for logic outside requests. Add a feature key to a plan only when a feature that checks it exists. Routes that change data use `tenant.writable` so read-only schools can still read. Degrade quietly instead of refusing work already done (for example a late sync after a downgrade). Test every gated feature on each tier.

## Workflow

- One feature per branch and PR, branched from `origin/main`. Never commit to `main` directly.
- Commits end with the `Co-Authored-By` and `Claude-Session` trailers; PR bodies end with the "Generated with Claude Code" line and the session URL.
- After pushing, wait for CI (about two minutes), read the check runs and annotations, fix failures, then open the PR (use the REST API with `gh`). The owner merges only on green CI; if a PR was merged early with a defect, open a fix PR. Before saying something is merged, check the PR's base branch is `main`.
- Stop-hook warnings about unpushed commits: compare `git ls-remote origin <branch>` with `git rev-parse HEAD`; if equal the warning is wrong.
- Every feature updates tests, `docs/`, `docs/SMOKE-TEST.md` and the EN/SW help articles. Help articles are Markdown files in the application, one per language: `resources/docs/{en,sw}/<category>/<slug>.md` with front matter (title, summary, keywords, order); `config/help.php` lists the categories (icon) and `lang/{en,sw}/help.php` their titles. A test refuses an article missing in either language, without a summary/keywords, or with a `help:category/slug` link that does not resolve.
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
