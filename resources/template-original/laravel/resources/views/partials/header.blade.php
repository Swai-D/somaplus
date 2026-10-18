@verbatim
<div class="header">
<div class="main-header">
<!-- Logo -->
<div class="header-left active">
<a class="logo logo-normal" href="@endverbatim{{ url('/') }}@verbatim">
<img alt="Img" src="@endverbatim{{ asset('assets/img/logo.svg') }}@verbatim"/>
</a>
<a class="logo logo-white" href="@endverbatim{{ url('/') }}@verbatim">
<img alt="Img" src="@endverbatim{{ asset('assets/img/logo-white.svg') }}@verbatim"/>
</a>
<a class="logo-small" href="@endverbatim{{ url('/') }}@verbatim">
<img alt="Img" src="@endverbatim{{ asset('assets/img/logo-small.svg') }}@verbatim"/>
</a>
<a class="logo-small-white" href="@endverbatim{{ url('/') }}@verbatim">
<img alt="Img" src="@endverbatim{{ asset('assets/img/logo-small-white.svg') }}@verbatim"/>
</a>
</div>
<!-- /Logo -->
<a class="mobile_btn" href="#sidebar" id="mobile_btn">
<span class="bar-icon">
<span></span>
<span></span>
<span></span>
</span>
</a>
<!-- Header Menu -->
<ul class="nav user-menu">
<!-- Search -->
<li class="nav-item nav-searchinputs">
<div class="top-nav-search">
<a class="responsive-search" href="javascript:void(0);">
<i class="fa fa-search"></i>
</a>
<form action="#" class="dropdown">
<div class="searchinputs input-group dropdown-toggle" data-bs-auto-close="outside" data-bs-toggle="dropdown" id="dropdownMenuClickable">
<input placeholder="Search" type="text"/>
<div class="search-addon">
<span><i class="ti ti-search"></i></span>
</div>
<span class="input-group-text">
<kbd class="d-flex align-items-center"><img alt="img" class="me-1" src="@endverbatim{{ asset('assets/img/icons/command.svg') }}@verbatim"/>K</kbd>
</span>
</div>
<div aria-labelledby="dropdownMenuClickable" class="dropdown-menu search-dropdown">
<div class="search-info">
<h6><span><i class="feather-16" data-feather="search"></i></span>Recent Searches
										</h6>
<ul class="search-tags"><li><a href="@endverbatim{{ url('student-list') }}@verbatim">Students</a></li><li><a href="@endverbatim{{ url('teacher-list') }}@verbatim">Teachers</a></li><li><a href="@endverbatim{{ url('fees-collect') }}@verbatim">Fees</a></li></ul>
</div>
<div class="search-info">
<h6><span><i class="feather-16" data-feather="help-circle"></i></span>Help</h6>


</div>

</div>
</form>
</div>
</li>
<!-- /Search -->
<!-- Select Store -->
<!-- /Select Store -->
<!-- Flag -->
<li class="nav-item dropdown has-arrow flag-nav nav-item-box">
<a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);" role="button">
<img alt="Language" class="img-fluid" src="@endverbatim{{ asset('assets/img/flags/us-flag.svg') }}@verbatim"/>
</a>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" data-lang="en" href="javascript:void(0);">
<img alt="Img" height="16" src="@endverbatim{{ asset('assets/img/flags/english.svg') }}@verbatim"/>English
							</a>
<a class="dropdown-item" data-lang="sw" href="javascript:void(0);">
<img alt="Img" height="16" src="@endverbatim{{ asset('assets/img/flags/tz.svg') }}@verbatim"/>Kiswahili
							</a>
</div>
</li>
<!-- /Flag -->
<li class="nav-item nav-item-box">
<a href="javascript:void(0);" id="btnFullscreen">
<i class="ti ti-maximize"></i>
</a>
</li>
<li class="nav-item nav-item-box">
<a href="@endverbatim{{ url('email') }}@verbatim">
<i class="ti ti-mail"></i>
<span class="badge rounded-pill">1</span>
</a>
</li>
<!-- Notifications -->
<li class="nav-item dropdown nav-item-box">
<a class="dropdown-toggle nav-link" data-bs-toggle="dropdown" href="javascript:void(0);">
<i class="ti ti-bell"></i>
</a>
<div class="dropdown-menu notifications">
<div class="topnav-dropdown-header">
<h5 class="notification-title">Notifications</h5>
<a class="clear-noti" href="javascript:void(0)">Mark all as read</a>
</div>
<div class="noti-content">
<ul class="notification-list"><li class="notification-message"><a class="recent-msg" href="@endverbatim{{ url('notice-board') }}@verbatim"><div class="media d-flex"><span class="avatar flex-shrink-0"><img alt="" src="@endverbatim{{ asset('assets/img/profiles/avatar-03.jpg') }}@verbatim"/></span><div class="flex-grow-1"><p class="noti-details"><span class="noti-title">Amina Juma</span> applied for sick leave</p><p class="noti-time">10 mins ago</p></div></div></a></li><li class="notification-message"><a class="recent-msg" href="@endverbatim{{ url('notice-board') }}@verbatim"><div class="media d-flex"><span class="avatar flex-shrink-0"><img alt="" src="@endverbatim{{ asset('assets/img/profiles/avatar-17.jpg') }}@verbatim"/></span><div class="flex-grow-1"><p class="noti-details">Fee payment received from <span class="noti-title">Class 7 (A)</span></p><p class="noti-time">05 mins ago</p></div></div></a></li><li class="notification-message"><a class="recent-msg" href="@endverbatim{{ url('notice-board') }}@verbatim"><div class="media d-flex"><span class="avatar flex-shrink-0"><img alt="" src="@endverbatim{{ asset('assets/img/profiles/avatar-02.jpg') }}@verbatim"/></span><div class="flex-grow-1"><p class="noti-details">New notice posted: <span class="noti-title">Mid-term exam schedule</span></p><p class="noti-time">1 hour ago</p></div></div></a></li></ul>
</div>
<div class="topnav-dropdown-footer d-flex align-items-center gap-3">
<a class="btn btn-secondary btn-md w-100" href="@endverbatim{{ url('admin-dashboard') }}@verbatim">Cancel</a>
<a class="btn btn-primary btn-md w-100" href="@endverbatim{{ url('notification') }}@verbatim">View all</a>
</div>
</div>
</li>
<!-- /Notifications -->
<li class="nav-item nav-item-box">
<a href="@endverbatim{{ url('general') }}@verbatim"><i class="ti ti-settings"></i></a>
</li>
<li class="nav-item dropdown has-arrow main-drop profile-nav">
<a class="nav-link userset" data-bs-toggle="dropdown" href="javascript:void(0);">
<span class="user-info p-0">
<span class="user-letter">
<img alt="Img" class="img-fluid" src="@endverbatim{{ asset('assets/img/profiles/avatar-01.jpg') }}@verbatim"/>
</span>
</span>
</a>
<div class="dropdown-menu menu-drop-user">
<div class="profileset d-flex align-items-center">
<span class="user-img me-2">
<img alt="Img" src="@endverbatim{{ asset('assets/img/profiles/avatar-01.jpg') }}@verbatim"/>
</span>
<div>
<h6 class="fw-medium">School Admin</h6>
<p>Administrator</p>
</div>
</div>
<a class="dropdown-item" href="@endverbatim{{ url('profile') }}@verbatim"><i class="ti ti-user-circle me-2"></i>My Profile</a>
<a class="dropdown-item" href="@endverbatim{{ url('general') }}@verbatim"><i class="ti ti-file-text me-2"></i>Reports</a>
<a class="dropdown-item" href="@endverbatim{{ url('general') }}@verbatim"><i class="ti ti-settings-2 me-2"></i>Settings</a>
<hr class="my-2"/>
<a class="dropdown-item logout" href="@endverbatim{{ url('login') }}@verbatim"><i class="ti ti-logout me-2"></i>Logout</a>
</div>
</li>
</ul>
<!-- /Header Menu -->
<!-- Mobile Menu -->
<div class="dropdown mobile-user-menu">
<a aria-expanded="false" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);"><i class="fa fa-ellipsis-v"></i></a>
<div class="dropdown-menu dropdown-menu-right">
<a class="dropdown-item" href="@endverbatim{{ url('profile') }}@verbatim">My Profile</a>
<a class="dropdown-item" href="@endverbatim{{ url('general') }}@verbatim">Settings</a>
<a class="dropdown-item" href="@endverbatim{{ url('login') }}@verbatim">Logout</a>
</div>
</div>
<!-- /Mobile Menu -->
</div>
</div>
@endverbatim