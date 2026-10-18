@extends('layouts.app')
@section('title', 'School Dashboard')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>School Dashboard</h4><h6>Manage your school, track attendance, exams, fees and more</h6></div></div>
<ul class="table-top-head"><li class="me-2"><a data-bs-toggle="tooltip" title="Refresh" href="javascript:location.reload()"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-toggle="tooltip" title="Collapse" id="collapse-header"><i class="ti ti-chevron-up"></i></a></li></ul></div><div class="row"><div class="col-xl-4 col-md-6"><div class="card bg-primary border-0"><div class="card-body d-flex align-items-center justify-content-between">
<div><p class="mb-1 text-white">Total Students</p><h4 class="text-white mb-1">20,000</h4>
<span class="fs-12 text-white"><i class="ti ti-trending-up me-1"></i>10% <span class="opacity-75">+5 This Month</span></span></div>
<span class="avatar avatar-lg" style="background:rgba(255,255,255,.2)"><i class="ti ti-school text-white fs-24"></i></span></div></div></div><div class="col-xl-4 col-md-6"><div class="card bg-secondary border-0"><div class="card-body d-flex align-items-center justify-content-between">
<div><p class="mb-1 text-white">Total Teachers</p><h4 class="text-white mb-1">20,000</h4>
<span class="fs-12 text-white"><i class="ti ti-trending-up me-1"></i>10% <span class="opacity-75">+5 This Month</span></span></div>
<span class="avatar avatar-lg" style="background:rgba(255,255,255,.2)"><i class="ti ti-user-check text-white fs-24"></i></span></div></div></div><div class="col-xl-4 col-md-6"><div class="card bg-teal border-0"><div class="card-body d-flex align-items-center justify-content-between">
<div><p class="mb-1 text-white">Total Guardians</p><h4 class="text-white mb-1">20,000</h4>
<span class="fs-12 text-white"><i class="ti ti-trending-up me-1"></i>10% <span class="opacity-75">+5 This Month</span></span></div>
<span class="avatar avatar-lg" style="background:rgba(255,255,255,.2)"><i class="ti ti-users-group text-white fs-24"></i></span></div></div></div><div class="col-xl-4 col-md-6"><div class="card bg-info border-0"><div class="card-body d-flex align-items-center justify-content-between">
<div><p class="mb-1 text-white">Fees Collected</p><h4 class="text-white mb-1">20,000</h4>
<span class="fs-12 text-white"><i class="ti ti-trending-up me-1"></i>10% <span class="opacity-75">+5 This Month</span></span></div>
<span class="avatar avatar-lg" style="background:rgba(255,255,255,.2)"><i class="ti ti-cash text-white fs-24"></i></span></div></div></div><div class="col-xl-4 col-md-6"><div class="card bg-purple border-0"><div class="card-body d-flex align-items-center justify-content-between">
<div><p class="mb-1 text-white">Total Revenue</p><h4 class="text-white mb-1">20,000</h4>
<span class="fs-12 text-white"><i class="ti ti-trending-up me-1"></i>10% <span class="opacity-75">+5 This Month</span></span></div>
<span class="avatar avatar-lg" style="background:rgba(255,255,255,.2)"><i class="ti ti-coin text-white fs-24"></i></span></div></div></div><div class="col-xl-4 col-md-6"><div class="card bg-success border-0"><div class="card-body d-flex align-items-center justify-content-between">
<div><p class="mb-1 text-white">Total Expenses</p><h4 class="text-white mb-1">20,000</h4>
<span class="fs-12 text-white"><i class="ti ti-trending-up me-1"></i>10% <span class="opacity-75">+5 This Month</span></span></div>
<span class="avatar avatar-lg" style="background:rgba(255,255,255,.2)"><i class="ti ti-wallet text-white fs-24"></i></span></div></div></div></div><div class="row"><div class="col-xl-8 d-flex"><div class="card flex-fill"><div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-2">
<h5 class="card-title mb-0">Revenue Statistic</h5><div class="d-flex align-items-center gap-3"><span class="fs-13"><i class="ti ti-point-filled text-teal"></i>Total Fee: <b>TSh 500</b></span><span class="fs-13"><i class="ti ti-point-filled text-primary"></i>Collected Fee: <b>TSh 300</b></span></div></div>
<div class="card-body"><div id="revenueStatistic"></div></div></div></div><div class="col-xl-4 d-flex"><div class="card flex-fill ">
<div class="card-header d-flex align-items-center justify-content-between"><h5 class="card-title mb-0">Student Attendance</h5><a href="javascript:void(0);" class="btn btn-sm btn-light">View all</a></div>
<div class="card-body"><div class="mb-4"><div class="d-flex justify-content-between mb-2"><span class="text-dark">Present</span><span class="fw-semibold text-dark">87%</span></div><div class="progress progress-xs"><div class="progress-bar bg-success" style="width:87%"></div></div></div><div class="mb-4"><div class="d-flex justify-content-between mb-2"><span class="text-dark">Absent</span><span class="fw-semibold text-dark">40%</span></div><div class="progress progress-xs"><div class="progress-bar bg-danger" style="width:40%"></div></div></div><div class="mb-4"><div class="d-flex justify-content-between mb-2"><span class="text-dark">Late</span><span class="fw-semibold text-dark">20%</span></div><div class="progress progress-xs"><div class="progress-bar bg-warning" style="width:20%"></div></div></div><div class="mb-4"><div class="d-flex justify-content-between mb-2"><span class="text-dark">Half day</span><span class="fw-semibold text-dark">20%</span></div><div class="progress progress-xs"><div class="progress-bar bg-info" style="width:20%"></div></div></div></div></div></div></div><div class="row"><div class="col-xl-4 col-md-6 d-flex"><div class="card flex-fill ">
<div class="card-header d-flex align-items-center justify-content-between"><h5 class="card-title mb-0">Notice Board</h5><a href="@endverbatim{{ url('notice-board') }}@verbatim" class="btn btn-sm btn-light">View all</a></div>
<div class="card-body"><div class="d-flex align-items-start gap-3 border-bottom pb-3 mb-3"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/notice-board-img1.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span>
<div><h6 class="fs-14 fw-semibold mb-1">Admin</h6><p class="fs-13 mb-1">Lorem Ipsum is simply dummy text of the printing and typesetti</p><span class="fs-12 text-gray-5"><i class="ti ti-calendar me-1"></i>25 Jan 2024</span></div></div><div class="d-flex align-items-start gap-3 border-bottom pb-3 mb-3"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/notice-board-img2.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span>
<div><h6 class="fs-14 fw-semibold mb-1">Kathryn Murphy</h6><p class="fs-13 mb-1">Lorem Ipsum is simply dummy text of the printing and typesett ing industry Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p><span class="fs-12 text-gray-5"><i class="ti ti-calendar me-1"></i>25 Jan 2024</span></div></div><div class="d-flex align-items-start gap-3 border-bottom pb-3 mb-3"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/notice-board-img3.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span>
<div><h6 class="fs-14 fw-semibold mb-1">Admin</h6><p class="fs-13 mb-1">Lorem Ipsum is simply dummy text of the printing and typesetti</p><span class="fs-12 text-gray-5"><i class="ti ti-calendar me-1"></i>25 Jan 2024</span></div></div><div class="d-flex align-items-start gap-3 "><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/notice-board-img2.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span>
<div><h6 class="fs-14 fw-semibold mb-1">John Doe</h6><p class="fs-13 mb-1">Lorem ipsum dolor sit amet consectetur adipisicing elit. Laborum voluptas corporis qui dolore est odit officia fuga?</p><span class="fs-12 text-gray-5"><i class="ti ti-calendar me-1"></i>25 Jan 2024</span></div></div></div></div></div><div class="col-xl-4 col-md-6 d-flex"><div class="card flex-fill ">
<div class="card-header d-flex align-items-center justify-content-between"><h5 class="card-title mb-0">Leave Requests</h5><a href="@endverbatim{{ url('leave-request') }}@verbatim" class="btn btn-sm btn-light">View all</a></div>
<div class="card-body"><div class="d-flex align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/leave-request-img1.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Darlene Robertson</h6><span class="fs-12 text-gray-5">English Teacher</span></div></div>
<div class="text-end"><span class="badge badge-soft-warning mb-1">3 Days</span><p class="fs-12 mb-0 text-gray-5">Apply on: 10 April</p></div></div><div class="d-flex align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/leave-request-img2.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Esther Howard</h6><span class="fs-12 text-gray-5">English Teacher</span></div></div>
<div class="text-end"><span class="badge badge-soft-warning mb-1">3 Days</span><p class="fs-12 mb-0 text-gray-5">Apply on: 10 April</p></div></div><div class="d-flex align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/leave-request-img3.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Kristin Watson</h6><span class="fs-12 text-gray-5">English Teacher</span></div></div>
<div class="text-end"><span class="badge badge-soft-warning mb-1">3 Days</span><p class="fs-12 mb-0 text-gray-5">Apply on: 10 April</p></div></div><div class="d-flex align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/leave-request-img4.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Leslie Alexander</h6><span class="fs-12 text-gray-5">English Teacher</span></div></div>
<div class="text-end"><span class="badge badge-soft-warning mb-1">3 Days</span><p class="fs-12 mb-0 text-gray-5">Apply on: 10 April</p></div></div><div class="d-flex align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/leave-request-img5.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Dianne Russell</h6><span class="fs-12 text-gray-5">English Teacher</span></div></div>
<div class="text-end"><span class="badge badge-soft-warning mb-1">3 Days</span><p class="fs-12 mb-0 text-gray-5">Apply on: 10 April</p></div></div><div class="d-flex align-items-center justify-content-between gap-2 ">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/leave-request-img3.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Kristin Watson</h6><span class="fs-12 text-gray-5">English Teacher</span></div></div>
<div class="text-end"><span class="badge badge-soft-warning mb-1">3 Days</span><p class="fs-12 mb-0 text-gray-5">Apply on: 10 April</p></div></div></div></div></div><div class="col-xl-4 d-flex"><div class="card flex-fill ">
<div class="card-header d-flex align-items-center justify-content-between"><h5 class="card-title mb-0">Calendar</h5><a href="@endverbatim{{ url('event') }}@verbatim" class="btn btn-sm btn-light">Events</a></div>
<div class="card-body"><div id="dash-calendar" class="sp-calendar mb-3"></div><h6 class="fs-14 fw-semibold border-top pt-3 mb-3">Upcoming Events</h6><div class="d-flex align-items-center justify-content-between gap-2 mb-3"><div class="ps-3" style="border-left:3px solid #7638FF">
<h6 class="fs-14 fw-semibold mb-0">09:00 - 09:45 <small class="text-gray-5 fw-normal">AM</small></h6><p class="fs-13 mb-0">Marketing Strategy Kickoff</p><p class="fs-12 text-gray-5 mb-0">Lead by <a href="javascript:void(0);" class="link-primary">Robert Fox</a></p></div>
<a href="@endverbatim{{ url('event') }}@verbatim" class="btn btn-sm btn-light">View</a></div><div class="d-flex align-items-center justify-content-between gap-2 mb-3"><div class="ps-3" style="border-left:3px solid #03C95A">
<h6 class="fs-14 fw-semibold mb-0">11:15 - 12:00 <small class="text-gray-5 fw-normal">AM</small></h6><p class="fs-13 mb-0">Product Design Brainstorm</p><p class="fs-12 text-gray-5 mb-0">Lead by <a href="javascript:void(0);" class="link-primary">Leslie Alexander</a></p></div>
<a href="@endverbatim{{ url('event') }}@verbatim" class="btn btn-sm btn-light">View</a></div><div class="d-flex align-items-center justify-content-between gap-2 mb-3"><div class="ps-3" style="border-left:3px solid #FE9F43">
<h6 class="fs-14 fw-semibold mb-0">02:00 - 03:00 <small class="text-gray-5 fw-normal">PM</small></h6><p class="fs-13 mb-0">Client Feedback Review</p><p class="fs-12 text-gray-5 mb-0">Lead by <a href="javascript:void(0);" class="link-primary">Courtney Henry</a></p></div>
<a href="@endverbatim{{ url('event') }}@verbatim" class="btn btn-sm btn-light">View</a></div><div class="d-flex align-items-center justify-content-between gap-2 mb-3"><div class="ps-3" style="border-left:3px solid #092C4C">
<h6 class="fs-14 fw-semibold mb-0">04:15 - 05:00 <small class="text-gray-5 fw-normal">PM</small></h6><p class="fs-13 mb-0">Sprint Planning & Task Allocation</p><p class="fs-12 text-gray-5 mb-0">Lead by <a href="javascript:void(0);" class="link-primary">Eleanor Pena</a></p></div>
<a href="@endverbatim{{ url('event') }}@verbatim" class="btn btn-sm btn-light">View</a></div></div></div></div></div><div class="row"><div class="col-xl-4 col-md-6 d-flex"><div class="card flex-fill ">
<div class="card-header d-flex align-items-center justify-content-between"><h5 class="card-title mb-0">User Overview</h5><a href="javascript:void(0);" class="btn btn-sm btn-light">View all</a></div>
<div class="card-body"><div id="userOverview"></div><div class="d-flex justify-content-around mt-3"><div class="text-center"><p class="fs-13 mb-1">Student</p><h6 class="fw-bold mb-0">750</h6></div><div class="text-center"><p class="fs-13 mb-1">Teacher</p><h6 class="fw-bold mb-0">56</h6></div><div class="text-center"><p class="fs-13 mb-1">Staffs</p><h6 class="fw-bold mb-0">15</h6></div></div></div></div></div><div class="col-xl-8 col-md-6 d-flex"><div class="card flex-fill"><div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-2">
<h5 class="card-title mb-0">Income Vs Expense</h5><div class="d-flex align-items-center gap-3"><span class="fs-13"><i class="ti ti-point-filled text-success"></i>Income: <b>TSh 500</b></span><span class="fs-13"><i class="ti ti-point-filled text-primary"></i>Expense: <b>TSh 300</b></span></div></div>
<div class="card-body"><div id="incomeExpense"></div></div></div></div></div><div class="row"><div class="col-xl-4 col-md-6 d-flex"><div class="card flex-fill ">
<div class="card-header d-flex align-items-center justify-content-between"><h5 class="card-title mb-0">Top Teachers</h5><a href="@endverbatim{{ url('teacher-list') }}@verbatim" class="btn btn-sm btn-light">View all</a></div>
<div class="card-body"><div class="d-flex align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/top-teacher-img1.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Theresa Webb</h6><span class="fs-12 text-gray-5">example@gmail.com</span></div></div>
<span class="badge badge-soft-primary">Mathematics</span></div><div class="d-flex align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/top-teacher-img2.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Darrell Steward</h6><span class="fs-12 text-gray-5">example@gmail.com</span></div></div>
<span class="badge badge-soft-primary">Physics</span></div><div class="d-flex align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/top-teacher-img3.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Jane Cooper</h6><span class="fs-12 text-gray-5">example@gmail.com</span></div></div>
<span class="badge badge-soft-primary">Biology</span></div><div class="d-flex align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/top-teacher-img4.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Savannah Nguyen</h6><span class="fs-12 text-gray-5">example@gmail.com</span></div></div>
<span class="badge badge-soft-primary">English</span></div><div class="d-flex align-items-center justify-content-between gap-2 ">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/top-teacher-img5.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Eleanor Pena</h6><span class="fs-12 text-gray-5">example@gmail.com</span></div></div>
<span class="badge badge-soft-primary">Math</span></div></div></div></div><div class="col-xl-4 col-md-6 d-flex"><div class="card flex-fill ">
<div class="card-header d-flex align-items-center justify-content-between"><h5 class="card-title mb-0">New Admissions</h5></div>
<div class="card-body"><div class="position-relative"><div id="newAdmissions"></div><div class="position-absolute top-50 start-50 translate-middle text-center"><h4 class="mb-0">50</h4><span class="fs-12 text-gray-5">Total Admissions</span></div></div><div class="text-center mt-3"><span class="fs-13 me-3"><i class="ti ti-point-filled" style="color:#092C4C"></i>English: <b>15</b></span><span class="fs-13 me-3"><i class="ti ti-point-filled" style="color:#0E9384"></i>Math: <b>15</b></span><span class="fs-13 me-3"><i class="ti ti-point-filled" style="color:#3538CD"></i>Biology: <b>5</b></span><span class="fs-13 me-3"><i class="ti ti-point-filled" style="color:#7638FF"></i>Physics: <b>10</b></span></div></div></div></div><div class="col-xl-4 d-flex"><div class="card flex-fill ">
<div class="card-header d-flex align-items-center justify-content-between"><h5 class="card-title mb-0">Top Students</h5><a href="@endverbatim{{ url('student-list') }}@verbatim" class="btn btn-sm btn-light">View all</a></div>
<div class="card-body"><div class="d-flex align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img1.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Brooklyn Simmons</h6><span class="fs-12 text-gray-5">Class: Six</span></div></div>
<div class="text-end"><span class="fs-12 text-gray-5">Marks</span><h6 class="fs-14 fw-bold mb-0">20</h6></div></div><div class="d-flex align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img2.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Floyd Miles</h6><span class="fs-12 text-gray-5">Class: Seven</span></div></div>
<div class="text-end"><span class="fs-12 text-gray-5">Marks</span><h6 class="fs-14 fw-bold mb-0">35</h6></div></div><div class="d-flex align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img2.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Courtney Henry</h6><span class="fs-12 text-gray-5">Class: Eight</span></div></div>
<div class="text-end"><span class="fs-12 text-gray-5">Marks</span><h6 class="fs-14 fw-bold mb-0">45</h6></div></div><div class="d-flex align-items-center justify-content-between gap-2 border-bottom pb-3 mb-3">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img4.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Kathryn Murphy</h6><span class="fs-12 text-gray-5">Class: Nine</span></div></div>
<div class="text-end"><span class="fs-12 text-gray-5">Marks</span><h6 class="fs-14 fw-bold mb-0">65</h6></div></div><div class="d-flex align-items-center justify-content-between gap-2 ">
<div class="d-flex align-items-center gap-2"><span class="avatar avatar-md rounded-circle flex-shrink-0"><img src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img5.png') }}@verbatim" class="img-fluid rounded-circle" alt=""></span><div><h6 class="fs-14 fw-semibold mb-0">Annette Black</h6><span class="fs-12 text-gray-5">Class: Ten</span></div></div>
<div class="text-end"><span class="fs-12 text-gray-5">Marks</span><h6 class="fs-14 fw-bold mb-0">65</h6></div></div></div></div></div></div>
@endverbatim
@endsection
@push('vendor')
@verbatim
<script src="@endverbatim{{ asset('assets/plugins/apexchart/apexcharts.min.js') }}@verbatim"></script>

@endverbatim
@endpush
@push('scripts')
@verbatim
<script>

(function(){
  var months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  new ApexCharts(document.querySelector("#revenueStatistic"),{
    series:[{name:'Total Fee',data:[25,35,50,60,26,20,40,20,50,16,10,40]},{name:'Collected Fee',data:[15,16,24,30,20,15,20,10,25,10,6,20]}],
    chart:{type:'bar',height:280,stacked:true,toolbar:{show:false}},colors:['#0E9384','#FE9F43'],
    plotOptions:{bar:{columnWidth:'45%',borderRadius:4}},xaxis:{categories:months},
    yaxis:{labels:{formatter:function(v){return 'TSh '+v+'k'}}},legend:{show:false},grid:{borderColor:'#E6EAED',strokeDashArray:3},fill:{opacity:1},dataLabels:{enabled:false}
  }).render();
  new ApexCharts(document.querySelector("#incomeExpense"),{
    series:[{name:'Income',data:[48,35,55,32,48,30,15,50,57]},{name:'Expense',data:[12,20,15,26,22,60,40,32,25]}],
    chart:{type:'area',height:290,toolbar:{show:false}},colors:['#03C95A','#FE9F43'],stroke:{curve:'smooth',width:2},
    dataLabels:{enabled:false},legend:{show:false},xaxis:{categories:months.slice(0,9)},yaxis:{labels:{formatter:function(v){return 'TSh '+v+'k'}}},
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

</script>
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
