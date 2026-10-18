@extends('layouts.app')
@section('title', 'Leave Request')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Leave Request</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a href="javascript:void(0);" title="Pdf"><img alt="pdf" src="@endverbatim{{ asset('assets/img/icons/pdf.svg') }}@verbatim"/></a></li><li class="me-2"><a href="javascript:void(0);" title="Excel"><img alt="excel" src="@endverbatim{{ asset('assets/img/icons/excel.svg') }}@verbatim"/></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul></div>

<div class="mt-4">
<div class="card">

<div class="card-header d-flex align-items-center justify-content-between flex-wrap row-gap-3"><div class="search-set"><div class="search-input"><span class="btn-searchset"><i class="ti ti-search fs-14 feather-search"></i></span></div></div><div class="d-flex align-items-center flex-wrap gap-2"></div></div><div class="card-body p-0"><div class="table-responsive"><table class="datatable table">
<thead class="thead-light">
<tr>
<th scope="col">S.L</th>
<th scope="col">Apply Date</th>
<th scope="col">Name</th>
<th scope="col">User type</th>
<th scope="col">Leave Type</th>
<th scope="col">Date</th>
<th scope="col">Duration</th>
<th scope="col">Status</th>
<th scope="col">Action</th>
</tr>
</thead>
<tbody>
<tr>
<td>01</td>
<td>07 May 2025</td>
<td>Jerome Bell</td>
<td>Teacher</td>
<td><i class="ti ti-building-hospital me-1"></i> Medical Leave</td>
<td>07 May 2025 - 08 May 2025</td>
<td>1</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Approved</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>02</td>
<td>10 May 2025</td>
<td>Jane Cooper</td>
<td>Student</td>
<td><i class="ti ti-sun me-1"></i> Casual Leave</td>
<td>10 May 2025 - 12 May 2025</td>
<td>2</td>
<td><span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pending</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>03</td>
<td>12 May 2025</td>
<td>Devon Lane</td>
<td>Teacher</td>
<td><i class="ti ti-clock me-1"></i> Half Day Leave</td>
<td>12 May 2025</td>
<td>0.5</td>
<td><span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Rejected</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>04</td>
<td>13 May 2025</td>
<td>Cody Fisher</td>
<td>Admin</td>
<td><i class="ti ti-plane-departure me-1"></i> Vacation Leave</td>
<td>13 May 2025 - 20 May 2025</td>
<td>7</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Approved</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>05</td>
<td>14 May 2025</td>
<td>Theresa Webb</td>
<td>Teacher</td>
<td><i class="ti ti-book me-1"></i> Study Leave</td>
<td>14 May 2025 - 16 May 2025</td>
<td>2</td>
<td><span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pending</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>06</td>
<td>15 May 2025</td>
<td>Darrell Steward</td>
<td>Student</td>
<td><i class="ti ti-cash me-1"></i> Paid Leave</td>
<td>15 May 2025 - 17 May 2025</td>
<td>2</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Approved</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>07</td>
<td>17 May 2025</td>
<td>Leslie Alexander</td>
<td>Teacher</td>
<td><i class="ti ti-alarm me-1"></i> Emergency Leave</td>
<td>17 May 2025 - 18 May 2025</td>
<td>1</td>
<td><span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Rejected</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>08</td>
<td>18 May 2025</td>
<td>Guy Hawkins</td>
<td>Admin</td>
<td><i class="ti ti-users me-1"></i> Maternity Leave</td>
<td>18 May 2025 - 28 May 2025</td>
<td>10</td>
<td><span class="badge badge-success d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Approved</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>09</td>
<td>19 May 2025</td>
<td>Brooklyn Simmons</td>
<td>Teacher</td>
<td><i class="ti ti-user-heart me-1"></i> Paternity Leave</td>
<td>19 May 2025 - 24 May 2025</td>
<td>5</td>
<td><span class="badge badge-warning d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Pending</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
<tr>
<td>10</td>
<td>20 May 2025</td>
<td>Kristin Watson</td>
<td>Student</td>
<td><i class="ti ti-circle-x me-1"></i> Unpaid Leave</td>
<td>20 May 2025 - 21 May 2025</td>
<td>1</td>
<td><span class="badge badge-danger d-inline-flex align-items-center badge-xs"><i class="ti ti-point-filled me-1"></i>Rejected</span>
</td>
<td class="action-table-data"><div class="edit-delete-action d-flex align-items-center justify-content-start"><a class="me-2 p-2 d-flex align-items-center border rounded" data-sp-row="" href="javascript:void(0);" title="View"><i class="ti ti-eye"></i></a><a class="me-2 p-2 d-flex align-items-center border rounded" data-bs-target="#delete-modal" data-bs-toggle="modal" data-sp-row="" href="javascript:void(0);" title="Delete"><i class="ti ti-trash"></i></a></div></td>
</tr>
</tbody>
</table></div></div></div>
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
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
