@extends('layouts.app')
@section('title', 'Edit Student')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Edit Student</h4><h6>Dashboard / Student</h6></div></div><ul class="table-top-head"><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul></div>

<form action="edit-student.html#" class="mt-4">
<div class="row gy-3">
<div class="col-lg-12">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Personal Info</h6>
</div>
<div class="card-body p-3">
<div class="row gy-3">
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="academicYear">Academic
                                            Year <span class="text-danger">* </span> </label>
<select class="form-control form-select" id="academicYear">
<option value="Jun 2025/2026">Jun 2025/2026</option>
<option value="Jun 2026/2027">Jun 2026/2027</option>
<option value="Jun 2027/2028">Jun 2027/2028</option>
<option value="Jun 2028/2029">Jun 2028/2029</option>
</select>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="classSelection">Class
                                            <span class="text-danger">* </span> </label>
<select class="form-control form-select" id="classSelection">
<option disabled="" value="Select a class">Select a class</option>
<option value="Primary">Primary</option>
<option value="High school">High school</option>
<option value="College">College</option>
</select>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="section">Section
                                            <span class="text-danger">* </span> </label>
<select class="form-control form-select" id="section">
<option disabled="" value="Select section">Select section</option>
<option value="Science">Science</option>
<option value="Art">Art</option>
<option value="Commerce">Commerce</option>
</select>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="rollNumber">Roll
                                            Number </label>
<input class="form-control" id="rollNumber" placeholder="Enter your rollNumber" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="admissionNo">Admission
                                            No <span class="text-danger">*</span> </label>
<input class="form-control" id="admissionNo" placeholder="Enter admission number" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="fullName">Full Name
                                            <span class="text-danger">*</span> </label>
<input class="form-control" id="fullName" placeholder="Enter your Full Name" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="category">Category
                                            <span class="text-danger">*</span> </label>
<input class="form-control" id="category" placeholder="Select a Category" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="gender">Gender</label>
<select class="form-control form-select" id="gender">
<option disabled="" value="Select section">Select Gender</option>
<option value="Male">Male</option>
<option value="Female">Female</option>
</select>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="dateOfBirth">Date Of
                                            Birth <span class="text-danger">*</span> </label>
<input class="form-control" id="dateOfBirth" type="date"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="phoneNumber">Phone
                                            Number <span class="text-danger">*</span> </label>
<input class="form-control" id="phoneNumber" placeholder="Enter your Phone Number" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="studentEmail">Email
                                            <span class="text-danger">*</span> </label>
<input class="form-control" id="studentEmail" placeholder="Enter your Email" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label">Student
                                            Photo <span class="text-danger">*</span> </label>
<div class="image-upload download sp-upload mb-0"><input name="myFile" type="file"/><div class="image-uploads"><i class="ti ti-circle-plus fs-18"></i><h4>Drag &amp; drop or click to upload</h4></div></div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-lg-12">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Parent &amp; Guardian Info </h6>
</div>
<div class="card-body p-3">
<div class="row gy-3">
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="fathersName">Fathers
                                            Name </label>
<input class="form-control" id="fathersName" placeholder="Enter Fathers Name" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="fathersPhoneNumber">Phone
                                            Number </label>
<input class="form-control" id="fathersPhoneNumber" placeholder="Enter Fathers Number" type="tel"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="fathersFatherOccupation">Father
                                            Occupation </label>
<input class="form-control" id="fathersFatherOccupation" placeholder="Enter Father Occupation" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label">Fathers
                                            Photo <span class="text-danger">*</span> </label>
<div class="image-upload download sp-upload mb-0"><input name="myFile" type="file"/><div class="image-uploads"><i class="ti ti-circle-plus fs-18"></i><h4>Drag &amp; drop or click to upload</h4></div></div>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="mothersName">Mothers
                                            Name </label>
<input class="form-control" id="mothersName" placeholder="Enter mothers Name" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="mothersPhoneNumber">Phone
                                            Number </label>
<input class="form-control" id="mothersPhoneNumber" placeholder="Enter mothers Number" type="tel"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="mothersFatherOccupation">Father
                                            Occupation </label>
<input class="form-control" id="mothersFatherOccupation" placeholder="Enter Father Occupation" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label">Mothers
                                            Photo <span class="text-danger">*</span> </label>
<div class="image-upload download sp-upload mb-0"><input name="myFile" type="file"/><div class="image-uploads"><i class="ti ti-circle-plus fs-18"></i><h4>Drag &amp; drop or click to upload</h4></div></div>
</div>
</div>
<div class="col-12">
<div class="mt-4">
<span class="fw-bold text-dark d-inline-block mb-2">Select a
                                            Guardian </span>
<div class="d-flex align-items-center flex-wrap gap-4">
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="selectFather" name="radio" type="radio"/>
<label class="form-check-label fw-medium text-gray-5" for="selectFather"> Father </label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="selectMother" name="radio" type="radio"/>
<label class="form-check-label fw-medium text-gray-5" for="selectMother"> Mother </label>
</div>
<div class="form-check d-flex align-items-center gap-2">
<input class="form-check-input" id="selectOthers" name="radio" type="radio"/>
<label class="form-check-label fw-medium text-gray-5" for="selectOthers"> Others </label>
</div>
</div>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="guardianName">Guardian
                                            Name </label>
<input class="form-control" id="guardianName" placeholder="Enter Guardian Name" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="guardianEmail">Guardian
                                            Email </label>
<input class="form-control" id="guardianEmail" placeholder="Enter Guardian Email" type="email"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="guardianPhoneNumber">Phone
                                            Number </label>
<input class="form-control" id="guardianPhoneNumber" placeholder="Enter Guardian Number" type="tel"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="guardianFatherOccupation">Father
                                            Occupation </label>
<input class="form-control" id="guardianFatherOccupation" placeholder="Enter Father Occupation" type="text"/>
</div>
</div>
<div class="col-xl-9 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="guardianGuardianAddress">Guardian
                                            Address </label>
<input class="form-control" id="guardianGuardianAddress" placeholder="Enter Guardian Address" type="text"/>
</div>
</div>
<div class="col-xl-3 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label">Guardian
                                            Photo <span class="text-danger">*</span> </label>
<div class="image-upload download sp-upload mb-0"><input name="myFile" type="file"/><div class="image-uploads"><i class="ti ti-circle-plus fs-18"></i><h4>Drag &amp; drop or click to upload</h4></div></div>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-lg-12">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Medical Details</h6>
</div>
<div class="card-body p-3">
<div class="row gy-3">
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="bloodGroup">Blood
                                            Group</label>
<select class="form-control form-select" id="bloodGroup">
<option disabled="" value="Select blood group">Select blood group</option>
<option value="A+">A+</option>
<option value="AB+">AB+</option>
<option value="A-">A-</option>
<option value="AB-">AB-</option>
</select>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="medicalHeight">Height
                                        </label>
<input class="form-control" id="medicalHeight" placeholder="Enter height" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="medicalWeight">Weight
                                        </label>
<input class="form-control" id="medicalWeight" placeholder="Enter Weight" type="text"/>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-lg-12">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Bank Details </h6>
</div>
<div class="card-body p-3">
<div class="row gy-3">
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="bankAccountNumber">Bank
                                            Account Number </label>
<input class="form-control" id="bankAccountNumber" placeholder="Enter bank account number" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="bankName">Bank Name
                                        </label>
<input class="form-control" id="bankName" placeholder="Enter bank name" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="iFSCCode">IFSC Code
                                        </label>
<input class="form-control" id="iFSCCode" placeholder="Enter IFSC Code" type="text"/>
</div>
</div>
<div class="col-xxl-3 col-xl-4 col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="nationalIdentificationNumber">National
                                            Identification Number</label>
<input class="form-control" id="nationalIdentificationNumber" placeholder="Enter national identification number" type="text"/>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-xxl-6">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Previous School Details</h6>
</div>
<div class="card-body p-3">
<div class="row gy-3">
<div class="col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="schoolNamee">School
                                            Name </label>
<input class="form-control" id="schoolNamee" placeholder="Enter School Name" type="text"/>
</div>
</div>
<div class="col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="Address">Address
                                        </label>
<input class="form-control" id="Address" placeholder="Enter Address" type="text"/>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-xxl-6">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Address</h6>
</div>
<div class="card-body p-3">
<div class="row gy-3">
<div class="col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="currentAddress">Current
                                            Address </label>
<input class="form-control" id="currentAddress" placeholder="Enter Current Address" type="text"/>
</div>
</div>
<div class="col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="permanentAddress">Permanent
                                            Address </label>
<input class="form-control" id="permanentAddress" placeholder="Enter Permanent Address" type="text"/>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-xxl-6">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Hostel Details</h6>
</div>
<div class="card-body p-3">
<div class="row gy-3">
<div class="col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="hostel">Hostel
                                        </label>
<input class="form-control" id="hostel" placeholder="Enter Hostel" type="text"/>
</div>
</div>
<div class="col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="roomNo">Room No
                                        </label>
<input class="form-control" id="roomNo" placeholder="Enter Room No" type="text"/>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-xxl-6">
<div class="rounded-3 h-100 overflow-hidden card">
<div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
<h6 class="fw-semibold mb-0">Upload Documents</h6>
</div>
<div class="card-body p-3">
<div class="row gy-3">
<div class="col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="docName">Doc Name
                                        </label>
<input class="form-control" id="docName" placeholder="Enter Doc Name" type="text"/>
</div>
</div>
<div class="col-sm-6">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label">Guardian
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
<h6 class="fw-semibold mb-0">Student Details</h6>
</div>
<div class="card-body p-3">
<div class="row gy-3">
<div class="col-sm-12">
<div class="">
<label class="fw-semibold text-dark d-inline-block mb-2 form-label" for="moreDetails">Details
                                        </label>
<textarea class="form-control" id="moreDetails" placeholder="Enter details"></textarea>
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
                            Cancel
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
        $(toggleSelector).on('click', function() {
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
