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

## Tenancy (planned for PR 2)

One school is one tenant, in a single database. Tenant-owned models will use a `BelongsToTenant` trait that adds a global scope; the tenant is resolved from the subdomain or custom domain. Details are written here when the code lands.

## Plans (planned for PR 2)

Plans (Basic, Medium, Enterprise) live in the `subscription_plans` table. Features are gated by the `plan:` route middleware, the `feature` key in `config/menu.php` and `Plans::current()->allows()`. Adding a feature to a plan needs a data migration.

## Environments

- Local supporting services (PostgreSQL 16, Redis, Mailpit) run from `docker-compose.yml`. PHP and Node run on the host; Laravel Sail is not used.
- Tests run on SQLite in memory.
- CI (`.github/workflows/ci.yml`) is the source of truth for quality gates: Pint, PHPStan level 5, Pest, `composer audit` and `npm run build`.
