@extends('layouts.app')
@section('title', 'Edudash - School, College & LMS Admin Dashboard Template | PHP')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Event</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul><div class="page-btn"><button class="my-sidebar-btn btn btn-primary d-flex align-items-center gap-1" type="button">

<i class="ti ti-circle-plus me-1"></i>

                Add Event
            </button></div></div>

<div class="mt-4">
<div class="row gy-4">
<div class="col-xxl-3 col-lg-4">
<div class="card h-100 p-0">
<div class="card-body p-4">
<div class="mt-2">
<div class="d-flex align-items-center justify-content-between gap-4 pb-3 mb-3 border border-start-0 border-end-0 border-top-0">
<div class="">
<div class="d-flex align-items-center gap-2">
<span class="bg-warning rounded-circle fw-medium" style="width:12px;height:12px"></span>
<span class="text-gray-5">Today, 10:30 PM - 02:30 AM</span>
</div>
<span class="text-dark fw-semibold mt-4">Design
                                            Conference</span>
</div>
<div class="dropdown">
<a aria-expanded="false" class="action-icon btn btn-icon btn-sm btn-outline-white" data-bs-toggle="dropdown" href="javascript:void(0);">
<i class="ti ti-dots-vertical"></i>
</a>
<ul class="dropdown-menu p-2 border shadow">
<li>
<button class="dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" data-bs-target="#exampleModalView" data-bs-toggle="modal" type="button">
<i class="ti ti-eye"></i>
                                                    View
                                                </button>
</li>
<li>
<button class="edit-sidebar-btn dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" type="button">
<i class="ti ti-edit"></i>
                                                    Edit
                                                </button>
</li>
<li>
<button class="dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" data-bs-target="#exampleModalDelete" data-bs-toggle="modal" type="button">
<i class="ti ti-trash"></i>
                                                    Delete
                                                </button>
</li>
</ul>
</div>
</div>
<div class="d-flex align-items-center justify-content-between gap-4 pb-3 mb-3 border border-start-0 border-end-0 border-top-0">
<div class="">
<div class="d-flex align-items-center gap-2">
<span class="bg-success rounded-circle fw-medium" style="width:12px;height:12px"></span>
<span class="text-gray-5">Today, 10:30 PM - 02:30 AM</span>
</div>
<span class="text-dark fw-semibold mt-4">Weekend
                                            Festival</span>
</div>
<div class="dropdown">
<a aria-expanded="false" class="action-icon btn btn-icon btn-sm btn-outline-white" data-bs-toggle="dropdown" href="javascript:void(0);">
<i class="ti ti-dots-vertical"></i>
</a>
<ul class="dropdown-menu p-2 border shadow">
<li>
<button class="dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" data-bs-target="#exampleModalView" data-bs-toggle="modal" type="button">
<i class="ti ti-eye"></i>
                                                    View
                                                </button>
</li>
<li>
<button class="edit-sidebar-btn dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" type="button">
<i class="ti ti-edit"></i>
                                                    Edit
                                                </button>
</li>
<li>
<button class="dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" data-bs-target="#exampleModalDelete" data-bs-toggle="modal" type="button">
<i class="ti ti-trash"></i>
                                                    Delete
                                                </button>
</li>
</ul>
</div>
</div>
<div class="d-flex align-items-center justify-content-between gap-4 pb-3 mb-3 border border-start-0 border-end-0 border-top-0">
<div class="">
<div class="d-flex align-items-center gap-2">
<span class="bg-info rounded-circle fw-medium" style="width:12px;height:12px"></span>
<span class="text-gray-5">Today, 10:30 PM - 02:30 AM</span>
</div>
<span class="text-dark fw-semibold mt-4">Design
                                            Conference</span>
</div>
<div class="dropdown">
<a aria-expanded="false" class="action-icon btn btn-icon btn-sm btn-outline-white" data-bs-toggle="dropdown" href="javascript:void(0);">
<i class="ti ti-dots-vertical"></i>
</a>
<ul class="dropdown-menu p-2 border shadow">
<li>
<button class="dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" data-bs-target="#exampleModalView" data-bs-toggle="modal" type="button">
<i class="ti ti-eye"></i>
                                                    View
                                                </button>
</li>
<li>
<button class="edit-sidebar-btn dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" type="button">
<i class="ti ti-edit"></i>
                                                    Edit
                                                </button>
</li>
<li>
<button class="dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" data-bs-target="#exampleModalDelete" data-bs-toggle="modal" type="button">
<i class="ti ti-trash"></i>
                                                    Delete
                                                </button>
</li>
</ul>
</div>
</div>
<div class="d-flex align-items-center justify-content-between gap-4 pb-3 mb-3 border border-start-0 border-end-0 border-top-0">
<div class="">
<div class="d-flex align-items-center gap-2">
<span class="bg-warning rounded-circle fw-medium" style="width:12px;height:12px"></span>
<span class="text-gray-5">Today, 10:30 PM - 02:30 AM</span>
</div>
<span class="text-dark fw-semibold mt-4">Ultra Europe
                                            2019</span>
</div>
<div class="dropdown">
<a aria-expanded="false" class="action-icon btn btn-icon btn-sm btn-outline-white" data-bs-toggle="dropdown" href="javascript:void(0);">
<i class="ti ti-dots-vertical"></i>
</a>
<ul class="dropdown-menu p-2 border shadow">
<li>
<button class="dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" data-bs-target="#exampleModalView" data-bs-toggle="modal" type="button">
<i class="ti ti-eye"></i>
                                                    View
                                                </button>
</li>
<li>
<button class="edit-sidebar-btn dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" type="button">
<i class="ti ti-edit"></i>
                                                    Edit
                                                </button>
</li>
<li>
<button class="dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" data-bs-target="#exampleModalDelete" data-bs-toggle="modal" type="button">
<i class="ti ti-trash"></i>
                                                    Delete
                                                </button>
</li>
</ul>
</div>
</div>
<div class="d-flex align-items-center justify-content-between gap-4 pb-3 mb-3 border border-start-0 border-end-0 border-top-0">
<div class="">
<div class="d-flex align-items-center gap-2">
<span class="bg-warning rounded-circle fw-medium" style="width:12px;height:12px"></span>
<span class="text-gray-5">Today, 10:30 PM - 02:30 AM</span>
</div>
<span class="text-dark fw-semibold mt-4">Design
                                            Conference</span>
</div>
<div class="dropdown">
<a aria-expanded="false" class="action-icon btn btn-icon btn-sm btn-outline-white" data-bs-toggle="dropdown" href="javascript:void(0);">
<i class="ti ti-dots-vertical"></i>
</a>
<ul class="dropdown-menu p-2 border shadow">
<li>
<button class="dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" data-bs-target="#exampleModalView" data-bs-toggle="modal" type="button">
<i class="ti ti-eye"></i>
                                                    View
                                                </button>
</li>
<li>
<button class="edit-sidebar-btn dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" type="button">
<i class="ti ti-edit"></i>
                                                    Edit
                                                </button>
</li>
<li>
<button class="dropdown-item px-3 py-2 rounded text-gray-5 d-flex align-items-center gap-2" data-bs-target="#exampleModalDelete" data-bs-toggle="modal" type="button">
<i class="ti ti-trash"></i>
                                                    Delete
                                                </button>
</li>
</ul>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="col-xxl-9 col-lg-8">
<div class="card h-100 p-0">
<div class="card-body p-4">
<div id="wrap">
<div id="calendar"></div>
<div style="clear:both"></div>
</div>
</div>
</div>
</div>
</div>
</div>

@endverbatim
@endsection
@push('modals')
@verbatim
<div aria-hidden="true" class="modal fade" id="exampleModalView" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content rounded-3 card">
<div class="modal-header py-3 px-4 border border-top-0 border-start-0 border-end-0">
<span class="modal-title fs-5 d-block" id="exampleModalViewLabel">View Details</span>
<button aria-label="Close" class="btn-close" data-bs-dismiss="modal" type="button"></button>
</div>
<div class="modal-body p-4">
<div class="mb-2">
<span class="text-gray-5 fw-medium">Title</span>
<h6 class="text-dark fw-semibold mb-0 mt-4">Design Conference</h6>
</div>
<div class="mb-2">
<span class="text-gray-5 fw-medium">Start Date</span>
<h6 class="text-dark fw-semibold mb-0 mt-4">25 Jan 2024, 10:30AM</h6>
</div>
<div class="mb-2">
<span class="text-gray-5 fw-medium">End Date</span>
<h6 class="text-dark fw-semibold mb-0 mt-4">25 Jan 2024, 2:30AM</h6>
</div>
<div class="mb-2">
<span class="text-gray-5 fw-medium">Description</span>
<h6 class="text-dark fw-semibold mb-0 mt-4">N/A</h6>
</div>
<div class="mb-2">
<span class="text-gray-5 fw-medium">Label</span>
<h6 class="text-dark fw-semibold mb-0 mt-4 d-flex align-items-center gap-2">
<span class="bg-success rounded-circle" style="width:8px;height:8px"></span>
                        Business
                    </h6>
</div>
</div>
</div>
</div>
</div><div aria-hidden="true" class="modal fade" id="exampleModalDelete" tabindex="-1">
<div class="modal-dialog modal-sm modal-dialog-centered" style="max-width:340px">
<div class="modal-content rounded-3 card">
<div class="modal-body pt-4 px-4 pb-4 text-center">
<span class="mb-3 fs-1 text-danger">
<i class="ti ti-trash"></i>
</span>
<h6 class="fw-semibold text-dark mb-0">Are your sure you want to Suspend this teacher
                </h6>
<div class="d-flex align-items-center justify-content-center gap-3 mt-4">
<button class="flex-grow-1 border text-danger px-4 py-2 rounded" data-bs-dismiss="modal" type="reset">
                        Cancel
                    </button>
<button class="flex-grow-1 btn btn-primary border px-3 py-2 rounded" type="button">
                        Yes, Suspend
                    </button>
</div>
</div>
</div>
</div>
</div>
@endverbatim
@endpush
@push('vendor')
@verbatim
<script src="@endverbatim{{ asset('assets/js/jquery-3.7.1.min.js') }}@verbatim"></script>
<script src="@endverbatim{{ asset('assets/plugins/fullcalendar/index.global.min.js') }}@verbatim"></script>

@endverbatim
@endpush
@push('scripts')
@verbatim
<script>

    // Flat pickr or date picker js 
    function getDatePicker(receiveID) {
        flatpickr(receiveID, {
            enableTime: true,
            dateFormat: "d/m/Y H:i",
        });
    }
    getDatePicker('#startDate');
    getDatePicker('#endDate');

    getDatePicker('#editstartDate');
    getDatePicker('#editendDate');


    // Sidebar js start
    $('.my-sidebar-btn').on('click', function () {
        $('.my-sidebar').addClass('active');
        $('.overlay').addClass('active');
    });
    $('.close-my-sidebar, .overlay').on('click', function () {
        $('.my-sidebar').removeClass('active');
        $('.overlay').removeClass('active');
    });


    $('.edit-sidebar-btn').on('click', function () {
        $('.edit-sidebar').addClass('active');
        $('.overlay').addClass('active');
    });
    $('.close-edit-sidebar, .overlay').on('click', function () {
        $('.edit-sidebar').removeClass('active');
        $('.overlay').removeClass('active');
    });
    // Sidebar js end


</script>
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script><script>
document.addEventListener('DOMContentLoaded',function(){
  var el=document.getElementById('calendar'); if(!el||!window.FullCalendar) return;
  var d=new Date(), y=d.getFullYear(), m=String(d.getMonth()+1).padStart(2,'0');
  new FullCalendar.Calendar(el,{initialView:'dayGridMonth',height:'auto',
    headerToolbar:{left:'prev,next today',center:'title',right:'dayGridMonth,timeGridWeek,timeGridDay'},
    events:[{title:'Design Conference',start:y+'-'+m+'-05',color:'#FE9F43'},{title:'Weekend Festival',start:y+'-'+m+'-12',end:y+'-'+m+'-14',color:'#092C4C'},
            {title:'Ultra Europe 2019',start:y+'-'+m+'-20',color:'#0E9384'},{title:'Parents Meeting',start:y+'-'+m+'-26T10:00:00',color:'#3538CD'}]
  }).render();
});</script>

@endverbatim
@endpush
