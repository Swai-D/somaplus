#!/usr/bin/env python3
"""
SomaPlus build script
---------------------
Takes every Edudash page (content) and re-renders it inside the DreamsPOS
shell (design).  Run:  python3 tools/build.py [page-stem ...]
"""
import os, re, sys, glob, copy
from bs4 import BeautifulSoup, NavigableString, Tag

HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.dirname(HERE)
EDU = os.environ.get("EDU_SRC", "/home/claude/merge-temp/edudash-temp")
DREAM = os.environ.get("DREAM_SRC", "/home/claude/merge-temp/dream-temp")
APP = "SomaPlus"
sys.path.insert(0, HERE)
from dashboards import render_school, render_student, render_teacher, render_parent, render_lms, render_subscription
SPECIAL = {"index": render_school, "index-2": render_student, "index-3": render_teacher, "index-4": render_parent, "index-5": render_lms, "subscription-plan": render_subscription}

# ------------------------------------------------------------------ tokens
def css_tokens(path):
    t = open(path, encoding="utf8", errors="ignore").read()
    return set(re.findall(r"\.([A-Za-z][\w-]*)", t))

DREAM_TOKENS = css_tokens(f"{ROOT}/assets/css/style.css") | css_tokens(f"{ROOT}/assets/css/bootstrap.min.css")
TABLER = css_tokens(f"{ROOT}/assets/plugins/tabler-icons/tabler-icons.min.css")
DREAM_TOKENS |= TABLER | {"ti", "fa", "fas", "far", "fab", "datatable", "no-sort", "thead-light", "table-responsive", "table-action", "filter-menu", "search-set", "search-input", "btn-searchset", "feather-search", "image-upload", "image-uploads", "sp-upload", "nav-tabs-solid", "nav-tabs-rounded", "nav-tabs"}

def ti(name, fallback="circle"):
    return name if f"ti-{name}" in TABLER else fallback

# ------------------------------------------------------------------ icons
RI2TI = {
    "ri-home-4-line": "layout-grid", "ri-graduation-cap-line": "school", "ri-user-follow-line": "user-check",
    "ri-account-circle-line": "users", "ri-list-view": "chalkboard", "ri-file-edit-line": "file-pencil",
    "ri-money-dollar-circle-line": "cash", "ri-calendar-check-line": "calendar-check", "ri-time-line": "clock",
    "ri-book-2-line": "books", "ri-user-settings-line": "user-cog", "ri-booklet-line": "notes",
    "ri-calendar-event-line": "calendar-event", "ri-message-2-line": "message", "ri-price-tag-3-line": "tag",
    "ri-macbook-line": "shield-lock", "ri-shield-check-line": "lock", "ri-delete-bin-6-line": "trash",
    "ri-delete-bin-2-line": "trash", "ri-edit-2-line": "edit", "ri-edit-line": "edit", "ri-user-3-line": "user",
    "ri-settings-3-line": "settings", "ri-close-large-line": "x", "ri-close-fill": "x", "ri-book-open-line": "book",
    "ri-arrow-right-s-line": "chevron-right", "ri-arrow-left-s-line": "chevron-left", "ri-arrow-down-s-line": "chevron-down",
    "ri-arrow-up-s-fill": "chevron-up", "ri-eye-line": "eye", "ri-eye-off-line": "eye-off", "ri-building-4-line": "building",
    "ri-error-warning-line": "alert-circle", "ri-file-upload-line": "file-export", "ri-file-excel-line": "file-spreadsheet",
    "ri-file-3-line": "file-type-pdf", "ri-add-large-line": "circle-plus", "ri-money-dollar-box-line": "currency-dollar",
    "ri-star-fill": "star-filled", "ri-arrow-go-back-line": "arrow-back-up", "ri-login-box-line": "login",
    "ri-printer-line": "printer", "ri-lock-2-line": "lock", "ri-chat-1-line": "message-circle", "ri-calendar-close-line": "calendar-off",
    "ri-calendar-2-line": "calendar", "ri-group-line": "users-group", "ri-file-text-line": "file-text",
    "ri-download-2-line": "download", "ri-arrow-left-up-line": "arrow-up-left", "ri-arrow-left-down-line": "arrow-down-left",
    "ri-book-line": "book", "ri-wallet-3-line": "wallet", "ri-user-heart-line": "user-heart", "ri-parent-line": "users",
    "ri-hospital-line": "building-hospital", "ri-flight-takeoff-line": "plane-departure", "ri-close-circle-line": "circle-x",
    "ri-alarm-warning-line": "alarm", "ri-circle-fill": "point-filled", "ri-sun-line": "sun", "ri-moon-line": "moon",
    "ri-computer-line": "device-desktop", "ri-shut-down-line": "logout", "ri-contract-left-line": "chevrons-left",
}
ICONIFY2TI = {
    "tabler:dots-vertical": "dots-vertical", "ion:search-outline": "search", "radix-icons:cross-2": "x",
    "iconoir:bell": "bell", "heroicons:bars-3-solid": "menu-2", "bitcoin-icons:verify-outline": "rosette-discount-check",
    "fluent:delete-24-regular": "trash", "lucide:edit": "edit", "hugeicons:view": "eye", "entypo:dots-three-vertical": "dots-vertical",
    "iconamoon:check-light": "check", "bxs:up-arrow": "arrow-up", "solar:calendar-linear": "calendar",
    "solar:gallery-linear": "photo", "ph:link": "link", "mdi:clear-circle-outline": "circle-x",
    "ic:baseline-block": "ban", "f7:paperplane": "send",
}

# ------------------------------------------------------------------ class mapping
COLOR_ALIASES = {"neutral": "secondary", "lilac": "purple", "danger": "danger", "success": "success",
                 "warning": "warning", "info": "info", "primary": "primary", "secondary": "secondary",
                 "purple": "purple", "orange": "orange", "cyan": "cyan", "pink": "pink", "teal": "teal", "yellow": "warning"}

def px2bs(n):
    n = int(n)
    return 0 if n == 0 else 1 if n <= 6 else 2 if n <= 12 else 3 if n <= 20 else 4 if n <= 36 else 5

PAGE_KEEP = set()

def map_class(tok):
    """Return a list of Dream-compatible classes for one Edudash class token."""
    if tok in PAGE_KEEP:
        return [tok]
    m = re.match(r"^(btn)-(primary|secondary|success|danger|warning|info)-(\d+)$", tok)
    if m:
        c, n = m.group(2), int(m.group(3))
        return [f"btn-{c}"] if n >= 400 else [f"btn-outline-{c}"]
    m = re.match(r"^(m|p)([tbseyx])?-(\d+)$", tok)
    if m and int(m.group(3)) > 5:
        return [f"{m.group(1)}{m.group(2) or ''}-{px2bs(m.group(3))}"]
    m = re.match(r"^(gap|row-gap|column-gap)-(\d+)$", tok)
    if m and int(m.group(2)) > 5:
        return [f"{m.group(1)}-{px2bs(m.group(2))}"]
    m = re.match(r"^(p|m)([tbseyx])?-(\d+)-px$", tok)
    if m:
        return [f"{m.group(1)}{m.group(2) or ''}-{px2bs(m.group(3))}"]
    if tok in ("text-secondary-light",):
        return ["text-gray-5"] if "text-gray-5" in DREAM_TOKENS else ["text-muted"]
    if tok in ("text-primary-light", "text-neutral-900", "text-neutral-800"):
        return ["text-dark"]
    if tok in ("fw-semibold", "fw-medium", "fw-bold", "fw-normal"):
        return [tok]
    m = re.match(r"^bg-(\w+)-(50|100|200)$", tok)
    if m and COLOR_ALIASES.get(m.group(1)):
        t = f"bg-{COLOR_ALIASES[m.group(1)]}-transparent"
        return [t] if t in DREAM_TOKENS else []
    m = re.match(r"^bg-(\w+)-(300|400|500|600|700|800|900)$", tok)
    if m and COLOR_ALIASES.get(m.group(1)):
        t = f"bg-{COLOR_ALIASES[m.group(1)]}"
        return [t] if t in DREAM_TOKENS else []
    m = re.match(r"^text-(\w+)-(500|600|700)$", tok)
    if m and COLOR_ALIASES.get(m.group(1)):
        return [f"text-{COLOR_ALIASES[m.group(1)]}"]
    if tok == "radius-8" or tok == "radius-4":
        return ["rounded"]
    if tok in ("radius-12", "radius-16", "rounded-circle"):
        return ["rounded-circle"] if tok == "rounded-circle" else ["rounded-3"]
    if tok in DREAM_TOKENS:
        return [tok]
    return []

SIZE_RE = re.compile(r"^(w|h|max-w|max-h|min-w|min-h|width|height)-(\d+)-px$")
SIZE_PROP = {"w": "width", "h": "height", "max-w": "max-width", "max-h": "max-height", "min-w": "min-width",
             "min-h": "min-height", "width": "width", "height": "height"}

def clean_classes(root):
    for el in root.find_all(True):
        cl = el.get("class")
        if not cl:
            continue
        out = []
        styles = []
        for t in cl:
            m = SIZE_RE.match(t)
            if m:
                styles.append(f"{SIZE_PROP[m.group(1)]}:{m.group(2)}px")
                continue
            for x in map_class(t):
                if x not in out:
                    out.append(x)
        if styles:
            el["style"] = (el.get("style", "").rstrip(";") + ";" if el.get("style") else "") + ";".join(styles)
        if out:
            el["class"] = out
        else:
            del el["class"]

# ------------------------------------------------------------------ helpers
def new(soup, name, cls=None, **attrs):
    t = soup.new_tag(name)
    if cls:
        t["class"] = cls.split()
    for k, v in attrs.items():
        t[k.replace("_", "-")] = v
    return t

def frag(html):
    return BeautifulSoup(html, "lxml").body.contents[0]

CURRENCY = "TSh"

def localize(s):
    s = re.sub(r"\$(?=\d)", CURRENCY + " ", s)
    return s.replace("'$'+", f"'{CURRENCY} '+").replace('"$"+', f'"{CURRENCY} "+')

def link_fix(s):
    s = re.sub(r"\.php\.html", ".html", s)
    return s.replace("https://edudash-php.theme.picode.in/assets/images/", "assets/img/edu/").replace("assets/images/", "assets/img/edu/")

def convert_icons(root, soup):
    for i in root.find_all("i"):
        cl = i.get("class", [])
        for c in cl:
            if c.startswith("ri-"):
                name = ti(RI2TI.get(c, "circle"))
                keep = [x for x in cl if not x.startswith("ri-") and x in DREAM_TOKENS and x not in ("animate-spin",)]
                i["class"] = ["ti", f"ti-{name}"] + keep
                break
    for ic in root.find_all("iconify-icon"):
        name = ti(ICONIFY2TI.get(ic.get("icon", ""), "circle"))
        i = new(soup, "i", f"ti ti-{name}")
        ic.replace_with(i)

def unwrap_icon_spans(root):
    for sp in root.find_all("span"):
        kids = [k for k in sp.children if not (isinstance(k, NavigableString) and not k.strip())]
        if len(kids) == 1 and getattr(kids[0], "name", None) == "i" and not sp.get_text(strip=True):
            if sp.parent and sp.parent.name in ("a", "button") and "btn" in (sp.parent.get("class") or []):
                sp.unwrap()

def dots_buttons(root):
    for b in root.select("button[data-bs-toggle=dropdown]"):
        if b.find("i", class_="ti-dots-vertical") and not b.get_text(strip=True):
            b.name = "a"; b["href"] = "javascript:void(0);"; b.attrs.pop("type", None)
            b["class"] = ["action-icon", "btn", "btn-icon", "btn-sm", "btn-outline-white"]

def tidy_buttons(root):
    for b in root.select("a.btn, button.btn"):
        for i in b.find_all("i", recursive=False):
            if i.next_sibling or i.previous_sibling:
                txt = b.get_text(strip=True)
                if txt and "me-1" not in i.get("class", []) and "ms-1" not in i.get("class", []):
                    i["class"] = i.get("class", []) + ["me-1"]

# ------------------------------------------------------------------ badges
def badges(root, soup, orig_classes):
    for el, cl in orig_classes:
        if el.name == "button" and "btn" not in cl and any(c.startswith("bg-neutral") for c in cl) and any(c.startswith("px-") for c in cl):
            el["class"] = ["btn", "btn-sm", "btn-light"]
            continue
        if el.name not in ("span", "div", "a", "p"):
            continue
        bg = [re.match(r"^bg-(\w+)-100$", c) for c in cl]
        tx = [re.match(r"^text-(\w+)-600$", c) for c in cl]
        bg = [m.group(1) for m in bg if m]
        tx = [m.group(1) for m in tx if m]
        has_pad = any(c.startswith("px-") for c in cl)
        if bg and tx and bg[0] == tx[0] and has_pad and el.name == "span" and "badge" not in el.get("class", []):
            col = COLOR_ALIASES.get(bg[0], "secondary")
            el["class"] = ["badge", f"badge-{col}", "d-inline-flex", "align-items-center", "badge-xs"]
            p = new(soup, "i", "ti ti-point-filled me-1")
            el.insert(0, p)

# ------------------------------------------------------------------ tables
def checkbox_cells(root, soup):
    for fc in root.select("div.form-check"):
        inp = fc.find("input", {"type": "checkbox"})
        if not inp:
            continue
        lab = fc.find("label")
        txt = lab.get_text(strip=True) if lab else ""
        wrap = new(soup, "div", "d-flex align-items-center")
        l = new(soup, "label", "checkboxs")
        i2 = soup.new_tag("input", type="checkbox")
        for k in ("id", "name", "value", "checked"):
            if inp.get(k) is not None:
                i2[k] = inp[k]
        l.append(i2)
        l.append(new(soup, "span", "checkmarks"))
        wrap.append(l)
        if txt:
            s = new(soup, "span", "ms-2")
            s.string = txt
            wrap.append(s)
        fc.replace_with(wrap)

def action_dropdowns(root, soup):
    for bg in root.select("div.btn-group"):
        menu = bg.find("ul", class_="dropdown-menu")
        if not menu:
            continue
        dd = new(soup, "div", "dropdown table-action")
        trg = new(soup, "a", "action-icon btn btn-icon btn-sm btn-outline-white", href="javascript:void(0);")
        trg["data-bs-toggle"] = "dropdown"
        trg["aria-expanded"] = "false"
        trg.append(new(soup, "i", "ti ti-dots-vertical"))
        dd.append(trg)
        ul = new(soup, "ul", "dropdown-menu dropdown-menu-end p-2")
        for li in menu.find_all("li", recursive=False):
            src = li.find(["a", "button"])
            if not src:
                continue
            a = new(soup, "a", "dropdown-item d-flex align-items-center rounded-1")
            a["href"] = src.get("href", "javascript:void(0);")
            for k, v in src.attrs.items():
                if k.startswith("data-bs-"):
                    a[k] = v
            ic = src.find("i")
            if ic:
                ic2 = copy.copy(ic)
                ic2["class"] = [c for c in ic2.get("class", []) if c.startswith("ti")] + ["me-2"]
                a.append(ic2)
            a.append(NavigableString(src.get_text(strip=True)))
            if re.search(r"delete|remove", src.get_text(), re.I):
                a["class"] = a["class"] + ["text-danger"]
            nl = new(soup, "li")
            nl.append(a)
            ul.append(nl)
        dd.append(ul)
        bg.replace_with(dd)

def restructure_table_card(body, soup):
    """Edudash: .card > .card-body.dataTable-wrapper > [toolbar, div.p-0>table]  ->  Dream card-header + table."""
    for wrap in body.select(".dataTable-wrapper"):
        tbl = wrap.find("table")
        if not tbl:
            continue
        toolbar = None
        for ch in wrap.find_all("div", recursive=False):
            if "border-bottom" in ch.get("class", []):
                toolbar = ch
                break
        card = wrap.find_parent("div", class_="card")
        if not card:
            continue
        header = new(soup, "div", "card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3")
        left = new(soup, "div", "d-flex align-items-center flex-wrap gap-2")
        if toolbar:
            for dd in toolbar.select("div.dropdown"):
                if dd.find_parent("form"):
                    continue
                t = dd.find("button")
                label = t.get_text(" ", strip=True) if t else ""
                if not label:
                    continue
                for x in dd.find_all("i", recursive=True):
                    pass
                btn = new(soup, "a", "btn btn-white btn-md d-inline-flex align-items-center dropdown-toggle", href="javascript:void(0);")
                btn["data-bs-toggle"] = "dropdown"
                if "Filter" in label:
                    btn["data-bs-auto-close"] = "outside"
                    btn.append(new(soup, "i", "ti ti-filter me-1"))
                elif "Export" in label:
                    btn.append(new(soup, "i", "ti ti-file-export me-1"))
                btn.append(NavigableString(re.sub(r"\s+", " ", label).strip()))
                menu = dd.find(["ul", "div"], class_="dropdown-menu")
                if menu:
                    menu["class"] = ["dropdown-menu", "p-3"] + (["dropdown-menu-lg", "filter-menu"] if "Filter" in label else [])
                    if "Filter" in label:
                        menu["style"] = "min-width:320px"
                nd = new(soup, "div", "dropdown")
                nd.append(btn)
                if menu:
                    nd.append(menu.extract())
                left.append(nd)
        ss = frag('<div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div>')
        header.insert(0, ss)
        header.append(left)
        table_holder = tbl.find_parent("div")
        tw = new(soup, "div", "table-responsive")
        tbl.extract()
        tw.append(tbl)
        newbody = new(soup, "div", "card-body p-0")
        newbody.append(tw)
        for ch in list(card.children):
            if getattr(ch, "name", None):
                ch.extract()
        card.append(header)
        card.append(newbody)
        card["class"] = ["card"]

def style_tables(root):
    for t in root.find_all("table"):
        cl = set(t.get("class", []))
        cl.discard("bordered-table")
        cl.discard("data-table")
        cl.discard("mb-0")
        cl.add("table")
        if t.find_parent("div", class_="modal") is None:
            cl.add("datatable")
        t["class"] = sorted(cl)
        th = t.find("thead")
        if th:
            th["class"] = ["thead-light"]
        t.attrs.pop("id", None)
        t.attrs.pop("data-page-length", None)

# ------------------------------------------------------------------ page header
def page_header(bc, soup):
    hh = bc.find(lambda t: t.name in ("h1", "h2", "h3", "h4", "h5", "h6") or (t.name == "span" and "h6" in t.get("class", [])))
    title = hh.get_text(strip=True) if hh else ""
    crumbs = []
    holder = bc.find("div")
    if holder:
        for a in holder.find_all("a"):
            if "d-none" in a.get("class", []):
                continue
            t = a.get_text(strip=True).lstrip("/ ").strip()
            if t and t not in crumbs:
                crumbs.append(t)
    sub = " / ".join(crumbs) if crumbs else ""
    ph = new(soup, "div", "page-header")
    ai = new(soup, "div", "add-item d-flex")
    pt = new(soup, "div", "page-title")
    h4 = new(soup, "h4"); h4.string = title
    h6 = new(soup, "h6"); h6.string = sub or "Overview"
    pt.append(h4); pt.append(h6); ai.append(pt); ph.append(ai)
    ul = frag('<ul class="table-top-head"><li class="me-2"><a data-bs-toggle="tooltip" data-bs-placement="top" title="Refresh" href="javascript:location.reload()"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-toggle="tooltip" data-bs-placement="top" title="Collapse" id="collapse-header"><i class="ti ti-chevron-up"></i></a></li></ul>')
    ph.append(ul)
    action = None
    for ch in bc.find_all(["a", "button"], recursive=False):
        if "d-none" not in ch.get("class", []):
            action = ch
    if action:
        pb = new(soup, "div", "page-btn")
        pb.append(action.extract())
        ph.append(pb)
    return ph

# ------------------------------------------------------------------ forms
def tabs(root):
    for ul in root.select("ul.nav"):
        if "bordered-tab" in ul.get("class", []) or "nav-pills" in ul.get("class", []):
            ul["class"] = ["nav", "nav-tabs", "nav-tabs-solid", "nav-tabs-rounded", "mb-3"]

def dropzones(root, soup):
    for dz in root.select("div.drop-zone"):
        inp = dz.find("input", {"type": "file"})
        wrap = new(soup, "div", "image-upload download sp-upload mb-0")
        if inp:
            i2 = soup.new_tag("input", type="file")
            for k in ("name", "id", "accept", "multiple"):
                if inp.get(k) is not None:
                    i2[k] = inp[k]
            wrap.append(i2)
        up = new(soup, "div", "image-uploads")
        up.append(new(soup, "i", "ti ti-circle-plus fs-18"))
        h = new(soup, "h4"); h.string = "Drag & drop or click to upload"
        up.append(h)
        wrap.append(up)
        dz.replace_with(wrap)

def forms(root):
    for lab in root.find_all("label"):
        cl = lab.get("class", [])
        if "form-check-label" in cl or "checkboxs" in cl:
            continue
        if "form-label" not in cl:
            lab["class"] = cl + ["form-label"]

# ------------------------------------------------------------------ shell partials
def build_menu_tree():
    s = BeautifulSoup(open(f"{EDU}/student-list.php.html", encoding="utf8").read(), "lxml")
    out = []
    for li in s.select_one("ul#sidebar-menu").find_all("li", recursive=False):
        a = li.find("a", recursive=False)
        ic = [c for c in a.find("i").get("class", []) if c.startswith("ri-")][0]
        item = {"label": " ".join(a.get_text(" ", strip=True).split()), "icon": ti(RI2TI.get(ic, "circle")),
                "href": link_fix(a.get("href", "")), "children": []}
        for sa in li.select("ul.sidebar-submenu > li > a"):
            item["children"].append({"label": " ".join(sa.get_text(" ", strip=True).split()), "href": link_fix(sa["href"])})
        out.append(item)
    return out

SECTIONS = [
    ("Main", ["Dashboard"]),
    ("Academics", ["Students", "Teachers", "Guardian", "Classes", "Examinations", "Attendance", "Leaves", "Certificate", "Library"]),
    ("Finance", ["Fees Collection", "Accounts"]),
    ("People", ["HRM"]),
    ("Communication", ["Notice Board", "Event", "Message"]),
    ("Administration", ["Subscription Plan", "Role & Access", "Assign Role", "Settings"]),
    ("Pages", ["Authentication"]),
]
ICON_OVERRIDE = {"Dashboard": "layout-grid", "Certificate": "certificate", "Assign Role": "user-shield", "Role & Access": "shield-lock",
                 "Authentication": "lock", "Subscription Plan": "tag", "Event": "calendar-event", "Notice Board": "notes"}

def build_sidebar(current, blade=False):
    tree = {i["label"]: i for i in build_menu_tree()}
    def href(u):
        if not blade:
            return u
        return "{{ url('/') }}" if u == "index.html" else "{{ url('%s') }}" % u[:-5]
    def is_(u):
        return "request()->is('%s')" % ("/" if u == "index.html" else u[:-5])
    def cls_active(u, extra=""):
        if blade:
            return ' class="{{ %s ? \'active\' : \'\' }}"' % is_(u)
        return ' class="active"' if u == current else ""
    h = []
    for title, labels in SECTIONS:
        h.append(f'<li class="submenu-open"><h6 class="submenu-hdr">{title}</h6><ul>')
        for lb in labels:
            it = tree.get(lb)
            if not it:
                continue
            icon = ti(ICON_OVERRIDE.get(lb, it["icon"]))
            if it["children"]:
                if blade:
                    cond = " || ".join(is_(c["href"]) for c in it["children"])
                    a_cls = "{{ (%s) ? 'subdrop active' : '' }}" % cond
                    ul_style = " style=\"{{ (%s) ? 'display: block;' : '' }}\"" % cond
                else:
                    active = any(c["href"] == current for c in it["children"])
                    a_cls = "subdrop active" if active else ""
                    ul_style = ' style="display: block;"' if active else ""
                h.append(f'<li class="submenu"><a href="javascript:void(0);" class="{a_cls}"><i class="ti ti-{icon} fs-16 me-2"></i><span>{lb}</span><span class="menu-arrow"></span></a>')
                h.append(f'<ul{ul_style}>')
                for c in it["children"]:
                    h.append(f'<li><a href="{href(c["href"])}"{cls_active(c["href"])}>{c["label"]}</a></li>')
                h.append("</ul></li>")
            else:
                h.append(f'<li><a href="{href(it["href"])}"{cls_active(it["href"])}><i class="ti ti-{icon} fs-16 me-2"></i><span>{lb}</span></a></li>')
        h.append("</ul></li>")
    logo = lambda cls, src: f'<a href="{href("index.html")}" class="{cls}"><img src="assets/img/{src}" alt="{APP}"></a>'
    return f'''<div class="sidebar" id="sidebar">
<div class="sidebar-logo">{logo("logo logo-normal","logo.svg")}{logo("logo logo-white","logo-white.svg")}{logo("logo-small","logo-small.svg")}{logo("logo-small-white","logo-small-white.svg")}
<a id="toggle_btn" href="javascript:void(0);"><i data-feather="chevrons-left" class="feather-16"></i></a></div>
<div class="sidebar-header p-3 pb-0 pt-2"><div class="text-center rounded bg-light p-2 mb-4 sidebar-profile d-flex align-items-center">
<div class="avatar avatar-md online"><img src="assets/img/edu/users/user1.png" alt="Admin" class="img-fluid rounded-circle"></div>
<div class="text-start sidebar-profile-info ms-2"><h6 class="fs-14 fw-bold mb-1">School Admin</h6><p class="fs-12">Administrator</p></div></div></div>
<div class="sidebar-inner slimscroll"><div id="sidebar-menu" class="sidebar-menu"><ul>{"".join(h)}</ul></div></div></div>'''

_dream_header = None
def build_header():
    global _dream_header
    if _dream_header is None:
        s = BeautifulSoup(open(f"{DREAM}/admin-dashboard.html", encoding="utf8").read(), "lxml")
        hd = s.select_one("div.header")
        for sel in ("li.select-store-dropdown", "li.link-nav", "li.pos-nav"):
            for x in hd.select(sel):
                x.decompose()
        for a in hd.select("a.logo img, a.logo-small img, a.logo-small-white img"):
            pass
        html = str(hd)
        html = html.replace("logo-small-white.png", "logo-small-white.svg").replace("logo-small.png", "logo-small.svg")
        html = html.replace('href="admin-dashboard.html#sidebar"', 'href="#sidebar"').replace('action="admin-dashboard.html#"', 'action="#"')
        html = re.sub(r"(John Smilga)", "School Admin", html)
        html = html.replace("<p>Admin</p>", "<p>Administrator</p>")
        html = html.replace("avator1.jpg", "avatar-01.jpg").replace('href="signin.html"', 'href="login.html"')
        html = html.replace('href="sales-report.html"', 'href="general.html"').replace('href="general-settings.html"', 'href="general.html"')
        html = html.replace("Arabic", "Kiswahili")
        hs = BeautifulSoup(html, "lxml")
        # school-flavoured search + notifications
        for ul in hs.select("ul.search-tags"):
            ul.clear()
            for t, u in (("Students", "student-list.html"), ("Teachers", "teacher-list.html"), ("Fees", "fees-collect.html")):
                ul.append(frag(f'<li><a href="{u}">{t}</a></li>'))
        for p in hs.select(".search-info p"):
            p.decompose()
        for h6 in hs.select(".search-info h6"):
            if "Customers" in h6.get_text():
                h6.find_parent("div", class_="search-info").decompose()
        notes = [("assets/img/profiles/avatar-03.jpg", "<span class=\"noti-title\">Amina Juma</span> applied for sick leave", "10 mins ago"),
                 ("assets/img/profiles/avatar-17.jpg", "Fee payment received from <span class=\"noti-title\">Class 7 (A)</span>", "05 mins ago"),
                 ("assets/img/profiles/avatar-02.jpg", "New notice posted: <span class=\"noti-title\">Mid-term exam schedule</span>", "1 hour ago")]
        lst = hs.select_one("ul.notification-list")
        if lst:
            lst.clear()
            for img, txt, tm in notes:
                lst.append(frag(f'<li class="notification-message"><a href="notice-board.html" class="recent-msg"><div class="media d-flex"><span class="avatar flex-shrink-0"><img alt="" src="{img}"></span><div class="flex-grow-1"><p class="noti-details">{txt}</p><p class="noti-time">{tm}</p></div></div></a></li>'))
        for a in hs.select("div.topnav-dropdown-footer a"):
            if a.get("href", "").startswith("activities"):
                a["href"] = "notification.html"
        for i, a in enumerate(hs.select("li.flag-nav a.dropdown-item")[:2]):
            a["data-lang"] = ("en", "sw")[i]
            if i == 1:
                a.find("img")["src"] = "assets/img/flags/tz.svg"
        _dream_header = str(hs.select_one("div.header"))
        _dream_header = _dream_header.replace("activities.html", "notice-board.html")
    return _dream_header

HEAD = '''<!DOCTYPE html>
<html lang="en" data-layout-mode="light_mode">
<head>
<meta charset="utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{title} | SomaPlus School Management</title>
<script src="assets/js/theme-script.js"></script>
<link rel="shortcut icon" type="image/svg+xml" href="assets/img/favicon.svg">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/css/animate.css">
<link rel="stylesheet" href="assets/plugins/tabler-icons/tabler-icons.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/edu-widgets.css">
<link rel="stylesheet" href="assets/css/somaplus.css">
<link rel="stylesheet" href="assets/plugins/flatpickr/flatpickr.min.css">
</head>
<body>
<div id="global-loader"><div class="whirly-loader"></div></div>
<div class="main-wrapper">
'''

TAIL = '''<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/feather.min.js"></script>
<script src="assets/plugins/simplebar/simplebar.min.js"></script>
{apex}<script src="assets/js/datatable.js"></script>
<script src="assets/plugins/flatpickr/flatpickr.min.js"></script>
<script src="assets/js/script.js"></script>
<script src="assets/js/somaplus-i18n.js"></script>
<script src="assets/js/somaplus-crud.js"></script>
{inline}
</body>
</html>
'''

# ------------------------------------------------------------------ convert one page
CHART_COLORS = {"#25A194": "#0E9384", "#FF7A2C": "#FE9F43", "#487FFF": "#3538CD", "#45B369": "#03C95A",
                "#EF4A00": "#E70D0D", "#FF9F29": "#FE9F43", "#9935FE": "#7638FF", "#FF7E56": "#FE9F43"}

EXTRA = {"event": ('<script src="assets/plugins/fullcalendar/index.global.min.js"></script>\n', """
document.addEventListener('DOMContentLoaded',function(){
  var el=document.getElementById('calendar'); if(!el||!window.FullCalendar) return;
  var d=new Date(), y=d.getFullYear(), m=String(d.getMonth()+1).padStart(2,'0');
  new FullCalendar.Calendar(el,{initialView:'dayGridMonth',height:'auto',
    headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth,timeGridWeek,timeGridDay'},
    events:[{title:'Design Conference',start:y+'-'+m+'-05',color:'#FE9F43'},{title:'Weekend Festival',start:y+'-'+m+'-12',end:y+'-'+m+'-14',color:'#092C4C'},
            {title:'Ultra Europe 2019',start:y+'-'+m+'-20',color:'#0E9384'},{title:'Parents Meeting',start:y+'-'+m+'-26T10:00:00',color:'#3538CD'}]
  }).render();
});""")}

PARTS = {}

def assemble(out_name, title, content, modal_html, inline):
    content, modal_html = localize(content), localize(modal_html)
    inline = [localize(c) for c in inline]
    html = HEAD.format(title=title)
    html += build_header() + "\n" + build_sidebar(out_name)
    html += '\n<div class="page-wrapper"><div class="content">\n' + content + '\n</div>\n'
    html += f'<div class="copyright-footer d-flex align-items-center justify-content-between border-top bg-white gap-3 flex-wrap"><p class="fs-13 text-gray-9 mb-0">&copy; <span id="current-year"></span> {APP}. All rights reserved.</p><p>Built by <a href="javascript:void(0);" class="link-primary">Odessa Lab Technologies</a></p></div></div>\n'
    html += "</div>\n" + modal_html
    needs_apex = any("ApexCharts" in c for c in inline)
    jq = '<script src="assets/js/jquery-3.7.1.min.js"></script>\n' if any(re.search(r"\$\(|\$\.|jQuery", c) for c in inline) else ""
    apex = jq + ('<script src="assets/plugins/apexchart/apexcharts.min.js"></script>\n' if needs_apex else "")
    inl = "".join(f"<script>\n{c}\n</script>\n" for c in inline)
    inl += '<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>'
    stem = out_name[:-5]
    if stem in EXTRA:
        apex += EXTRA[stem][0]
        inl += f"<script>{EXTRA[stem][1]}</script>\n"
    PARTS[stem] = {"title": title, "content": link_fix(content), "modal": link_fix(modal_html), "vendor": apex, "scripts": inl}
    html += TAIL.format(apex=apex, inline=inl)
    html = link_fix(html)
    return html

def convert(path):
    stem = os.path.basename(path).replace(".php.html", "")
    out_name = f"{stem}.html"
    if stem in SPECIAL:
        content, inline, title = SPECIAL[stem](path)
        return out_name, assemble(out_name, title, content, "", inline)
    soup = BeautifulSoup(open(path, encoding="utf8").read(), "lxml")
    body = soup.select_one("div.dashboard-main-body")
    if body is None:
        return out_name, None
    title = (soup.select_one("div.breadcrumb h1") or soup.title).get_text(strip=True)
    modals = [m.extract() for m in soup.select("div.modal") if not body.find(lambda t: t is m)]
    inline = []
    for sc in soup.find_all("script"):
        if sc.get("src") or not (sc.string or "").strip():
            continue
        code = sc.string
        if "DataTable" in code and "Chart" not in code and "ApexCharts" not in code:
            continue
        code = re.sub(r"toggleClass\((['\"])ri-eye-off-line\1\)", 'toggleClass("ti-eye ti-eye-off")', code)
        for a, b in CHART_COLORS.items():
            code = code.replace(a, b).replace(a.lower(), b)
        inline.append(code)
    PAGE_KEEP.clear()
    if body.select_one(".chat-wrapper"):
        PAGE_KEEP.update(set(re.findall(r"\.([A-Za-z][\w-]*)", open(f"{ROOT}/assets/css/edu-widgets.css").read())))
    for c in inline:
        for q in re.findall(r"""['"`]([^'"`\n]{1,80})['"`]""", c):
            PAGE_KEEP.update(re.findall(r"\.([A-Za-z][\w-]*)", q))
    # capture original classes before cleaning (for badge detection)
    orig = [(el, list(el.get("class", []))) for el in body.find_all(True)] + [(el, list(el.get("class", []))) for m in modals for el in m.find_all(True)]
    bc = body.select_one("div.breadcrumb")
    ph = page_header(bc, soup) if bc else None
    if bc:
        bc.decompose()
    container = soup.new_tag("div")
    for m in modals:
        container.append(m)
    # --- structural passes on body + modals
    for el, cl in orig:
        if "bg-base" in cl and any(x in cl for x in ("radius-12", "radius-16")) and "card" not in cl and el.name == "div":
            el["class"] = el.get("class", []) + ["card"]
    for root in (body, container):
        dropzones(root, soup)
        tabs(root)
        restructure_table_card(root, soup)
        checkbox_cells(root, soup)
        action_dropdowns(root, soup)
    badges(body, soup, orig)
    for root in (body, container):
        convert_icons(root, soup)
        style_tables(root)
        clean_classes(root)
        unwrap_icon_spans(root)
        tidy_buttons(root)
        dots_buttons(root)
        forms(root)
    import crud
    crud.apply(stem, body, container, ph)
    content = "".join(str(c) for c in body.contents)
    modal_html = str(container)[len('<div>'):-len('</div>')]
    if ph is not None:
        wrap = soup.new_tag("div"); wrap.append(ph)
        convert_icons(wrap, soup); clean_classes(wrap); unwrap_icon_spans(wrap); tidy_buttons(wrap)
        content = "".join(str(c) for c in wrap.contents) + content
    return out_name, assemble(out_name, title, content, modal_html, inline)

def main():
    only = set(sys.argv[1:])
    for p in sorted(glob.glob(f"{EDU}/*.php.html")):
        stem = os.path.basename(p).replace(".php.html", "")
        if only and stem not in only:
            continue
        name, html = convert(p)
        if html is None:
            print("skip (no dashboard-main-body):", stem)
            continue
        open(f"{ROOT}/{name}", "w", encoding="utf8").write(html)
        print("built", name)
    if not only:
        import json
        json.dump(PARTS, open(f"{HERE}/parts.json", "w"))

if __name__ == "__main__":
    main()
