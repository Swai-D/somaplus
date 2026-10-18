@extends('layouts.app')
@section('title', 'Issue Return')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Issue Return</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><a class="btn btn-primary d-flex align-items-center gap-1" data-bs-target="#add-modal" data-bs-toggle="modal" href="javascript:void(0);"><i class="ti ti-circle-plus me-1"></i>Issue Book</a></div></div>

<div class="mt-4">
<div class="card">

<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"></div></div><div class="card-body p-0"><div class="table-responsive"><table class="datatable table">
<thead class="thead-light">
<tr>
<th scope="col">
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">S.L</span></div>
</th>
<th scope="col">Card No</th>
<th scope="col">Issue To</th>
<th scope="col">Class</th>
<th scope="col">Book Name</th>
<th scope="col">Number</th>
<th scope="col">Issue Date</th>
<th scope="col">Return Date</th>
<th scope="col">Action</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">01</span></div>
</td>
<td>12563</td>
<td>Jon Dev</td>
<td>Class 1 (A)</td>
<td>The Little Prince</td>
<td>101</td>
<td>01 Jun 2015</td>
<td>01 Feb 2015</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModalReturn" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Book Return"><i class="ti ti-arrow-back-up"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">02</span></div>
</td>
<td>12874</td>
<td>Sarah Khan</td>
<td>Class 2 (B)</td>
<td>To Kill a Mockingbird</td>
<td>102</td>
<td>10 Jul 2016</td>
<td>25 Jul 2016</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModalReturn" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Book Return"><i class="ti ti-arrow-back-up"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">03</span></div>
</td>
<td>13345</td>
<td>Michael Lee</td>
<td>Class 3 (C)</td>
<td>1984</td>
<td>103</td>
<td>12 Mar 2017</td>
<td>02 Apr 2017</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModalReturn" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Book Return"><i class="ti ti-arrow-back-up"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">04</span></div>
</td>
<td>14122</td>
<td>Emma Watson</td>
<td>Class 4 (A)</td>
<td>Pride and Prejudice</td>
<td>104</td>
<td>05 Aug 2018</td>
<td>28 Aug 2018</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModalReturn" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Book Return"><i class="ti ti-arrow-back-up"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">05</span></div>
</td>
<td>14567</td>
<td>David Miller</td>
<td>Class 5 (C)</td>
<td>The Great Gatsby</td>
<td>105</td>
<td>11 Nov 2018</td>
<td>05 Dec 2018</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModalReturn" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Book Return"><i class="ti ti-arrow-back-up"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">06</span></div>
</td>
<td>15231</td>
<td>Olivia Brown</td>
<td>Class 6 (B)</td>
<td>The Hobbit</td>
<td>106</td>
<td>22 May 2019</td>
<td>10 Jun 2019</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModalReturn" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Book Return"><i class="ti ti-arrow-back-up"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">07</span></div>
</td>
<td>15890</td>
<td>Lucas Smith</td>
<td>Class 7 (A)</td>
<td>Jane Eyre</td>
<td>107</td>
<td>03 Apr 2020</td>
<td>20 Apr 2020</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModalReturn" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Book Return"><i class="ti ti-arrow-back-up"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">08</span></div>
</td>
<td>16324</td>
<td>Ella Johnson</td>
<td>Class 8 (B)</td>
<td>The Alchemist</td>
<td>108</td>
<td>09 Oct 2021</td>
<td>28 Oct 2021</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModalReturn" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Book Return"><i class="ti ti-arrow-back-up"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td>17215</td>
<td>Noah Wilson</td>
<td>Class 9 (C)</td>
<td>Brave New World</td>
<td>109</td>
<td>05 Jan 2022</td>
<td>28 Jan 2022</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModalReturn" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Book Return"><i class="ti ti-arrow-back-up"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td>18009</td>
<td>Sophia Davis</td>
<td>Class 10 (A)</td>
<td>Harry Potter and the Sorcerer’s Stone</td>
<td>110</td>
<td>15 Mar 2023</td>
<td>10 Apr 2023</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModalReturn" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Book Return"><i class="ti ti-arrow-back-up"></i></a></div></td>
</tr>
</tbody>
</table></div></div></div>
</div>

@endverbatim
@endsection
@push('modals')
@verbatim
<div aria-hidden="true" class="modal fade" id="exampleModalReturn" tabindex="-1">
<div class="modal-dialog modal-sm modal-dialog-centered" style="max-width:340px">
<div class="modal-content rounded-3 card">
<div class="modal-body pt-4 px-4 pb-4 text-center">
<span class="mb-3 fs-1 text-primary">
<i class="ti ti-book"></i>
</span>
<h6 class="fw-semibold text-dark mb-0">Are your sure you want to Return this book!
                </h6>
<div class="d-flex align-items-center justify-content-center gap-3 mt-4">
<button class="flex-grow-1 border text-danger px-4 py-2 rounded" type="reset">
                        No
                    </button>
<button class="flex-grow-1 btn btn-primary border px-3 py-2 rounded" type="button">
                        Yes Return
                    </button>
</div>
</div>
</div>
</div>
</div><div aria-hidden="true" class="modal fade" id="add-modal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
<div class="modal-header"><div class="page-title"><h4>Issue Book</h4></div><button aria-label="Close" class="close bg-danger text-white fs-16" data-bs-dismiss="modal" type="button"><span aria-hidden="true">×</span></button></div>
<form action="#" data-sp-form="" method="post"><div class="modal-body"><div class="row"><div class="col-md-6 mb-1"><label class="form-label" for="add-card_no">Card No<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="1" id="add-card_no" name="card_no" placeholder="Enter card no" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-issue_to">Issue To<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="2" id="add-issue_to" name="issue_to" placeholder="Enter issue to" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-class">Class<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="3" id="add-class" name="class" placeholder="Enter class" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-book_name">Book Name<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="4" id="add-book_name" name="book_name" placeholder="Enter book name" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-number">Number<span class="text-danger ms-1">*</span></label><input class="form-control" data-col="5" id="add-number" name="number" placeholder="Enter number" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-issue_date">Issue Date<span class="text-danger ms-1">*</span></label><input autocomplete="off" class="form-control sp-date" data-col="6" id="add-issue_date" name="issue_date" placeholder="dd Mon yyyy" type="text"/></div><div class="col-md-6 mb-1"><label class="form-label" for="add-return_date">Return Date<span class="text-danger ms-1">*</span></label><input autocomplete="off" class="form-control sp-date" data-col="7" id="add-return_date" name="return_date" placeholder="dd Mon yyyy" type="text"/></div></div></div>
<div class="modal-footer"><button class="btn me-2 btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Issue Book</button></div></form></div></div></div>
@endverbatim
@endpush
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
