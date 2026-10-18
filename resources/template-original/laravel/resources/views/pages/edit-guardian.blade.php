@extends('layouts.app')
@section('title', 'Edit Guardian')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Edit Guardian</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul></div>

<form action="edit-guardian.html#" class="mt-4">
<div class="row gy-3">
<div class="col-xl-12">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Personal Info</h6>
</div>
<div class="card-body p-3">
<div class="row gy-3">
<div class="col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="guardianType">Guardian
                                            Type
                                        </label>
<select class="form-control form-select" id="guardianType">
<option disabled="" value="Select Guardian">Select Guardian</option>
<option value="Father">Father</option>
<option value="Mother">Mother</option>
<option value="Brother">Brother</option>
<option value="Sister">Sister</option>
<option value="Uncle">Uncle</option>
</select>
</div>
</div>
<div class="col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="guardianName">Guardian
                                            Name
                                        </label>
<input class="form-control" id="guardianName" placeholder="Enter guardian name" type="text"/>
</div>
</div>
<div class="col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="phoneNumber">Instagram
                                        </label>
<input class="form-control" id="phoneNumber" placeholder="Enter phone number" type="tel"/>
</div>
</div>
<div class="col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="occupation">Occupation
                                        </label>
<input class="form-control" id="occupation" placeholder="Enter occupation" type="text"/>
</div>
</div>
<div class="col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="guardianAddress">Guardian
                                            Address
                                        </label>
<input class="form-control" id="guardianAddress" placeholder="Enter guardian address" type="text"/>
</div>
</div>
<div class="col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label">Teacher
                                            Photo <span class="text-danger">*</span> </label>
<div class="image-upload download sp-upload mb-0"><input name="myFile" type="file"/><div class="image-uploads"><i class="ti ti-circle-plus fs-18"></i><h4>Drag &amp; drop or click to upload</h4></div></div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-xl-12">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Login Details</h6>
</div>
<div class="card-body p-3">
<div class="row gy-3">
<div class="col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="myEmail">Email
                                            <span class="text-danger">*</span>
</label>
<input class="form-control" id="myEmail" placeholder="Enter Email" type="email"/>
</div>
</div>
<div class="col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="your-password">Password
                                            <span class="text-danger">*</span>
</label>
<div class="position-relative">
<input class="form-control" id="your-password" placeholder="Enter your password" type="password"/>
<span class="toggle-password position-absolute end-0 top-50 translate-middle-y me-3 text-gray-5" data-toggle="#your-password"></span>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-12">
<div class="d-flex align-items-center justify-content-center gap-3 mt-2">
<button class="border text-danger px-5 py-2 rounded" type="reset">
                            Reset
                        </button>
<button class="btn btn-primary border px-4 py-2 rounded" type="submit">
                            Save Changes
                        </button>
</div>
</div>
</div>
</form>

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

    // ================== Password Show Hide Js Start ==========
    function initializePasswordToggle(toggleSelector) {
        $(toggleSelector).on('click', function () {
            $(this).toggleClass("ti-eye ti-eye-off");
            var input = $($(this).attr("data-toggle"));
            if (input.attr("type") === "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    }
    // Call the function
    initializePasswordToggle('.toggle-password');
    // ========================= Password Show Hide Js End ===========================

    // ========================== Drag & Drop Upload photo Js start ========================
    document.querySelectorAll(".drop-zone__input").forEach((inputElement) => {
        const dropZoneElement = inputElement.closest(".drop-zone");

        dropZoneElement.addEventListener("click", (e) => {
            inputElement.click();
        });

        inputElement.addEventListener("change", (e) => {
            if (inputElement.files.length) {
                updateThumbnail(dropZoneElement, inputElement.files[0]);
            }
        });

        dropZoneElement.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropZoneElement.classList.add("drop-zone--over");
        });

        ["dragleave", "dragend"].forEach((type) => {
            dropZoneElement.addEventListener(type, (e) => {
                dropZoneElement.classList.remove("drop-zone--over");
            });
        });

        dropZoneElement.addEventListener("drop", (e) => {
            e.preventDefault();

            if (e.dataTransfer.files.length) {
                inputElement.files = e.dataTransfer.files;
                updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
            }

            dropZoneElement.classList.remove("drop-zone--over");
        });
    });

    /**
     * Updates the thumbnail on a drop zone element.
     *
     * @param {HTMLElement} dropZoneElement
     * @param {File} file
     */
    function updateThumbnail(dropZoneElement, file) {
        let thumbnailElement = dropZoneElement.querySelector(".drop-zone__thumb");

        // First time - remove the prompt
        if (dropZoneElement.querySelector(".drop-zone__prompt")) {
            dropZoneElement.querySelector(".drop-zone__prompt").remove();
        }

        // First time - there is no thumbnail element, so lets create it
        if (!thumbnailElement) {
            thumbnailElement = document.createElement("div");
            thumbnailElement.classList.add("drop-zone__thumb");
            dropZoneElement.appendChild(thumbnailElement);
        }

        thumbnailElement.dataset.label = file.name;

        // Show thumbnail for image files
        if (file.type.startsWith("image/")) {
            const reader = new FileReader();

            reader.readAsDataURL(file);
            reader.onload = () => {
                thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
            };
        } else {
            thumbnailElement.style.backgroundImage = null;
        }
    }
    // ========================== Drag & Drop Upload photo Js end ========================


</script>
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
