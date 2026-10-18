@extends('layouts.app')
@section('title', 'Employee List')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Employee List</h4><h6>Dashboard / HRM</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><a class="btn btn-primary d-flex align-items-center gap-1" href="@endverbatim{{ url('add-new-student') }}@verbatim">

<i class="ti ti-circle-plus me-1"></i>

      Add Employee    </a></div></div>

<div class="mt-4">
<div class="card">

<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"><div class="dropdown"><a class="btn btn-white btn-md d-inline-flex align-items-center dropdown-toggle" data-bs-auto-close="outside" data-bs-toggle="dropdown" href="javascript:void(0);"><i class="ti ti-filter me-1"></i>Filter</a><div class="dropdown-menu p-3 filter-menu" style="min-width:320px">
<div class="d-flex align-items-center justify-content-between border-bottom py-2 px-3">
<span class="fw-semibold text-dark">Filter</span>
<button type="button">
<i class="ti ti-x"></i>
</button>
</div>
<form action="employee-list.html#" class="p-3">
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
</div></div></div></div><div class="card-body p-0"><div class="table-responsive"><table class="datatable table">
<thead class="thead-light">
<tr>
<th scope="col">
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">S.L</span></div>
</th>
<th scope="col">ID</th>
<th scope="col">Name</th>
<th scope="col">Subject</th>
<th scope="col">Class</th>
<th scope="col">Email</th>
<th scope="col">Phone Number</th>
<th scope="col">Join Date</th>
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
<img alt="Marvin McKinney Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img1.png') }}@verbatim"/>
<div class="">
<h6 class="mb-0 fw-medium flex-grow-1">Marvin McKinney</h6>
</div>
</div>
</td>
<td>Mathematics</td>
<td>1 (A), 2(A), 3(A)</td>
<td>chinthaka@hotmail.com</td>
<td>209.555.0104</td>
<td>05 May 2012</td>
<td> <span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('employee-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('add-new-employee') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">01</span></div>
</td>
<td><span class="text-primary">AD52365</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Ralph Edwards Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img2.png') }}@verbatim"/>
<div class="">
<h6 class="mb-0 fw-medium flex-grow-1">Ralph Edwards</h6>
</div>
</div>
</td>
<td>Physics</td>
<td>9 (A), 10 (B), </td>
<td>mobileip@mac.com</td>
<td>209.555.0104</td>
<td>05 May 2012</td>
<td> <span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Inactive</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('employee-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('add-new-employee') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">03</span></div>
</td>
<td><span class="text-primary">AD52367</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Courtney Henry Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img3.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Courtney Henry</h6>
</div>
</div>
</td>
<td>Biology</td>
<td>6 (A), 7 (B)</td>
<td>courtney@edu.com</td>
<td>209.555.0134</td>
<td>18 Jan 2014</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('employee-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('add-new-employee') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">04</span></div>
</td>
<td><span class="text-primary">AD52368</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Eleanor Pena Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img4.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Eleanor Pena</h6>
</div>
</div>
</td>
<td>Chemistry</td>
<td>8 (B), 9 (A)</td>
<td>eleanor.pena@school.org</td>
<td>209.555.0189</td>
<td>22 Aug 2016</td>
<td><span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Inactive</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('employee-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('add-new-employee') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">05</span></div>
</td>
<td><span class="text-primary">AD52369</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Cody Fisher Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img5.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Cody Fisher</h6>
</div>
</div>
</td>
<td>English</td>
<td>5 (A), 6 (A)</td>
<td>cody.fisher@school.com</td>
<td>209.555.0192</td>
<td>14 Mar 2015</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('employee-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('add-new-employee') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">06</span></div>
</td>
<td><span class="text-primary">AD52370</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Devon Lane Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img6.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Devon Lane</h6>
</div>
</div>
</td>
<td>Geography</td>
<td>7 (C), 8 (A)</td>
<td>devon@edu.org</td>
<td>209.555.0119</td>
<td>09 Jul 2018</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('employee-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('add-new-employee') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">07</span></div>
</td>
<td><span class="text-primary">AD52371</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Bessie Cooper Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img7.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Bessie Cooper</h6>
</div>
</div>
</td>
<td>History</td>
<td>9 (B), 10 (A)</td>
<td>bessie.cooper@school.org</td>
<td>209.555.0156</td>
<td>23 Feb 2013</td>
<td><span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Inactive</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('employee-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('add-new-employee') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">08</span></div>
</td>
<td><span class="text-primary">AD52372</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Arlene McCoy Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img8.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Arlene McCoy</h6>
</div>
</div>
</td>
<td>Economics</td>
<td>11 (B), 12 (A)</td>
<td>arlene.mccoy@edu.org</td>
<td>209.555.0172</td>
<td>16 Oct 2019</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('employee-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('add-new-employee') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td><span class="text-primary">AD52373</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Annette Black Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img9.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Annette Black</h6>
</div>
</div>
</td>
<td>ICT</td>
<td>8 (A), 9 (B)</td>
<td>annette@school.edu</td>
<td>209.555.0195</td>
<td>05 May 2020</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('employee-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('add-new-employee') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td><span class="text-primary">AD52374</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Guy Hawkins Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img2.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Guy Hawkins</h6>
</div>
</div>
</td>
<td>Accounting</td>
<td>10 (A), 11 (A)</td>
<td>guy.hawkins@edu.com</td>
<td>209.555.0184</td>
<td>11 Dec 2017</td>
<td><span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Inactive</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('employee-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('add-new-employee') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">11</span></div>
</td>
<td><span class="text-primary">AD52375</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Theresa Webb Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img9.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Theresa Webb</h6>
</div>
</div>
</td>
<td>Computer Science</td>
<td>11 (B), 12 (A)</td>
<td>theresa.webb@school.edu</td>
<td>209.555.0217</td>
<td>29 Mar 2019</td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Active</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('employee-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('add-new-employee') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">12</span></div>
</td>
<td><span class="text-primary">AD52376</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Kathryn Murphy Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img6.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Kathryn Murphy</h6>
</div>
</div>
</td>
<td>Environmental Science</td>
<td>9 (A), 10 (B)</td>
<td>kathryn.murphy@college.edu</td>
<td>209.555.0259</td>
<td>03 Feb 2020</td>
<td>
<span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Inactive</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('employee-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('add-new-employee') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Inactive</a></li></ul></div></div></td>
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
<h4 class="fs-20 fw-bold mb-2 mt-1">Delete Employee</h4><p class="mb-0 fs-16">Are you sure you want to delete this employee?</p>
<div class="modal-footer-btn mt-3 d-flex justify-content-center"><button class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none" data-bs-dismiss="modal" type="button">Cancel</button>
<button class="btn btn-primary fs-13 fw-medium p-2 px-3" id="sp-confirm-delete" type="button">Yes Delete</button></div></div></div></div></div></div>
@endverbatim
@endpush
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
