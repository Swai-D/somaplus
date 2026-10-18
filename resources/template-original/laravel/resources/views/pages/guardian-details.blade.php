@extends('layouts.app')
@section('title', 'Guardian Details')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Guardian Details</h4><h6>Dashboard / Guardian</h6></div></div><ul class="table-top-head"><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><button class="btn btn-primary d-flex align-items-center gap-1 text-dark" type="button">

<i class="ti ti-lock me-1"></i>

                Login Details
            </button></div></div>

<div class="mt-4">
<div class="card h-100">
<div class="card-body p-4">
<div class="d-flex gap-4 flex-md-row flex-column">
<div class="w-100 text-center" style="max-width:300px">
<figure class="mb-4 mx-auto rounded-circle overflow-hidden" style="width:120px;height:120px">
<img alt="guardian Image" class="w-100 h-100 object-fit-cover" src="@endverbatim{{ asset('assets/img/edu/thumbs/teacher-details-img.png') }}@verbatim"/>
</figure>
<h2 class="h6 text-dark mb-2 fw-semibold">Marvin McKinney</h2>
<p class="mb-0">ID: <span class="text-primary fw-semibold"> AD1256589</span>
</p>
<div class="mt-3 d-flex gap-3 w-100">
<a class="btn btn-primary border fw-medium d-flex justify-content-center align-items-center gap-2 flex-grow-1 px-2 py-2 rounded" href="@endverbatim{{ url('edit-guardian') }}@verbatim">

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
<div class="mt-3 d-flex flex-column gap-3">
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Guardians Type</span>
<span class="fw-normal text-gray-5">: Father</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Phone Number</span>
<span class="fw-normal text-gray-5">: +1 21541214</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Occupation</span>
<span class="fw-normal text-gray-5">: N/A</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Address</span>
<span class="fw-normal text-gray-5">: 8502 Preston Rd. Inglewood,
                                        Maine 98380</span>
</div>
<div class="d-flex gap-4">
<span class="fw-semibold text-dark" style="width:110px">Join Date</span>
<span class="fw-normal text-gray-5">: 10 Nov 2006</span>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="mt-3">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Profile Detail</h6>
</div>
<div class="card-body p-0">
<div class="">
<div class="p-3">
<div class="row g-4">
<div class="col-sm-4">
<div class="d-flex align-items-center gap-2">
<figure class="rounded-circle overflow-hidden mb-0" style="width:48px;height:48px">
<img alt="Guardian Image" class="flex-shrink-0 w-100 h-100 object-fit-cover" src="@endverbatim{{ asset('assets/img/edu/thumbs/guardian-img1.png') }}@verbatim"/>
</figure>
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Robert Fox
                                                </h6>
<span class="">Father</span>
</div>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Class</h6>
<span class="">+19854 65642</span>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Roll No</h6>
<span class="">father@example.com</span>
</div>
</div>
</div>
</div>
<div class="p-3">
<div class="row g-4">
<div class="col-sm-4">
<div class="d-flex align-items-center gap-2">
<figure class="rounded-circle overflow-hidden mb-0" style="width:48px;height:48px">
<img alt="Guardian Image" class="flex-shrink-0 w-100 h-100 object-fit-cover" src="@endverbatim{{ asset('assets/img/edu/thumbs/guardian-img2.png') }}@verbatim"/>
</figure>
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Brooklyn
                                                    Simmons</h6>
<span class="">Mother</span>
</div>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Class</h6>
<span class="">+19854 65642</span>
</div>
</div>
<div class="col-sm-4">
<div class="">
<h6 class="mb-2 fw-medium flex-grow-1">Roll No</h6>
<span class="">mother@example.com</span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

@endverbatim
@endsection
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
