<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPlanAccess
{
    public function handle($request, Closure $next)
    {
        $user = Auth::guard('beneficiary')->user() ?? Auth::guard('dependent')->user();
        
        if (!$user) {
            return redirect()->route('beneficiary.login');
        }
        
        // Se for um Dependent, pegar o beneficiário titular
        if ($user instanceof \App\Models\Dependent) {
            $beneficiary = $user->beneficiary;
        } else {
            $beneficiary = $user;
        }
        
        // ✅ BYPASS PARA BENEFICIÁRIOS DEMO (com verificação de segurança)
        if (method_exists($beneficiary, 'isDemo') && $beneficiary->isDemo()) {
            // Verificar se demo expirou
            if (method_exists($beneficiary, 'isDemoExpired') && $beneficiary->isDemoExpired()) {
                return redirect()
                    ->route('beneficiary.login')
                    ->withErrors('Seu período de demonstração expirou. Entre em contato para ativar seu plano.');
            }
            
            // Demo válido, permitir acesso
            return $next($request);
        }
        
        // Validação normal para beneficiários não-demo
        try {
            $currentPlan = $beneficiary->currentPlan();
            
            if (!$currentPlan) {
                return redirect()
                    ->route('beneficiary.area.index')
                    ->withErrors('Você não possui um plano ativo.');
            }
            
            // Verificar se plano está expirado (com verificação de segurança)
            if (method_exists($currentPlan, 'isExpired') && $currentPlan->isExpired()) {
                return redirect()
                    ->route('beneficiary.area.index')
                    ->withErrors('Seu plano está expirado.');
            }
            
            // Verificar se plano está ativo (com verificação de segurança)
            if (method_exists($currentPlan, 'isActive') && !$currentPlan->isActive()) {
                return redirect()
                    ->route('beneficiary.area.index')
                    ->withErrors('Você não possui um plano ativo.');
            }
        } catch (\Exception $e) {
            // Em caso de erro, logar e permitir acesso (para não bloquear usuários)
            \Log::error('Erro no CheckPlanAccess: ' . $e->getMessage());
        }
        
        return $next($request);
    }
}
