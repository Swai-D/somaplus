@php($letterhead = \App\Models\SchoolProfile::current())
<div class="text-center mb-3 school-letterhead">
    <h4 class="mb-1">{{ app(\App\Tenancy\TenantContext::class)->current()?->name }}</h4>
    @foreach ($letterhead->lines() as $line)
        <div class="text-muted fs-13">{{ $line }}</div>
    @endforeach
</div>
