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
