@extends('layouts.app')
@section('title', 'Exam Schedule')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Exam Schedule</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><a class="btn btn-primary d-flex align-items-center gap-1" data-bs-target="#add-modal" data-bs-toggle="modal" href="javascript:void(0);"><i class="ti ti-circle-plus me-1"></i>Add Exam Schedule</a></div></div>

<div class="mt-4">
<div class="card">

<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"></div></div><div class="card-body p-0"><div class="table-responsive"><table class="datatable table">
<thead class="thead-light">
<tr>
<th scope="col">
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">S.L</span></div>
</th>
<th scope="col">Class</th>
<th scope="col">Subject</th>
<th scope="col">Exam Date</th>
<th scope="col">Start Time</th>
<th scope="col">End Time</th>
<th scope="col">Duration</th>
<th scope="col">Room </th>
<th scope="col">Action</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">01</span></div>
</td>
<td>Class 1 (A)</td>
<td>English</td>
<td>05 Jun 2015</td>
<td>10:00 AM</td>
<td>01:00 PM</td>
<td>3 hrs</td>
<td>101</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">01</span></div>
</td>
<td>Class 1 (A)</td>
<td>English</td>
<td>05 Jun 2015</td>
<td>10:00 AM</td>
<td>01:00 PM</td>
<td>3 hrs</td>
<td>101</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">02</span></div>
</td>
<td>Class 2 (B)</td>
<td>Mathematics</td>
<td>12 Jul 2016</td>
<td>09:30 AM</td>
<td>12:30 PM</td>
<td>3 hrs</td>
<td>102</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">03</span></div>
</td>
<td>Class 3 (C)</td>
<td>Science</td>
<td>18 Sep 2017</td>
<td>11:00 AM</td>
<td>02:00 PM</td>
<td>3 hrs</td>
<td>103</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">04</span></div>
</td>
<td>Class 4 (A)</td>
<td>History</td>
<td>02 Jan 2018</td>
<td>08:30 AM</td>
<td>11:30 AM</td>
<td>3 hrs</td>
<td>104</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">05</span></div>
</td>
<td>Class 5 (B)</td>
<td>Geography</td>
<td>10 Mar 2019</td>
<td>12:00 PM</td>
<td>03:00 PM</td>
<td>3 hrs</td>
<td>105</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">06</span></div>
</td>
<td>Class 6 (A)</td>
<td>Bangla</td>
<td>20 Apr 2020</td>
<td>09:00 AM</td>
<td>12:00 PM</td>
<td>3 hrs</td>
<td>106</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">07</span></div>
</td>
<td>Class 7 (C)</td>
<td>Computer</td>
<td>15 Aug 2021</td>
<td>01:00 PM</td>
<td>04:00 PM</td>
<td>3 hrs</td>
<td>107</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">08</span></div>
</td>
<td>Class 8 (B)</td>
<td>Physics</td>
<td>09 Oct 2022</td>
<td>10:30 AM</td>
<td>01:30 PM</td>
<td>3 hrs</td>
<td>108</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td>Class 9 (A)</td>
<td>Chemistry</td>
<td>25 Dec 2023</td>
<td>09:45 AM</td>
<td>12:45 PM</td>
<td>3 hrs</td>
<td>109</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td>Class 10 (C)</td>
<td>Biology</td>
<td>03 Feb 2024</td>
<td>02:00 PM</td>
<td>05:00 PM</td>
<td>3 hrs</td>
<td>110</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
</tbody>
</table></div></div></div>
</div>

@endverbatim
@endsection
@push('modals')
@verbatim
<div aria-hidden="true" class="modal fade" id="add-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
<div class="modal-header"><div class="page-title"><h4>Add Exam Schedule</h4></div><button aria-label="Close" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" type="button"><span aria-hidden="true">×</span></button></div>
<form action="#" data-sp-form="" method="post"><div class="modal-body"><div class="row"><div class="col-md-6 mb-1"><label class="form-label" for="add-class">Class<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="1" id="add-class" name="class" placeholder="Enter class" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-subject">Subject<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="2" id="add-subject" name="subject" placeholder="Enter subject" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-exam_date">Exam Date<span class="text-danger ms-1">*</span></label><input autocomplete="off" class="form-control sp-date" data-col="3" id="add-exam_date" name="exam_date" placeholder="dd Mon yyyy" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-start_time">Start Time<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="4" id="add-start_time" name="start_time" placeholder="Enter start time" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-end_time">End Time<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="5" id="add-end_time" name="end_time" placeholder="Enter end time" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-duration">Duration<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="6" id="add-duration" name="duration" placeholder="Enter duration" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-room">Room<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="7" id="add-room" name="room" placeholder="Enter room" type="text"/></div></div></div>
<div class="modal-footer"><button class="btn me-2 btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Add Exam Schedule</button></div></form></div></div></div><div aria-hidden="true" class="modal fade" id="edit-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
<div class="modal-header"><div class="page-title"><h4>Edit Exam Schedule</h4></div><button aria-label="Close" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" type="button"><span aria-hidden="true">×</span></button></div>
<form action="#" data-sp-form="" method="post"><div class="modal-body"><div class="row"><div class="col-md-6 mb-1"><label class="form-label" for="edit-class">Class<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="1" id="edit-class" name="class" placeholder="Enter class" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-subject">Subject<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="2" id="edit-subject" name="subject" placeholder="Enter subject" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-exam_date">Exam Date<span class="text-danger ms-1">*</span></label><input autocomplete="off" class="form-control sp-date" data-col="3" id="edit-exam_date" name="exam_date" placeholder="dd Mon yyyy" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-start_time">Start Time<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="4" id="edit-start_time" name="start_time" placeholder="Enter start time" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-end_time">End Time<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="5" id="edit-end_time" name="end_time" placeholder="Enter end time" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-duration">Duration<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="6" id="edit-duration" name="duration" placeholder="Enter duration" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-room">Room<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="7" id="edit-room" name="room" placeholder="Enter room" type="text"/></div></div></div>
<div class="modal-footer"><button class="btn me-2 btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Save Changes</button></div></form></div></div></div><div aria-hidden="true" class="modal fade" id="delete-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="page-wrapper-new p-0"><div class="content p-5 px-3 text-center">
<span class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"><i class="ti ti-trash fs-24 text-danger"></i></span>
<h4 class="fs-20 fw-bold mb-2 mt-1">Delete Exam Schedule</h4><p class="mb-0 fs-16">Are you sure you want to delete this exam schedule?</p>
<div class="modal-footer-btn mt-3 d-flex justify-content-center"><button class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none" data-bs-dismiss="modal" type="button">Cancel</button>
<button class="btn btn-primary fs-13 fw-medium p-2 px-3" id="sp-confirm-delete" type="button">Yes Delete</button></div></div></div></div></div></div>
@endverbatim
@endpush
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
