#!/usr/bin/env python3
"""Rebrand SomaPlus: python3 tools/rebrand.py --primary "#0E7C66" --name "Msingi Secondary" [--admin "Head Office"]
Replaces Dream's orange (#FE9F43 + its hover shade) everywhere in assets/ and rebuilds every page."""
import argparse, os, re, subprocess, sys
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
ap = argparse.ArgumentParser()
ap.add_argument("--primary", help="new brand colour, e.g. #0E7C66")
ap.add_argument("--name", help="school / product name shown in the footer and page titles")
ap.add_argument("--admin", help="name shown in the sidebar profile")
a = ap.parse_args()

def shade(hexc, f):
    h = hexc.lstrip("#"); r, g, b = (int(h[i:i + 2], 16) for i in (0, 2, 4))
    return "#%02X%02X%02X" % tuple(int(c * f) for c in (r, g, b))

if a.primary:
    new, hover = a.primary.upper(), shade(a.primary, 0.9)
    pairs = [("#FE9F43", new), ("#E88A2D", hover), ("254, 159, 67", ", ".join(str(int(a.primary.lstrip('#')[i:i + 2], 16)) for i in (0, 2, 4)))]
    for dp, _, files in os.walk(f"{ROOT}/assets"):
        if "plugins" in dp or "/edu" in dp:
            continue
        for f in files:
            if f.endswith((".css", ".js", ".svg")):
                p = os.path.join(dp, f); t = open(p, encoding="utf8", errors="ignore").read(); o = t
                for x, y in pairs:
                    t = re.sub(re.escape(x), y, t, flags=re.I)
                if t != o: open(p, "w", encoding="utf8").write(t)
    for f in os.listdir(f"{ROOT}/tools"):
        if f in ("dashboards.py", "build.py", "auth.py"):
            p = f"{ROOT}/tools/{f}"; t = open(p).read()
            open(p, "w").write(re.sub("#FE9F43", new, t, flags=re.I))
    print("primary ->", new)
if a.name or a.admin:
    p = f"{ROOT}/tools/build.py"; t = open(p).read()
    if a.name: t = re.sub(r'^APP = ".*"', f'APP = "{a.name}"', t, flags=re.M)
    if a.admin: t = t.replace("School Admin", a.admin)
    open(p, "w").write(t)
for cmd in ("build.py", "auth.py", "blade.py"):
    subprocess.run([sys.executable, f"{ROOT}/tools/{cmd}"], check=True, stdout=subprocess.DEVNULL)
print("rebuilt static pages + laravel package")
