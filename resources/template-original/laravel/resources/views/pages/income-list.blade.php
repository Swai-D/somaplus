@extends('layouts.app')
@section('title', 'Income List')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Income List</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><a class="btn btn-primary d-flex align-items-center gap-1" data-bs-target="#add-modal" data-bs-toggle="modal" href="javascript:void(0);"><i class="ti ti-circle-plus me-1"></i>Add Income</a></div></div>

<div class="mt-4">
<div class="card">

<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"></div></div><div class="card-body p-0"><div class="table-responsive"><table class="datatable table">
<thead class="thead-light">
<tr>
<th scope="col">
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">S.L</span></div>
</th>
<th scope="col">Invoice</th>
<th scope="col">Name</th>
<th scope="col">Description</th>
<th scope="col">Income Head</th>
<th scope="col">Payment Type</th>
<th scope="col">Amount</th>
<th scope="col">Date</th>
<th scope="col">Action</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">02</span></div>
</td>
<td>1002</td>
<td>Sports Fund</td>
<td>Football tournament registration fees</td>
<td>Sports</td>
<td>Online</td>
<td>TSh 420</td>
<td>12 Jan 2024</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">03</span></div>
</td>
<td>1003</td>
<td>Exam Fees</td>
<td>Mid-term exam fee collection</td>
<td>Examination</td>
<td>Bank Transfer</td>
<td>TSh 1,200</td>
<td>20 Jan 2024</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">04</span></div>
</td>
<td>1004</td>
<td>Admission</td>
<td>New student admission fees</td>
<td>Admission</td>
<td>Cheque</td>
<td>TSh 2,500</td>
<td>03 Feb 2024</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">05</span></div>
</td>
<td>1005</td>
<td>Science Lab</td>
<td>Lab maintenance fund collection</td>
<td>Laboratory</td>
<td>Cash</td>
<td>TSh 780</td>
<td>14 Feb 2024</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">06</span></div>
</td>
<td>1006</td>
<td>Donation</td>
<td>Alumni donation for scholarship</td>
<td>Donation</td>
<td>Online</td>
<td>TSh 5,000</td>
<td>28 Feb 2024</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">07</span></div>
</td>
<td>1007</td>
<td>Library Sale</td>
<td>Sale of old library books</td>
<td>Library</td>
<td>Cash</td>
<td>TSh 350</td>
<td>03 Mar 2024</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">08</span></div>
</td>
<td>1008</td>
<td>Transport Fees</td>
<td>Monthly bus service collection</td>
<td>Transport</td>
<td>Cash</td>
<td>TSh 980</td>
<td>12 Mar 2024</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td>1009</td>
<td>Event Income</td>
<td>Annual cultural program tickets</td>
<td>Event</td>
<td>Online</td>
<td>TSh 2,300</td>
<td>22 Mar 2024</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td>1010</td>
<td>Miscellaneous</td>
<td>Other small income sources</td>
<td>General</td>
<td>Cash</td>
<td>TSh 250</td>
<td>30 Mar 2024</td>
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
<div class="modal-header"><div class="page-title"><h4>Add Income</h4></div><button aria-label="Close" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" type="button"><span aria-hidden="true">×</span></button></div>
<form action="#" data-sp-form="" method="post"><div class="modal-body"><div class="row"><div class="col-md-6 mb-1"><label class="form-label" for="add-invoice">Invoice<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="1" id="add-invoice" name="invoice" placeholder="Enter invoice" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-name">Name<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="2" id="add-name" name="name" placeholder="Enter name" type="text"/></div><div class="col-12 mb-1"><label class="form-label" for="add-description">Description<span class="text-danger ms-1">*</span></label><textarea class="form-control" data-col="3" id="add-description" name="description" rows="3"></textarea></div><div class="col-md-6 mb-1"><label class="form-label" for="add-income_head">Income Head<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="4" id="add-income_head" name="income_head" placeholder="Enter income head" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-payment_type">Payment Type<span class="text-danger ms-1">*</span></label><select class="form-select" data-col="5" id="add-payment_type" name="payment_type"><option value="">Select</option><option>Cash</option><option>Online</option><option>Bank Transfer</option><option>Cheque</option></select></div><div class="col-md-6 mb-1"><label class="form-label" for="add-amount">Amount<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="6" id="add-amount" name="amount" placeholder="Enter amount" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-date">Date<span class="text-danger ms-1">*</span></label><input autocomplete="off" class="form-control sp-date" data-col="7" id="add-date" name="date" placeholder="dd Mon yyyy" type="text"/></div></div></div>
<div class="modal-footer"><button class="btn me-2 btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Add Income</button></div></form></div></div></div><div aria-hidden="true" class="modal fade" id="edit-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
<div class="modal-header"><div class="page-title"><h4>Edit Income</h4></div><button aria-label="Close" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" type="button"><span aria-hidden="true">×</span></button></div>
<form action="#" data-sp-form="" method="post"><div class="modal-body"><div class="row"><div class="col-md-6 mb-1"><label class="form-label" for="edit-invoice">Invoice<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="1" id="edit-invoice" name="invoice" placeholder="Enter invoice" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-name">Name<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="2" id="edit-name" name="name" placeholder="Enter name" type="text"/></div><div class="col-12 mb-1"><label class="form-label" for="edit-description">Description<span class="text-danger ms-1">*</span></label><textarea class="form-control" data-col="3" id="edit-description" name="description" rows="3"></textarea></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-income_head">Income Head<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="4" id="edit-income_head" name="income_head" placeholder="Enter income head" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-payment_type">Payment Type<span class="text-danger ms-1">*</span></label><select class="form-select" data-col="5" id="edit-payment_type" name="payment_type"><option value="">Select</option><option>Cash</option><option>Online</option><option>Bank Transfer</option><option>Cheque</option></select></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-amount">Amount<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="6" id="edit-amount" name="amount" placeholder="Enter amount" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-date">Date<span class="text-danger ms-1">*</span></label><input autocomplete="off" class="form-control sp-date" data-col="7" id="edit-date" name="date" placeholder="dd Mon yyyy" type="text"/></div></div></div>
<div class="modal-footer"><button class="btn me-2 btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Save Changes</button></div></form></div></div></div><div aria-hidden="true" class="modal fade" id="delete-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="page-wrapper-new p-0"><div class="content p-5 px-3 text-center">
<span class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"><i class="ti ti-trash fs-24 text-danger"></i></span>
<h4 class="fs-20 fw-bold mb-2 mt-1">Delete Income</h4><p class="mb-0 fs-16">Are you sure you want to delete this income?</p>
<div class="modal-footer-btn mt-3 d-flex justify-content-center"><button class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none" data-bs-dismiss="modal" type="button">Cancel</button>
<button class="btn btn-primary fs-13 fw-medium p-2 px-3" id="sp-confirm-delete" type="button">Yes Delete</button></div></div></div></div></div></div>
@endverbatim
@endpush
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
