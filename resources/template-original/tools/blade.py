#!/usr/bin/env python3
"""Generate a Laravel Blade package (layout + partials + pages + routes) from the built SomaPlus pages.
Run build.py (full build) and auth.py first.  Output: ../laravel/"""
import os, re, json, shutil, sys
HERE = os.path.dirname(os.path.abspath(__file__)); ROOT = os.path.dirname(HERE)
sys.path.insert(0, HERE)
import build as B

OUT = f"{ROOT}/laravel"
V = f"{OUT}/resources/views"
ASSET = re.compile(r'(src|href)="assets/([^"]*)"')
PAGELINK = re.compile(r'href="([a-z0-9-]+)\.html(#[^"]*)?"')

def route(stem):
    return "/" if stem == "index" else stem

def bl(html):
    """Wrap static html in @verbatim, breaking out only for asset()/url() helpers."""
    html = ASSET.sub(lambda m: f'{m.group(1)}="@endverbatim{{{{ asset(\'assets/{m.group(2)}\') }}}}@verbatim"', html)
    html = PAGELINK.sub(lambda m: f'href="@endverbatim{{{{ url(\'{route(m.group(1))}\') }}}}@verbatim"', html)
    return "@verbatim\n" + html + "\n@endverbatim"

def live(html):
    """For fragments that already contain Blade expressions (sidebar): only rewrite assets."""
    return ASSET.sub(lambda m: f'{m.group(1)}="{{{{ asset(\'assets/{m.group(2)}\') }}}}"', html)

def w(path, text):
    os.makedirs(os.path.dirname(path), exist_ok=True)
    open(path, "w", encoding="utf8").write(text)

def main():
    parts = json.load(open(f"{HERE}/parts.json"))
    if os.path.isdir(OUT):
        shutil.rmtree(OUT)
    brk = lambda name: f"@endverbatim{name}@verbatim"

    # ---- layout
    head = B.HEAD.format(title="@@TITLE@@").replace('<div class="main-wrapper">\n', "")
    tail = B.TAIL.format(apex="@@VENDOR@@", inline="@@SCRIPTS@@")
    head_b = bl(head).replace("@@TITLE@@", brk("@yield('title', 'SomaPlus')"))
    tail_b = bl(tail).replace("@@VENDOR@@", brk("@stack('vendor')")).replace("@@SCRIPTS@@", brk("@stack('scripts')"))
    layout = (head_b + '\n<div class="main-wrapper">\n@include(\'partials.header\')\n@include(\'partials.sidebar\')\n'
              '<div class="page-wrapper"><div class="content">\n@yield(\'content\')\n</div>\n@include(\'partials.footer\')\n</div>\n</div>\n'
              "@stack('modals')\n" + tail_b)
    w(f"{V}/layouts/app.blade.php", layout)

    # ---- partials
    w(f"{V}/partials/header.blade.php", bl(B.build_header()))
    w(f"{V}/partials/sidebar.blade.php", live(B.build_sidebar("", blade=True)))
    w(f"{V}/partials/footer.blade.php", bl(
        f'<div class="copyright-footer d-flex align-items-center justify-content-between border-top bg-white gap-3 flex-wrap"><p class="fs-13 text-gray-9 mb-0">&copy; <span id="current-year"></span> {B.APP}. All rights reserved.</p>'
        '<p>Built by <a href="javascript:void(0);" class="link-primary">Odessa Lab Technologies</a></p></div>'))

    # ---- pages
    routes = []
    for stem, p in sorted(parts.items()):
        title = p["title"].replace("\\", "\\\\").replace("'", "\\'")
        t = ["@extends('layouts.app')", f"@section('title', '{title}')", "@section('content')", bl(p["content"]), "@endsection"]
        if p["modal"].strip():
            t += ["@push('modals')", bl(p["modal"]), "@endpush"]
        if p["vendor"].strip():
            t += ["@push('vendor')", bl(p["vendor"]), "@endpush"]
        if p["scripts"].strip():
            t += ["@push('scripts')", bl(p["scripts"]), "@endpush"]
        w(f"{V}/pages/{stem}.blade.php", "\n".join(t) + "\n")
        routes.append((stem, f"pages.{stem}"))

    # ---- auth (standalone documents)
    for stem in ("login", "register"):
        html = open(f"{ROOT}/{stem}.html", encoding="utf8").read()
        w(f"{V}/auth/{stem}.blade.php", bl(html) + "\n")
        routes.append((stem, f"auth.{stem}"))

    # ---- routes
    lines = ["<?php", "", "use Illuminate\\Support\\Facades\\Route;", "",
             "// SomaPlus UI routes (generated). Swap Route::view for controllers as you add the backend.", ""]
    for stem, view in routes:
        lines.append(f"Route::view('{'/' if stem == 'index' else '/' + stem}', '{view}')->name('{'home' if stem == 'index' else stem}');")
    w(f"{OUT}/routes/somaplus.php", "\n".join(lines) + "\n")

    w(f"{OUT}/README-LARAVEL.md", """# SomaPlus -> Laravel

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
""")
    print(f"laravel package: {len(parts)} pages + auth -> {OUT}")

if __name__ == "__main__":
    main()
