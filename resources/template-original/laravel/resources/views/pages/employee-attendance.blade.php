@extends('layouts.app')
@section('title', 'Employee Attendance')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Employee Attendance</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul></div>

<div class="mt-4">
<div class="card">

<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"><div class="dropdown"><a class="btn btn-white btn-md d-inline-flex align-items-center dropdown-toggle" data-bs-auto-close="outside" data-bs-toggle="dropdown" href="javascript:void(0);"><i class="ti ti-filter me-1"></i>Filter</a><div class="dropdown-menu p-3 filter-menu" style="min-width:320px">
<div class="d-flex align-items-center justify-content-between border-bottom py-2 px-3">
<span class="fw-semibold text-dark">Filter</span>
<button type="button">
<i class="ti ti-x"></i>
</button>
</div>
<form action="employee-attendance.html#" class="p-3">
<div class="row g-3">
<div class="col-6">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="departments">Departments</label>
<select class="form-control form-select" id="departments">
<option value="Principal">Principal</option>
<option value="Senior Teacher">Senior Teacher</option>
<option value="Subject Teacher">Subject Teacher</option>
<option value="Assistant Teacher">Assistant Teacher</option>
<option value="Librarian">Librarian</option>
</select>
</div>
<div class="col-6">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="designation">Designation</label>
<select class="form-control form-select" id="designation">
<option value="English">English</option>
<option value="Bangla">Bangla</option>
<option value="Math">Mathematics</option>
<option value="Physics">Physics</option>
<option value="Chemistry">Chemistry</option>
</select>
</div>
<div class="col-12">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="date">Date</label>
<input class="form-control" id="date" type="date"/>
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
<th scope="col">Admission No</th>
<th scope="col">Name</th>
<th scope="col">Departments</th>
<th scope="col">Designation</th>
<th scope="col">Attendance</th>
<th scope="col">Note</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">01</span></div>
</td>
<td><span class="text-primary">AD52365</span></td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Marvin McKinney Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img1.png') }}@verbatim"/>
<div class="">
<h6 class="mb-0 fw-medium flex-grow-1">Marvin McKinney</h6>
</div>
</div>
</td>
<td>Mathematics</td>
<td>Principal</td>
<td>
<div class="d-flex align-items-center flex-wrap gap-4">
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="p1" name="radio1" type="radio"/>
<label class="form-check-label" for="p1">Present</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="l1" name="radio1" type="radio"/>
<label class="form-check-label" for="l1">Late</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="a1" name="radio1" type="radio"/>
<label class="form-check-label" for="a1">Absent</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="h1" name="radio1" type="radio"/>
<label class="form-check-label" for="h1">Halfday</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="ho1" name="radio1" type="radio"/>
<label class="form-check-label" for="ho1">Holiday</label>
</div>
</div>
</td>
<td><input class="form-control" placeholder="Write note..." type="text"/></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">02</span></div>
</td>
<td><span class="text-primary">AD52366</span></td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Cody Fisher Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img2.png') }}@verbatim"/>
<div class="">
<h6 class="mb-0 fw-medium flex-grow-1">Cody Fisher</h6>
</div>
</div>
</td>
<td>Physics</td>
<td>Senior Teacher</td>
<td>
<div class="d-flex align-items-center flex-wrap gap-4">
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="p2" name="radio2" type="radio"/>
<label class="form-check-label" for="p2">Present</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="l2" name="radio2" type="radio"/>
<label class="form-check-label" for="l2">Late</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="a2" name="radio2" type="radio"/>
<label class="form-check-label" for="a2">Absent</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="h2" name="radio2" type="radio"/>
<label class="form-check-label" for="h2">Halfday</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="ho2" name="radio2" type="radio"/>
<label class="form-check-label" for="ho2">Holiday</label>
</div>
</div>
</td>
<td><input class="form-control" placeholder="Write note..." type="text"/></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">03</span></div>
</td>
<td><span class="text-primary">AD52367</span></td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Jenny Wilson Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img3.png') }}@verbatim"/>
<div class="">
<h6 class="mb-0 fw-medium flex-grow-1">Jenny Wilson</h6>
</div>
</div>
</td>
<td>Biology</td>
<td>Subject Teacher</td>
<td>
<div class="d-flex align-items-center flex-wrap gap-4">
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="p3" name="radio3" type="radio"/>
<label class="form-check-label" for="p3">Present</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="l3" name="radio3" type="radio"/>
<label class="form-check-label" for="l3">Late</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="a3" name="radio3" type="radio"/>
<label class="form-check-label" for="a3">Absent</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="h3" name="radio3" type="radio"/>
<label class="form-check-label" for="h3">Halfday</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="ho3" name="radio3" type="radio"/>
<label class="form-check-label" for="ho3">Holiday</label>
</div>
</div>
</td>
<td><input class="form-control" placeholder="Write note..." type="text"/></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">04</span></div>
</td>
<td><span class="text-primary">AD52368</span></td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Guy Hawkins Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img4.png') }}@verbatim"/>
<div class="">
<h6 class="mb-0 fw-medium flex-grow-1">Guy Hawkins</h6>
</div>
</div>
</td>
<td>English</td>
<td>Assistant Teacher</td>
<td>
<div class="d-flex align-items-center flex-wrap gap-4">
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="p4" name="radio4" type="radio"/>
<label class="form-check-label" for="p4">Present</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="l4" name="radio4" type="radio"/>
<label class="form-check-label" for="l4">Late</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="a4" name="radio4" type="radio"/>
<label class="form-check-label" for="a4">Absent</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="h4" name="radio4" type="radio"/>
<label class="form-check-label" for="h4">Halfday</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="ho4" name="radio4" type="radio"/>
<label class="form-check-label" for="ho4">Holiday</label>
</div>
</div>
</td>
<td><input class="form-control" placeholder="Write note..." type="text"/></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">05</span></div>
</td>
<td><span class="text-primary">AD52369</span></td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Esther Howard Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img5.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Esther Howard</h6>
</div>
</div>
</td>
<td>Math</td>
<td>Pre-Primary Teacher</td>
<td>
<div class="d-flex align-items-center flex-wrap gap-4">
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="p5" name="radio5" type="radio"/>
<label class="form-check-label" for="p5">Present</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="l5" name="radio5" type="radio"/>
<label class="form-check-label" for="l5">Late</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="a5" name="radio5" type="radio"/>
<label class="form-check-label" for="a5">Absent</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="h5" name="radio5" type="radio"/>
<label class="form-check-label" for="h5">Halfday</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="ho5" name="radio5" type="radio"/>
<label class="form-check-label" for="ho5">Holiday</label>
</div>
</div>
</td>
<td><input class="form-control" placeholder="Write note..." type="text"/></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">06</span></div>
</td>
<td><span class="text-primary">AD52370</span></td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Jane Cooper Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img6.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Jane Cooper</h6>
</div>
</div>
</td>
<td>Chemistry</td>
<td>Librarian</td>
<td>
<div class="d-flex align-items-center flex-wrap gap-4">
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="p6" name="radio6" type="radio"/>
<label class="form-check-label" for="p6">Present</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="l6" name="radio6" type="radio"/>
<label class="form-check-label" for="l6">Late</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="a6" name="radio6" type="radio"/>
<label class="form-check-label" for="a6">Absent</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="h6" name="radio6" type="radio"/>
<label class="form-check-label" for="h6">Halfday</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="ho6" name="radio6" type="radio"/>
<label class="form-check-label" for="ho6">Holiday</label>
</div>
</div>
</td>
<td><input class="form-control" placeholder="Write note..." type="text"/></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">07</span></div>
</td>
<td><span class="text-primary">AD52371</span></td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Robert Fox Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img7.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Robert Fox</h6>
</div>
</div>
</td>
<td>Biology</td>
<td>Lab Assistant</td>
<td>
<div class="d-flex align-items-center flex-wrap gap-4">
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="p7" name="radio7" type="radio"/>
<label class="form-check-label" for="p7">Present</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="l7" name="radio7" type="radio"/>
<label class="form-check-label" for="l7">Late</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="a7" name="radio7" type="radio"/>
<label class="form-check-label" for="a7">Absent</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="h7" name="radio7" type="radio"/>
<label class="form-check-label" for="h7">Halfday</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="ho7" name="radio7" type="radio"/>
<label class="form-check-label" for="ho7">Holiday</label>
</div>
</div>
</td>
<td><input class="form-control" placeholder="Write note..." type="text"/></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">08</span></div>
</td>
<td><span class="text-primary">AD52372</span></td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Theresa Webb Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img8.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Theresa Webb</h6>
</div>
</div>
</td>
<td>Mathematics</td>
<td>Senior Teacher</td>
<td>
<div class="d-flex align-items-center flex-wrap gap-4">
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="p8" name="radio8" type="radio"/>
<label class="form-check-label" for="p8">Present</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="l8" name="radio8" type="radio"/>
<label class="form-check-label" for="l8">Late</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="a8" name="radio8" type="radio"/>
<label class="form-check-label" for="a8">Absent</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="h8" name="radio8" type="radio"/>
<label class="form-check-label" for="h8">Halfday</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="ho8" name="radio8" type="radio"/>
<label class="form-check-label" for="ho8">Holiday</label>
</div>
</div>
</td>
<td><input class="form-control" placeholder="Write note..." type="text"/></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">09</span></div>
</td>
<td><span class="text-primary">AD52373</span></td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Courtney Henry Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img9.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Courtney Henry</h6>
</div>
</div>
</td>
<td>Physics</td>
<td>Senior Teacher</td>
<td>
<div class="d-flex align-items-center flex-wrap gap-4">
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="p9" name="radio9" type="radio"/>
<label class="form-check-label" for="p9">Present</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="l9" name="radio9" type="radio"/>
<label class="form-check-label" for="l9">Late</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="a9" name="radio9" type="radio"/>
<label class="form-check-label" for="a9">Absent</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="h9" name="radio9" type="radio"/>
<label class="form-check-label" for="h9">Halfday</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="ho9" name="radio9" type="radio"/>
<label class="form-check-label" for="ho9">Holiday</label>
</div>
</div>
</td>
<td><input class="form-control" placeholder="Write note..." type="text"/></td>
</tr>
<tr>
<td>
<div class="d-flex align-items-center"><label class="checkboxs"><input type="checkbox"/><span class="checkmarks"></span></label><span class="ms-2">10</span></div>
</td>
<td><span class="text-primary">AD52374</span></td>
<td>
<div class="d-flex align-items-center flex-grow-1">
<img alt="Wade Warren Image" class="flex-shrink-0 me-2 rounded" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-avatar-img6.png') }}@verbatim"/>
<div>
<h6 class="mb-0 fw-medium flex-grow-1">Wade Warren</h6>
</div>
</div>
</td>
<td>Biology</td>
<td>Subject Teacher</td>
<td>
<div class="d-flex align-items-center flex-wrap gap-4">
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="p10" name="radio10" type="radio"/>
<label class="form-check-label" for="p10">Present</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="l10" name="radio10" type="radio"/>
<label class="form-check-label" for="l10">Late</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="a10" name="radio10" type="radio"/>
<label class="form-check-label" for="a10">Absent</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="h10" name="radio10" type="radio"/>
<label class="form-check-label" for="h10">Halfday</label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="ho10" name="radio10" type="radio"/>
<label class="form-check-label" for="ho10">Holiday</label>
</div>
</div>
</td>
<td><input class="form-control" placeholder="Write note..." type="text"/></td>
</tr>
</tbody>
</table></div></div></div>
</div>

@endverbatim
@endsection
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
