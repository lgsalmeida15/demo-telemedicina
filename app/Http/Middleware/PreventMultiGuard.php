<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class PreventMultiGuard
{
    public function handle($request, Closure $next)
    {
        // 🔍 DEBUG: Log para verificar se está interferindo
        if ($request->is('beneficiary-area*')) {
            \Log::info('PreventMultiGuard - Verificando guards', [
                'web_check' => Auth::guard('web')->check(),
                'beneficiary_check' => Auth::guard('beneficiary')->check(),
                'dependent_check' => Auth::guard('dependent')->check(),
            ]);
        }

        // Admin + Beneficiário
        if (Auth::guard('web')->check() && Auth::guard('beneficiary')->check()) {
            \Log::warning('PreventMultiGuard: Logout beneficiary (web também autenticado)');
            Auth::guard('beneficiary')->logout();
        }

        if (Auth::guard('beneficiary')->check() && Auth::guard('web')->check()) {
            \Log::warning('PreventMultiGuard: Logout web (beneficiary também autenticado)');
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
