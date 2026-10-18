# SomaPlus - Dream design + Edudash content

Open `index.html` (static) - 64 pages, 0 JS errors (checked with Chromium via tools/audit.py).

## Two ready outputs
1. Static HTML  : this folder (index.html, student-list.html ...)
2. Laravel Blade: laravel/  -> see laravel/README-LARAVEL.md (layout, partials, 62 pages + login/register, routes)

## Features added in Phase 4
- Currency shown as TSh (CURRENCY in tools/build.py)
- English / Kiswahili switch in the header flag menu (assets/js/somaplus-i18n.js, saved in localStorage;
  add words to the dictionary D at the top of that file)
- Blade package generated from the same sources

## CRUD flow (Dream style)
- Simple lists (class, section, subject, department, fees, notice, books ... ~27 pages): Add / Edit / Delete are modals
  (tools/crud.py builds the forms from the table columns; assets/js/somaplus-crud.js fills the edit modal from the clicked row).
- Complex lists (students, teachers, guardians, employees): eye / edit / trash icons -> details page, edit page, delete modal;
  extra actions (Collect Fees, Inactive, Login) stay in a "more" menu.
- Add MODAL / PAGES entries in tools/crud.py to change which page uses which flow.

## Tools (python3 tools/<name>.py)
- build.py       Edudash page -> Dream shell (static pages + tools/parts.json)
- dashboards.py  hand-built Dream dashboards (school, student, teacher, parent, LMS)
- auth.py        login / register
- blade.py       Laravel package from parts.json   | check_blade.py renders it with a mini Blade emulator
- rebrand.py     --primary "#HEX" --name "School" --admin "Name"  (recolour + rebuild everything)
- audit.py       Chromium audit of all pages | port_css.py ports Edudash widget CSS
Replace assets/img/logo*.svg, logo-small*.svg and favicon.svg with your real logo.
