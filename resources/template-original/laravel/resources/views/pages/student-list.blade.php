@extends('layouts.app')
@section('title', 'Student List')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Student List</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><a class="btn btn-primary d-flex align-items-center gap-1" href="@endverbatim{{ url('add-new-student') }}@verbatim">

<i class="ti ti-circle-plus me-1"></i>

      Add Student    </a></div></div>

<div class="mt-4">
<div class="card">

<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"><div class="dropdown"><a class="btn btn-white btn-md d-inline-flex align-items-center dropdown-toggle" data-bs-auto-close="outside" data-bs-toggle="dropdown" href="javascript:void(0);"><i class="ti ti-filter me-1"></i>Filter</a><div class="dropdown-menu p-3 filter-menu" style="min-width:320px">
<div class="d-flex align-items-center justify-content-between border-bottom py-2 px-3">
<span class="fw-semibold text-dark">Filter</span>
<button type="button">
<i class="ti ti-x"></i>
</button>
</div>
<form action="student-list.html#" class="p-3 d-grid gap-3">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="class">Class</label>
<select class="form-control form-select" id="class">
<option disabled="" value="Select">Select Class</option>
<option value="Primary">Primary</option>
<option value="SSC">SSC</option>
<option value="HSC">HSC</option>
<option value="Hons">Hons</option>
<option value="Masters">Masters</option>
</select>
</div>
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="section">Section</label>
<select class="form-control form-select" id="section">
<option value="Select">Select Section</option>
<option value="Arts">Arts</option>
<option value="Science">Science</option>
<option value="Commerce">Commerce</option>
</select>
</div>
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="gender">Gender</label>
<select class="form-control form-select" id="gender">
<option value="Select">Select Gender</option>
<option value="Male">Male</option>
<option value="Female">Female</option>
</select>
</div>
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="status">Status</label>
<select class="form-control form-select" id="status">
<option value="Select">Select Status</option>
<option value="Active">Active</option>
<option value="Inactive">Inactive</option>
</select>
</div>
<div class="">
<button class="btn btn-outline-danger text-danger w-100" type="reset">Reset</button>
</div>
<div class="">
<button class="btn btn-primary w-100" type="submit">Apply</button>
</div>
</form>
</div></div></div></div><div class="card-body p-0"><div class="table-responsive"><table class="datatable table">
<thead class="thead-light">
<tr>
<th scope="col">
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">S.L</span></div>
</th>
<th scope="col">Admission No</th>
<th scope="col">Name</th>
<th scope="col">Class</th>
<th scope="col">Date of Birth</th>
<th scope="col">Gender</th>
<th scope="col">Mobile Number</th>
<th scope="col">Category</th>
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
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img1.png') }}@verbatim"/>
<div class="">
<h6 class="mb-0 fw-medium flex-grow-1">Kathryn Murphy</h6>
<span class="">Roll No: <span class="fw-semibold">12</span> </span>
</div>
</div>
</td>
<td>Class 1 (A)</td>
<td>05 May 2012</td>
<td>Male</td>
<td>209.555.0104</td>
<td>General</td>
<td> <span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('student-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-student') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Collect Fees</a></li><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">02</span></div>
</td>
<td><span class="text-primary">AD52365</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img2.png') }}@verbatim"/>
<div class="">
<h6 class="mb-0 fw-medium flex-grow-1">Floyd Miles</h6>
<span class="">Roll No: <span class="fw-semibold">1</span> </span>
</div>
</div>
</td>
<td>Class 2 (B)</td>
<td>05 May 2012</td>
<td>Female</td>
<td>209.555.0104</td>
<td>Special</td>
<td> <span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Inactive</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('student-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-student') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Collect Fees</a></li><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">03</span></div>
</td>
<td><span class="text-primary">AD52367</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img3.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Cody Fisher</h6>
<span>Roll No: <span class="fw-semibold">7</span></span>
</div>
</div>
</td>
<td>Class 3 (A)</td>
<td>12 Feb 2013</td>
<td>Male</td>
<td>207.445.9821</td>
<td>OBC</td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('student-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-student') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Collect Fees</a></li><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">04</span></div>
</td>
<td><span class="text-primary">AD52368</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img4.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Jane Cooper</h6>
<span>Roll No: <span class="fw-semibold">8</span></span>
</div>
</div>
</td>
<td>Class 4 (C)</td>
<td>17 Mar 2014</td>
<td>Female</td>
<td>204.658.4421</td>
<td>Special</td>
<td>
<span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Inactive</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('student-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-student') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Collect Fees</a></li><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">05</span></div>
</td>
<td><span class="text-primary">AD52369</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img5.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Esther Howard</h6>
<span>Roll No: <span class="fw-semibold">15</span></span>
</div>
</div>
</td>
<td>Class 5 (B)</td>
<td>25 Jul 2013</td>
<td>Female</td>
<td>209.875.9987</td>
<td>General</td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('student-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-student') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Collect Fees</a></li><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">06</span></div>
</td>
<td><span class="text-primary">AD52370</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img6.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Albert Flores</h6>
<span>Roll No: <span class="fw-semibold">3</span></span>
</div>
</div>
</td>
<td>Class 6 (A)</td>
<td>08 Dec 2011</td>
<td>Male</td>
<td>208.324.1110</td>
<td>OBC</td>
<td>
<span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Inactive</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('student-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-student') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Collect Fees</a></li><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">07</span></div>
</td>
<td><span class="text-primary">AD52371</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img7.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Jenny Wilson</h6>
<span>Roll No: <span class="fw-semibold">9</span></span>
</div>
</div>
</td>
<td>Class 7 (C)</td>
<td>19 Sep 2010</td>
<td>Female</td>
<td>206.211.4567</td>
<td>General</td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('student-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-student') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Collect Fees</a></li><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">08</span></div>
</td>
<td><span class="text-primary">AD52367</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img3.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Jane Cooper</h6>
<span>Roll No: <span class="fw-semibold">5</span></span>
</div>
</div>
</td>
<td>Class 3 (A)</td>
<td>12 Jan 2013</td>
<td>Female</td>
<td>202.444.0089</td>
<td>OBC</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('student-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-student') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Collect Fees</a></li><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td><span class="text-primary">AD52368</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img4.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Cameron Williamson</h6>
<span>Roll No: <span class="fw-semibold">23</span></span>
</div>
</div>
</td>
<td>Class 4 (C)</td>
<td>08 Jul 2011</td>
<td>Male</td>
<td>203.111.0456</td>
<td>SC</td>
<td><span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Inactive</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('student-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-student') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Collect Fees</a></li><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td><span class="text-primary">AD52369</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img5.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Theresa Webb</h6>
<span>Roll No: <span class="fw-semibold">10</span></span>
</div>
</div>
</td>
<td>Class 5 (A)</td>
<td>18 Nov 2010</td>
<td>Female</td>
<td>205.777.0190</td>
<td>General</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('student-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-student') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Collect Fees</a></li><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">11</span></div>
</td>
<td><span class="text-primary">AD52370</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img6.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Marvin McKinney</h6>
<span>Roll No: <span class="fw-semibold">7</span></span>
</div>
</div>
</td>
<td>Class 6 (B)</td>
<td>21 Mar 2011</td>
<td>Male</td>
<td>209.660.0912</td>
<td>General</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('student-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-student') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Collect Fees</a></li><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">12</span></div>
</td>
<td><span class="text-primary">AD52371</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img7.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Courtney Henry</h6>
<span>Roll No: <span class="fw-semibold">15</span></span>
</div>
</div>
</td>
<td>Class 7 (A)</td>
<td>10 Feb 2009</td>
<td>Female</td>
<td>204.120.0023</td>
<td>OBC</td>
<td><span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Inactive</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('student-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-student') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Collect Fees</a></li><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
</tbody>
</table></div></div></div>
</div>

@endverbatim
@endsection
@push('modals')
@verbatim
<div aria-hidden="true" class="modal fade" id="delete-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="page-wrapper-new p-0"><div class="content p-5 px-3 text-center">
<span class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"><i class="ti ti-trash fs-24 text-danger"></i></span>
<h4 class="fs-20 fw-bold mb-2 mt-1">Delete Student</h4><p class="mb-0 fs-16">Are you sure you want to delete this student?</p>
<div class="modal-footer-btn mt-3 d-flex justify-content-center"><button class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none" data-bs-dismiss="modal" type="button">Cancel</button>
<button class="btn btn-primary fs-13 fw-medium p-2 px-3" id="sp-confirm-delete" type="button">Yes Delete</button></div></div></div></div></div></div>
@endverbatim
@endpush
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
