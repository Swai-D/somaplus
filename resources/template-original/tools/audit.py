import glob, os, json
from playwright.sync_api import sync_playwright
root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
os.makedirs("/home/claude/shots/all", exist_ok=True)
res = {}
with sync_playwright() as p:
    b = p.chromium.launch()
    for f in sorted(glob.glob(f"{root}/*.html")):
        name = os.path.basename(f)[:-5]
        pg = b.new_page(viewport={"width": 1440, "height": 900})
        errs = []
        pg.on("pageerror", lambda e: errs.append(str(e)[:120]))
        pg.on("requestfailed", lambda r: errs.append("FAILED " + r.url.split("/assets/")[-1][:60]) if "file://" in r.url and "theme/" not in r.url else None)
        pg.goto("file://" + f); pg.wait_for_timeout(900)
        info = pg.evaluate("""() => ({h: (document.querySelector('.page-wrapper')||document.body).scrollHeight,
          overflowX: document.documentElement.scrollWidth > innerWidth + 2,
          unstyled: [...document.querySelectorAll('body [class]')].filter(e=>/(^| )(bg-base|radius-|px-\\d|py-\\d)/.test(e.className)).length})""")
        pg.screenshot(path=f"/home/claude/shots/all/{name}.png")
        res[name] = {"errs": errs, **info}
        pg.close()
    b.close()
bad = {k: v for k, v in res.items() if v["errs"] or v["overflowX"]}
print(len(res), "pages;", len(bad), "with issues")
for k, v in bad.items(): print(k, v["errs"][:3], "overflowX" if v["overflowX"] else "")
json.dump(res, open("/home/claude/shots/audit.json", "w"))
