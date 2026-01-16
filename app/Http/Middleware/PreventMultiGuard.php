<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PreventMultiGuard
{
    public function handle($request, Closure $next)
    {
        // Admin + Beneficiário
        if (Auth::guard('web')->check() && Auth::guard('beneficiary')->check()) {
            Auth::guard('beneficiary')->logout();
        }

        if (Auth::guard('beneficiary')->check() && Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        // Admin + Dependente
        if (Auth::guard('web')->check() && Auth::guard('dependent')->check()) {
            Auth::guard('dependent')->logout();
        }

        if (Auth::guard('dependent')->check() && Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        // Beneficiário + Dependente
        if (Auth::guard('beneficiary')->check() && Auth::guard('dependent')->check()) {
            Auth::guard('dependent')->logout();
        }

        if (Auth::guard('dependent')->check() && Auth::guard('beneficiary')->check()) {
            Auth::guard('beneficiary')->logout();
        }

        return $next($request);
    }
}
