@extends('layouts.app')
@section('title', 'Message')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Message</h4><h6>Dashboard</h6></div></div><ul class="table-top-head"><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul></div>

<div class="mt-4">
<div class="chat-wrapper">
<div class="chat-sidebar card">
<div class="px-4 py-3">
<form class="d-block">
<input class="bg-transparent w-100" name="search" placeholder="Search" type="text"/>
<i class="ti ti-search"></i>
</form>
</div>
<div class="chat-all-list">
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/2.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Kathryn Murphy</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/3.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">James Michael</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/4.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Russell Lucas</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/5.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Caleb Bradley</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/6.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Bobby Roy</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/7.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Vincent Liam</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/8.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Randy Mason</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/9.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Albert Wayne</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/10.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Elijah Willie</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/2.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Kathryn Murphy</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/3.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">James Michael</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/4.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Russell Lucas</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/5.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Caleb Bradley</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/6.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Bobby Roy</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/7.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Vincent Liam</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/8.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Randy Mason</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/9.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Albert Wayne</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/10.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-1">Elijah Willie</h6>
<p class="mb-0">hey! there i'm...</p>
</div>
<div class="action text-end">
<p class="mb-0 lh-1">12:30 PM</p>
<span class="rounded-circle text-white d-inline-flex align-items-center justify-content-center" style="width:16px;height:16px">8</span>
</div>
</div><!-- chat-sidebar-single end -->
</div>
</div>
<div class="chat-main card">
<div class="chat-sidebar-single active">
<div class="img">
<img alt="image" src="@endverbatim{{ asset('assets/img/edu/chat/11.png') }}@verbatim"/>
</div>
<div class="info">
<h6 class="mb-0">Kathryn Murphy</h6>
<p class="mb-0">Available</p>
</div>
<div class="action d-inline-flex align-items-center gap-3">
<div class="dropdown table-action"><a aria-expanded="false" class="btn btn-icon btn-sm" data-bs-toggle="dropdown" href="javascript:void(0);"><i class="ti ti-dots-vertical"></i></a><ul class="dropdown-menu dropdown-menu-end p-2"><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Clear All</a></li><li><a class="dropdown-item d-flex align-items-center rounded-1" href="javascript:void(0);">Block</a></li></ul></div>
</div>
</div><!-- chat-sidebar-single end -->
<div class="chat-message-list">
<div class="chat-single-message left">
<img alt="image" class="avatar-lg object-fit-cover rounded-circle" src="@endverbatim{{ asset('assets/img/edu/chat/11.png') }}@verbatim"/>
<div class="chat-message-content">
<p class="mb-3">It is a long established fact that a reader will be distracted by the
                                    readable
                                    content
                                    of a page when looking at its layout. The point of using Lorem Ipsum is that it has
                                    a
                                    more-or-less
                                    normal distribution of letters.</p>
<p class="chat-time mb-0">
<span>6.30 pm</span>
</p>
</div>
</div><!-- chat-single-message end -->
<div class="chat-single-message right">
<div class="chat-message-content">
<p class="mb-3">It is a long established fact that a reader will be distracted by the
                                    readable
                                    content
                                    of a page when looking at its layout. The point of using Lorem Ipsum is that it has
                                    a
                                    more-or-less
                                    normal distribution of letters.</p>
<p class="chat-time mb-0">
<span>6.30 pm</span>
</p>
</div>
</div><!-- chat-single-message end -->
<div class="chat-single-message left">
<img alt="image" class="avatar-lg object-fit-cover rounded-circle" src="@endverbatim{{ asset('assets/img/edu/chat/11.png') }}@verbatim"/>
<div class="chat-message-content">
<p class="mb-3">The point of using Lorem Ipsum is that it has a more-or-less normal
                                    distribution of
                                    letters, as opposed to using 'Content here, content here', making it look like
                                    readable English.
                                    Many
                                    desktop publishing packages and web page editors now use Lorem Ipsum as their
                                    default.Contrary
                                    to
                                    popular belief, Lorem Ipsum is not simply random text is the model text for your
                                    company.</p>
<p class="chat-time mb-0">
<span>6.30 pm</span>
</p>
</div>
</div><!-- chat-single-message end -->
</div>
<form class="chat-message-box">
<input name="chatMessage" placeholder="Write message" type="text"/>
<div class="chat-message-box-action">
<button type="button">
<i class="ti ti-link"></i>
</button>
<button type="button">
<i class="ti ti-photo"></i>
</button>
<button class="btn btn-sm btn-primary rounded d-inline-flex align-items-center gap-1" type="submit">
                                Send
                                <i class="ti ti-send me-1"></i>
</button>
</div>
</form>
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
