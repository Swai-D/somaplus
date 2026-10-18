"""Mini Blade emulator for the restricted syntax SomaPlus uses; renders every view to /tmp/blade_out for Chromium checks."""
import os, re, sys, json
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__))); V = f"{ROOT}/laravel/resources/views"
OUT = "/tmp/blade_out"; os.makedirs(OUT, exist_ok=True)
rd = lambda n: open(f"{V}/{n.replace('.', '/')}.blade.php", encoding="utf8").read()

def verb_split(t):
    keep = []
    def stash(m): keep.append(m.group(1)); return f"\x00V{len(keep)-1}\x00"
    return re.sub(r"(?<!@)@verbatim(.*?)@endverbatim", stash, t, flags=re.S), keep

def render(name, path, stacks=None, sections=None, top=True):
    t = rd(name); t, keep = verb_split(t)
    ext = re.match(r"\s*@extends\('([^']+)'\)", t)
    secs = dict(sections or {}); stacks = stacks if stacks is not None else {}
    for m in re.finditer(r"@section\('(\w+)', '((?:[^'\\]|\\.)*)'\)", t): secs[m.group(1)] = m.group(2)
    for m in re.finditer(r"@section\('(\w+)'\)(.*?)@endsection", t, re.S): secs[m.group(1)] = m.group(2)
    for m in re.finditer(r"@push\('(\w+)'\)(.*?)@endpush", t, re.S): stacks.setdefault(m.group(1), []).append(m.group(2))
    if ext:
        return render(ext.group(1), path, stacks, {k: restore(v, keep) for k, v in secs.items()}, False) if False else layout(ext.group(1), path, stacks, secs, keep)
    return finish(t, keep, path, secs, stacks)

def layout(name, path, stacks, secs, keep_page):
    t = rd(name); t, keep = verb_split(t)
    secs = {k: restore(v, keep_page) for k, v in secs.items()}
    stacks = {k: [restore(x, keep_page) for x in v] for k, v in stacks.items()}
    return finish(t, keep, path, secs, stacks)

def restore(t, keep): return re.sub(r"\x00V(\d+)\x00", lambda m: keep[int(m.group(1))], t)

def expr(e, path):
    e = e.strip()
    m = re.match(r"asset\('([^']+)'\)$", e)
    if m: return m.group(1)
    m = re.match(r"url\('([^']+)'\)$", e)
    if m: return "" if m.group(1) == "/" else m.group(1)
    e2 = re.sub(r"request\(\)->is\('([^']+)'\)", lambda m: str(("/" if path == "index" else path) == m.group(1)), e)
    e2 = e2.replace("||", " or ")
    m = re.match(r"\((.+)\) \? '([^']*)' : '([^']*)'$|(.+?) \? '([^']*)' : '([^']*)'$", e2)
    if m:
        c, a, b = (m.group(1), m.group(2), m.group(3)) if m.group(1) else (m.group(4), m.group(5), m.group(6))
        return a if eval(c) else b
    raise ValueError("unsupported blade expression: " + e)

def finish(t, keep, path, secs, stacks):
    t = re.sub(r"@include\('([^']+)'\)", lambda m: render_partial(m.group(1), path), t)
    t = re.sub(r"@yield\('(\w+)'(?:, '([^']*)')?\)", lambda m: secs.get(m.group(1), m.group(2) or ""), t)
    t = re.sub(r"@stack\('(\w+)'\)", lambda m: "".join(stacks.get(m.group(1), [])), t)
    t = re.sub(r"\{\{(.*?)\}\}", lambda m: expr(m.group(1), path), t, flags=re.S)
    t = restore(t, keep)
    left = re.findall(r"@(?:extends|section|endsection|push|endpush|include|yield|stack|verbatim|endverbatim)\b|\{\{|\x00V", t)
    assert not left, f"{path}: unprocessed blade tokens {left[:3]}"
    return t

def render_partial(name, path):
    t = rd(name); t, keep = verb_split(t)
    t = re.sub(r"\{\{(.*?)\}\}", lambda m: expr(m.group(1), path), t, flags=re.S)
    return restore(t, keep)

n = 0
for f in sorted(os.listdir(f"{V}/pages")) + [f"../auth/{x}" for x in ("login.blade.php", "register.blade.php")]:
    stem = os.path.basename(f)[:-10]; view = ("auth." if "auth" in f else "pages.") + stem
    html = render(view, stem)
    open(f"{OUT}/{stem}.html", "w", encoding="utf8").write(html); n += 1
print("rendered", n, "views OK")
