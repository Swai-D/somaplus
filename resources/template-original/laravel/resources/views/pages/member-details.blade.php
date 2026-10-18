@extends('layouts.app')
@section('title', 'Member Details')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Member Details</h4><h6>Dashboard / Library</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul></div>

<div class="mt-4">
<div class="card h-100">
<div class="card-body p-4">
<div class="d-flex gap-4 flex-md-row flex-column">
<div class="w-100 text-center" style="max-width:300px">
<figure class="mb-3 mx-auto rounded-circle overflow-hidden" style="width:120px;height:120px">
<img alt="Student Image" class="w-100 h-100 object-fit-cover" src="@endverbatim{{ asset('assets/img/edu/thumbs/student-details-img.png') }}@verbatim"/>
</figure>
<h2 class="h6 text-dark mb-4 fw-semibold">Seth Hallam</h2>
<p class="mb-0">Admission No: <span class="text-primary fw-semibold">AD1256589</span>
</p>
<div class="mt-4 d-flex gap-3 w-100">
<button class="btn border fw-medium text-danger d-flex justify-content-center align-items-center gap-2 flex-grow-1 px-2 py-2 rounded" data-bs-target="#exampleModalDelete" data-bs-toggle="modal" type="button">

<i class="ti ti-trash me-1"></i>

                                    Suspend
                                </button>
<a class="btn btn-primary border fw-medium d-flex justify-content-center align-items-center gap-2 flex-grow-1 px-2 py-2 rounded" href="@endverbatim{{ url('edit-student') }}@verbatim">

<i class="ti ti-edit me-1"></i>

                                    Edit
                                </a>
</div>
</div>
<div class="">
<span class="h-100 bg-secondary-transparent" style="width:1px"></span>
</div>
<div class="flex-grow-1">
<div class="pb-3 border-bottom d-flex align-items-center justify-content-between gap-3">
<h3 class="h6 text-dark mb-0 fw-semibold">Personal Info</h3>
</div>
<div class="mt-3 d-flex flex-column gap-2">
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Class</span>
<span class="fw-normal text-gray-5">: Class 6 (2025-26)</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Section</span>
<span class="fw-normal text-gray-5">: A</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Join Date</span>
<span class="fw-normal text-gray-5">: 10</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Gender</span>
<span class="fw-normal text-gray-5">: 10 Nov 2006</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Phone Number</span>
<span class="fw-normal text-primary">: 789678456</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Email</span>
<span class="fw-normal text-primary">: Set@example.com</span>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="my-3">
<div class="card">


<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"></div></div><div class="card-body p-0"><div class="table-responsive"><table class="datatable table w-100">
<thead class="thead-light">
<tr>
<th scope="col">
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">S.L</span></div>
</th>
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
<td>The Catcher in the Rye</td>
<td>106</td>
<td>14 Jan 2019</td>
<td>02 Feb 2019</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModalReturn" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Book Return"><i class="ti ti-arrow-back-up"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">07</span></div>
</td>
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
<td>The Hobbit</td>
<td>108</td>
<td>22 May 2021</td>
<td>10 Jun 2021</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModalReturn" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Book Return"><i class="ti ti-arrow-back-up"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td>The Alchemist</td>
<td>109</td>
<td>09 Oct 2022</td>
<td>28 Oct 2022</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModalReturn" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Book Return"><i class="ti ti-arrow-back-up"></i></a></div></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td>Harry Potter and the Sorcerer’s Stone</td>
<td>110</td>
<td>15 Jan 2023</td>
<td>10 Feb 2023</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#exampleModalReturn" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Book Return"><i class="ti ti-arrow-back-up"></i></a></div></td>
</tr>
</tbody>
</table></div></div></div>
</div>
</div>

@endverbatim
@endsection
@push('modals')
@verbatim
<div aria-hidden="true" class="modal fade" id="exampleModalDelete" tabindex="-1">
<div class="modal-dialog modal-sm modal-dialog-centered" style="max-width:340px">
<div class="modal-content rounded-3 card">
<div class="modal-body pt-4 px-4 pb-4 text-center">
<span class="mb-3 fs-1 text-danger">
<i class="ti ti-trash"></i>
</span>
<h6 class="fw-semibold text-dark mb-0">Are your sure you want to Suspend this Student
                </h6>
<div class="d-flex align-items-center justify-content-center gap-3 mt-4">
<button class="flex-grow-1 border text-danger px-4 py-2 rounded" type="reset">
                        Cancel
                    </button>
<button class="flex-grow-1 btn btn-primary border px-3 py-2 rounded" type="button">
                        Yes, Suspend
                    </button>
</div>
</div>
</div>
</div>
</div><div aria-hidden="true" class="modal fade" id="exampleModalReturn" tabindex="-1">
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
</div>
@endverbatim
@endpush
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
