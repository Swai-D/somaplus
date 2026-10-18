"""Dream-style CRUD flow: icon row actions + add/edit/delete modals (simple entities), icon links (complex entities)."""
import re
from bs4 import BeautifulSoup

# simple entities: add/edit/delete in modals.  stem -> entity name
MODAL = {
    "class-list": "Class", "section-list": "Section", "subject-list": "Subject", "class-room-list": "Class Room", "currencies": "Currency",
    "department": "Department", "designation": "Designation", "exam": "Exam", "exam-schedule": "Exam Schedule", "expense-head": "Expense Head",
    "expense-list": "Expense", "fees-discount": "Fees Discount", "fees-group": "Fees Group", "fees-type": "Fees Type", "income-head": "Income Head",
    "income-list": "Income", "languages": "Language", "leave-types": "Leave Type", "notice-board": "Notice", "role-access": "Role",
    "assign-role-plan": "Assign Role", "student-category": "Student Category", "transaction": "Transaction", "books-list": "Book",
    "members-list": "Member", "certificate": "Certificate",
}
ADD_ONLY = {"fees-collect": ("Collect Fees", "Collect Fees"), "issue-return": ("Issue Book", "Issue Book")}
# complex entities: separate pages.  stem -> (details page, edit page)
PAGES = {
    "student-list": ("student-details.html", "edit-student.html"), "suspended-student": ("student-details.html", "edit-student.html"),
    "teacher-list": ("teacher-details.html", "edit-teacher.html"), "guardian-list": ("guardian-details.html", "edit-guardian.html"),
    "employee-list": ("employee-details.html", "add-new-employee.html"), "members-list": ("member-details.html", None),
}
ATTENDANCE = {"student-attendance", "teacher-attendance", "employee-attendance"}
PAYMENT_TYPES = ["Cash", "Online", "Bank Transfer", "Cheque"]
SKIP = {"s.l", "action", "actions"}

def slug(t):
    return re.sub(r"[^a-z0-9]+", "_", t.lower()).strip("_")

def fragment(html):
    return list(BeautifulSoup(html, "lxml").body.children)

STATUS_SELECT = False

def field(h, i, pre):
    low, key = h.lower(), slug(h)
    star = '<span class="text-danger ms-1">*</span>'
    lab = f'<label class="form-label" for="{pre}-{key}">{h}{star}</label>'
    if "status" in low and STATUS_SELECT:
        opts = "".join(f"<option>{o}</option>" for o in ("Paid", "Partial", "Unpaid"))
        return f'<div class="col-md-6 mb-1">{lab}<select class="form-select" id="{pre}-{key}" name="{key}" data-col="{i}"><option value="">Select</option>{opts}</select></div>'
    if "status" in low:
        return f'<div class="col-12 mt-2"><div class="status-toggle modal-status d-flex justify-content-between align-items-center"><span class="status-label">{h}{star}</span><input type="checkbox" id="{pre}-status" class="check" checked data-sp-status="{i}"><label for="{pre}-status" class="checktoggle"></label></div></div>'
    if low in ("description", "features"):
        return f'<div class="col-12 mb-1">{lab}<textarea class="form-control" rows="3" id="{pre}-{key}" name="{key}" data-col="{i}"></textarea></div>'
    if "image" in low:
        return f'<div class="col-12 mb-1">{lab}<input type="file" class="form-control" id="{pre}-{key}" name="{key}"></div>'
    if "payment type" in low:
        opts = "".join(f"<option>{o}</option>" for o in PAYMENT_TYPES)
        return f'<div class="col-md-6 mb-1">{lab}<select class="form-select" id="{pre}-{key}" name="{key}" data-col="{i}"><option value="">Select</option>{opts}</select></div>'
    if low.endswith("date") or low == "date":
        return f'<div class="col-md-6 mb-1">{lab}<input type="text" class="form-control sp-date" id="{pre}-{key}" name="{key}" data-col="{i}" placeholder="dd Mon yyyy" autocomplete="off"></div>'
    wide = "col-12" if len(h) > 22 else "col-md-6"
    return f'<div class="{wide} mb-1">{lab}<input type="text" class="form-control" id="{pre}-{key}" name="{key}" data-col="{i}" placeholder="Enter {h.lower()}"></div>'

def form_modal(mid, title, heads, pre, submit, big):
    body = "".join(field(h, i, pre) for i, h in enumerate(heads) if h.lower() not in SKIP and not (i == 0 and h.lower() == "s.l"))
    size = " modal-lg" if big else ""
    return f'''<div class="modal fade" id="{mid}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered{size}"><div class="modal-content">
<div class="modal-header"><div class="page-title"><h4>{title}</h4></div><button aria-label="Close" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button></div>
<form action="#" method="post" data-sp-form><div class="modal-body"><div class="row">{body}</div></div>
<div class="modal-footer"><button class="btn me-2 btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">{submit}</button></div></form></div></div></div>'''

DELETE_MODAL = '''<div class="modal fade" id="delete-modal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="page-wrapper-new p-0"><div class="content p-5 px-3 text-center">
<span class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"><i class="ti ti-trash fs-24 text-danger"></i></span>
<h4 class="fs-20 fw-bold mb-2 mt-1">Delete {entity}</h4><p class="mb-0 fs-16">Are you sure you want to delete this {entity_l}?</p>
<div class="modal-footer-btn mt-3 d-flex justify-content-center"><button class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none" data-bs-dismiss="modal" type="button">Cancel</button>
<button class="btn btn-primary fs-13 fw-medium p-2 px-3" type="button" id="sp-confirm-delete">Yes Delete</button></div></div></div></div></div></div>'''

def icon_link(icon, title, href="javascript:void(0);", attrs=""):
    return (f'<a class="me-2 p-2 d-flex align-items-center border rounded" href="{href}" title="{title}" data-sp-row {attrs}><i class="ti ti-{icon}"></i></a>')

def row_actions(stem, body):
    """Replace each 3-dot dropdown by Dream's icon group; leftovers stay in a small 'more' menu.
    Edudash's mock rows differ from each other, so every row of a table gets that table's most common action set."""
    from collections import Counter
    detail, edit = PAGES.get(stem, (None, None))
    for table in body.select("table"):
        dds = table.select("div.table-action")
        if not dds:
            continue
        rows = []
        for dd in dds:
            items = [(li.get_text(strip=True), li) for li in dd.select("ul.dropdown-menu li a")]
            rows.append((dd, items))
        mode = Counter(tuple(l for l, _ in items) for _, items in rows).most_common(1)[0][0]
        template = {}
        for _, items in rows:
            for l, a in items:
                template.setdefault(l, a)
        for dd, items in rows:
            have = {l: a for l, a in items}
            items = [(l, have.get(l) or template[l]) for l in mode]
            icons, extras = [], []
            for label, a in items:
                low = label.lower()
                tgt = a.get("data-bs-target")
                if low.startswith("view"):
                    if detail:
                        icons.append(icon_link("eye", "View", detail))
                    elif tgt:
                        icons.append(icon_link("eye", "View", attrs=f'data-bs-toggle="modal" data-bs-target="{tgt}"'))
                    else:
                        icons.append(icon_link("eye", "View"))
                elif low == "edit":
                    if edit:
                        icons.append(icon_link("edit", "Edit", edit))
                    elif stem in MODAL:
                        icons.append(icon_link("edit", "Edit", attrs='data-bs-toggle="modal" data-bs-target="#edit-modal"'))
                    else:
                        icons.append(icon_link("edit", "Edit"))
                elif low == "delete":
                    icons.append(icon_link("trash", "Delete", attrs='data-bs-toggle="modal" data-bs-target="#delete-modal"'))
                elif low == "print":
                    icons.append(icon_link("printer", "Print", "javascript:window.print();"))
                elif low == "book return":
                    icons.append(icon_link("arrow-back-up", "Book Return", attrs=f'data-bs-toggle="modal" data-bs-target="{tgt}"'))
                else:
                    extras.append((label, a))
            order = {"eye": 0, "edit": 1, "printer": 2, "arrow-back-up": 2, "trash": 3}
            icons.sort(key=lambda h: order.get(re.search(r"ti ti-([\w-]+)", h).group(1), 9))
            html = '<div class="edit-delete-action d-flex align-items-center justify-content-start">' + "".join(icons)
            if extras:
                menu = "".join(f'<li><a class="dropdown-item d-flex align-items-center rounded-1" href="{a.get("href", "javascript:void(0);")}">{l}</a></li>' for l, a in extras)
                html += f'<div class="dropdown"><a href="javascript:void(0);" class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2">{menu}</ul></div>'
            html += "</div>"
            td = dd.find_parent("td")
            if td is not None:
                td.clear(); td["class"] = ["action-table-data"]
                for n in fragment(html):
                    td.append(n)

def apply(stem, body, container, ph):
    heads = [th.get_text(strip=True) for th in body.select("table.datatable thead th")]
    # page header: Pdf / Excel icons like Dream
    if ph is not None and heads:
        ul = ph.select_one("ul.table-top-head")
        if ul is not None:
            for n in reversed(fragment('<li class="me-2"><a title="Pdf" href="javascript:void(0);"><img src="assets/img/icons/pdf.svg" alt="pdf"></a></li><li class="me-2"><a title="Excel" href="javascript:void(0);"><img src="assets/img/icons/excel.svg" alt="excel"></a></li>')):
                ul.insert(0, n)
    # export dropdown is replaced by the header icons
    for dd in body.select("div.card-header div.dropdown"):
        if "Export" in dd.get_text():
            dd.decompose()
    if stem in ATTENDANCE and ph is not None:
        pb = ph.select_one(".page-btn")
        if pb is not None:
            pb.clear()
            for n in fragment('<button type="button" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>Save Attendance</button>'):
                pb.append(n)
        return
    if stem not in MODAL and stem not in ADD_ONLY and stem not in PAGES and heads:
        row_actions(stem, body)
        return
    entity = MODAL.get(stem) or (ADD_ONLY[stem][1] if stem in ADD_ONLY else None)
    row_actions(stem, body)
    if stem in MODAL or stem in ADD_ONLY:
        for m in container.select("div.modal"):
            if m.get("id") in ("exampleModalDelete",):
                m.decompose()
        label = f"Add {entity}" if stem in MODAL else ADD_ONLY[stem][0]
        if ph is not None:
            btn = ph.select_one(".page-btn a, .page-btn button")
            if btn is not None:
                btn.name = "a"; btn.attrs.pop("type", None)
                btn["href"] = "javascript:void(0);"; btn["data-bs-toggle"] = "modal"; btn["data-bs-target"] = "#add-modal"
                btn.clear()
                for n in fragment(f'<i class="ti ti-circle-plus me-1"></i>{label}'):
                    btn.append(n)
        global STATUS_SELECT
        STATUS_SELECT = stem == "fees-collect"
        big = len([h for h in heads if h.lower() not in SKIP]) > 5
        mods = form_modal("add-modal", label, heads, "add", label, big)
        if stem in MODAL:
            mods += form_modal("edit-modal", f"Edit {entity}", heads, "edit", "Save Changes", big)
            mods += DELETE_MODAL.format(entity=entity, entity_l=entity.lower())
        for n in fragment(mods):
            container.append(n)
    elif stem in PAGES:
        for m in container.select("div.modal"):
            if m.get("id") == "exampleModalDelete":
                m.decompose()
        ent = {"student-list": "Student", "suspended-student": "Student", "teacher-list": "Teacher", "guardian-list": "Guardian", "employee-list": "Employee"}.get(stem, "Record")
        for n in fragment(DELETE_MODAL.format(entity=ent, entity_l=ent.lower())):
            container.append(n)
