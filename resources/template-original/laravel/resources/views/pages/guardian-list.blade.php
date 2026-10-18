@extends('layouts.app')
@section('title', 'Guardian List')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Guardian List</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><a class="btn btn-primary d-flex align-items-center gap-1" href="@endverbatim{{ url('add-new-guardian') }}@verbatim">

<i class="ti ti-circle-plus me-1"></i>

      Add Guardian    </a></div></div>

<div class="mt-4">
<div class="card">

<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"><div class="dropdown"><a class="btn btn-white btn-md d-inline-flex align-items-center dropdown-toggle" data-bs-auto-close="outside" data-bs-toggle="dropdown" href="javascript:void(0);"><i class="ti ti-filter me-1"></i>Filter</a><div class="dropdown-menu p-3 filter-menu" style="min-width:320px">
<div class="d-flex align-items-center justify-content-between border-bottom py-2 px-3">
<span class="fw-semibold text-dark">Filter</span>
<button type="button">
<i class="ti ti-x"></i>
</button>
</div>
<form action="guardian-list.html#" class="p-3">
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
<th scope="col">Child</th>
<th scope="col">Email</th>
<th scope="col">Phone Number</th>
<th scope="col">Join Date</th>
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
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                    Marvin McKinney</h6>
</div>
</div>
</td>
<td>
<div class="d-flex align-items-center">
<img alt="Darlene Robertson Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img1.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                    Darlene Robertson</h6>
<span class="text-gray-5">Class: 1 (A)</span>
</div>
</div>
</td>
<td>chinthaka@hotmail.com</td>
<td>209.555.0104</td>
<td>05 May 2012</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('guardian-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-guardian') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="@endverbatim{{ url('login') }}@verbatim">Login</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">02</span></div>
</td>
<td><span class="text-primary">AD65412</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Esther Howard Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img2.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium text-gray-5">Esther Howard
                                                </h6>
</div>
</div>
</td>
<td>
<div class="d-flex align-items-center">
<img alt="Jane Cooper Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img2.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium text-gray-5">Jane Cooper</h6>
<span class="text-gray-5">Class: 2 (B)</span>
</div>
</div>
</td>
<td>esther@example.com</td>
<td>305.442.0899</td>
<td>10 Feb 2014</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('guardian-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-guardian') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="@endverbatim{{ url('login') }}@verbatim">Login</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">03</span></div>
</td>
<td><span class="text-primary">AD76548</span></td>
<td>
<div class="d-flex align-items-center"><img alt="Cody Fisher" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img3.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium text-gray-5">Cody Fisher</h6>
</div>
</div>
</td>
<td>
<div class="d-flex align-items-center"><img alt="Robert Fox" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img3.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium text-gray-5">Robert Fox</h6>
<span class="text-gray-5">Class: 3 (A)</span>
</div>
</div>
</td>
<td>codyf@example.com</td>
<td>312.900.0981</td>
<td>08 Mar 2016</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('guardian-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-guardian') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="@endverbatim{{ url('login') }}@verbatim">Login</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">04</span></div>
</td>
<td><span class="text-primary">AD33221</span></td>
<td>
<div class="d-flex align-items-center"><img alt="Jenny Wilson" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img4.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium text-gray-5">Jenny Wilson
                                                </h6>
</div>
</div>
</td>
<td>
<div class="d-flex align-items-center"><img alt="Albert Flores" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img4.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium text-gray-5">Albert Flores
                                                </h6><span class="text-gray-5">Class: 4 (B)</span>
</div>
</div>
</td>
<td>jenny@example.com</td>
<td>404.788.1120</td>
<td>15 Aug 2017</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('guardian-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-guardian') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="@endverbatim{{ url('login') }}@verbatim">Login</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">05</span></div>
</td>
<td><span class="text-primary">AD77231</span></td>
<td>
<div class="d-flex align-items-center"><img alt="Theresa Webb" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img5.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium text-gray-5">Theresa Webb
                                                </h6>
</div>
</div>
</td>
<td>
<div class="d-flex align-items-center"><img alt="Leslie Alexander" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img5.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium text-gray-5">Leslie Alexander
                                                </h6><span class="text-gray-5">Class: 5 (A)</span>
</div>
</div>
</td>
<td>theresa.webb@example.com</td>
<td>213.987.7770</td>
<td>22 Sep 2018</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('guardian-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-guardian') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="@endverbatim{{ url('login') }}@verbatim">Login</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">06</span></div>
</td>
<td><span class="text-primary">AD52366</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="John Smith Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img2.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">John
                                                    Smith</h6>
</div>
</div>
</td>
<td>
<div class="d-flex align-items-center">
<img alt="Student Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img2.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                    Kathryn Murphy</h6>
<span class="text-gray-5">Class: 2 (B)</span>
</div>
</div>
</td>
<td>kathryn.murphy@example.com</td>
<td>208.555.1122</td>
<td>12 Jan 2013</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('guardian-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-guardian') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="@endverbatim{{ url('login') }}@verbatim">Login</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">07</span></div>
</td>
<td><span class="text-primary">AD52367</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Teacher Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img3.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                    Theresa Webb</h6>
</div>
</div>
</td>
<td>
<div class="d-flex align-items-center">
<img alt="Student Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img3.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">Guy
                                                    Hawkins</h6>
<span class="text-gray-5">Class: 3 (C)</span>
</div>
</div>
</td>
<td>guy.hawkins@example.com</td>
<td>203.555.2145</td>
<td>27 Mar 2014</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('guardian-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-guardian') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="@endverbatim{{ url('login') }}@verbatim">Login</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">08</span></div>
</td>
<td><span class="text-primary">AD52368</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Teacher Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img4.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                    Courtney Henry</h6>
</div>
</div>
</td>
<td>
<div class="d-flex align-items-center">
<img alt="Student Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img4.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                    Eleanor Pena</h6>
<span class="text-gray-5">Class: 4 (A)</span>
</div>
</div>
</td>
<td>eleanor.pena@example.com</td>
<td>210.555.1098</td>
<td>08 Sep 2014</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('guardian-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-guardian') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="@endverbatim{{ url('login') }}@verbatim">Login</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td><span class="text-primary">AD52369</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Teacher Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img5.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                    Albert Flores</h6>
</div>
</div>
</td>
<td>
<div class="d-flex align-items-center">
<img alt="Student Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img5.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                    Jenny Wilson</h6>
<span class="text-gray-5">Class: 5 (B)</span>
</div>
</div>
</td>
<td>jenny.wilson@example.com</td>
<td>212.555.3223</td>
<td>11 Nov 2015</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('guardian-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-guardian') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="@endverbatim{{ url('login') }}@verbatim">Login</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td><span class="text-primary">AD52370</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Teacher Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img6.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                    Jerome Bell</h6>
</div>
</div>
</td>
<td>
<div class="d-flex align-items-center">
<img alt="Student Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img6.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                    Esther Howard</h6>
<span class="text-gray-5">Class: 6 (A)</span>
</div>
</div>
</td>
<td>esther.howard@example.com</td>
<td>210.555.7766</td>
<td>22 Feb 2016</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('guardian-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-guardian') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="@endverbatim{{ url('login') }}@verbatim">Login</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">11</span></div>
</td>
<td><span class="text-primary">AD52371</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Teacher Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img7.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                    Devon Lane</h6>
</div>
</div>
</td>
<td>
<div class="d-flex align-items-center">
<img alt="Student Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img7.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                    Robert Fox</h6>
<span class="text-gray-5">Class: 7 (C)</span>
</div>
</div>
</td>
<td>robert.fox@example.com</td>
<td>202.555.8974</td>
<td>10 Jul 2017</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('guardian-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-guardian') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="@endverbatim{{ url('login') }}@verbatim">Login</a></li></ul></div></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">12</span></div>
</td>
<td><span class="text-primary">AD52372</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Teacher Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img8.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                    Floyd Miles</h6>
</div>
</div>
</td>
<td>
<div class="d-flex align-items-center">
<img alt="Student Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img8.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1 text-gray-5">
                                                    Leslie Alexander</h6>
<span class="text-gray-5">Class: 8 (A)</span>
</div>
</div>
</td>
<td>leslie.alexander@example.com</td>
<td>205.555.6742</td>
<td>21 Oct 2018</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('guardian-details') }}@verbatim" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="@endverbatim{{ url('edit-guardian') }}@verbatim" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a><div class="dropdown"><a class="p-2 d-flex align-items-center border rounded" data-bs-toggle="dropdown" href="javascript:void(0);" title="More"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="@endverbatim{{ url('login') }}@verbatim">Login</a></li></ul></div></div></td>
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
<h4 class="fs-20 fw-bold mb-2 mt-1">Delete Guardian</h4><p class="mb-0 fs-16">Are you sure you want to delete this guardian?</p>
<div class="modal-footer-btn mt-3 d-flex justify-content-center"><button class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none" data-bs-dismiss="modal" type="button">Cancel</button>
<button class="btn btn-primary fs-13 fw-medium p-2 px-3" id="sp-confirm-delete" type="button">Yes Delete</button></div></div></div></div></div></div>
@endverbatim
@endpush
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
