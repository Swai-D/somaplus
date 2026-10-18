@extends('layouts.app')
@section('title', 'Teacher Details')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Teacher Details</h4><h6>Dashboard / Teacher</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><button class="btn btn-primary d-flex align-items-center gap-1 text-dark" type="button">

<i class="ti ti-lock me-1"></i>

                Login Details
            </button></div></div>

<div class="mt-4">
<div class="card h-100">
<div class="card-body p-4">
<div class="d-flex gap-4 flex-md-row flex-column">
<div class="w-100 text-center" style="max-width:300px">
<figure class="mb-4 mx-auto rounded-circle overflow-hidden" style="width:120px;height:120px">
<img alt="teacher Image" class="w-100 h-100 object-fit-cover" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-details-img.png') }}@verbatim"/>
</figure>
<h2 class="h6 text-dark mb-3 fw-semibold">Marvin McKinney</h2>
<p class="mb-0">ID: <span class="text-primary fw-semibold"> AD1256589</span>
</p>
<p class="mb-0">Subject: <span class="text-dark fw-semibold">Mathematics</span>
</p>
<div class="mt-4 d-flex gap-3 w-100">
<button class="btn border fw-medium text-danger d-flex justify-content-center align-items-center gap-2 flex-grow-1 px-2 py-2 rounded" data-bs-target="#exampleModalDelete" data-bs-toggle="modal" type="button">

<i class="ti ti-trash me-1"></i>

                                    Suspend
                                </button>
<a class="btn btn-primary border fw-medium d-flex justify-content-center align-items-center gap-2 flex-grow-1 px-2 py-2 rounded" href="@endverbatim{{ url('edit-teacher') }}@verbatim">

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
<span class="fw-normal text-gray-5">: Class 6 (2025-26)</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Contract Type</span>
<span class="fw-normal text-gray-5">: Permanent</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Shift</span>
<span class="fw-normal text-gray-5">: Morning</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Work Location</span>
<span class="fw-normal text-gray-5">: 2nd Floor</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Date Of Birth</span>
<span class="fw-normal text-gray-5">: 10 Nov 2006</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Gender</span>
<span class="fw-normal text-gray-5">: Male</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Join Date</span>
<span class="fw-normal text-gray-5">: 05 May 2012</span>
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
<button aria-controls="pills-teacherDetails" aria-selected="true" class="nav-link d-flex align-items-center gap-2 text-gray-5 fw-medium text-capitalize bg-transparent px-3 py-2 active" data-bs-target="#pills-teacherDetails" data-bs-toggle="pill" id="pills-teacherDetails-tab" role="tab" type="button">
<span class="d-flex tab-icon">
<i class="ti ti-users-group"></i>
</span>
                            Teacher Details
                        </button>
</li>
<li class="nav-item" role="presentation">
<button aria-controls="pills-class-routine" aria-selected="false" class="nav-link d-flex align-items-center gap-2 text-gray-5 fw-medium text-capitalize bg-transparent px-3 py-2" data-bs-target="#pills-class-routine" data-bs-toggle="pill" id="pills-class-routine-tab" role="tab" type="button">
<span class="d-flex tab-icon">
<i class="ti ti-file-pencil"></i>
</span>
                            Class Routine
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
<button aria-controls="pills-payroll" aria-selected="false" class="nav-link d-flex align-items-center gap-2 text-gray-5 fw-medium text-capitalize bg-transparent px-3 py-2" data-bs-target="#pills-payroll" data-bs-toggle="pill" id="pills-payroll-tab" role="tab" type="button">
<span class="d-flex tab-icon">
<i class="ti ti-currency-dollar"></i>
</span>
                            Payroll
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
<!-- Teacher Details tab start -->
<div aria-labelledby="pills-teacherDetails-tab" class="tab-pane fade show active" id="pills-teacherDetails" role="tabpanel" tabindex="0">
<div class="row gy-4">
<div class="col-md-12">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Profile Detail</h6>
</div>
<div class="card-body p-0">
<div class="p-3">
<div class="row gy-4">
<div class="col-md-3 col-sm-6">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Date of Birth
                                                        </h6>
<span class="">10 Nov 1995</span>
</div>
</div>
<div class="col-md-3 col-sm-6">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Martial Status
                                                        </h6>
<span class="">Married </span>
</div>
</div>
<div class="col-md-3 col-sm-6">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Qualification
                                                        </h6>
<span class="">MBA</span>
</div>
</div>
<div class="col-md-3 col-sm-6">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Experience</h6>
<span class="">7 Years</span>
</div>
</div>
<div class="col-md-3 col-sm-6">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Father Name </h6>
<span class="">Ralph Edwards</span>
</div>
</div>
<div class="col-md-3 col-sm-6">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Mother Name</h6>
<span class="">Floyd Miles</span>
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
<h6 class="fw-semibold mb-0">Social Media</h6>
</div>
<div class="card-body p-0">
<div class="p-3">
<div class="row gy-4">
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Facebook</h6>
<span class="">www.facebook.com</span>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">LinkedIn</h6>
<span class="">www.linkedin.com</span>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Instagram</h6>
<span class="">www.instagram.com</span>
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
<!-- Teacher Details tab end -->
<!-- Class Routine tab start -->
<div aria-labelledby="pills-class-routine-tab" class="tab-pane fade" id="pills-class-routine" role="tabpanel" tabindex="0">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Class Routine </h6>
</div>
<div class="card-body p-3 d-flex flex-column gap-3">
<div class="overflow-x-auto d-flex pb-2">
<div class="d-flex gap-3 flex-shrink-0 flex-grow-1">
<div class="flex-grow-1">
<h6 class="mb-2">Monday</h6>
<div class="d-flex flex-column gap-3">
<div class="border rounded overflow-hidden">
<h6 class="bg-warning-transparent text-warning fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 1 (A)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: Math</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 16</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">09:00 AM - 09:45 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-info-transparent text-info fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 2 (B)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: English</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 10</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">09:50 AM - 10:35 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-success-transparent text-success fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 3 (A)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: Science</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 22</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">10:40 AM - 11:25 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-danger-transparent text-danger fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 4 (C)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: History</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 8</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">11:30 AM - 12:15 PM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-primary-transparent text-primary fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 5 (B)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: CSE</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 25</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">12:20 PM - 01:05 PM</span>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="flex-grow-1">
<h6 class="mb-2">Tuesday</h6>
<div class="d-flex flex-column gap-3">
<div class="border rounded overflow-hidden">
<h6 class="bg-warning-transparent text-warning fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 1 (A)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: Math</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 16</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">09:00 AM - 09:45 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-info-transparent text-info fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 2 (B)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: English</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 10</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">09:50 AM - 10:35 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-success-transparent text-success fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 3 (A)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: Science</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 22</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">10:40 AM - 11:25 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-danger-transparent text-danger fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 4 (C)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: History</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 8</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">11:30 AM - 12:15 PM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-primary-transparent text-primary fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 5 (B)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: CSE</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 25</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">12:20 PM - 01:05 PM</span>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="flex-grow-1">
<h6 class="mb-2">Wednesday</h6>
<div class="d-flex flex-column gap-3">
<div class="border rounded overflow-hidden">
<h6 class="bg-warning-transparent text-warning fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 1 (A)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: Math</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 16</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">09:00 AM - 09:45 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-info-transparent text-info fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 2 (B)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: English</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 10</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">09:50 AM - 10:35 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-success-transparent text-success fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 3 (A)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: Science</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 22</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">10:40 AM - 11:25 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-danger-transparent text-danger fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 4 (C)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: History</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 8</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">11:30 AM - 12:15 PM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-primary-transparent text-primary fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 5 (B)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: CSE</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 25</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">12:20 PM - 01:05 PM</span>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="flex-grow-1">
<h6 class="mb-2">Thursday</h6>
<div class="d-flex flex-column gap-3">
<div class="border rounded overflow-hidden">
<h6 class="bg-warning-transparent text-warning fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 1 (A)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: Math</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 16</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">09:00 AM - 09:45 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-info-transparent text-info fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 2 (B)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: English</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 10</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">09:50 AM - 10:35 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-success-transparent text-success fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 3 (A)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: Science</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 22</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">10:40 AM - 11:25 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-danger-transparent text-danger fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 4 (C)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: History</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 8</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">11:30 AM - 12:15 PM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-primary-transparent text-primary fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 5 (B)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: CSE</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 25</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">12:20 PM - 01:05 PM</span>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="flex-grow-1">
<h6 class="mb-2">Friday</h6>
<div class="d-flex flex-column gap-3">
<div class="border rounded overflow-hidden">
<h6 class="bg-warning-transparent text-warning fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 1 (A)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: Math</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 16</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">09:00 AM - 09:45 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-info-transparent text-info fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 2 (B)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: English</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 10</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">09:50 AM - 10:35 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-success-transparent text-success fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 3 (A)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: Science</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 22</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">10:40 AM - 11:25 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-danger-transparent text-danger fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 4 (C)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: History</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 8</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">11:30 AM - 12:15 PM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-primary-transparent text-primary fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 5 (B)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: CSE</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 25</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">12:20 PM - 01:05 PM</span>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="flex-grow-1">
<h6 class="mb-2">Saturday</h6>
<div class="d-flex flex-column gap-3">
<div class="border rounded overflow-hidden">
<h6 class="bg-warning-transparent text-warning fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 1 (A)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: Math</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 16</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">09:00 AM - 09:45 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-info-transparent text-info fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 2 (B)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: English</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 10</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">09:50 AM - 10:35 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-success-transparent text-success fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 3 (A)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: Science</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 22</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">10:40 AM - 11:25 AM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-danger-transparent text-danger fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 4 (C)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: History</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 8</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">11:30 AM - 12:15 PM</span>
</div>
</div>
</div>
</div>
<div class="border rounded overflow-hidden">
<h6 class="bg-primary-transparent text-primary fw-semibold py-2 px-3 text-center mb-0">
                                                        Class: 5 (B)
                                                    </h6>
<div class="px-2 py-3 d-flex flex-column gap-2">
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-book"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Subject </span>
<span class="flex-grow-1">: CSE</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-building"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-shrink-0" style="width:64px"> Room No </span>
<span class="flex-grow-1">: 25</span>
</div>
</div>
<div class="d-flex align-items-center gap-2">
<span class="d-flex text-gray-5">
<i class="ti ti-clock"></i>
</span>
<div class="text-dark d-flex">
<span class="flex-grow-1">12:20 PM - 01:05 PM</span>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="flex-grow-1">
<h6 class="mb-2">Sunday</h6>
<div class="d-flex flex-column gap-3">
<div class="border rounded overflow-hidden">
<h6 class="bg-warning-transparent text-warning fw-semibold py-2 px-3 text-center mb-0">
                                                        Holiday 
                                                    </h6>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- Class Routine tab start -->
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
<!-- Payroll tab start -->
<div aria-labelledby="pills-payroll-tab" class="tab-pane fade" id="pills-payroll" role="tabpanel" tabindex="0">
<div class="pb-3">
<div class="row g-3">
<div class="col-xl-3 col-sm-6">
<div class="card px-3 py-4 rounded h-100 border shadow-none">
<div class="card-body p-0">
<div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
<div>
<h6 class="fw-semibold mb-2">TSh 50,000</h6>
<span class="fw-medium text-gray-5">Total Net Salary </span>
</div>
<span class="mb-0 bg-success text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6" style="width:48px;height:48px">
<img alt="Present Icon" src="@endverbatim{{ asset('assets/img/edu/icons/fees-icon3.png') }}@verbatim"/>
</span>
</div>
</div>
</div>
</div>
<div class="col-xl-3 col-sm-6">
<div class="card px-3 py-4 rounded h-100 border shadow-none">
<div class="card-body p-0">
<div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
<div>
<h6 class="fw-semibold mb-2">TSh 5,000</h6>
<span class="fw-medium text-gray-5">Total Gross Salary</span>
</div>
<span class="mb-0 bg-info text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6" style="width:48px;height:48px">
<img alt="Clock Icon" src="@endverbatim{{ asset('assets/img/edu/icons/fees-icon1.png') }}@verbatim"/>
</span>
</div>
</div>
</div>
</div>
<div class="col-xl-3 col-sm-6">
<div class="card px-3 py-4 rounded h-100 border shadow-none">
<div class="card-body p-0">
<div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
<div>
<h6 class="fw-semibold mb-2">TSh 3,000</h6>
<span class="fw-medium text-gray-5">Total Deduction</span>
</div>
<span class="mb-0 bg-danger text-white flex-shrink-0 d-flex justify-content-center align-items-center rounded-circle h6" style="width:48px;height:48px">
<img alt="Absent Icon" src="@endverbatim{{ asset('assets/img/edu/icons/fees-icon2.png') }}@verbatim"/>
</span>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="card">


<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"></div></div><div class="card-body p-0"><div class="table-responsive"><table class="datatable table w-100">
<thead class="thead-light">
<tr>
<th scope="col">
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">S.L</span></div>
</th>
<th scope="col">Invoice ID</th>
<th scope="col">Salary For</th>
<th scope="col">Date</th>
<th scope="col">Net Salary</th>
<th scope="col">Payment Method</th>
<th scope="col">Status</th>
<th scope="col">Action</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">01</span></div>
</td>
<td><span class="text-primary">AD52365</span></td>
<td>Jan 2025</td>
<td>07 Jan 2025</td>
<td>TSh 5,000</td>
<td>Bank</td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                            Paid
                                        </span>
</td>
<td>
<button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">
                                            View Payslip
                                        </button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">02</span></div>
</td>
<td><span class="text-primary">AD52366</span></td>
<td>Feb 2025</td>
<td>08 Feb 2025</td>
<td>TSh 4,800</td>
<td>Cash</td>
<td>
<span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                            Pending
                                        </span>
</td>
<td>
<button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">
                                            View Payslip
                                        </button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">03</span></div>
</td>
<td><span class="text-primary">AD52367</span></td>
<td>Mar 2025</td>
<td>09 Mar 2025</td>
<td>TSh 5,100</td>
<td>Bank</td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                            Paid
                                        </span>
</td>
<td>
<button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">
                                            View Payslip
                                        </button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">04</span></div>
</td>
<td><span class="text-primary">AD52368</span></td>
<td>Apr 2025</td>
<td>06 Apr 2025</td>
<td>TSh 4,950</td>
<td>Online</td>
<td>
<span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                            Failed
                                        </span>
</td>
<td>
<button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">
                                            View Payslip
                                        </button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">05</span></div>
</td>
<td><span class="text-primary">AD52369</span></td>
<td>May 2025</td>
<td>05 May 2025</td>
<td>TSh 5,200</td>
<td>Bank</td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                            Paid
                                        </span>
</td>
<td>
<button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">
                                            View Payslip
                                        </button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">06</span></div>
</td>
<td><span class="text-primary">AD52370</span></td>
<td>Jun 2025</td>
<td>06 Jun 2025</td>
<td>TSh 4,600</td>
<td>Cash</td>
<td>
<span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                            Pending
                                        </span>
</td>
<td>
<button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">
                                            View Payslip
                                        </button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">07</span></div>
</td>
<td><span class="text-primary">AD52371</span></td>
<td>Jul 2025</td>
<td>08 Jul 2025</td>
<td>TSh 5,300</td>
<td>Bank</td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                            Paid
                                        </span>
</td>
<td>
<button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">
                                            View Payslip
                                        </button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">08</span></div>
</td>
<td><span class="text-primary">AD52372</span></td>
<td>Aug 2025</td>
<td>05 Aug 2025</td>
<td>TSh 4,750</td>
<td>Online</td>
<td>
<span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                            Pending
                                        </span>
</td>
<td>
<button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">
                                            View Payslip
                                        </button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td><span class="text-primary">AD52373</span></td>
<td>Sep 2025</td>
<td>06 Sep 2025</td>
<td>TSh 5,400</td>
<td>Bank</td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                            Paid
                                        </span>
</td>
<td>
<button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">
                                            View Payslip
                                        </button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td><span class="text-primary">AD52374</span></td>
<td>Oct 2025</td>
<td>07 Oct 2025</td>
<td>TSh 4,850</td>
<td>Cash</td>
<td>
<span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                            Failed
                                        </span>
</td>
<td>
<button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">
                                            View Payslip
                                        </button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">11</span></div>
</td>
<td><span class="text-primary">AD52375</span></td>
<td>Nov 2025</td>
<td>06 Nov 2025</td>
<td>TSh 5,150</td>
<td>Bank</td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                            Paid
                                        </span>
</td>
<td>
<button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">
                                            View Payslip
                                        </button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">12</span></div>
</td>
<td><span class="text-primary">AD52376</span></td>
<td>Dec 2025</td>
<td>08 Dec 2025</td>
<td>TSh 5,000</td>
<td>Online</td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>
                                            Paid
                                        </span>
</td>
<td>
<button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">
                                            View Payslip
                                        </button>
</td>
</tr>
</tbody>
</table></div></div></div>
</div>
<!-- Payroll tab end -->
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
<h6 class="fw-semibold text-dark mb-0">Are your sure you want to Suspend this teacher
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
</div><div aria-hidden="true" class="modal fade" id="payslipModal" tabindex="-1">
<div class="modal-dialog modal-sm modal-dialog-centered" style="max-width:600px">
<div class="modal-content rounded-3 card">
<div class="modal-body p-4">
<div class="text-center">
<h6 class="mb-0">School Name</h6>
<p class="text-gray-5">Smithbroand, Unit 4, Holler Tower, San Diego</p>
</div>
<div class="d-flex align-items-center justify-content-between gap-3 flex-wrap mt-4">
<div class="d-flex flex-column">
<div class="fw-medium d-flex">
<span class="text-dark text-start" style="width:110px">Invoice No</span>
<span class="text-dark">: #5695</span>
</div>
<div class="fw-medium d-flex">
<span class="text-dark text-start" style="width:110px">Teacher Name</span>
<span class="text-dark">: Jon Dan</span>
</div>
<div class="fw-medium d-flex">
<span class="text-dark text-start" style="width:110px">Phone</span>
<span class="text-dark">: +112515474</span>
</div>
</div>
<div class="d-flex flex-column">
<div class="fw-medium d-flex">
<span class="text-dark text-start">Payslip</span>
</div>
<div class="fw-medium d-flex">
<span class="text-gray-5 text-start">Month: January 2025</span>
</div>
<div class="fw-medium d-flex">
<span class="text-gray-5 text-start">Payment : 15 Jan 2025</span>
</div>
</div>
</div>
<ul class="border mt-4 rounded overflow-hidden">
<li class="py-2 px-3 d-flex align-items-center justify-content-between gap-3 bg-secondary-transparent border-bottom">
<span class="text-dark fw-semibold">Name</span>
<span class="text-dark fw-semibold">Amount</span>
</li>
<li class="py-2 px-3 d-flex align-items-center justify-content-between gap-3 border-bottom">
<span class="text-dark">Base Salary</span>
<span class="text-dark">TSh 2000</span>
</li>
<li class="py-2 px-3 d-flex align-items-center justify-content-between gap-3 border-bottom">
<span class="text-dark">Overtime Pay</span>
<span class="text-dark">TSh 1000</span>
</li>
<li class="py-2 px-3 d-flex align-items-center justify-content-between gap-3 border-bottom">
<span class="text-dark">Bonuses</span>
<span class="text-dark">TSh 2000</span>
</li>
<li class="py-2 px-3 d-flex align-items-center justify-content-between gap-3 border-bottom">
<span class="text-dark">Gross Salary</span>
<span class="text-dark">TSh 5000</span>
</li>
<li class="py-2 px-3 d-flex align-items-center justify-content-between gap-3 bg-secondary-transparent">
<span class="text-dark fw-semibold">Total</span>
<span class="text-dark fw-semibold">TSh 5000</span>
</li>
</ul>
<div class="pt-4 ms-3 text-start">
<p class="text-dark fw-medium mb-0">Payment type : Bank</p>
</div>
<div class="text-center mt-5">
<h6 class="mb-4">Thanks</h6>
<p class="text-gray-5 mb-0">If you need further assistance, please feel free to contact HR at <span class="fw-semibold text-dark">Example school</span> </p>
</div>
<div class="text-center mt-5">
<p class="text-gray-5 mb-0">Made by <span class="fw-semibold">Wowtheme7.</span> </p>
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
