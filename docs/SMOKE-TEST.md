# Smoke test

Manual checks to run after a deploy or a significant merge. Each PR that adds a feature adds its steps here.

## PR 1: skeleton

1. `docker compose up -d` starts PostgreSQL, Redis and Mailpit; `docker compose ps` shows all three running.
2. `php artisan migrate --seed` completes without errors.
3. `php artisan serve` starts, and the home page loads with the title "Somaplus".
4. A template asset loads, for example `/assets/css/somaplus.css` returns 200.
5. `vendor/bin/pest`, `vendor/bin/pint --test`, `vendor/bin/phpstan analyse --level=5`, `composer audit` and `npm run build` all pass.
6. The template is unchanged: `git diff -M --summary f622034 e11ca8c | grep -v '(100%)' | grep -c rename` prints `0` (all 456 template files were moved as 100% renames).
