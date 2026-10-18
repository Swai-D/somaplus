@extends('layouts.app')
@section('title', 'Student Details')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Student Details</h4><h6>Dashboard / Student</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><button class="btn btn-primary d-flex align-items-center gap-1 text-dark" type="button">

<i class="ti ti-lock me-1"></i>

                Login Details
            </button></div></div>

<div class="mt-4">
<div class="card h-100">
<div class="card-body p-4">
<div class="d-flex gap-4 flex-md-row flex-column">
<div class="w-100 text-center" style="max-width:300px">
<figure class="mb-4 mx-auto rounded-circle overflow-hidden" style="width:120px;height:120px">
<img alt="Student Image" class="w-100 h-100 object-fit-cover" src="@endverbatim{{ asset('assets/img/edu/thumbs/student-details-img.png') }}@verbatim"/>
</figure>
<h2 class="h6 text-dark mb-3 fw-semibold">Seth Hallam</h2>
<p class="mb-0">Admission No: <span class="text-primary fw-semibold">AD1256589</span>
</p>
<p class="mb-0">Admission No: <span class="text-dark fw-semibold">10</span> </p>
<div class="mt-4 d-flex gap-3 w-100">
<button class="btn border fw-medium text-danger d-flex justify-content-center align-items-center gap-2 flex-grow-1 px-2 py-2 rounded" data-bs-target="#exampleModalDelete" data-bs-toggle="modal" type="button">

<i class="ti ti-trash me-1"></i>

                                    Suspend
                                </button>
<a class="btn btn-primary border fw-medium d-flex justify-content-center align-items-center gap-2 flex-grow-1 px-2 py-2 rounded" href="@endverbatim{{ url('edit-student') }}@verbatim">

<i class="ti ti-edit me-1"></i>

                                    Edit
                                </a>
</div>
</div>
<div class="">
<span class="h-100 bg-secondary-transparent" style="width:1px"></span>
</div>
<div class="flex-grow-1">
<div class="pb-3 border-bottom d-flex align-items-center justify-content-between gap-3">
<h3 class="h6 text-dark mb-0 fw-semibold">Personal Info</h3>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</div>
<div class="mt-3 d-flex flex-column gap-2">
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Class</span>
<span class="fw-normal text-gray-5">: 1 (A), 2(A), 3(A)</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Section</span>
<span class="fw-normal text-gray-5">: A</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Roll No</span>
<span class="fw-normal text-gray-5">: 10</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Gender</span>
<span class="fw-normal text-gray-5">: Male</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Date Of Birth</span>
<span class="fw-normal text-gray-5">: 10 Nov 2006</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Category</span>
<span class="fw-normal text-gray-5">: General</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Academic Year</span>
<span class="fw-normal text-gray-5">: Jun 2025/2026</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Phone Number</span>
<span class="fw-normal text-primary">: 789678456</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Email</span>
<span class="fw-normal text-primary">: set@example.com</span>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="my-3">
<ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded mb-3" id="pills-tab" role="tablist">
<li class="nav-item" role="presentation">
<button aria-controls="pills-studentDetails" aria-selected="true" class="nav-link d-flex align-items-center gap-2 text-gray-5 fw-medium text-capitalize bg-transparent px-3 py-2 active" data-bs-target="#pills-studentDetails" data-bs-toggle="pill" id="pills-studentDetails-tab" role="tab" type="button">
<span class="d-flex tab-icon">
<i class="ti ti-users-group"></i>
</span>
                            Student Details
                        </button>
</li>
<li class="nav-item" role="presentation">
<button aria-controls="pills-attendance" aria-selected="false" class="nav-link d-flex align-items-center gap-2 text-gray-5 fw-medium text-capitalize bg-transparent px-3 py-2" data-bs-target="#pills-attendance" data-bs-toggle="pill" id="pills-attendance-tab" role="tab" type="button">
<span class="d-flex tab-icon">
<i class="ti ti-calendar-check"></i>
</span>
                            Attendance
                        </button>
</li>
<li class="nav-item" role="presentation">
<button aria-controls="pills-leave" aria-selected="false" class="nav-link d-flex align-items-center gap-2 text-gray-5 fw-medium text-capitalize bg-transparent px-3 py-2" data-bs-target="#pills-leave" data-bs-toggle="pill" id="pills-leave-tab" role="tab" type="button">
<span class="d-flex tab-icon">
<i class="ti ti-login"></i>
</span>
                            Leave
                        </button>
</li>
<li class="nav-item" role="presentation">
<button aria-controls="pills-fees" aria-selected="false" class="nav-link d-flex align-items-center gap-2 text-gray-5 fw-medium text-capitalize bg-transparent px-3 py-2" data-bs-target="#pills-fees" data-bs-toggle="pill" id="pills-fees-tab" role="tab" type="button">
<span class="d-flex tab-icon">
<i class="ti ti-currency-dollar"></i>
</span>
                            Fees
                        </button>
</li>
<li class="nav-item" role="presentation">
<button aria-controls="pills-exam" aria-selected="false" class="nav-link d-flex align-items-center gap-2 text-gray-5 fw-medium text-capitalize bg-transparent px-3 py-2" data-bs-target="#pills-exam" data-bs-toggle="pill" id="pills-exam-tab" role="tab" type="button">
<span class="d-flex tab-icon">
<i class="ti ti-file-pencil"></i>
</span>
                            exam
                        </button>
</li>
<li class="nav-item" role="presentation">
<button aria-controls="pills-library" aria-selected="false" class="nav-link d-flex align-items-center gap-2 text-gray-5 fw-medium text-capitalize bg-transparent px-3 py-2" data-bs-target="#pills-library" data-bs-toggle="pill" id="pills-library-tab" role="tab" type="button">
<span class="d-flex tab-icon">
<i class="ti ti-book"></i>
</span>
                            library
                        </button>
</li>
</ul>
<div class="tab-content" id="pills-tabContent">
<!-- Student Details tab start -->
<div aria-labelledby="pills-studentDetails-tab" class="tab-pane fade show active" id="pills-studentDetails" role="tabpanel" tabindex="0">
<div class="row gy-4">
<div class="col-12">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Parent Guardian Detail</h6>
</div>
<div class="card-body p-0">
<div class="p-3">
<div class="row g-4">
<div class="col-sm-4">
<div class="d-flex align-items-center gap-2">
<figure class="rounded-circle overflow-hidden mb-0" style="width:48px;height:48px">
<img alt="Guardian Image" class="flex-shrink-0 w-100 h-100 object-fit-cover" src="@endverbatim{{ asset('assets/img/edu/thumbs/guardian-img1.png') }}@verbatim"/>
</figure>
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Robert Fox
                                                            </h6>
<span class="">Father</span>
</div>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Phone</h6>
<span class="">+19854 65642</span>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Email</h6>
<span class="">father@example.com</span>
</div>
</div>
</div>
</div>
<div class="p-3">
<div class="row g-4">
<div class="col-sm-4">
<div class="d-flex align-items-center gap-2">
<figure class="rounded-circle overflow-hidden mb-0" style="width:48px;height:48px">
<img alt="Guardian Image" class="flex-shrink-0 w-100 h-100 object-fit-cover" src="@endverbatim{{ asset('assets/img/edu/thumbs/guardian-img2.png') }}@verbatim"/>
</figure>
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Brooklyn
                                                                Simmons</h6>
<span class="">Mother</span>
</div>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Phone</h6>
<span class="">+19854 65642</span>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Email</h6>
<span class="">mother@example.com</span>
</div>
</div>
</div>
</div>
<div class="p-3">
<div class="row g-4">
<div class="col-sm-4">
<div class="d-flex align-items-center gap-2">
<figure class="rounded-circle overflow-hidden mb-0" style="width:48px;height:48px">
<img alt="Guardian Image" class="flex-shrink-0 w-100 h-100 object-fit-cover" src="@endverbatim{{ asset('assets/img/edu/thumbs/guardian-img1.png') }}@verbatim"/>
</figure>
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Robert Fox
                                                            </h6>
<span class="">Guardian (Father)</span>
</div>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Phone</h6>
<span class="">+19854 65642</span>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Email</h6>
<span class="">father@example.com</span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-md-6">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Previous School Details</h6>
</div>
<div class="card-body p-0">
<div class="p-3">
<div class="row gy-4">
<div class="col-sm-12">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Previous School
                                                            Name</h6>
<span class="">Stuyvesant High School</span>
</div>
</div>
<div class="col-sm-12">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Current School
                                                            Name</h6>
<span class="">Bronx High School of Science</span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-md-6">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Address</h6>
</div>
<div class="card-body p-0">
<div class="p-3">
<div class="row gy-4">
<div class="col-sm-12">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Current Address
                                                        </h6>
<span class="">8502 Preston Rd. Inglewood, Maine 98380</span>
</div>
</div>
<div class="col-sm-12">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Permanent Address
                                                        </h6>
<span class="">2118 Thornridge Cir. Syracuse, Connecticut
                                                            35624</span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-md-6">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Bank Details</h6>
</div>
<div class="card-body p-0">
<div class="p-3">
<div class="row gy-4">
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Bank Name</h6>
<span class="">Bank of America</span>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Branch</h6>
<span class="">New York</span>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">IFSC Code</h6>
<span class="">5283209832</span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-md-6">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Medical Details</h6>
</div>
<div class="card-body p-0">
<div class="p-3">
<div class="row gy-4">
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Blood Group</h6>
<span class="">O+</span>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Height</h6>
<span class="">5.2</span>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Weight</h6>
<span class="">60kg</span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-md-6">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Documents</h6>
</div>
<div class="card-body p-3">
<div class="p-2 border rounded">
<div class="d-flex align-items-center justify-content-between gap-3">
<div class="d-flex align-items-center gap-2">
<span class="rounded bg-secondary-transparent d-flex justify-content-center align-items-center" style="width:36px;height:36px">
<i class="ti ti-file-text"></i>
</span>
<span class="text-gray-5">BirthCertificate.pdf</span>
</div>
<button class="rounded bg-primary-transparent text-primary d-flex justify-content-center align-items-center" style="width:36px;height:36px" type="button">
<i class="ti ti-download"></i>
</button>
</div>
</div>
</div>
</div>
</div>
<div class="col-md-6">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Hostel</h6>
</div>
<div class="card-body p-0">
<div class="p-3">
<div class="row gy-4">
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Hostel</h6>
<span class="">Boys Hostel 101</span>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Room No.</h6>
<span class="">Room No.</span>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Room Type</h6>
<span class="">One Bed</span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-md-12">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Description</h6>
</div>
<div class="card-body p-0">
<div class="p-3">
<p class="text-gray-5">Known for their punctuality and positive
                                                attitude,
                                                [he/she/they] consistently demonstrates a strong commitment to academic
                                                excellence
                                                and co-curricular participation. [He/She/They] maintains good behavior,
                                                shows
                                                respect toward teachers and peers, and actively engages in classroom
                                                discussions and
                                                group activities. </p>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- Student Details tab end -->
<!-- Attendance tab start -->
<div aria-labelledby="pills-attendance-tab" class="tab-pane fade" id="pills-attendance" role="tabpanel" tabindex="0">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Attendance</h6>
</div>
<div class="card-body p-0">
<div class="px-3 pt-3">
<div class="row row-cols-xxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1 g-3">
<div class="col">
<div class="card px-3 py-4 rounded h-100 border shadow-none">
<div class="card-body p-0">
<div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
<div>
<h6 class="fw-semibold mb-2">227</h6>
<span class="fw-medium text-gray-5">Total
                                                                Present</span>
</div>
<span class="mb-0 bg-success text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6" style="width:48px;height:48px">
<img alt="Present Icon" src="@endverbatim{{ asset('assets/img/edu/icons/attendence-icon1.png') }}@verbatim"/>
</span>
</div>
</div>
</div>
</div>
<div class="col">
<div class="card px-3 py-4 rounded h-100 border shadow-none">
<div class="card-body p-0">
<div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
<div>
<h6 class="fw-semibold mb-2">70</h6>
<span class="fw-medium text-gray-5">Total
                                                                Absent</span>
</div>
<span class="mb-0 bg-danger text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6" style="width:48px;height:48px">
<img alt="Absent Icon" src="@endverbatim{{ asset('assets/img/edu/icons/attendence-icon2.png') }}@verbatim"/>
</span>
</div>
</div>
</div>
</div>
<div class="col">
<div class="card px-3 py-4 rounded h-100 border shadow-none">
<div class="card-body p-0">
<div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
<div>
<h6 class="fw-semibold mb-2">27</h6>
<span class="fw-medium text-gray-5">Half
                                                                Day</span>
</div>
<span class="mb-0 bg-purple text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6" style="width:48px;height:48px">
<img alt="Calendar Icon" src="@endverbatim{{ asset('assets/img/edu/icons/attendence-icon3.png') }}@verbatim"/>
</span>
</div>
</div>
</div>
</div>
<div class="col">
<div class="card px-3 py-4 rounded h-100 border shadow-none">
<div class="card-body p-0">
<div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
<div>
<h6 class="fw-semibold mb-2">28</h6>
<span class="fw-medium text-gray-5">Total
                                                                Late</span>
</div>
<span class="mb-0 bg-info text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6" style="width:48px;height:48px">
<img alt="Clock Icon" src="@endverbatim{{ asset('assets/img/edu/icons/attendence-icon4.png') }}@verbatim"/>
</span>
</div>
</div>
</div>
</div>
<div class="col">
<div class="card px-3 py-4 rounded h-100 border shadow-none">
<div class="card-body p-0">
<div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
<div>
<h6 class="fw-semibold mb-2">12</h6>
<span class="fw-medium text-gray-5">Total
                                                                Holiday</span>
</div>
<span class="mb-0 bg-orange text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6" style="width:48px;height:48px">
<img alt="Holiday Icon" src="@endverbatim{{ asset('assets/img/edu/icons/attendence-icon5.png') }}@verbatim"/>
</span>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="mt-4 mb-3 mx-3">
<div class="d-flex flex-wrap align-items-center gap-4 justify-content-between">
<div class="d-flex flex-wrap align-items-center gap-3">
<div class="">
<select class="form-control form-select">
<option value="Jun 2025/2026">Jun 2025/2026</option>
<option value="Jun 2026/2027">Jun 2026/2027</option>
<option value="Jun 2027/2028">Jun 2027/2028</option>
<option value="Jun 2028/2029">Jun 2028/2029</option>
</select>
</div>
<div class="dropdown">
<button aria-expanded="false" class="px-2 py-2 border rounded d-flex align-items-center gap-3" data-bs-toggle="dropdown" type="button">
<span class="d-flex align-items-center gap-1 text-gray-5">
<i class="ti ti-file-export"></i>
                                                        Export
                                                    </span>
<span class="">
<i class="ti ti-chevron-down"></i>
</span>
</button>
<ul class="dropdown-menu p-2 border shadow">
<li>
<button class="dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" data-bs-target="#exampleModalView" data-bs-toggle="modal" type="button">
<i class="ti ti-file-type-pdf"></i>
                                                            PDF
                                                        </button>
</li>
<li>
<button class="dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" data-bs-target="#exampleModalEdit" data-bs-toggle="modal" type="button">
<i class="ti ti-file-spreadsheet"></i>
                                                            Excel
                                                        </button>
</li>
</ul>
</div>
</div>
<div class="d-flex align-items-center flex-wrap gap-2">
<p class="text-dark fw-medium mb-0">
                                                Present:
                                                <span class="fw-semibold text-success">P </span>
</p>
<p class="text-dark fw-medium mb-0">
                                                Absent:
                                                <span class="fw-semibold text-danger">A </span>
</p>
<p class="text-dark fw-medium mb-0">
                                                Holiday:
                                                <span class="fw-semibold text-warning">H </span>
</p>
<p class="text-dark fw-medium mb-0">
                                                Late:
                                                <span class="fw-semibold text-info">L </span>
</p>
<p class="text-dark fw-medium mb-0">
                                                Half Day:
                                                <span class="fw-semibold text-purple">F </span>
</p>
</div>
</div>
</div>
<div class="table-responsive overflow-x-auto">
<table class="datatable table">
<thead class="thead-light">
<tr>
<th class="bg-secondary-transparent text-dark px-2 py-3">Month
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">1</th>
<th class="bg-secondary-transparent text-dark px-2 py-3">2</th>
<th class="bg-secondary-transparent text-dark px-2 py-3">3</th>
<th class="bg-secondary-transparent text-dark px-2 py-3">4</th>
<th class="bg-secondary-transparent text-dark px-2 py-3">5</th>
<th class="bg-secondary-transparent text-dark px-2 py-3">6</th>
<th class="bg-secondary-transparent text-dark px-2 py-3">7</th>
<th class="bg-secondary-transparent text-dark px-2 py-3">8</th>
<th class="bg-secondary-transparent text-dark px-2 py-3">9</th>
<th class="bg-secondary-transparent text-dark px-2 py-3">10
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">11
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">12
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">13
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">14
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">15
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">15
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">16
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">17
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">18
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">19
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">20
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">21
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">22
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">23
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">24
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">25
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">26
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">27
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">28
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">29
                                                </th>
<th class="bg-secondary-transparent text-dark px-2 py-3">30
                                                </th>
</tr>
</thead>
<tbody>
<tr>
<td class="px-2 py-3">Jan</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">H</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">A</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">F</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">L</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">H</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">A</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">L</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">h</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">F</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">H</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">P</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">A</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">H</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">p</span>
</td>
<td class="px-2 py-3 text-uppercase">
<span class="attendance">p</span>
</td>
</tr>
<tr>
<td class="px-2 py-3">Feb</td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
</tr>
<tr>
<td class="px-2 py-3">Mar</td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
</tr>
<tr>
<td class="px-2 py-3">Apr</td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
</tr>
<tr>
<td class="px-2 py-3">May</td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
</tr>
<tr>
<td class="px-2 py-3">May</td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">F</span></td>
<td class="px-2 py-3"><span class="attendance">L</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">H</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">P</span></td>
<td class="px-2 py-3"><span class="attendance">A</span></td>
</tr>
<tr>
<td class="px-2 py-3">Jun</td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
</tr>
<tr>
<td class="px-2 py-3">Ju</td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
</tr>
<tr>
<td class="px-2 py-3">Aug</td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
</tr>
<tr>
<td class="px-2 py-3">Sep</td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
</tr>
<tr>
<td class="px-2 py-3">Oct</td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
</tr>
<tr>
<td class="px-2 py-3">Nov</td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
</tr>
<tr>
<td class="px-2 py-3">Dec</td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
<td class="px-2 py-3"><span class="attendance"></span></td>
</tr>
</tbody>
</table>
</div>
</div>
</div>
</div>
<!-- Attendance tab end -->
<!-- leaves tab start -->
<div aria-labelledby="pills-leave-tab" class="tab-pane fade" id="pills-leave" role="tabpanel" tabindex="0">
<div class="card">


<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"></div></div><div class="card-body p-0"><div class="table-responsive"><table class="datatable table w-100">
<thead class="thead-light">
<tr>
<th scope="col">
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">S.L</span></div>
</th>
<th scope="col">Leave Type</th>
<th scope="col">Date</th>
<th scope="col">Duration</th>
<th scope="col">Apply Date</th>
<th scope="col">Status</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">01</span></div>
</td>
<td>Medical Leave</td>
<td>07 May 2025 - 08 may 2025</td>
<td>1</td>
<td>07 May 2025 </td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Approved</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">02</span></div>
</td>
<td>Special Leave</td>
<td>07 May 2025 - 08 may 2025</td>
<td>3</td>
<td>07 May 2025 </td>
<td>
<span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pending</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">03</span></div>
</td>
<td>Medical Leave</td>
<td>07 May 2025 - 08 may 2025</td>
<td>5</td>
<td>07 May 2025 </td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Approved</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">04</span></div>
</td>
<td>Casual Leave</td>
<td>07 May 2025 - 08 may 2025</td>
<td>6</td>
<td>07 May 2025 </td>
<td>
<span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pending</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">05</span></div>
</td>
<td>Medical Leave</td>
<td>07 May 2025 - 08 may 2025</td>
<td>1</td>
<td>07 May 2025 </td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Approved</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">06</span></div>
</td>
<td>Special Leave</td>
<td>07 May 2025 - 08 may 2025</td>
<td>2</td>
<td>07 May 2025 </td>
<td>
<span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Rejected</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">07</span></div>
</td>
<td>Medical Leave</td>
<td>07 May 2025 - 08 may 2025</td>
<td>5</td>
<td>07 May 2025 </td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Approved</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">08</span></div>
</td>
<td>Casual Leave</td>
<td>07 May 2025 - 08 may 2025</td>
<td>6</td>
<td>07 May 2025 </td>
<td>
<span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Rejected</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td>Medical Leave</td>
<td>07 May 2025 - 08 may 2025</td>
<td>1</td>
<td>07 May 2025 </td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Approved</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td>Special Leave</td>
<td>07 May 2025 - 08 may 2025</td>
<td>2</td>
<td>07 May 2025 </td>
<td>
<span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Rejected</span>
</td>
</tr>
</tbody>
</table></div></div></div>
</div>
<!-- leaves tab start -->
<!-- Fees tab start -->
<div aria-labelledby="pills-fees-tab" class="tab-pane fade" id="pills-fees" role="tabpanel" tabindex="0">
<div class="card">


<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"></div></div><div class="card-body p-0"><div class="table-responsive"><table class="datatable table w-100">
<thead class="thead-light">
<tr>
<th scope="col">
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">S.L</span></div>
</th>
<th scope="col">Fees Type</th>
<th scope="col">Due Date</th>
<th scope="col">Payment Type</th>
<th scope="col">Amount</th>
<th scope="col">Discount</th>
<th scope="col">Fine</th>
<th scope="col">Paid</th>
<th scope="col">Due</th>
<th scope="col">Paid Date</th>
<th scope="col">Status</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">01</span></div>
</td>
<td>May Month Fees</td>
<td>05 May 2025</td>
<td>Bank</td>
<td>TSh 700.50</td>
<td>10%</td>
<td>TSh 50</td>
<td>TSh 700.50</td>
<td>TSh 0</td>
<td>12 May 2025</td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                                    Paid
                                                </span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">02</span></div>
</td>
<td>June Month Fees</td>
<td>05 Jun 2025</td>
<td>Cash</td>
<td>TSh 680.00</td>
<td>5%</td>
<td>TSh 30</td>
<td>TSh 350.00</td>
<td>TSh 330.00</td>
<td>09 Jun 2025</td>
<td>
<span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                                    Partial
                                                </span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">03</span></div>
</td>
<td>July Month Fees</td>
<td>05 Jul 2025</td>
<td>Bank</td>
<td>TSh 700.00</td>
<td>10%</td>
<td>TSh 0</td>
<td>TSh 0.00</td>
<td>TSh 700.00</td>
<td>-</td>
<td>
<span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                                    Unpaid
                                                </span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">04</span></div>
</td>
<td>August Month Fees</td>
<td>05 Aug 2025</td>
<td>Online</td>
<td>TSh 750.00</td>
<td>15%</td>
<td>TSh 40</td>
<td>TSh 750.00</td>
<td>TSh 0</td>
<td>07 Aug 2025</td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                                    Paid
                                                </span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">05</span></div>
</td>
<td>September Month Fees</td>
<td>05 Sep 2025</td>
<td>Bank</td>
<td>TSh 720.00</td>
<td>10%</td>
<td>TSh 25</td>
<td>TSh 360.00</td>
<td>TSh 360.00</td>
<td>10 Sep 2025</td>
<td>
<span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                                    Partial
                                                </span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">06</span></div>
</td>
<td>June month fees</td>
<td>05 Jun 2025</td>
<td>Cash</td>
<td>TSh 700.50</td>
<td>5%</td>
<td>TSh 0</td>
<td>TSh 665.00</td>
<td>TSh 35.50</td>
<td>07 Jun 2025</td>
<td><span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Partial</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">07</span></div>
</td>
<td>July month fees</td>
<td>05 Jul 2025</td>
<td>Bank</td>
<td>TSh 700.50</td>
<td>10%</td>
<td>TSh 25</td>
<td>TSh 700.50</td>
<td>TSh 0</td>
<td>06 Jul 2025</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Paid</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">08</span></div>
</td>
<td>August month fees</td>
<td>05 Aug 2025</td>
<td>Card</td>
<td>TSh 700.50</td>
<td>0%</td>
<td>TSh 0</td>
<td>TSh 0</td>
<td>TSh 700.50</td>
<td>-</td>
<td><span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Unpaid</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td>September month fees</td>
<td>05 Sep 2025</td>
<td>Online</td>
<td>TSh 700.50</td>
<td>5%</td>
<td>TSh 15</td>
<td>TSh 350.00</td>
<td>TSh 350.50</td>
<td>08 Sep 2025</td>
<td><span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Partial</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td>October month fees</td>
<td>05 Oct 2025</td>
<td>Bank</td>
<td>TSh 700.50</td>
<td>10%</td>
<td>TSh 20</td>
<td>TSh 700.50</td>
<td>TSh 0</td>
<td>06 Oct 2025</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Paid</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">11</span></div>
</td>
<td>November month fees</td>
<td>05 Nov 2025</td>
<td>Cash</td>
<td>TSh 700.50</td>
<td>0%</td>
<td>TSh 0</td>
<td>TSh 0</td>
<td>TSh 700.50</td>
<td>-</td>
<td><span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Unpaid</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">12</span></div>
</td>
<td>December month fees</td>
<td>05 Dec 2025</td>
<td>Online</td>
<td>TSh 700.50</td>
<td>15%</td>
<td>TSh 0</td>
<td>TSh 595.00</td>
<td>TSh 105.50</td>
<td>10 Dec 2025</td>
<td><span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Partial</span>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">13</span></div>
</td>
<td>January month fees</td>
<td>05 Jan 2026</td>
<td>Bank</td>
<td>TSh 700.50</td>
<td>10%</td>
<td>TSh 0</td>
<td>TSh 700.50</td>
<td>TSh 0</td>
<td>06 Jan 2026</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Paid</span>
</td>
</tr>
</tbody>
</table></div></div></div>
</div>
<!-- Fees tab end -->
<!-- Exam tab start -->
<div aria-labelledby="pills-exam-tab" class="tab-pane fade" id="pills-exam" role="tabpanel" tabindex="0">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Exam </h6>
</div>
<div class="card-body p-3 d-flex flex-column gap-3">
<div class="border rounded overflow-hidden">
<button class="fw-semibold text-gray-5 w-100 py-2 px-3 d-flex align-items-center gap-2 justify-content-between" type="button">
                                        First Semester
                                        <span class="d-flex">
<i class="ti ti-chevron-down"></i>
</span>
</button>
<div>
<table class="datatable table w-100">
<thead class="thead-light">
<tr>
<th class="text-start" scope="col">Subject</th>
<th class="text-start" scope="col">Max Marks</th>
<th class="text-start" scope="col">Min Marks</th>
<th class="text-start" scope="col">Marks Obtained</th>
<th class="text-start" scope="col">Grade</th>
<th class="text-start" scope="col">Result</th>
</tr>
</thead>
<tbody>
<tr>
<td class="text-start">Bangla</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">80</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">English</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">60</td>
<td class="text-start">B+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">ICT</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">70</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Physics </td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">60 </td>
<td class="text-start">B</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Chemistry </td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">48 </td>
<td class="text-start">B</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Mathematics</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">80</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
</tbody>
<tfoot>
<tr>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Rank: 30
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Total: 600
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
</td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Total Obtain Marks: 398
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Grande: A
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Results: Pass
                                                    </td>
</tr>
</tfoot>
</table>
</div>
</div>
<div class="border rounded overflow-hidden">
<button class="fw-semibold text-gray-5 w-100 py-2 px-3 d-flex align-items-center gap-2 justify-content-between" type="button">
                                        Monthly Test (Jun-2025)
                                        <span class="d-flex">
<i class="ti ti-chevron-down"></i>
</span>
</button>
<div>
<table class="datatable table w-100">
<thead class="thead-light">
<tr>
<th class="text-start" scope="col">Subject</th>
<th class="text-start" scope="col">Max Marks</th>
<th class="text-start" scope="col">Min Marks</th>
<th class="text-start" scope="col">Marks Obtained</th>
<th class="text-start" scope="col">Grade</th>
<th class="text-start" scope="col">Result</th>
</tr>
</thead>
<tbody>
<tr>
<td class="text-start">Bangla</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">80</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">English</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">60</td>
<td class="text-start">B+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">ICT</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">70</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Physics </td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">60 </td>
<td class="text-start">B</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Chemistry </td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">48 </td>
<td class="text-start">B</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Mathematics</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">80</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
</tbody>
<tfoot>
<tr>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Rank: 30
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Total: 600
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
</td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Total Obtain Marks: 398
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Grande: A
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Results: Pass
                                                    </td>
</tr>
</tfoot>
</table>
</div>
</div>
<div class="border rounded overflow-hidden">
<button class="fw-semibold text-gray-5 w-100 py-2 px-3 d-flex align-items-center gap-2 justify-content-between" type="button">
                                        Weekly Test(Jun-2025)
                                        <span class="d-flex">
<i class="ti ti-chevron-down"></i>
</span>
</button>
<div>
<table class="datatable table w-100">
<thead class="thead-light">
<tr>
<th class="text-start" scope="col">Subject</th>
<th class="text-start" scope="col">Max Marks</th>
<th class="text-start" scope="col">Min Marks</th>
<th class="text-start" scope="col">Marks Obtained</th>
<th class="text-start" scope="col">Grade</th>
<th class="text-start" scope="col">Result</th>
</tr>
</thead>
<tbody>
<tr>
<td class="text-start">Bangla</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">80</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">English</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">60</td>
<td class="text-start">B+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">ICT</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">70</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Physics </td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">60 </td>
<td class="text-start">B</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Chemistry </td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">48 </td>
<td class="text-start">B</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Mathematics</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">80</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
</tbody>
<tfoot>
<tr>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Rank: 30
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Total: 600
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
</td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Total Obtain Marks: 398
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Grande: A
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Results: Pass
                                                    </td>
</tr>
</tfoot>
</table>
</div>
</div>
<div class="border rounded overflow-hidden">
<button class="fw-semibold text-gray-5 w-100 py-2 px-3 d-flex align-items-center gap-2 justify-content-between" type="button">
                                        Weekly Test(May-2025)
                                        <span class="d-flex">
<i class="ti ti-chevron-down"></i>
</span>
</button>
<div>
<table class="datatable table w-100">
<thead class="thead-light">
<tr>
<th class="text-start" scope="col">Subject</th>
<th class="text-start" scope="col">Max Marks</th>
<th class="text-start" scope="col">Min Marks</th>
<th class="text-start" scope="col">Marks Obtained</th>
<th class="text-start" scope="col">Grade</th>
<th class="text-start" scope="col">Result</th>
</tr>
</thead>
<tbody>
<tr>
<td class="text-start">Bangla</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">80</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">English</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">60</td>
<td class="text-start">B+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">ICT</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">70</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Physics </td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">60 </td>
<td class="text-start">B</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Chemistry </td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">48 </td>
<td class="text-start">B</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Mathematics</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">80</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
</tbody>
<tfoot>
<tr>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Rank: 30
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Total: 600
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
</td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Total Obtain Marks: 398
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Grande: A
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Results: Pass
                                                    </td>
</tr>
</tfoot>
</table>
</div>
</div>
<div class="border rounded overflow-hidden">
<button class="fw-semibold text-gray-5 w-100 py-2 px-3 d-flex align-items-center gap-2 justify-content-between" type="button">
                                        weeklyTestMay
                                        <span class="d-flex">
<i class="ti ti-chevron-down"></i>
</span>
</button>
<div>
<table class="datatable table w-100">
<thead class="thead-light">
<tr>
<th class="text-start" scope="col">Subject</th>
<th class="text-start" scope="col">Max Marks</th>
<th class="text-start" scope="col">Min Marks</th>
<th class="text-start" scope="col">Marks Obtained</th>
<th class="text-start" scope="col">Grade</th>
<th class="text-start" scope="col">Result</th>
</tr>
</thead>
<tbody>
<tr>
<td class="text-start">Bangla</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">80</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">English</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">60</td>
<td class="text-start">B+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">ICT</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">70</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Physics </td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">60 </td>
<td class="text-start">B</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Chemistry </td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">48 </td>
<td class="text-start">B</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
<tr>
<td class="text-start">Mathematics</td>
<td class="text-start">100.00</td>
<td class="text-start">35.00</td>
<td class="text-start">80</td>
<td class="text-start">A+</td>
<td class="text-start">
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pass</span>
</td>
</tr>
</tbody>
<tfoot>
<tr>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Rank: 30
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Total: 600
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
</td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Total Obtain Marks: 398
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Grande: A
                                                    </td>
<td class="text-dark fw-semibold border-top border-bottom text-start bg-secondary-transparent">
                                                        Results: Pass
                                                    </td>
</tr>
</tfoot>
</table>
</div>
</div>
</div>
</div>
</div>
<!-- Exam tab start -->
<!-- Library tab start -->
<div aria-labelledby="pills-library-tab" class="tab-pane fade" id="pills-library" role="tabpanel" tabindex="0">
<div class="card">


<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"></div></div><div class="card-body p-0"><div class="table-responsive"><table class="datatable table w-100">
<thead class="thead-light">
<tr>
<th class="text-start" scope="col">S.L</th>
<th class="text-start" scope="col">Book Name</th>
<th class="text-start" scope="col">Book Category</th>
<th class="text-start" scope="col">Book Number</th>
<th class="text-start" scope="col">Taken ON</th>
<th class="text-start" scope="col">Last Date</th>
</tr>
</thead>
<tbody>
<tr>
<td class="text-start">01</td>
<td class="text-start">
<div class="d-flex align-items-center">
<img alt="Library Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/library-img1.png') }}@verbatim" style="width:36px;height:36px"/>
<div class="">
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                            Marigold (NCERT)
                                                        </h6>
</div>
</div>
</td>
<td class="text-start">English</td>
<td class="text-start">8512</td>
<td class="text-start"> 05 May 2025</td>
<td class="text-start">05 Jun 2025</td>
</tr>
<tr>
<td class="text-start">02</td>
<td class="text-start">
<div class="d-flex align-items-center">
<img alt="Library Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/library-img2.png') }}@verbatim" style="width:36px;height:36px"/>
<div class="">
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                            Number Magic
                                                        </h6>
</div>
</div>
</td>
<td class="text-start">Mathematics</td>
<td class="text-start">85620</td>
<td class="text-start"> 05 May 2025</td>
<td class="text-start">05 Jun 2025</td>
</tr>
<tr>
<td class="text-start">03</td>
<td class="text-start">
<div class="d-flex align-items-center">
<img alt="Library Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/library-img3.png') }}@verbatim" style="width:36px;height:36px"/>
<div class="">
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                            Mental Math
                                                        </h6>
</div>
</div>
</td>
<td class="text-start">Mathematics</td>
<td class="text-start">8512</td>
<td class="text-start"> 05 May 2025</td>
<td class="text-start">05 Jun 2025</td>
</tr>
<tr>
<td class="text-start">04</td>
<td class="text-start">
<div class="d-flex align-items-center">
<img alt="Library Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/library-img4.png') }}@verbatim" style="width:36px;height:36px"/>
<div class="">
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                            Our Environment
                                                        </h6>
</div>
</div>
</td>
<td class="text-start">Environmental Studies</td>
<td class="text-start">85620</td>
<td class="text-start"> 05 May 2025</td>
<td class="text-start">05 Jun 2025</td>
</tr>
<tr>
<td class="text-start">05</td>
<td class="text-start">
<div class="d-flex align-items-center">
<img alt="Library Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/library-img5.png') }}@verbatim" style="width:36px;height:36px"/>
<div class="">
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                            Brainvita
                                                        </h6>
</div>
</div>
</td>
<td class="text-start">General Knowledge</td>
<td class="text-start">8512</td>
<td class="text-start"> 05 May 2025</td>
<td class="text-start">05 Jun 2025</td>
</tr>
</tbody>
</table></div></div></div>
</div>
<!-- Library tab start -->
</div>
</div>
</div>

@endverbatim
@endsection
@push('modals')
@verbatim
<div aria-hidden="true" class="modal fade" id="exampleModalDelete" tabindex="-1">
<div class="modal-dialog modal-sm modal-dialog-centered" style="max-width:340px">
<div class="modal-content rounded-3 card">
<div class="modal-body pt-4 px-4 pb-4 text-center">
<span class="mb-3 fs-1 text-danger">
<i class="ti ti-trash"></i>
</span>
<h6 class="fw-semibold text-dark mb-0">Are your sure you want to Suspend this Student
                </h6>
<div class="d-flex align-items-center justify-content-center gap-3 mt-4">
<button class="flex-grow-1 border text-danger px-4 py-2 rounded" type="reset">
                        Cancel
                    </button>
<button class="flex-grow-1 btn btn-primary border px-3 py-2 rounded" type="button">
                        Yes, Suspend
                    </button>
</div>
</div>
</div>
</div>
</div>
@endverbatim
@endpush
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
