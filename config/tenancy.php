<?php

/*
|--------------------------------------------------------------------------
| Tenancy
|--------------------------------------------------------------------------
|
| One school is one tenant. The tenant is resolved from the request host:
|
|   - a host listed in `central_domains` has no tenant (super-admin area);
|   - a tenant's own `domain` matches the host exactly;
|   - otherwise "<slug>.<base_domain>" resolves the tenant with that slug.
|
| Any other host is a 404. There is deliberately no header or session
| override for choosing a tenant.
|
*/

return [

    'central_domains' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('TENANCY_CENTRAL_DOMAINS', 'localhost,127.0.0.1')),
    ))),

    'base_domain' => env('TENANCY_BASE_DOMAIN', 'localhost'),

];
