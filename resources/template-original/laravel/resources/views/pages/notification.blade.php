@extends('layouts.app')
@section('title', 'Notification')
@section('content')
@verbatim
<div class="page-header"><div class="add-item d-flex"><div class="page-title"><h4>Notification</h4><h6>Dashboard / Settings</h6></div></div><ul class="table-top-head"><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" href="javascript:location.reload()" title="Refresh"><i class="ti ti-refresh"></i></a></li><li class="me-2"><a data-bs-placement="top" data-bs-toggle="tooltip" id="collapse-header" title="Collapse"><i class="ti ti-chevron-up"></i></a></li></ul></div>

<div class="card h-100 p-0 rounded-3 overflow-hidden">
<div class="card-body p-5">
<form action="notification.html#">
<div class="row">
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="firebaseSecretKey">Firebase secret
                                    key</label>
<input class="form-control rounded" id="firebaseSecretKey" placeholder="Firebase secret key" type="text" value="AAAAxGHw9lE:APA91bHKj6OsrD6EhnG5p26oTiQkXvOxTZwZEfVuuuipyUSNM-a8NB_CugVwfvvaosOvWgFAhQJOLMvxtv7e3Sw8DYpaWKwJIN3kjyIPoNRAe541sBz3x7E6sXZkA-ebueqnQiqNtbdP"/>
</div>
</div>
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="firebasePublicVapidKey">Firebase public vapid
                                    key (key pair)</label>
<input class="form-control rounded" id="firebasePublicVapidKey" placeholder="Firebase public vapid key (key pair)" type="text" value="BKAvKJbnB3QATdp8n1aUo_uhoNK3exVKLVzy7MP8VKydjjzthdlAWdlku6LQISxm4zA7dWoRACI9AHymf4V64kA"/>
</div>
</div>
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="firebaseAPIKey">Firebase API
                                    Key</label>
<input class="form-control rounded" id="firebaseAPIKey" placeholder="Firebase  API Key" type="text" value="AIzaSyDg1xBSwmHKV0usIKxTFL5a6fFTb4s3XVM"/>
</div>
</div>
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="firebaseAuthDomain">Firebase AUTH
                                    Domain</label>
<input class="form-control rounded" id="firebaseAuthDomain" placeholder="Firebase  AUTH Domain" type="text" value="wowdash.firebaseapp.com"/>
</div>
</div>
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="firebaseProjectID">Firebase Project
                                    ID</label>
<input class="form-control rounded" id="firebaseProjectID" placeholder="Firebase Project ID" type="text" value="wowdash.com"/>
</div>
</div>
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="firebaseStorageBucket">Firebase Storage
                                    Bucket</label>
<input class="form-control rounded" id="firebaseStorageBucket" placeholder="Firebase Storage Bucket" type="text" value="wowdash.appsport.com"/>
</div>
</div>
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="firebaseMessageSenderID">Firebase Message
                                    Sender ID</label>
<input class="form-control rounded" id="firebaseMessageSenderID" placeholder="Firebase  Message Sender ID" type="text" value="52362145"/>
</div>
</div>
<div class="col-sm-6">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="firebaseAppID">Firebase App
                                    ID</label>
<input class="form-control rounded" id="firebaseAppID" placeholder="Firebase  App ID" type="text" value="1:843456771665:web:ac1e3115e9e17ee1582a70"/>
</div>
</div>
<div class="col-sm-12">
<div class="mb-3">
<label class="form-label fw-semibold text-dark mb-2" for="firebaseMeasurmentID">Firebase Measurement
                                    ID</label>
<input class="form-control rounded" id="firebaseMeasurmentID" placeholder="Firebase  Measurement ID" type="text" value="G-GSJPS921XW"/>
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
@push('scripts')
@verbatim
<script>document.querySelectorAll("#current-year").forEach(function(e){e.textContent=new Date().getFullYear()});</script>
@endverbatim
@endpush
