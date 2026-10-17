import sys, os
from playwright.sync_api import sync_playwright
root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
pages = sys.argv[1:] or ["index"]
os.makedirs("/home/claude/shots", exist_ok=True)
with sync_playwright() as p:
    b = p.chromium.launch()
    pg = b.new_page(viewport={"width": 1440, "height": 900})
    for name in pages:
        errs = []
        pg.on("pageerror", lambda e: errs.append(str(e)))
        pg.on("requestfailed", lambda r: errs.append("FAILED " + r.url[-80:]))
        pg.goto(f"file://{root}/{name}.html"); pg.wait_for_timeout(1500)
        pg.screenshot(path=f"/home/claude/shots/{name}.png", full_page=False)
        pg.screenshot(path=f"/home/claude/shots/{name}-full.png", full_page=True)
        print(name, "errors:", errs[:8])
        pg.remove_listener("pageerror", pg.listeners("pageerror")[0]) if hasattr(pg, "listeners") else None
    b.close()
