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

        // ✅ REMOVIDO: Validações que bloqueavam acesso sem plano ou com plano expirado
        // Agora permite acesso mesmo sem validação do Asaas
        
        return $next($request);
    }
}
