<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenancyFromSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($tenantId = session('selected_tenant_id')) {
            if ($tenant = Tenant::find($tenantId)) {
                tenancy()->initialize($tenant);
            }
        }else{
            tenancy()->initialize(Tenant::firstOrFail());
        }

        return $next($request);
    }
}
