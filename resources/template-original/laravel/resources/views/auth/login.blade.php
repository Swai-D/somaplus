@verbatim
<!DOCTYPE html>
<html lang="en">
<head>
<!-- Meta Tags -->
<meta charset="utf-8"/>
<meta content="IE=edge" http-equiv="X-UA-Compatible"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>



<meta content="index, follow" name="robots"/>
<title>Login | SomaPlus School Management</title>
<!-- Favicon -->
<link href="@endverbatim{{ asset('assets/img/favicon.svg') }}@verbatim" rel="shortcut icon" type="image/svg+xml"/>
<!-- Apple Touch Icon -->

<!-- Bootstrap CSS -->
<link href="@endverbatim{{ asset('assets/css/bootstrap.min.css') }}@verbatim" rel="stylesheet"/>
<!-- Fontawesome CSS -->
<link href="@endverbatim{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}@verbatim" rel="stylesheet"/>
<link href="@endverbatim{{ asset('assets/plugins/fontawesome/css/all.min.css') }}@verbatim" rel="stylesheet"/>
<!-- Tabler Icon CSS -->
<link href="@endverbatim{{ asset('assets/plugins/tabler-icons/tabler-icons.min.css') }}@verbatim" rel="stylesheet"/>
<!-- Main CSS -->
<link href="@endverbatim{{ asset('assets/css/style.css') }}@verbatim" rel="stylesheet"/>
<link href="@endverbatim{{ asset('assets/css/somaplus.css') }}@verbatim" rel="stylesheet"/></head>
<body class="account-page"><a aria-hidden="true" href="https://dreamspos.dreamstechnologies.com/cdn-cgi/content?id=CvjkLcNEbaRBmezljA_4p4L0fSFJQAk64afyZfexST0-1791050856.1784859-1.2.1.1-m3n67NAwcvZf48wT8omTXLALxiGt_Yj4vBpc66EbDncL1LLIp6_2GM.yPbjbAKPI" rel="nofollow noopener" style="display: none !important; visibility: hidden !important"></a>
<div id="global-loader">
<div class="whirly-loader"> </div>
</div>
<!-- Main Wrapper -->
<div class="main-wrapper">
<div class="account-content">
<div class="login-wrapper bg-img">
<div class="login-content authent-content">
<form action="index.html">
<div class="login-userset">
<div class="login-logo logo-normal">
<img alt="img" src="@endverbatim{{ asset('assets/img/logo.svg') }}@verbatim"/>
</div>
<a class="login-logo logo-white" href="@endverbatim{{ url('/') }}@verbatim">
<img alt="Img" src="@endverbatim{{ asset('assets/img/logo-white.svg') }}@verbatim"/>
</a>
<div class="login-userheading">
<h3>Sign In</h3>
<h4 class="fs-16">Access the SomaPlus panel using your email and password.</h4>
</div>
<div class="mb-3">
<label class="form-label">Email Address<span class="text-danger"> *</span></label>
<div class="input-group">
<input class="form-control border-end-0" placeholder="Enter your email" type="email" value=""/>
<span class="input-group-text border-start-0">
<i class="ti ti-mail"></i>
</span>
</div>
</div>
<div class="mb-3">
<label class="form-label">Password <span class="text-danger"> *</span></label>
<div class="pass-group">
<input class="pass-input form-control" placeholder="Enter your password" type="password"/>
<span class="ti toggle-password ti-eye-off text-gray-9"></span>
</div>
</div>
<div class="form-login authentication-check">
<div class="row">
<div class="col-12 d-flex align-items-center justify-content-between">
<div class="custom-control custom-checkbox">
<label class="checkboxs ps-4 mb-0 pb-0 line-height-1 fs-16 text-gray-6">
<input class="form-control" type="checkbox"/>
<span class="checkmarks"></span>Remember me
                                               </label>
</div>
<div class="text-end">
<a class="text-orange fs-16 fw-medium" href="@endverbatim{{ url('forgot-password') }}@verbatim">Forgot Password?</a>
</div>
</div>
</div>
</div>
<div class="form-login">
<button class="btn btn-primary w-100" type="submit">Sign In</button>
</div>
<div class="signinform">
<h4>New on our platform?<a class="hover-a" href="@endverbatim{{ url('register') }}@verbatim"> Create an account</a></h4>
</div>
<div class="form-setlogin or-text">
<h4>or login as</h4>
</div>
<div class="mt-2"><div class="d-flex flex-wrap justify-content-center"><a class="btn btn-sm btn-outline-white m-1" href="@endverbatim{{ url('/') }}@verbatim">Super Admin</a><a class="btn btn-sm btn-outline-white m-1" href="@endverbatim{{ url('/') }}@verbatim">Admin</a><a class="btn btn-sm btn-outline-white m-1" href="@endverbatim{{ url('index-2') }}@verbatim">Student</a><a class="btn btn-sm btn-outline-white m-1" href="@endverbatim{{ url('index-3') }}@verbatim">Teacher</a><a class="btn btn-sm btn-outline-white m-1" href="@endverbatim{{ url('index-4') }}@verbatim">Guardian</a><a class="btn btn-sm btn-outline-white m-1" href="@endverbatim{{ url('books-list') }}@verbatim">Librarian</a></div></div>
<div class="my-4 d-flex justify-content-center align-items-center copyright-text">
<p>Copyright © 2026 SomaPlus</p>
</div>
</div>
</form>
</div>
</div>
</div>
</div>
<!-- /Main Wrapper -->
<!-- Feather Icon JS -->
<script src="@endverbatim{{ asset('assets/js/feather.min.js') }}@verbatim"></script>
<!-- Bootstrap Core JS -->
<script src="@endverbatim{{ asset('assets/js/bootstrap.bundle.min.js') }}@verbatim"></script>
<!-- Custom JS -->
<script src="@endverbatim{{ asset('assets/js/script.js') }}@verbatim"></script>
</body>
</html>
@endverbatim
