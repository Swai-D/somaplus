"""Port widget-specific rules (chat, calendar, pricing...) from Edudash's style.css into assets/css/edu-widgets.css"""
import re, sys
SRC = "/home/claude/merge-temp/edudash-temp/assets/css/style.css"
PATTERNS = sys.argv[1:] or [r"\.chat-"]
VARS = {  # Edudash design tokens -> Dream-ish values
 "--white": "#fff", "--bg-color": "#F7F7F7", "--neutral-200": "#E6EAED", "--neutral-300": "#D5DADF", "--neutral-400": "#B8BCC9",
 "--input-form-light": "#E6EAED", "--input-bg": "#fff", "--primary-600": "#FE9F43", "--success-main": "#03C95A",
 "--text-primary-light": "#212B36", "--text-secondary-light": "#646B72", "--danger-600": "#E70D0D", "--warning-600": "#FFC107",
}
def blocks(t, i=0):
    out = []; n = len(t)
    while i < n:
        j = t.find("{", i)
        if j < 0: break
        depth = 1; k = j + 1
        while k < n and depth:
            depth += (t[k] == "{") - (t[k] == "}"); k += 1
        out.append((t[i:j].strip(), t[j + 1:k - 1])); i = k
    return out
t = re.sub(r"/\*.*?\*/", "", open(SRC, encoding="utf8").read(), flags=re.S)
rx = re.compile("|".join(PATTERNS))
res = []
for sel, body in blocks(t):
    if sel.startswith("@media"):
        inner = [f"  {s} {{{b}}}" for s, b in blocks(body) if rx.search(s)]
        if inner: res.append(sel + " {\n" + "\n".join(inner) + "\n}")
    elif rx.search(sel):
        res.append(f"{sel} {{{body}}}")
css = "\n".join(res)
css = re.sub(r"var\((--[\w-]+)\)", lambda m: VARS.get(m.group(1), "#888"), css)
open("/home/claude/somaplus/assets/css/edu-widgets.css", "w").write("/* ported from Edudash style.css */\n" + css + "\n")
print(len(res), "rules;", "tokens:", len(set(re.findall(r"\.([A-Za-z][\w-]*)", css))))
