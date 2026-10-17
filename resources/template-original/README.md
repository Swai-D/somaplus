# SomaPlus – merged template (Dream design + Edudash content)

Rebuild all pages from the Edudash sources:
    EDU_SRC=../merge-temp/edudash-temp DREAM_SRC=../merge-temp/dream-temp python3 tools/build.py
Single pages: python3 tools/build.py index student-list

- tools/build.py       Edudash page -> Dream shell converter (header, sidebar, tables, forms, badges, icons)
- tools/dashboards.py  hand-built Dream-style dashboards (School dashboard done)
- assets/css/somaplus.css  your overrides on top of Dream's style.css
Status: Phase 1+2 done (pages reviewed except index-2..5 dashboards, login, register - Phase 3).
