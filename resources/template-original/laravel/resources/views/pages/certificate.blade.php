@extends('layouts.app')
@section('title', 'Certificate')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Certificate</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><a class="btn btn-primary d-flex align-items-center gap-1" data-bs-target="#add-modal" data-bs-toggle="modal" href="javascript:void(0);"><i class="ti ti-circle-plus me-1"></i>Add Certificate</a></div></div>

<div class="mt-4">
<div class="card">

<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"></div></div><div class="card-body p-0"><div class="table-responsive"><table class="datatable table">
<thead class="thead-light">
<tr>
<th scope="col">
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">S.L</span></div>
</th>
<th scope="col">Name</th>
<th scope="col">Class</th>
<th scope="col">Certificate Name</th>
<th scope="col">Background Image</th>
<th scope="col">Action</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">01</span></div>
</td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Marvin McKinney" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img1.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Marvin McKinney</h6>
<span>Roll No: <span class="fw-semibold">12</span></span>
</div>
</div>
</td>
<td>Class 1 (A)</td>
<td>Transfer Certificate</td>
<td><img alt="Image" src="@endverbatim{{ asset('assets/img/edu/thumbs/background-img.png') }}@verbatim"/></td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:window.print();" title="Print"><i class="ti ti-printer"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">02</span></div>
</td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Kathryn Murphy" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img2.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Kathryn Murphy</h6>
<span>Roll No: <span class="fw-semibold">18</span></span>
</div>
</div>
</td>
<td>Class 2 (B)</td>
<td>Character Certificate</td>
<td><img alt="Image" src="@endverbatim{{ asset('assets/img/edu/thumbs/background-img.png') }}@verbatim"/></td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:window.print();" title="Print"><i class="ti ti-printer"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">03</span></div>
</td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Devon Lane" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img3.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Devon Lane</h6>
<span>Roll No: <span class="fw-semibold">21</span></span>
</div>
</div>
</td>
<td>Class 3 (A)</td>
<td>Sports Achievement Certificate</td>
<td><img alt="Image" src="@endverbatim{{ asset('assets/img/edu/thumbs/background-img.png') }}@verbatim"/></td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:window.print();" title="Print"><i class="ti ti-printer"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">04</span></div>
</td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Cody Fisher" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img4.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Cody Fisher</h6>
<span>Roll No: <span class="fw-semibold">9</span></span>
</div>
</div>
</td>
<td>Class 4 (C)</td>
<td>Merit Certificate</td>
<td><img alt="Image" src="@endverbatim{{ asset('assets/img/edu/thumbs/background-img.png') }}@verbatim"/></td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:window.print();" title="Print"><i class="ti ti-printer"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">05</span></div>
</td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Theresa Webb" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img5.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Theresa Webb</h6>
<span>Roll No: <span class="fw-semibold">15</span></span>
</div>
</div>
</td>
<td>Class 5 (B)</td>
<td>Attendance Certificate</td>
<td><img alt="Image" src="@endverbatim{{ asset('assets/img/edu/thumbs/background-img.png') }}@verbatim"/></td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:window.print();" title="Print"><i class="ti ti-printer"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">06</span></div>
</td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Darrell Steward" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img6.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Darrell Steward</h6>
<span>Roll No: <span class="fw-semibold">5</span></span>
</div>
</div>
</td>
<td>Class 6 (A)</td>
<td>Scholarship Certificate</td>
<td><img alt="Image" src="@endverbatim{{ asset('assets/img/edu/thumbs/background-img.png') }}@verbatim"/></td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:window.print();" title="Print"><i class="ti ti-printer"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">07</span></div>
</td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Leslie Alexander" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img7.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Leslie Alexander</h6>
<span>Roll No: <span class="fw-semibold">11</span></span>
</div>
</div>
</td>
<td>Class 7 (B)</td>
<td>Excellence Certificate</td>
<td><img alt="Image" src="@endverbatim{{ asset('assets/img/edu/thumbs/background-img.png') }}@verbatim"/></td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:window.print();" title="Print"><i class="ti ti-printer"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">08</span></div>
</td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Guy Hawkins" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img8.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Guy Hawkins</h6>
<span>Roll No: <span class="fw-semibold">17</span></span>
</div>
</div>
</td>
<td>Class 8 (A)</td>
<td>Science Fair Certificate</td>
<td><img alt="Image" src="@endverbatim{{ asset('assets/img/edu/thumbs/background-img.png') }}@verbatim"/></td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:window.print();" title="Print"><i class="ti ti-printer"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Brooklyn Simmons" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img9.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Brooklyn Simmons</h6>
<span>Roll No: <span class="fw-semibold">22</span></span>
</div>
</div>
</td>
<td>Class 9 (C)</td>
<td>Best Student Award</td>
<td><img alt="Image" src="@endverbatim{{ asset('assets/img/edu/thumbs/background-img.png') }}@verbatim"/></td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:window.print();" title="Print"><i class="ti ti-printer"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Kristin Watson" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img10.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Kristin Watson</h6>
<span>Roll No: <span class="fw-semibold">19</span></span>
</div>
</div>
</td>
<td>Class 10 (A)</td>
<td>Completion Certificate</td>
<td><img alt="Image" src="@endverbatim{{ asset('assets/img/edu/thumbs/background-img.png') }}@verbatim"/></td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:window.print();" title="Print"><i class="ti ti-printer"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
</tbody>
</table></div></div></div>
</div>

@endverbatim
@endsection
@push('modals')
@verbatim
<div aria-hidden="true" class="modal fade" id="exampleModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content bg-transparent border-0">
<div class="p-0 text-end mb-3">
<button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
</div>
<div class="modal-body p-0">
<img alt="Certificate Image" src="@endverbatim{{ asset('assets/img/edu/thumbs/certificate-img.png') }}@verbatim"/>
</div>
</div>
</div>
</div><div aria-hidden="true" class="modal fade" id="add-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
<div class="modal-header"><div class="page-title"><h4>Add Certificate</h4></div><button aria-label="Close" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" type="button"><span aria-hidden="true">×</span></button></div>
<form action="#" data-sp-form="" method="post"><div class="modal-body"><div class="row"><div class="col-md-6 mb-1"><label class="form-label" for="add-name">Name<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="1" id="add-name" name="name" placeholder="Enter name" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-class">Class<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="2" id="add-class" name="class" placeholder="Enter class" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-certificate_name">Certificate Name<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="3" id="add-certificate_name" name="certificate_name" placeholder="Enter certificate name" type="text"/></div><div class="col-12 mb-1"><label class="form-label" for="add-background_image">Background Image<span class="text-danger ms-1">*</span></label><input class="form-control" id="add-background_image" name="background_image" type="file"/></div></div></div>
<div class="modal-footer"><button class="btn me-2 btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Add Certificate</button></div></form></div></div></div><div aria-hidden="true" class="modal fade" id="edit-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
<div class="modal-header"><div class="page-title"><h4>Edit Certificate</h4></div><button aria-label="Close" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" type="button"><span aria-hidden="true">×</span></button></div>
<form action="#" data-sp-form="" method="post"><div class="modal-body"><div class="row"><div class="col-md-6 mb-1"><label class="form-label" for="edit-name">Name<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="1" id="edit-name" name="name" placeholder="Enter name" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-class">Class<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="2" id="edit-class" name="class" placeholder="Enter class" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-certificate_name">Certificate Name<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="3" id="edit-certificate_name" name="certificate_name" placeholder="Enter certificate name" type="text"/></div><div class="col-12 mb-1"><label class="form-label" for="edit-background_image">Background Image<span class="text-danger ms-1">*</span></label><input class="form-control" id="edit-background_image" name="background_image" type="file"/></div></div></div>
<div class="modal-footer"><button class="btn me-2 btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Save Changes</button></div></form></div></div></div><div aria-hidden="true" class="modal fade" id="delete-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="page-wrapper-new p-0"><div class="content p-5 px-3 text-center">
<span class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"><i class="ti ti-trash fs-24 text-danger"></i></span>
<h4 class="fs-20 fw-bold mb-2 mt-1">Delete Certificate</h4><p class="mb-0 fs-16">Are you sure you want to delete this certificate?</p>
<div class="modal-footer-btn mt-3 d-flex justify-content-center"><button class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none" data-bs-dismiss="modal" type="button">Cancel</button>
<button class="btn btn-primary fs-13 fw-medium p-2 px-3" id="sp-confirm-delete" type="button">Yes Delete</button></div></div></div></div></div></div>
@endverbatim
@endpush
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
