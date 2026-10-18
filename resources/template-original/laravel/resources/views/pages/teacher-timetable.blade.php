@extends('layouts.app')
@section('title', 'Teacher Timetable')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Teacher Timetable</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><button class="my-sidebar-btn btn btn-primary d-flex align-items-center gap-1" type="button">

<i class="ti ti-circle-plus me-1"></i>

                Add Timetable
            </button></div></div>

<div class="mt-4">
<div class="card h-100">
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 px-3 py-2 border-bottom">
<div class="d-flex flex-wrap align-items-center gap-3">
<div class="dropdown">
<button aria-expanded="false" class="px-2 py-1 border rounded d-flex align-items-center gap-3" data-bs-toggle="dropdown" type="button">
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
<form class="m-0">
<input class="item-search-input bg-transparent rounded" name="search" placeholder="Search..." type="text"/>
<i class="ti ti-search"></i>
</form>
<div class="dropdown">
<button aria-expanded="false" class="px-2 py-1 border rounded d-flex align-items-center gap-3" data-bs-toggle="dropdown" type="button">
<span class="d-flex align-items-center gap-1 text-gray-5">
                                    Filter
                                </span>
<span class="">
<i class="ti ti-chevron-down"></i>
</span>
</button>
<div class="dropdown-menu border shadow p-0">
<div class="d-flex align-items-center justify-content-between border-bottom py-2 px-3">
<span class="fw-semibold text-dark">Filter</span>
<button type="button">
<i class="ti ti-x"></i>
</button>
</div>
<form action="teacher-timetable.html#" class="p-3">
<div class="row g-3">
<div class="col-12">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="subject">Gender</label>
<select class="form-control form-select" id="subject">
<option value="Select">Select Subject</option>
<option value="Match">Match</option>
<option value="English">English</option>
<option value="Bangla">Bangla</option>
<option value="Economics">Economics</option>
<option value="Physics">Physics</option>
</select>
</div>
<div class="col-12">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="status">Status</label>
<select class="form-control form-select" id="status">
<option value="Select">Select Status</option>
<option value="Active">Active</option>
<option value="Inactive">Inactive</option>
</select>
</div>
<div class="col-6">
<button class="btn btn-outline-danger text-danger w-100" type="reset">Reset</button>
</div>
<div class="col-6">
<button class="btn btn-primary w-100" type="submit">Apply</button>
</div>
</div>
</form>
</div>
</div>
</div>
</div>
<div class="card-body p-3 d-flex flex-column gap-3">
<div class="overflow-x-auto d-flex pb-2">
<div class="d-flex gap-3 flex-shrink-0 flex-grow-1">
<div class="flex-grow-1">
<h6 class="mb-2">Monday</h6>
<div class="d-flex flex-column gap-3">
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-warning-transparent text-warning fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-info-transparent text-info fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-success-transparent text-success fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-danger-transparent text-danger fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-primary-transparent text-primary fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-warning-transparent text-warning fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-info-transparent text-info fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-success-transparent text-success fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-danger-transparent text-danger fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-primary-transparent text-primary fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-warning-transparent text-warning fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-info-transparent text-info fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-success-transparent text-success fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-danger-transparent text-danger fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-primary-transparent text-primary fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-warning-transparent text-warning fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-info-transparent text-info fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-success-transparent text-success fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-danger-transparent text-danger fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-primary-transparent text-primary fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-warning-transparent text-warning fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-info-transparent text-info fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-success-transparent text-success fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-danger-transparent text-danger fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-primary-transparent text-primary fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-warning-transparent text-warning fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-info-transparent text-info fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-success-transparent text-success fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-danger-transparent text-danger fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-primary-transparent text-primary fw-semibold py-2 px-3 text-center mb-0 card-title">
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
<div class="attendance-card border rounded overflow-hidden">
<h6 class="bg-warning-transparent text-warning fw-semibold py-2 px-3 text-center mb-0 card-title">
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

@endverbatim
@endsection
@push('vendor')
@verbatim
<script src="@endverbatim{{ asset('assets/js/jquery-3.7.1.min.js') }}@verbatim"></script>

@endverbatim
@endpush
@push('scripts')
@verbatim
<script>

    // Sidebar js start
    $('.my-sidebar-btn').on('click', function () {
        $('.my-sidebar').addClass('active');
        $('.overlay').addClass('active');
    });
    $('.close-my-sidebar, .overlay').on('click', function () {
        $('.my-sidebar').removeClass('active');
        $('.overlay').removeClass('active');
    });
    // Sidebar js end

    // Search Item input js start
    $('.item-search-input').on('input', function () {
        const inputValue = event.target.value.trim().toLowerCase();

        let items = document.querySelectorAll('.attendance-card .card-title');
        items.forEach(item => {
            const itemText = item.innerText.trim().toLowerCase();

            const matched = itemText.includes(inputValue);
            if (matched) {
                item.parentElement.classList.remove('d-none');
            } else {
                item.parentElement.classList.add('d-none');
            }
        });
    });
    // Search Item input js end


</script>
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
