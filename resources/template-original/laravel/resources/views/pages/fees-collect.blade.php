@extends('layouts.app')
@section('title', 'Fees Collect')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Fees Collect</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><a class="btn btn-primary d-flex align-items-center gap-1" data-bs-target="#add-modal" data-bs-toggle="modal" href="javascript:void(0);"><i class="ti ti-circle-plus me-1"></i>Collect Fees</a></div></div>

<div class="mt-4">
<div class="card">

<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"><div class="dropdown"><a class="btn btn-white btn-md d-inline-flex align-items-center dropdown-toggle" data-bs-auto-close="outside" data-bs-toggle="dropdown" href="javascript:void(0);"><i class="ti ti-filter me-1"></i>Filter</a><div class="dropdown-menu p-3 filter-menu" style="min-width:320px">
<div class="d-flex align-items-center justify-content-between border-bottom py-2 px-3">
<span class="fw-semibold text-dark">Filter</span>
<button type="button">
<i class="ti ti-x"></i>
</button>
</div>
<form action="fees-collect.html#" class="p-3 d-grid gap-3">
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
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="sectionEdit">Section</label>
<select class="form-control form-select" id="sectionEdit">
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
<th scope="col">Roll No</th>
<th scope="col">Class</th>
<th scope="col">Amount</th>
<th scope="col">Paid</th>
<th scope="col">Due</th>
<th scope="col">Date </th>
<th scope="col">Status </th>
<th scope="col">Action</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">01</span></div>
</td>
<td>
<span class="text-primary">AD52365</span>
</td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img1.png') }}@verbatim"/>
<div class="">
<h6 class="mb-0 fw-medium flex-grow-1">Kathryn Murphy</h6>
</div>
</div>
</td>
<td>12</td>
<td>Class 1 (A)</td>
<td>TSh 700.50</td>
<td>TSh 700.50</td>
<td>TSh 0</td>
<td>12 May 2025</td>
<td>
<span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Paid</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#payslipModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">01</span></div>
</td>
<td><span class="text-primary">AD52365</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img1.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Kathryn Murphy</h6>
</div>
</div>
</td>
<td>12</td>
<td>Class 1 (A)</td>
<td>TSh 700.50</td>
<td>TSh 700.50</td>
<td>TSh 0</td>
<td>12 May 2025</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Paid</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#payslipModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">02</span></div>
</td>
<td><span class="text-primary">AD52366</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img2.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Jerome Bell</h6>
</div>
</div>
</td>
<td>08</td>
<td>Class 2 (B)</td>
<td>TSh 850.00</td>
<td>TSh 450.00</td>
<td>TSh 400.00</td>
<td>10 May 2025</td>
<td><span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Partial</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#payslipModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a></div></td>
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
<h6 class="mb-0 fw-medium flex-grow-1">Theresa Webb</h6>
</div>
</div>
</td>
<td>19</td>
<td>Class 3 (A)</td>
<td>TSh 920.75</td>
<td>TSh 0</td>
<td>TSh 920.75</td>
<td>08 May 2025</td>
<td><span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Unpaid</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#payslipModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a></div></td>
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
<h6 class="mb-0 fw-medium flex-grow-1">Cody Fisher</h6>
</div>
</div>
</td>
<td>10</td>
<td>Class 4 (C)</td>
<td>TSh 750.00</td>
<td>TSh 750.00</td>
<td>TSh 0</td>
<td>05 May 2025</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Paid</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#payslipModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a></div></td>
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
<h6 class="mb-0 fw-medium flex-grow-1">Annette Black</h6>
</div>
</div>
</td>
<td>16</td>
<td>Class 5 (B)</td>
<td>TSh 630.20</td>
<td>TSh 500.00</td>
<td>TSh 130.20</td>
<td>03 May 2025</td>
<td><span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Partial</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#payslipModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a></div></td>
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
<h6 class="mb-0 fw-medium flex-grow-1">Jenny Wilson</h6>
</div>
</div>
</td>
<td>09</td>
<td>Class 6 (A)</td>
<td>TSh 800.00</td>
<td>TSh 800.00</td>
<td>TSh 0</td>
<td>01 May 2025</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Paid</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#payslipModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a></div></td>
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
<h6 class="mb-0 fw-medium flex-grow-1">Darlene Robertson</h6>
</div>
</div>
</td>
<td>11</td>
<td>Class 7 (A)</td>
<td>TSh 950.00</td>
<td>TSh 400.00</td>
<td>TSh 550.00</td>
<td>28 Apr 2025</td>
<td><span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Partial</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#payslipModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">08</span></div>
</td>
<td><span class="text-primary">AD52372</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img8.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Wade Warren</h6>
</div>
</div>
</td>
<td>18</td>
<td>Class 8 (B)</td>
<td>TSh 880.00</td>
<td>TSh 880.00</td>
<td>TSh 0</td>
<td>25 Apr 2025</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Paid</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#payslipModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td><span class="text-primary">AD52373</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img9.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Esther Howard</h6>
</div>
</div>
</td>
<td>13</td>
<td>Class 9 (C)</td>
<td>TSh 990.00</td>
<td>TSh 0</td>
<td>TSh 990.00</td>
<td>22 Apr 2025</td>
<td><span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Unpaid</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#payslipModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td><span class="text-primary">AD52374</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/avatar-img10.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Guy Hawkins</h6>
</div>
</div>
</td>
<td>15</td>
<td>Class 10 (A)</td>
<td>TSh 1020.00</td>
<td>TSh 1020.00</td>
<td>TSh 0</td>
<td>20 Apr 2025</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Paid</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#payslipModal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a></div></td>
</tr>
</tbody>
</table></div></div></div>
</div>

@endverbatim
@endsection
@push('modals')
@verbatim
<div aria-hidden="true" class="modal fade" id="payslipModal" tabindex="-1">
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
<span class="text-dark text-start" style="width:110px">Student Name</span>
<span class="text-dark">: Jon Deve</span>
</div>
<div class="fw-medium d-flex">
<span class="text-dark text-start" style="width:110px">Class</span>
<span class="text-dark">: 5 (A)</span>
</div>
<div class="fw-medium d-flex">
<span class="text-dark text-start" style="width:110px">Roll No.</span>
<span class="text-dark">: 10</span>
</div>
</div>
<div class="d-flex flex-column">
<div class="fw-medium d-flex">
<span class="text-dark text-start">Date: 15 Jan 2025</span>
</div>
<div class="fw-medium d-flex">
<span class="text-dark text-start">Collected By: Admin</span>
</div>
<div class="fw-medium d-flex">
<span class="text-dark text-start">Payment By: Bank</span>
</div>
</div>
</div>
<ul class="border mt-4 rounded overflow-hidden">
<li class="py-2 px-3 d-flex align-items-center justify-content-between gap-3 bg-secondary-transparent border-bottom">
<span class="text-dark fw-semibold">Amount</span>
<span class="text-dark fw-semibold">Paid</span>
<span class="text-dark fw-semibold">Balance</span>
</li>
<li class="py-2 px-3 d-flex align-items-center justify-content-between gap-3">
<span class="text-dark">2500</span>
<span class="text-dark">TSh 1500</span>
<span class="text-dark">TSh 500</span>
</li>
</ul>
<div class="text-center mt-5">
<h6 class="mb-4">Thanks</h6>
<p class="text-gray-5 mb-0">This receipt is computer generated hence on signature is required</p>
</div>
<div class="text-center mt-5">
<p class="text-gray-5 mb-0">Made by <span class="fw-semibold">Wowtheme7.</span> </p>
</div>
</div>
</div>
</div>
</div><div aria-hidden="true" class="modal fade" id="add-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
<div class="modal-header"><div class="page-title"><h4>Collect Fees</h4></div><button aria-label="Close" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" type="button"><span aria-hidden="true">×</span></button></div>
<form action="#" data-sp-form="" method="post"><div class="modal-body"><div class="row"><div class="col-md-6 mb-1"><label class="form-label" for="add-admission_no">Admission No<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="1" id="add-admission_no" name="admission_no" placeholder="Enter admission no" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-name">Name<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="2" id="add-name" name="name" placeholder="Enter name" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-roll_no">Roll No<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="3" id="add-roll_no" name="roll_no" placeholder="Enter roll no" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-class">Class<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="4" id="add-class" name="class" placeholder="Enter class" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-amount">Amount<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="5" id="add-amount" name="amount" placeholder="Enter amount" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-paid">Paid<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="6" id="add-paid" name="paid" placeholder="Enter paid" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-due">Due<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="7" id="add-due" name="due" placeholder="Enter due" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-date">Date<span class="text-danger ms-1">*</span></label><input autocomplete="off" class="form-control sp-date" data-col="8" id="add-date" name="date" placeholder="dd Mon yyyy" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-status">Status<span class="text-danger ms-1">*</span></label><select class="form-select" data-col="9" id="add-status" name="status"><option value="">Select</option><option>Paid</option><option>Partial</option><option>Unpaid</option></select></div></div></div>
<div class="modal-footer"><button class="btn me-2 btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Collect Fees</button></div></form></div></div></div>
@endverbatim
@endpush
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
