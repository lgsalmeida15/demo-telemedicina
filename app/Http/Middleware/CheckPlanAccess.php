<?php

namespace App\Http\Middleware;

use Closure;

class CheckPlanAccess
{
    public function handle($request, Closure $next)
    {
        $user = auth('beneficiary')->user()
              ?? auth('dependent')->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $beneficiary = method_exists($user, 'beneficiary')
            ? $user->beneficiary
            : $user;

        $plan = $beneficiary->currentPlan();

        // ❌ Sem plano
        if (!$plan) {
            return redirect()->route('home');
        }

        // ❌ Plano expirado
        if ($plan->isExpired()) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
