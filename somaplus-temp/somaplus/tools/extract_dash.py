"""Pull the widget data out of an Edudash dashboard so it can be re-rendered in Dream style."""
import re
from bs4 import BeautifulSoup

def card_by_title(body, title):
    for c in body.select("div.card"):
        h = c.find(["h6", "h5"])
        if h and h.get_text(strip=True) == title:
            return c

def items_by_img(card):
    out = []
    for img in card.select("img"):
        node = img
        while node.parent is not card and len(node.parent.select("img")) == 1:
            node = node.parent
        strings = [re.sub(r"\s+", " ", s).strip() for s in node.stripped_strings]
        src = img.get("src", "")
        out.append({"img": src, "text": [s for s in strings if s]})
    return out

def extract(path):
    s = BeautifulSoup(open(path, encoding="utf8").read(), "lxml")
    b = s.select_one("div.dashboard-main-body")
    d = {}
    stats = []
    for c in b.select("div.card"):
        h = c.find("h6")
        if h and re.match(r"^[\d,]+$", h.get_text(strip=True)) and c.select_one("img"):
            sp = c.select_one("span.d-inline-flex")
            trend = re.sub(r"\s+", " ", c.select("p")[-1].get_text(" ", strip=True))
            stats.append({"icon": c.select_one("img")["src"], "value": h.get_text(strip=True), "trend": trend})
    d["stats"] = stats
    att = card_by_title(b, "Student Attendance")
    d["attendance"] = re.findall(r"([A-Za-z ]+?):?\s*(\d+)%", " ".join(att.stripped_strings))
    for key, title in (("notices", "Notice Board"), ("leaves", "Leave Requests"), ("teachers", "Top Teachers"), ("students", "Top Student")):
        d[key] = items_by_img(card_by_title(b, title))
    ev = card_by_title(b, "Calendar")
    d["events"] = []
    for blk in ev.select("div.border-start-width-3-px"):
        t = [re.sub(r"\s+", " ", x).strip() for x in blk.stripped_strings]
        d["events"].append(t)
    uo = card_by_title(b, "User Overview")
    d["overview"] = [re.sub(r"\s+", " ", x).strip() for x in uo.stripped_strings]
    na = card_by_title(b, "New Admissions")
    d["admissions"] = [re.sub(r"\s+", " ", x).strip() for x in na.stripped_strings]
    return d

if __name__ == "__main__":
    import json, sys
    d = extract("/home/claude/merge-temp/edudash-temp/index.php.html")
    print(json.dumps(d, indent=1)[:6000])
