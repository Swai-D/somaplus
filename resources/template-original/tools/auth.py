"""Login / register pages: Dream's auth layout, Edudash's fields (role select, 'login as' shortcuts)."""
import re
from bs4 import BeautifulSoup

DREAM = "/home/claude/merge-temp/dream-temp"
ROLES = [("Super Admin", "index.html"), ("Admin", "index.html"), ("Student", "index-2.html"),
         ("Teacher", "index-3.html"), ("Guardian", "index-4.html"), ("Librarian", "books-list.html")]

def _base(src, title):
    html = open(f"{DREAM}/{src}", encoding="utf8").read()
    html = re.sub(r"<a aria-hidden=\"true\" href=\"https://dreamspos[^>]*></a>", "", html)
    soup = BeautifulSoup(html, "lxml")
    soup.title.string = f"{title} | SomaPlus School Management"
    for l in soup.select('link[rel="shortcut icon"]'):
        l["href"] = "assets/img/favicon.svg"; l["type"] = "image/svg+xml"
    for l in soup.select('link[rel="apple-touch-icon"]'):
        l.decompose()
    for m in soup.select('meta[name="description"],meta[name="keywords"],meta[name="author"]'):
        m.decompose()
    soup.head.append(soup.new_tag("link", rel="stylesheet", href="assets/css/somaplus.css"))
    return soup

def _finish(soup):
    out = str(soup)
    out = out.replace("DreamsPOS", "SomaPlus").replace("Dreamspos", "SomaPlus").replace("Dreams POS", "SomaPlus")
    out = re.sub(r"Copyright © \d{4}", "Copyright © 2026", out)
    return out

def login():
    soup = _base("signin.html", "Login")
    form = soup.select_one("form")
    form["action"] = "index.html"
    soup.select_one(".login-userheading h3").string = "Sign In"
    soup.select_one(".login-userheading h4").string = "Access the SomaPlus panel using your email and password."
    for lab in soup.select("label.form-label"):
        if lab.get_text(strip=True).startswith("Email"):
            lab.contents[0].replace_with("Email Address")
    for inp in soup.select("input.form-control"):
        if inp.get("type") == "text":
            inp["type"] = "email"; inp["placeholder"] = "Enter your email"
        if inp.get("type") == "password":
            inp["placeholder"] = "Enter your password"
    # replace social buttons with Edudash's "login as" shortcuts
    chips = "".join(f'<a href="{u}" class="btn btn-sm btn-outline-white m-1">{n}</a>' for n, u in ROLES)
    block = soup.select_one(".form-setlogin.or-text h4")
    block.string = "or login as"
    social = block.find_parent("div").find_next_sibling("div")
    social.clear()
    social.append(BeautifulSoup(f'<div class="d-flex flex-wrap justify-content-center">{chips}</div>', "lxml").body.div)
    return _finish(soup)

def register():
    soup = _base("register.html", "Register")
    soup.select_one(".login-userheading h3").string = "Register"
    soup.select_one(".login-userheading h4").string = "Create your SomaPlus account"
    form = soup.select_one("form")
    form["action"] = "login.html"
    for lab in soup.select("label.form-label"):
        t = lab.get_text(strip=True)
        if t.startswith("Name"):
            lab.contents[0].replace_with("Full Name")
        elif t.startswith("Email"):
            lab.contents[0].replace_with("Email Address")
    # role select (from Edudash register)
    conf = [l for l in soup.select("label.form-label") if l.get_text(strip=True).startswith("Confirm")][0].find_parent("div", class_="mb-3")
    role = BeautifulSoup('''<div class="mb-3"><label class="form-label">Select Role <span class="text-danger">*</span></label>
<select class="form-select"><option value="">Choose your role</option><option>Student</option><option>Teacher</option><option>Guardian</option><option>Admin</option></select></div>''', "lxml").body.div
    conf.insert_after(role)
    return _finish(soup)

if __name__ == "__main__":
    import os
    root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    open(f"{root}/login.html", "w", encoding="utf8").write(login())
    open(f"{root}/register.html", "w", encoding="utf8").write(register())
    print("built login.html, register.html")
