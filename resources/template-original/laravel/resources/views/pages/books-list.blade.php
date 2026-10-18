@extends('layouts.app')
@section('title', 'Books List')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Books List</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><a class="btn btn-primary d-flex align-items-center gap-1" data-bs-target="#add-modal" data-bs-toggle="modal" href="javascript:void(0);"><i class="ti ti-circle-plus me-1"></i>Add Book</a></div></div>

<div class="mt-4">
<div class="card">

<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"></div></div><div class="card-body p-0"><div class="table-responsive"><table class="datatable table">
<thead class="thead-light">
<tr>
<th scope="col">
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">S.L</span></div>
</th>
<th scope="col">Subject</th>
<th scope="col">Book Name</th>
<th scope="col">Publisher</th>
<th scope="col">Author</th>
<th scope="col">Number</th>
<th scope="col">Rack No</th>
<th scope="col">Qty</th>
<th scope="col">Available</th>
<th scope="col">Price</th>
<th scope="col">Exam Date</th>
<th scope="col">Action</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">01</span></div>
</td>
<td>Art</td>
<td>The Little Prince</td>
<td>Devon Lane</td>
<td>Darrell Steward</td>
<td>101</td>
<td>1234</td>
<td>60</td>
<td>20</td>
<td>TSh 250</td>
<td>05 Jun 2015</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">02</span></div>
</td>
<td>Mathematics</td>
<td>Advanced Algebra</td>
<td>Penguin Books</td>
<td>Jane Cooper</td>
<td>102</td>
<td>5678</td>
<td>40</td>
<td>18</td>
<td>TSh 300</td>
<td>10 Jul 2016</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">03</span></div>
</td>
<td>Science</td>
<td>Physics for Beginners</td>
<td>HarperCollins</td>
<td>Guy Hawkins</td>
<td>103</td>
<td>8790</td>
<td>55</td>
<td>30</td>
<td>TSh 280</td>
<td>15 Mar 2017</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">04</span></div>
</td>
<td>History</td>
<td>World Wars</td>
<td>Oxford Press</td>
<td>Leslie Alexander</td>
<td>104</td>
<td>3210</td>
<td>35</td>
<td>12</td>
<td>TSh 200</td>
<td>21 Sep 2018</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">05</span></div>
</td>
<td>Geography</td>
<td>Earth &amp; Beyond</td>
<td>Macmillan</td>
<td>Robert Fox</td>
<td>105</td>
<td>4311</td>
<td>45</td>
<td>10</td>
<td>TSh 310</td>
<td>08 Jan 2019</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">06</span></div>
</td>
<td>Biology</td>
<td>Human Anatomy</td>
<td>Cambridge House</td>
<td>Annette Black</td>
<td>106</td>
<td>2915</td>
<td>70</td>
<td>35</td>
<td>TSh 400</td>
<td>11 Dec 2020</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">07</span></div>
</td>
<td>Economics</td>
<td>Money &amp; Markets</td>
<td>Random House</td>
<td>Esther Howard</td>
<td>107</td>
<td>3425</td>
<td>50</td>
<td>28</td>
<td>TSh 270</td>
<td>19 Mar 2021</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">08</span></div>
</td>
<td>Computer Science</td>
<td>JavaScript Essentials</td>
<td>TechWorld</td>
<td>Kathryn Murphy</td>
<td>108</td>
<td>5320</td>
<td>80</td>
<td>60</td>
<td>TSh 500</td>
<td>05 Apr 2022</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td>English</td>
<td>Shakespeare's Works</td>
<td>Vintage Books</td>
<td>Courtney Henry</td>
<td>109</td>
<td>1567</td>
<td>65</td>
<td>45</td>
<td>TSh 350</td>
<td>12 May 2023</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#edit-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Edit"><i class="ti ti-edit"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td>Chemistry</td>
<td>Organic Compounds</td>
<td>Scholastic</td>
<td>Wade Warren</td>
<td>110</td>
<td>4879</td>
<td>75</td>
<td>55</td>
<td>TSh 420</td>
<td>10 Feb 2024</td>
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
<div class="modal-header"><div class="page-title"><h4>Add Book</h4></div><button aria-label="Close" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" type="button"><span aria-hidden="true">×</span></button></div>
<form action="#" data-sp-form="" method="post"><div class="modal-body"><div class="row"><div class="col-md-6 mb-1"><label class="form-label" for="add-subject">Subject<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="1" id="add-subject" name="subject" placeholder="Enter subject" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-book_name">Book Name<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="2" id="add-book_name" name="book_name" placeholder="Enter book name" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-publisher">Publisher<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="3" id="add-publisher" name="publisher" placeholder="Enter publisher" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-author">Author<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="4" id="add-author" name="author" placeholder="Enter author" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-number">Number<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="5" id="add-number" name="number" placeholder="Enter number" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-rack_no">Rack No<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="6" id="add-rack_no" name="rack_no" placeholder="Enter rack no" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-qty">Qty<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="7" id="add-qty" name="qty" placeholder="Enter qty" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-available">Available<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="8" id="add-available" name="available" placeholder="Enter available" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-price">Price<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="9" id="add-price" name="price" placeholder="Enter price" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-exam_date">Exam Date<span class="text-danger ms-1">*</span></label><input autocomplete="off" class="form-control sp-date" data-col="10" id="add-exam_date" name="exam_date" placeholder="dd Mon yyyy" type="text"/></div></div></div>
<div class="modal-footer"><button class="btn me-2 btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Add Book</button></div></form></div></div></div><div aria-hidden="true" class="modal fade" id="edit-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
<div class="modal-header"><div class="page-title"><h4>Edit Book</h4></div><button aria-label="Close" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" type="button"><span aria-hidden="true">×</span></button></div>
<form action="#" data-sp-form="" method="post"><div class="modal-body"><div class="row"><div class="col-md-6 mb-1"><label class="form-label" for="edit-subject">Subject<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="1" id="edit-subject" name="subject" placeholder="Enter subject" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-book_name">Book Name<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="2" id="edit-book_name" name="book_name" placeholder="Enter book name" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-publisher">Publisher<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="3" id="edit-publisher" name="publisher" placeholder="Enter publisher" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-author">Author<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="4" id="edit-author" name="author" placeholder="Enter author" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-number">Number<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="5" id="edit-number" name="number" placeholder="Enter number" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-rack_no">Rack No<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="6" id="edit-rack_no" name="rack_no" placeholder="Enter rack no" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-qty">Qty<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="7" id="edit-qty" name="qty" placeholder="Enter qty" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-available">Available<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="8" id="edit-available" name="available" placeholder="Enter available" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-price">Price<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="9" id="edit-price" name="price" placeholder="Enter price" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="edit-exam_date">Exam Date<span class="text-danger ms-1">*</span></label><input autocomplete="off" class="form-control sp-date" data-col="10" id="edit-exam_date" name="exam_date" placeholder="dd Mon yyyy" type="text"/></div></div></div>
<div class="modal-footer"><button class="btn me-2 btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Save Changes</button></div></form></div></div></div><div aria-hidden="true" class="modal fade" id="delete-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="page-wrapper-new p-0"><div class="content p-5 px-3 text-center">
<span class="rounded-circle d-inline-flex p-2 bg-danger-transparent mb-2"><i class="ti ti-trash fs-24 text-danger"></i></span>
<h4 class="fs-20 fw-bold mb-2 mt-1">Delete Book</h4><p class="mb-0 fs-16">Are you sure you want to delete this book?</p>
<div class="modal-footer-btn mt-3 d-flex justify-content-center"><button class="btn me-2 btn-secondary fs-13 fw-medium p-2 px-3 shadow-none" data-bs-dismiss="modal" type="button">Cancel</button>
<button class="btn btn-primary fs-13 fw-medium p-2 px-3" id="sp-confirm-delete" type="button">Yes Delete</button></div></div></div></div></div></div>
@endverbatim
@endpush
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
