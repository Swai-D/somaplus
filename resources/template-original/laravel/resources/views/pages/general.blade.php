@extends('layouts.app')
@section('title', 'General')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>General</h4><h6>Dashboard / Settings</h6></div></div><ul class="table-top-head"><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><button class="btn btn-primary d-flex align-items-center gap-1" type="button">

<i class="ti ti-circle-plus me-1"></i>

                Add General
            </button></div></div>

<div class="card h-100 p-0 rounded-3 overflow-hidden">
<div class="card-body">
<form action="general.html#">
<div class="row">
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="name">Full
                                    Name <span class="text-danger">*</span></label>
<input class="form-control rounded" id="name" placeholder="Enter Full Name" type="text"/>
</div>
</div>
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="email">Email
                                    <span class="text-danger">*</span></label>
<input class="form-control rounded" id="email" placeholder="Enter email address" type="email"/>
</div>
</div>
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="number">Phone
                                    Number</label>
<input class="form-control rounded" id="number" placeholder="Enter phone number" type="email"/>
</div>
</div>
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="Website">
                                    Website</label>
<input class="form-control rounded" id="Website" placeholder="Website URL" type="url"/>
</div>
</div>
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="country">Country <span class="text-danger">*</span> </label>
<select class="form-control rounded form-select" id="country">
<option disabled="" selected="">Select Country</option>
<option>USA</option>
<option>Bangladesh</option>
<option>Pakistan</option>
<option>India</option>
<option>Canada</option>
</select>
</div>
</div>
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="city">City
                                    <span class="text-danger">*</span> </label>
<select class="form-control rounded form-select" id="city">
<option disabled="" selected="">Select City</option>
<option>Washington</option>
<option>Dhaka</option>
<option>Lahor</option>
<option>Panjab</option>
</select>
</div>
</div>
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="state">State
                                    <span class="text-danger">*</span> </label>
<select class="form-control rounded form-select" id="state">
<option disabled="" selected="">Select State</option>
<option>Washington</option>
<option>Dhaka</option>
<option>Lahor</option>
<option>Panjab</option>
</select>
</div>
</div>
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="zip"> Zip
                                    Code <span class="text-danger">*</span></label>
<input class="form-control rounded" id="zip" placeholder="Zip Code" type="text"/>
</div>
</div>
<div class="col-sm-12">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="address">
                                    Address* <span class="text-danger">*</span></label>
<input class="form-control rounded" id="address" placeholder="Enter Your Address" type="text"/>
</div>
</div>
<div class="row gy-4">
<div class="col-md-6">
<label class="form-label fw-semibold text-gray-5 mb-2" for="imageUpload">Logo <span class="text-gray-5 fw-normal">(140px X 140px)</span></label>
<input class="form-control rounded" id="imageUpload" type="file"/>
<div class="mt-3">
<div>
<div id="previewImage1"></div>
</div>
</div>
</div>
<div class="col-md-6">
<label class="form-label fw-semibold text-gray-5 mb-2" for="imageUploadTwo">Logo <span class="text-gray-5 fw-normal">(140px X 140px)</span></label>
<input class="form-control rounded" id="imageUploadTwo" type="file"/>
<div class="mt-3">
<div>
<div id="previewImage2"></div>
</div>
</div>
</div>
</div>
<div class="d-flex align-items-center justify-content-center gap-3 mt-4">
<button class="border text-danger px-5 py-2 rounded" type="reset">
                                Reset
                            </button>
<button class="btn btn-primary border px-4 py-2 rounded" type="submit">
                                Save Change
                            </button>
</div>
</div>
</form>
</div>
</div>

@endverbatim
@endsection
@push('vendor')
@verbatim
<script src="@endverbatim{{ asset('assets/js/jquery-3.7.1.min.js') }}@verbatim"></script>

@endverbatim
@endpush
@push('scripts')
@verbatim
<script>

    // ================== Image Upload Js Start ===========================
    function readURL(input, previewElementId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#' + previewElementId).css('background-image', 'url(' + e.target.result + ')');
                $('#' + previewElementId).hide();
                $('#' + previewElementId).fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imageUpload").change(function() {
        readURL(this, 'previewImage1');
    });

    $("#imageUploadTwo").change(function() {
        readURL(this, 'previewImage2');
    });
    // ================== Image Upload Js End ===========================

</script>
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
