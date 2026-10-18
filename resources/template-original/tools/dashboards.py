"""Dream-style renderers for the Edudash dashboards (content from Edudash, design from Dream)."""
import re
from extract_dash import extract

PALETTE = ["#FE9F43", "#092C4C", "#0E9384", "#3538CD", "#7638FF", "#03C95A"]
STAT_BG = ["bg-primary", "bg-secondary", "bg-teal", "bg-info", "bg-purple", "bg-success"]
# icon file -> (tabler icon, school-appropriate label for the placeholder "Total Student")
STAT_META = {
    "dashboard-icon1.png": ("school", "Total Students"), "dashboard-icon2.png": ("user-check", "Total Teachers"),
    "dashboard-icon3.png": ("users-group", "Total Guardians"), "dashboard-icon4.png": ("cash", "Fees Collected"),
    "dashboard-icon5.png": ("coin", "Total Revenue"), "dashboard-icon6.png": ("wallet", "Total Expenses"),
}

def img(src):
    return src.replace("assets/images/", "assets/img/edu/")

def card(title, body, extra="", action='<a href="javascript:void(0);" class="btn btn-sm btn-light">View all</a>', cls="col-xl-4 col-md-6"):
    return f'''<div class="{cls} d-flex"><div class="card flex-fill {extra}">
<div class="card-header d-flex align-items-center justify-content-between"><h5 class="card-title mb-0">{title}</h5>{action}</div>
<div class="card-body">{body}</div></div></div>'''

def avatar(src, size="md"):
    return f'<span class="avatar avatar-{size} rounded-circle flex-shrink-0"><img src="{img(src)}" class="img-fluid rounded-circle" alt=""></span>'

def render_school(path):
    d = extract(path)
    h = []
    # ---- stats
    h.append('<div class="row">')
    for i, s in enumerate(d["stats"]):
        key = s["icon"].split("/")[-1]
        icon, label = STAT_META.get(key, ("chart-bar", "Total"))
        pct, _, rest = s["trend"].partition(" ")
        h.append(f'''<div class="col-xl-4 col-md-6"><div class="card {STAT_BG[i % 6]} border-0"><div class="card-body d-flex align-items-center justify-content-between">
<div><p class="mb-1 text-white">{label}</p><h4 class="text-white mb-1">{s["value"]}</h4>
<span class="fs-12 text-white"><i class="ti ti-trending-up me-1"></i>{pct} <span class="opacity-75">{rest}</span></span></div>
<span class="avatar avatar-lg" style="background:rgba(255,255,255,.2)"><i class="ti ti-{icon} text-white fs-24"></i></span></div></div></div>''')
    h.append("</div>")

    # ---- revenue + attendance
    h.append('<div class="row">')
    h.append(f'''<div class="col-xl-8 d-flex"><div class="card flex-fill"><div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-2">
<h5 class="card-title mb-0">Revenue Statistic</h5><div class="d-flex align-items-center gap-3"><span class="fs-13"><i class="ti ti-point-filled text-teal"></i>Total Fee: <b>$500</b></span><span class="fs-13"><i class="ti ti-point-filled text-primary"></i>Collected Fee: <b>$300</b></span></div></div>
<div class="card-body"><div id="revenueStatistic"></div></div></div></div>''')
    colors = ["success", "danger", "warning", "info"]
    rows = []
    for k, (label, val) in enumerate(d["attendance"]):
        label = label.replace("Student Attendance", "").strip().rstrip(":")
        rows.append(f'<div class="mb-4"><div class="d-flex justify-content-between mb-2"><span class="text-dark">{label}</span><span class="fw-semibold text-dark">{val}%</span></div><div class="progress progress-xs"><div class="progress-bar bg-{colors[k % 4]}" style="width:{val}%"></div></div></div>')
    h.append(card("Student Attendance", "".join(rows), cls="col-xl-4"))
    h.append("</div>")

    # ---- notice board / leave requests / calendar
    h.append('<div class="row">')
    nb = "".join(f'''<div class="d-flex align-items-start gap-3 {"border-bottom pb-3 mb-3" if i < len(d["notices"]) - 1 else ""}">{avatar(n["img"])}
<div><h6 class="fs-14 fw-semibold mb-1">{n["text"][0]}</h6><p class="fs-13 mb-1">{n["text"][1]}</p><span class="fs-12 text-gray-5"><i class="ti ti-calendar me-1"></i>{n["text"][2]}</span></div></div>''' for i, n in enumerate(d["notices"]))
    h.append(card("Notice Board", nb, action='<a href="notice-board.html" class="btn btn-sm btn-light">View all</a>', cls="col-xl-4 col-md-6"))
    lv = "".join(f'''<div class="d-flex align-items-center justify-content-between gap-2 {"border-bottom pb-3 mb-3" if i < len(d["leaves"]) - 1 else ""}">
<div class="d-flex align-items-center gap-2">{avatar(l["img"])}<div><h6 class="fs-14 fw-semibold mb-0">{l["text"][0]}</h6><span class="fs-12 text-gray-5">{l["text"][1]}</span></div></div>
<div class="text-end"><span class="badge badge-soft-warning mb-1">{l["text"][2]}</span><p class="fs-12 mb-0 text-gray-5">{l["text"][3]}</p></div></div>''' for i, l in enumerate(d["leaves"]))
    h.append(card("Leave Requests", lv, action='<a href="leave-request.html" class="btn btn-sm btn-light">View all</a>', cls="col-xl-4 col-md-6"))
    ev = "".join(f'''<div class="d-flex align-items-center justify-content-between gap-2 mb-3"><div class="ps-3" style="border-left:3px solid {PALETTE[(i + 4) % 6]}">
<h6 class="fs-14 fw-semibold mb-0">{e[0]} <small class="text-gray-5 fw-normal">{e[1]}</small></h6><p class="fs-13 mb-0">{e[2]}</p><p class="fs-12 text-gray-5 mb-0">Lead by <a href="javascript:void(0);" class="link-primary">{e[4]}</a></p></div>
<a href="event.html" class="btn btn-sm btn-light">View</a></div>''' for i, e in enumerate(d["events"][:4]))
    cal = f'<div id="dash-calendar" class="sp-calendar mb-3"></div><h6 class="fs-14 fw-semibold border-top pt-3 mb-3">Upcoming Events</h6>{ev}'
    h.append(card("Calendar", cal, action='<a href="event.html" class="btn btn-sm btn-light">Events</a>', cls="col-xl-4"))
    h.append("</div>")

    # ---- user overview + income/expense
    h.append('<div class="row">')
    ov = d["overview"][d["overview"].index("Student"):]
    legend = "".join(f'<div class="text-center"><p class="fs-13 mb-1">{ov[i]}</p><h6 class="fw-bold mb-0">{ov[i + 1]}</h6></div>' for i in (0, 2, 4))
    h.append(card("User Overview", f'<div id="userOverview"></div><div class="d-flex justify-content-around mt-3">{legend}</div>', cls="col-xl-4 col-md-6"))
    h.append(f'''<div class="col-xl-8 col-md-6 d-flex"><div class="card flex-fill"><div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-2">
<h5 class="card-title mb-0">Income Vs Expense</h5><div class="d-flex align-items-center gap-3"><span class="fs-13"><i class="ti ti-point-filled text-success"></i>Income: <b>$500</b></span><span class="fs-13"><i class="ti ti-point-filled text-primary"></i>Expense: <b>$300</b></span></div></div>
<div class="card-body"><div id="incomeExpense"></div></div></div></div>''')
    h.append("</div>")

    # ---- teachers / admissions / students
    h.append('<div class="row">')
    tt = "".join(f'''<div class="d-flex align-items-center justify-content-between gap-2 {"border-bottom pb-3 mb-3" if i < len(d["teachers"]) - 1 else ""}">
<div class="d-flex align-items-center gap-2">{avatar(t["img"])}<div><h6 class="fs-14 fw-semibold mb-0">{t["text"][0]}</h6><span class="fs-12 text-gray-5">{t["text"][1]}</span></div></div>
<span class="badge badge-soft-primary">{t["text"][2]}</span></div>''' for i, t in enumerate(d["teachers"]))
    h.append(card("Top Teachers", tt, action='<a href="teacher-list.html" class="btn btn-sm btn-light">View all</a>', cls="col-xl-4 col-md-6"))
    adm = d["admissions"]
    total = adm[adm.index("Total Admissions") - 1]
    sub = [(adm[i], adm[i + 1]) for i in range(adm.index("Total Admissions") + 1, len(adm) - 1, 2)]
    lg = "".join(f'<span class="fs-13 me-3"><i class="ti ti-point-filled" style="color:{PALETTE[(k + 1) % 6]}"></i>{a} <b>{b}</b></span>' for k, (a, b) in enumerate(sub))
    h.append(card("New Admissions", f'<div class="position-relative"><div id="newAdmissions"></div><div class="position-absolute top-50 start-50 translate-middle text-center"><h4 class="mb-0">{total}</h4><span class="fs-12 text-gray-5">Total Admissions</span></div></div><div class="text-center mt-3">{lg}</div>', action="", cls="col-xl-4 col-md-6"))
    ts = "".join(f'''<div class="d-flex align-items-center justify-content-between gap-2 {"border-bottom pb-3 mb-3" if i < len(d["students"]) - 1 else ""}">
<div class="d-flex align-items-center gap-2">{avatar(s["img"])}<div><h6 class="fs-14 fw-semibold mb-0">{s["text"][0]}</h6><span class="fs-12 text-gray-5">{s["text"][1]}</span></div></div>
<div class="text-end"><span class="fs-12 text-gray-5">{s["text"][2]}</span><h6 class="fs-14 fw-bold mb-0">{s["text"][3]}</h6></div></div>''' for i, s in enumerate(d["students"]))
    h.append(card("Top Students", ts, action='<a href="student-list.html" class="btn btn-sm btn-light">View all</a>', cls="col-xl-4"))
    h.append("</div>")

    header = '''<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>School Dashboard</h4><h6>Manage your school, track attendance, exams, fees and more</h6></div></div>
<ul class="table-top-head"><li class="me-2"><a data-bs-toggle="tooltip" title="Refresh" href="javascript:location.reload()"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-toggle="tooltip" title="Collapse" id="collapse-header"><i class="ti ti-chevron-up"></i></a></li></ul></div>'''

    script = '''
(function(){
  var months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  new ApexCharts(document.querySelector("#revenueStatistic"),{
    series:[{name:'Total Fee',data:[25,35,50,60,26,20,40,20,50,16,10,40]},{name:'Collected Fee',data:[15,16,24,30,20,15,20,10,25,10,6,20]}],
    chart:{type:'bar',height:280,stacked:true,toolbar:{show:false}},colors:['#0E9384','#FE9F43'],
    plotOptions:{bar:{columnWidth:'45%',borderRadius:4}},xaxis:{categories:months},
    yaxis:{labels:{formatter:function(v){return '$'+v+'k'}}},legend:{show:false},grid:{borderColor:'#E6EAED',strokeDashArray:3},fill:{opacity:1},dataLabels:{enabled:false}
  }).render();
  new ApexCharts(document.querySelector("#incomeExpense"),{
    series:[{name:'Income',data:[48,35,55,32,48,30,15,50,57]},{name:'Expense',data:[12,20,15,26,22,60,40,32,25]}],
    chart:{type:'area',height:290,toolbar:{show:false}},colors:['#03C95A','#FE9F43'],stroke:{curve:'smooth',width:2},
    dataLabels:{enabled:false},legend:{show:false},xaxis:{categories:months.slice(0,9)},yaxis:{labels:{formatter:function(v){return '$'+v+'k'}}},
    grid:{borderColor:'#E6EAED',strokeDashArray:3},fill:{type:'gradient',gradient:{opacityFrom:.35,opacityTo:.02}}
  }).render();
  new ApexCharts(document.querySelector("#userOverview"),{
    series:[60,30,10],labels:['Student','Teacher','Staffs'],colors:['#FE9F43','#092C4C','#0E9384'],
    chart:{type:'donut',height:250},legend:{show:false},dataLabels:{enabled:true,formatter:function(v){return Math.round(v)+'%'}},
    plotOptions:{pie:{donut:{size:'62%'}}},stroke:{width:2}
  }).render();
  new ApexCharts(document.querySelector("#newAdmissions"),{
    series:[15,15,5,10],labels:['English','Math','Biology','Physics'],colors:['#092C4C','#0E9384','#FE9F43','#3538CD'],
    chart:{type:'donut',height:240,sparkline:{enabled:true}},legend:{show:false},dataLabels:{enabled:false},stroke:{width:2},plotOptions:{pie:{donut:{size:'72%'}}}
  }).render();
  if(window.flatpickr){flatpickr('#dash-calendar',{inline:true});}
})();
'''
    return header + "".join(h), [script], "School Dashboard"


# =====================================================================================
# Student / Teacher / Parent / LMS dashboards
# =====================================================================================
IDX = "/home/claude/merge-temp/edudash-temp/index.php.html"
COMMON = None
def common():
    global COMMON
    if COMMON is None:
        COMMON = extract(IDX)
    return COMMON

def hdr(title, sub):
    return f'''<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>{title}</h4><h6>{sub}</h6></div></div>
<ul class="table-top-head"><li class="me-2"><a data-bs-toggle="tooltip" title="Refresh" href="javascript:location.reload()"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-toggle="tooltip" title="Collapse" id="collapse-header"><i class="ti ti-chevron-up"></i></a></li></ul></div>'''

def period(label="Yearly"):
    items = "".join(f'<li><a class="dropdown-item rounded-1" href="javascript:void(0);">{x}</a></li>' for x in ("Yearly", "Monthly", "Weekly", "Today"))
    return f'<div class="dropdown"><a href="javascript:void(0);" class="btn btn-white btn-sm dropdown-toggle" data-bs-toggle="dropdown">{label}</a><ul class="dropdown-menu dropdown-menu-end p-2">{items}</ul></div>'

def sep(i, n):
    return "border-bottom pb-3 mb-3" if i < n - 1 else ""

def notice_card(cls="col-xl-4 col-md-6"):
    d = common()
    body = "".join(f'''<div class="d-flex align-items-start gap-3 {sep(i, len(d["notices"]))}">{avatar(n["img"])}
<div><h6 class="fs-14 fw-semibold mb-1">{n["text"][0]}</h6><p class="fs-13 mb-1">{n["text"][1]}</p><span class="fs-12 text-gray-5"><i class="ti ti-calendar me-1"></i>{n["text"][2]}</span></div></div>''' for i, n in enumerate(d["notices"]))
    return card("Notice Board", body, action='<a href="notice-board.html" class="btn btn-sm btn-light">View all</a>', cls=cls)

def events_card(cls="col-xl-4 col-md-6"):
    d = common()
    body = "".join(f'''<div class="d-flex align-items-center justify-content-between gap-2 mb-3"><div class="ps-3" style="border-left:3px solid {PALETTE[(i + 4) % 6]}">
<h6 class="fs-14 fw-semibold mb-0">{e[0]} <small class="text-gray-5 fw-normal">{e[1]}</small></h6><p class="fs-13 mb-0">{e[2]}</p><p class="fs-12 text-gray-5 mb-0">Lead by <a href="javascript:void(0);" class="link-primary">{e[4]}</a></p></div>
<a href="event.html" class="btn btn-sm btn-light">View</a></div>''' for i, e in enumerate(d["events"][:5]))
    return card("Upcoming Events", body, action='<a href="event.html" class="btn btn-sm btn-light">All events</a>', cls=cls)

def calendar_card(cls="col-xl-4 col-md-6"):
    return card("Calendar", '<div id="dash-calendar" class="sp-calendar"></div>', action='<a href="event.html" class="btn btn-sm btn-light">Events</a>', cls=cls)

def leave_card(cls="col-xl-4 col-md-6"):
    rows = [("Emergency Leave", "Pending"), ("Medical Leave", "Accepted"), ("Now Well", "Pending"), ("Medical Leave", "Accepted"),
            ("Emergency Leave", "Accepted"), ("Now Well", "Pending")]
    body = "".join(f'''<div class="d-flex align-items-center justify-content-between {sep(i, len(rows))}"><div><h6 class="fs-14 fw-semibold mb-0">{t}</h6><span class="fs-12 text-gray-5"><i class="ti ti-calendar me-1"></i>Date: 10/10/24</span></div>
<span class="badge {"badge-success" if s == "Accepted" else "badge-warning"} badge-xs">{s}</span></div>''' for i, (t, s) in enumerate(rows))
    return card("Leave Status", body, action=period(), cls=cls)

def class_card(cls="col-xl-4 col-md-6"):
    rows = [("English", "09:30 - 09:45 AM", "Completed"), ("Physics", "09:50 - 10:35 AM", "Inprogress"), ("Bangla", "09:30 - 09:45 AM", "Inprogress"),
            ("Chemistry", "09:30 - 09:45 AM", "Inprogress"), ("Accounting", "09:30 - 09:45 AM", "Inprogress"), ("English", "09:30 - 09:45 AM", "Completed")]
    body = "".join(f'''<div class="d-flex align-items-center justify-content-between {sep(i, len(rows))}"><div><h6 class="fs-14 fw-semibold mb-0">{t}</h6><span class="fs-12 text-gray-5"><i class="ti ti-clock me-1"></i>{tm}</span></div>
<span class="badge {"badge-success" if s == "Completed" else "badge-info"} badge-xs">{s}</span></div>''' for i, (t, tm, s) in enumerate(rows))
    return card("Today's Class", body, action=period("Today"), cls=cls)

def donut_card(title, cid, legend, cls="col-xl-4 col-md-6", act=None):
    lg = "".join(f'<div class="d-flex align-items-center justify-content-between mb-2"><span class="fs-13"><i class="ti ti-point-filled" style="color:{c}"></i>{n}</span><b>{v}</b></div>' for n, v, c in legend)
    return card(title, f'<div id="{cid}"></div><div class="mt-3">{lg}</div>', action=period() if act is None else act, cls=cls)

def table_card(title, heads, rows, cls="col-xl-8", badge_col=None, action=""):
    th = "".join(f"<th>{h}</th>" for h in heads)
    def cell(j, v):
        if j == badge_col:
            good = v in ("Pass", "Paid")
            return f'<span class="badge {"badge-success" if good else "badge-danger"} badge-xs"><i class="ti ti-point-filled me-1"></i>{v}</span>'
        return v
    tr = "".join("<tr>" + "".join(f"<td>{cell(j, v)}</td>" for j, v in enumerate(r)) + "</tr>" for r in rows)
    return f'''<div class="{cls} d-flex"><div class="card flex-fill"><div class="card-header d-flex align-items-center justify-content-between"><h5 class="card-title mb-0">{title}</h5>{action}</div>
<div class="card-body p-0"><div class="table-responsive"><table class="table"><thead class="thead-light"><tr>{th}</tr></thead><tbody>{tr}</tbody></table></div></div></div></div>'''

def stat_tile(label, value, trend, icon, bg):
    return f'''<div class="col-xl-3 col-md-6"><div class="card {bg} border-0"><div class="card-body d-flex align-items-center justify-content-between">
<div><p class="mb-1 text-white">{label}</p><h4 class="text-white mb-1">{value}</h4><span class="fs-12 text-white"><i class="ti ti-trending-up me-1"></i>{trend}</span></div>
<span class="avatar avatar-lg" style="background:rgba(255,255,255,.2)"><i class="ti ti-{icon} text-white fs-24"></i></span></div></div></div>'''

def profile_card(name, lines, photo, stats, cls="col-xl-4"):
    st = "".join(f'<div class="col-6"><div class="border rounded p-2 text-center"><p class="fs-12 text-gray-5 mb-1">{l}</p><h6 class="fw-bold mb-0">{v}</h6></div></div>' for l, v in stats)
    ln = "".join(f'<p class="fs-13 mb-0">{x}</p>' for x in lines)
    return f'''<div class="{cls} d-flex"><div class="card flex-fill overflow-hidden">
<div style="height:84px;background:url(assets/img/edu/bg/edit-profile-bg.png) center/cover,linear-gradient(135deg,#FE9F43,#092C4C)"></div>
<div class="card-body pt-0 text-center"><span class="avatar avatar-xxl rounded-circle border border-3 border-white" style="margin-top:-40px;width:80px;height:80px"><img src="assets/img/edu/thumbs/{photo}" class="img-fluid rounded-circle" alt=""></span>
<h5 class="mt-2 mb-1">{name}</h5>{ln}<a href="general.html" class="btn btn-primary btn-sm my-3"><i class="ti ti-edit me-1"></i>Edit Profile</a><div class="row g-2">{st}</div></div></div></div>'''

def users_list(title, items, cls="col-xl-4 col-md-6", right=lambda it: "", act='<a href="javascript:void(0);" class="btn btn-sm btn-light">View all</a>'):
    body = "".join(f'''<div class="d-flex align-items-center justify-content-between gap-2 {sep(i, len(items))}"><div class="d-flex align-items-center gap-2">{avatar(it["img"])}
<div><h6 class="fs-14 fw-semibold mb-0">{it["text"][0]}</h6><span class="fs-12 text-gray-5">{it["text"][1]}</span></div></div>{right(it)}</div>''' for i, it in enumerate(items))
    return card(title, body, action=act, cls=cls)

CAL_JS = "if(window.flatpickr&&document.getElementById('dash-calendar')){flatpickr('#dash-calendar',{inline:true});}"
GRID = "grid:{borderColor:'#E6EAED',strokeDashArray:3}"
DONUT = lambda cid, series, labels, colors: f"new ApexCharts(document.querySelector('#{cid}'),{{series:{series},labels:{labels},colors:{colors},chart:{{type:'donut',height:230}},legend:{{show:false}},dataLabels:{{enabled:false}},plotOptions:{{pie:{{donut:{{size:'70%'}}}}}},stroke:{{width:2}}}}).render();"
AREA = lambda cid, s1, s2, n1, n2, c1, c2, h=260: f"new ApexCharts(document.querySelector('#{cid}'),{{series:[{{name:'{n1}',data:{s1}}},{{name:'{n2}',data:{s2}}}],chart:{{type:'area',height:{h},toolbar:{{show:false}}}},colors:['{c1}','{c2}'],stroke:{{curve:'smooth',width:2}},dataLabels:{{enabled:false}},legend:{{show:false}},xaxis:{{categories:['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'].slice(0,{len(s1)})}},{GRID},fill:{{type:'gradient',gradient:{{opacityFrom:.35,opacityTo:.02}}}}}}).render();"

ATT_LEGEND = [("Present", 200, "#03C95A"), ("Half Day", 300, "#FE9F43"), ("Late", 172, "#3538CD"), ("Absent", 500, "#E70D0D")]
ATT_JS = DONUT("attendanceDonut", [200, 300, 172, 500], ["Present", "Half Day", "Late", "Absent"], ["#03C95A", "#FE9F43", "#3538CD", "#E70D0D"])
EXAM_ROWS = [("AD52365", "Class Test", "English", "A", "95%", "4.2", "Pass"), ("AD52365", "First Semester", "Chemistry", "A", "80%", "3.2", "Pass"),
             ("AD52365", "Class Test", "Bangla", "B", "70%", "4.5", "Pass"), ("AD52365", "Class Test", "Accounting", "C", "60%", "3.9", "Pass"),
             ("AD52365", "First Semester", "Chemistry", "A", "80%", "3.2", "Pass"), ("AD52365", "Class Test", "English", "F", "30%", "2.5", "Fail")]
EXAM_HEAD = ["ID", "Exam Name", "Subject", "Grade", "Marks%", "CGPA", "Status"]
ROW = lambda *c: '<div class="row">' + "".join(c) + "</div>"

def render_student(path):
    h = hdr("Student Dashboard", "Track your classes, results, attendance and notices")
    body = ROW(profile_card("Devon Lane", ["Class: 7", "Roll No: 03"], "studnt-edit-profile-img.png", [("Events", 10), ("Notifications", 15), ("Attendance", "90%")]),
               donut_card("Attendance", "attendanceDonut", ATT_LEGEND), class_card())
    body += ROW(table_card("Exam Results", EXAM_HEAD, EXAM_ROWS, badge_col=6, action='<a href="exam-result.html" class="btn btn-sm btn-light">View all</a>'),
                calendar_card("col-xl-4"))
    body += ROW(leave_card(), notice_card(), events_card())
    return h + body, [ATT_JS + CAL_JS], "Student Dashboard"

def render_teacher(path):
    h = hdr("Teacher Dashboard", "Manage your classes, students and marks")
    marks = [("AD45231", "Wade Warren", "Class 2 (B)", "88%", "3.9", "Pass"), ("AD67452", "Brooklyn Simmons", "Class 3 (A)", "72%", "3.1", "Pass"),
             ("AD98214", "Darlene Robertson", "Class 4 (C)", "54%", "2.4", "Fail"), ("AD76133", "Theresa Webb", "Class 5 (A)", "41%", "1.9", "Fail"),
             ("AD33578", "Arlene McCoy", "Class 1 (C)", "91%", "4.0", "Pass")]
    body = ROW(profile_card("Courtney Henry", ["Class: 1-A, V-B", "Physics"], "edit-profile-img.png", [("Total Students", "3,500")] * 4),
               donut_card("Attendance", "attendanceDonut", ATT_LEGEND), notice_card())
    body += ROW(table_card("Student Marks", ["Admission No", "Name", "Class", "Marks%", "CGPA", "Status"], marks, badge_col=5,
                           action='<a href="exam-result.html" class="btn btn-sm btn-light">View all</a>'), calendar_card("col-xl-4"))
    body += ROW(leave_card("col-xl-6"), events_card("col-xl-6"))
    return h + body, [ATT_JS + CAL_JS], "Teacher Dashboard"

def render_parent(path):
    h = hdr("Parent Dashboard", "Track student progress, attendance and academic performance in one place")
    tiles = "".join(stat_tile(l, "$500", "10% +5 This Month", "cash", bg) for (l, bg) in
                    (("Due Fees", "bg-primary"), ("Paid Fees", "bg-secondary"), ("Total Fees", "bg-teal"), ("Fee Discount", "bg-purple")))
    stat = card("Statistic", '<div class="d-flex gap-4 mb-2"><span class="fs-13"><i class="ti ti-point-filled text-primary"></i>Avg. Attendance: <b>200</b></span><span class="fs-13"><i class="ti ti-point-filled text-secondary"></i>Avg. Exam Score: <b>500</b></span></div><div id="courseActivityChart"></div>',
                action=period(), cls="col-xl-8")
    kids = "".join(f'''<div class="col-md-6"><div class="border rounded p-3 d-flex gap-3"><span class="avatar avatar-xl rounded-circle flex-shrink-0"><img src="assets/img/edu/thumbs/student-details-img.png" class="img-fluid rounded-circle" alt=""></span>
<div><h6 class="fw-bold mb-1">Seth Hallam</h6><p class="fs-13 mb-0">Admission No: <span class="link-primary">AD1256589</span></p><p class="fs-13 mb-0">Roll Number: 10</p><p class="fs-13 mb-0">Class: Class 6 (2025-26) · Section A</p><p class="fs-13 mb-0">Gender: Male · DOB: 10 Nov 2006</p><p class="fs-13 mb-0">Academic Year: Jun 2025/2026</p></div></div></div>''' for _ in range(2))
    body = ROW(tiles) + ROW(stat, class_card("col-xl-4"))
    body += ROW(table_card("Exam Results", EXAM_HEAD, EXAM_ROWS[:5], badge_col=6, action='<a href="exam-result.html" class="btn btn-sm btn-light">View all</a>'), notice_card("col-xl-4"))
    body += ROW(card("My Kids", f'<div class="row g-3">{kids}</div>', action='<a href="student-list.html" class="btn btn-sm btn-light">View all</a>', cls="col-xl-8"), events_card("col-xl-4"))
    return h + body, [AREA("courseActivityChart", [48, 35, 55, 32, 48, 30, 55, 50, 57], [12, 20, 15, 26, 22, 60, 40, 48, 25], "Avg. Attendance", "Avg. Exam Score", "#FE9F43", "#092C4C", 250)], "Parent Dashboard"

def render_lms(path):
    d = common()
    h = hdr("LMS Dashboard", "Manage courses, students, assignments and performance metrics")
    earn = card("Earning Statistic", '<div class="d-flex align-items-end gap-3 mb-2"><h3 class="mb-0">$27,200</h3><span class="text-success fs-13"><i class="ti ti-trending-up me-1"></i>10% <span class="text-gray-5">+ $1500 Per Day</span></span></div><div id="earningStatistic"></div>', action=period(), cls="col-xl-8")
    sess = [("Cameron Williamson", "session-img1.png"), ("Kristin Watson", "session-img2.png"), ("Kristin Watson", "session-img2.png"), ("Cameron Williamson", "session-img1.png")]
    sbody = "".join(f'''<div class="d-flex align-items-center justify-content-between gap-2 {sep(i, 4)}"><div class="d-flex align-items-center gap-2">{avatar("assets/images/thumbs/" + im)}
<div><h6 class="fs-14 fw-semibold mb-0">{n}</h6><span class="fs-12 text-gray-5">English · 15 Jun 2025 · 12:30 PM</span></div></div><a href="javascript:void(0);" class="btn btn-sm btn-primary">Join Class</a></div>''' for i, (n, im) in enumerate(sess))
    act = donut_card("User Activity", "userActivity", [("Organic Search", 875, "#FE9F43"), ("Referrals", 350, "#092C4C"), ("Social Media", 320, "#0E9384"), ("Google Search", 340, "#3538CD")])
    instr = [{"img": f"assets/images/users/user{k}.png", "text": [n, "Agent ID: 36254"]} for k, n in enumerate(("Dianne Russell", "Wade Warren", "Albert Flores", "Bessie Cooper", "Arlene McCoy"), 1)]
    courses = [("#829776", "John Doe", "Scorm drawing course", "$29.00", "PayPal", "04 Feb, 2025"), ("#829777", "Emily Carter", "Advanced UI/UX Design", "$49.00", "Stripe", "06 Feb, 2025"),
               ("#829778", "Michael Smith", "Full-Stack Development", "$79.00", "MasterCard", "08 Feb, 2025"), ("#829779", "Sarah Johnson", "Digital Marketing Pro", "$39.00", "Visa", "10 Feb, 2025"),
               ("#829780", "David Wilson", "Laravel API Development", "$59.00", "PayPal", "12 Feb, 2025")]
    cact = card("Course Activity", '<div class="d-flex gap-4 mb-2"><span class="fs-13"><i class="ti ti-point-filled text-primary"></i>Paid Course: <b>500</b></span><span class="fs-13"><i class="ti ti-point-filled text-secondary"></i>Free Course: <b>200</b></span></div><div id="courseActivityChart"></div>', action=period(), cls="col-xl-4")
    body = ROW(earn, calendar_card("col-xl-4"))
    body += ROW(card("Upcoming Sessions", sbody, action='<a href="javascript:void(0);" class="btn btn-sm btn-light">View all</a>', cls="col-xl-4 col-md-6"), act,
                users_list("Top Student", d["students"], right=lambda it: f'<div class="text-end"><span class="fs-12 text-gray-5">{it["text"][2]}</span><h6 class="fs-14 fw-bold mb-0">{it["text"][3]}</h6></div>', act='<a href="student-list.html" class="btn btn-sm btn-light">View all</a>'))
    body += ROW(users_list("Top Instructor", instr, right=lambda it: '<span class="badge badge-soft-warning"><i class="ti ti-star-filled me-1"></i>25 Reviews</span>', act='<a href="teacher-list.html" class="btn btn-sm btn-light">View all</a>'),
                table_card("Recent Enrolled Courses", ["Invoice", "Student", "Courses Name", "Amount", "Payment By", "Date"], courses, cls="col-xl-8"))
    body += ROW(cact)
    js = (AREA("earningStatistic", [20, 35, 28, 48, 40, 60, 55, 72, 65, 80, 74, 90], [10, 18, 15, 25, 22, 30, 28, 40, 35, 45, 38, 50], "Earning", "Expense", "#FE9F43", "#092C4C", 270)
          + DONUT("userActivity", [875, 350, 320, 340], ["Organic Search", "Referrals", "Social Media", "Google Search"], ["#FE9F43", "#092C4C", "#0E9384", "#3538CD"])
          + AREA("courseActivityChart", [48, 35, 55, 32, 48, 30, 55, 50, 57], [12, 20, 15, 26, 22, 60, 40, 48, 25], "Paid Course", "Free Course", "#FE9F43", "#092C4C", 230) + CAL_JS)
    return h + body, [js], "LMS Dashboard"


# =====================================================================================
# Subscription plan (pricing) page
# =====================================================================================
PLANS = [
    {"name": "Basic", "tag": "For individuals", "icon": "price-icon1.png", "price": 99, "featured": False},
    {"name": "Pro", "tag": "For startups", "icon": "price-icon2.png", "price": 199, "featured": True},
    {"name": "Enterprise", "tag": "For big companies", "icon": "price-icon3.png", "price": 399, "featured": False},
]
PLAN_DESC = "Lorem ipsum dolor sit amet doloroli sitiol conse ctetur adipiscing elit."
PLAN_FEATURES = ["All analytics features", "Up to 250,000 tracked visits", "Normal support", "Up to 3 team members"]

def render_subscription(path):
    cols = []
    for p in PLANS:
        f = p["featured"]
        feats = "".join(f'<li><span class="sp-check"><i class="ti ti-check"></i></span><span>{t}</span></li>' for t in PLAN_FEATURES)
        ribbon = '<span class="sp-ribbon">Popular</span>' if f else ""
        btn = (f'<button type="button" class="btn {"btn-light text-primary" if f else "btn-primary"} w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#planModal" data-plan="{p["name"]}">Get started</button>')
        cols.append(f'''<div class="col-xl-4 col-md-6"><div class="sp-plan{" featured" if f else ""}">{ribbon}
<div class="d-flex align-items-center gap-3 mb-3"><span class="sp-plan-icon"><img src="assets/img/edu/icons/{p["icon"]}" alt=""></span>
<div><span class="sp-plan-tag">{p["tag"]}</span><h5 class="mb-0 sp-plan-name">{p["name"]}</h5></div></div>
<p class="sp-plan-desc mb-4">{PLAN_DESC}</p>
<div class="sp-price mb-4"><span class="sp-cur">TSh</span> <span class="price-range" data-base="{p["price"]}">{p["price"]}</span> <span class="sp-per">/month</span></div>
<h6 class="fs-14 fw-semibold mb-3">What's included</h6><ul>{feats}</ul>{btn}</div></div>''')
    header = hdr("Subscription Plan", "Dashboard / Subscription Plan")
    body = f'''<div class="card"><div class="card-body p-4 p-lg-5">
<div class="text-center mb-5"><h3 class="mb-1">Simple, Transparent Pricing</h3><p class="text-gray-5 mb-4">Pick the plan that fits your school. Switch to annual billing for a yearly price.</p>
<div class="d-inline-flex align-items-center gap-3 sp-billing"><span class="fw-medium" id="lblMonthly">Monthly</span>
<div class="form-check form-switch m-0 p-0"><input class="form-check-input choose-plan-input m-0" type="checkbox" role="switch" id="billingSwitch"></div>
<span class="fw-medium text-gray-5" id="lblAnnual">Annually</span></div></div>
<div class="row g-4 justify-content-center align-items-stretch">{"".join(cols)}</div></div></div>
<div class="modal fade" id="planModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Confirm subscription</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
<div class="modal-body"><p class="mb-1">You are about to subscribe to the <b id="planName">Basic</b> plan.</p><p class="text-gray-5 mb-0" id="planPrice"></p></div>
<div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirm</button></div></div></div></div>'''
    js = '''
(function(){
  var sw=document.getElementById('billingSwitch'), prices=document.querySelectorAll('.price-range'), per=document.querySelectorAll('.sp-per');
  var lm=document.getElementById('lblMonthly'), la=document.getElementById('lblAnnual');
  function paint(){
    var yr=sw.checked;
    prices.forEach(function(p){p.textContent=(Number(p.dataset.base)*(yr?10:1)).toLocaleString('en-US');});
    per.forEach(function(e){e.textContent=yr?'/year':'/month';});
    lm.classList.toggle('text-gray-5',yr); la.classList.toggle('text-gray-5',!yr);
  }
  sw.addEventListener('change',paint); paint();
  var m=document.getElementById('planModal');
  m.addEventListener('show.bs.modal',function(e){
    var card=e.relatedTarget.closest('.sp-plan');
    document.getElementById('planName').textContent=e.relatedTarget.dataset.plan;
    document.getElementById('planPrice').textContent='TSh '+card.querySelector('.price-range').textContent+' '+card.querySelector('.sp-per').textContent;
  });
})();'''
    return header + body, [js], "Subscription Plan"
