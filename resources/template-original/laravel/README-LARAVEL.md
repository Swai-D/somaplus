# SomaPlus -> Laravel

1. Copy `resources/views/*` into your Laravel app's `resources/views/`.
2. Copy `routes/somaplus.php` into `routes/` and add `require __DIR__.'/somaplus.php';` at the end of `routes/web.php`.
3. Copy the static `assets/` folder from the SomaPlus project into `public/assets/`.
4. `php artisan serve` -> open `/` (School dashboard), `/student-list`, `/login` ...

Structure
- `layouts/app.blade.php`   shell: head, header, sidebar, footer, script stacks (`@stack('vendor')`, `@stack('scripts')`, `@stack('modals')`)
- `partials/sidebar.blade.php`  menu with active state via `request()->is()` - edit the menu in one place
- `pages/*.blade.php`      one view per page (`@section('content')`); static markup is wrapped in `@verbatim`,
  so to print real data close it: `@endverbatim {{ $student->name }} @verbatim`
- `routes/somaplus.php`    `Route::view` per page; replace with controllers/Eloquent data as you build the backend
