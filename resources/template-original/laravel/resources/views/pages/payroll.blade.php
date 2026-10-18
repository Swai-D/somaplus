@extends('layouts.app')
@section('title', 'Payroll')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Payroll</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><button class="btn btn-primary d-flex align-items-center gap-1" type="button">

<i class="ti ti-circle-plus me-1"></i>

                Proceed to pay
            </button></div></div>

<div class="mt-4">
<div class="card">

<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"><div class="dropdown"><a class="btn btn-white btn-md d-inline-flex align-items-center dropdown-toggle" data-bs-auto-close="outside" data-bs-toggle="dropdown" href="javascript:void(0);"><i class="ti ti-filter me-1"></i>Filter</a><div class="dropdown-menu p-3 filter-menu" style="min-width:320px">
<div class="d-flex align-items-center justify-content-between border-bottom py-2 px-3">
<span class="fw-semibold text-dark">Filter</span>
<button type="button">
<i class="ti ti-x"></i>
</button>
</div>
<form action="payroll.html#" class="p-3">
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
<th scope="col">Department</th>
<th scope="col">Designation</th>
<th scope="col">Payment Method</th>
<th scope="col">Net Salary</th>
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
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Marvin McKinney</h6>
</div>
</div>
</td>
<td>English Department</td>
<td>Principal</td>
<td>Bank</td>
<td>TSh 5,000</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Paid</span>
</td>
<td><button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">View Payslip</button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">02</span></div>
</td>
<td><span class="text-primary">AD52366</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Courtney Henry Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img2.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium">Courtney Henry</h6>
</div>
</div>
</td>
<td>Mathematics Department</td>
<td>Vice Principal</td>
<td>Cash</td>
<td>TSh 4,200</td>
<td><span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pending</span>
</td>
<td><button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">View Payslip</button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">03</span></div>
</td>
<td><span class="text-primary">AD52367</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Ralph Edwards Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img3.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium">Ralph Edwards</h6>
</div>
</div>
</td>
<td>Science Department</td>
<td>Lecturer</td>
<td>Bank</td>
<td>TSh 3,500</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Paid</span>
</td>
<td><button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">View Payslip</button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">04</span></div>
</td>
<td><span class="text-primary">AD52368</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Annette Black Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img4.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium">Annette Black</h6>
</div>
</div>
</td>
<td>IT Department</td>
<td>Software Engineer</td>
<td>Bank</td>
<td>TSh 4,800</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Paid</span>
</td>
<td><button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">View Payslip</button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">05</span></div>
</td>
<td><span class="text-primary">AD52369</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Theresa Webb Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img5.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium">Theresa Webb</h6>
</div>
</div>
</td>
<td>Administration</td>
<td>Office Manager</td>
<td>Cheque</td>
<td>TSh 3,200</td>
<td><span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Unpaid</span>
</td>
<td><button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">View Payslip</button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">06</span></div>
</td>
<td><span class="text-primary">AD52370</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Jacob Jones Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img6.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium">Jacob Jones</h6>
</div>
</div>
</td>
<td>Finance Department</td>
<td>Accountant</td>
<td>Bank</td>
<td>TSh 4,000</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Paid</span>
</td>
<td><button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">View Payslip</button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">07</span></div>
</td>
<td><span class="text-primary">AD52371</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Kathryn Murphy Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img7.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium">Kathryn Murphy</h6>
</div>
</div>
</td>
<td>Human Resources</td>
<td>HR Manager</td>
<td>Bank</td>
<td>TSh 4,600</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Paid</span>
</td>
<td><button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">View Payslip</button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">08</span></div>
</td>
<td><span class="text-primary">AD52372</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Esther Howard Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img8.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium">Esther Howard</h6>
</div>
</div>
</td>
<td>Marketing Department</td>
<td>Marketing Executive</td>
<td>Cash</td>
<td>TSh 3,700</td>
<td><span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pending</span>
</td>
<td><button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">View Payslip</button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td><span class="text-primary">AD52373</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Floyd Miles Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img9.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium">Floyd Miles</h6>
</div>
</div>
</td>
<td>Library Department</td>
<td>Librarian</td>
<td>Cheque</td>
<td>TSh 3,000</td>
<td><span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Unpaid</span>
</td>
<td><button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">View Payslip</button>
</td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td><span class="text-primary">AD52374</span></td>
<td>
<div class="d-flex align-items-center">
<img alt="Jane Cooper Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img4.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium">Jane Cooper</h6>
</div>
</div>
</td>
<td>Support Department</td>
<td>Office Assistant</td>
<td>Cash</td>
<td>TSh 2,500</td>
<td><span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pending</span>
</td>
<td><button class="btn btn-sm btn-light" data-bs-target="#payslipModal" data-bs-toggle="modal" type="button">View Payslip</button>
</td>
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
<span class="text-dark text-start" style="width:110px">Invoice No</span>
<span class="text-dark">: #5695</span>
</div>
<div class="fw-medium d-flex">
<span class="text-dark text-start" style="width:110px">employee Name</span>
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
<p class="text-gray-5 mb-0">If you need further assistance, please feel free to
                        contact HR at <span class="fw-semibold text-dark">Example school</span> </p>
</div>
<div class="text-center mt-5">
<p class="text-gray-5 mb-0">Made by <span class="fw-semibold">Wowtheme7.</span>
</p>
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
