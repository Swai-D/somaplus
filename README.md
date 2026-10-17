# Somaplus

School management system for East African schools, by Odessa Lab Technologies. One codebase and one database serve many schools; each school is a tenant. Available in English and Swahili.

Contributor rules are in [`CLAUDE.md`](CLAUDE.md); structure is explained in [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md).

## Requirements

PHP 8.3, Composer 2, Node 22 and Docker (for PostgreSQL 16, Redis and Mailpit).

## Getting started

```sh
docker compose up -d        # PostgreSQL, Redis, Mailpit
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

The app runs on <http://localhost:8000>; Mailpit's inbox is on <http://localhost:8025>.

## Checks

```sh
vendor/bin/pest                         # tests (SQLite in memory)
vendor/bin/pint --test                  # code style
vendor/bin/phpstan analyse --level=5    # static analysis
composer audit
npm run build
```

CI (`.github/workflows/ci.yml`) runs all of these on every pull request. After a deploy or significant merge, follow [`docs/SMOKE-TEST.md`](docs/SMOKE-TEST.md).
