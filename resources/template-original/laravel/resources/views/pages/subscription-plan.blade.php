@extends('layouts.app')
@section('title', 'Subscription Plan')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Subscription Plan</h4><h6>Dashboard / Subscription Plan</h6></div></div>
<ul class="table-top-head"><li class="me-2"><a data-bs-toggle="tooltip" title="Refresh" href="javascript:location.reload()"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-toggle="tooltip" title="Collapse" id="collapse-header"><i class="ti ti-chevron-up"></i></a></li></ul></div><div class="card"><div class="card-body p-4 p-lg-5">
<div class="text-center mb-5"><h3 class="mb-1">Simple, Transparent Pricing</h3><p class="text-gray-5 mb-4">Pick the plan that fits your school. Switch to annual billing for a yearly price.</p>
<div class="d-inline-flex align-items-center gap-3 sp-billing"><span class="fw-medium" id="lblMonthly">Monthly</span>
<div class="form-check form-switch m-0 p-0"><input class="form-check-input choose-plan-input m-0" type="checkbox" role="switch" id="billingSwitch"></div>
<span class="fw-medium text-gray-5" id="lblAnnual">Annually</span></div></div>
<div class="row g-4 justify-content-center align-items-stretch"><div class="col-xl-4 col-md-6"><div class="sp-plan">
<div class="d-flex align-items-center gap-3 mb-3"><span class="sp-plan-icon"><img src="@endverbatim{{ asset('assets/img/edu/icons/price-icon1.png') }}@verbatim" alt=""></span>
<div><span class="sp-plan-tag">For individuals</span><h5 class="mb-0 sp-plan-name">Basic</h5></div></div>
<p class="sp-plan-desc mb-4">Lorem ipsum dolor sit amet doloroli sitiol conse ctetur adipiscing elit.</p>
<div class="sp-price mb-4"><span class="sp-cur">TSh</span> <span class="price-range" data-base="99">99</span> <span class="sp-per">/month</span></div>
<h6 class="fs-14 fw-semibold mb-3">What's included</h6><ul><li><span class="sp-check"><i class="ti ti-check"></i></span><span>All analytics features</span></li><li><span class="sp-check"><i class="ti ti-check"></i></span><span>Up to 250,000 tracked visits</span></li><li><span class="sp-check"><i class="ti ti-check"></i></span><span>Normal support</span></li><li><span class="sp-check"><i class="ti ti-check"></i></span><span>Up to 3 team members</span></li></ul><button type="button" class="btn btn-primary w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#planModal" data-plan="Basic">Get started</button></div></div><div class="col-xl-4 col-md-6"><div class="sp-plan featured"><span class="sp-ribbon">Popular</span>
<div class="d-flex align-items-center gap-3 mb-3"><span class="sp-plan-icon"><img src="@endverbatim{{ asset('assets/img/edu/icons/price-icon2.png') }}@verbatim" alt=""></span>
<div><span class="sp-plan-tag">For startups</span><h5 class="mb-0 sp-plan-name">Pro</h5></div></div>
<p class="sp-plan-desc mb-4">Lorem ipsum dolor sit amet doloroli sitiol conse ctetur adipiscing elit.</p>
<div class="sp-price mb-4"><span class="sp-cur">TSh</span> <span class="price-range" data-base="199">199</span> <span class="sp-per">/month</span></div>
<h6 class="fs-14 fw-semibold mb-3">What's included</h6><ul><li><span class="sp-check"><i class="ti ti-check"></i></span><span>All analytics features</span></li><li><span class="sp-check"><i class="ti ti-check"></i></span><span>Up to 250,000 tracked visits</span></li><li><span class="sp-check"><i class="ti ti-check"></i></span><span>Normal support</span></li><li><span class="sp-check"><i class="ti ti-check"></i></span><span>Up to 3 team members</span></li></ul><button type="button" class="btn btn-light text-primary w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#planModal" data-plan="Pro">Get started</button></div></div><div class="col-xl-4 col-md-6"><div class="sp-plan">
<div class="d-flex align-items-center gap-3 mb-3"><span class="sp-plan-icon"><img src="@endverbatim{{ asset('assets/img/edu/icons/price-icon3.png') }}@verbatim" alt=""></span>
<div><span class="sp-plan-tag">For big companies</span><h5 class="mb-0 sp-plan-name">Enterprise</h5></div></div>
<p class="sp-plan-desc mb-4">Lorem ipsum dolor sit amet doloroli sitiol conse ctetur adipiscing elit.</p>
<div class="sp-price mb-4"><span class="sp-cur">TSh</span> <span class="price-range" data-base="399">399</span> <span class="sp-per">/month</span></div>
<h6 class="fs-14 fw-semibold mb-3">What's included</h6><ul><li><span class="sp-check"><i class="ti ti-check"></i></span><span>All analytics features</span></li><li><span class="sp-check"><i class="ti ti-check"></i></span><span>Up to 250,000 tracked visits</span></li><li><span class="sp-check"><i class="ti ti-check"></i></span><span>Normal support</span></li><li><span class="sp-check"><i class="ti ti-check"></i></span><span>Up to 3 team members</span></li></ul><button type="button" class="btn btn-primary w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#planModal" data-plan="Enterprise">Get started</button></div></div></div></div></div>
<div class="modal fade" id="planModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Confirm subscription</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
<div class="modal-body"><p class="mb-1">You are about to subscribe to the <b id="planName">Basic</b> plan.</p><p class="text-gray-5 mb-0" id="planPrice"></p></div>
<div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Confirm</button></div></div></div></div>
@endverbatim
@endsection
@push('scripts')
@verbatim
<script>

(function(){
  var sw=document.getElementById('billingSwitch'), prices=document.querySelectorAll('.price-range'), per=document.querySelectorAll('.sp-per');
  var lm=document.getElementById('lblMonthly'), la=document.getElementById('lblAnnual');
  function paint(){
    var yr=sw.checked;
    prices.forEach(function(p){p.textContent=(Number(p.dataset.base)*(yr?10:1)).toLocaleString('en-US');});
    per.forEach(function(e){e.textContent=yr?'/year':'/month';});
    lm.classList.toggle('text-gray-5',yr); la.classList.toggle('text-gray-5',!yr);
  }
  sw.addEventListener('change',paint); paint();
  var m=document.getElementById('planModal');
  m.addEventListener('show.bs.modal',function(e){
    var card=e.relatedTarget.closest('.sp-plan');
    document.getElementById('planName').textContent=e.relatedTarget.dataset.plan;
    document.getElementById('planPrice').textContent='TSh '+card.querySelector('.price-range').textContent+' '+card.querySelector('.sp-per').textContent;
  });
})();
</script>
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
