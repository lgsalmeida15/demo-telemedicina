<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        // 🔍 DEBUG: Log para verificar o que está acontecendo
        if ($request->is('beneficiary-area*')) {
            \Log::info('Middleware Authenticate - Verificando beneficiário', [
                'url' => $request->fullUrl(),
                'guards' => $guards,
                'beneficiary_check' => Auth::guard('beneficiary')->check(),
                'beneficiary_id' => Auth::guard('beneficiary')->id(),
                'session_id' => $request->session()->getId(),
            ]);
        }

        return parent::handle($request, $next, ...$guards);
    }

    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {

            // 🔹 Rotas da Área do Beneficiário
            if ($request->is('beneficiary-area*') || $request->is('beneficiario/*')) {
                \Log::warning('Redirecionando beneficiário não autenticado para login', [
                    'url' => $request->fullUrl(),
                    'session_id' => $request->session()->getId()
                ]);
                return route('beneficiary.login');
            }

            // 🔹 Rotas da Área do Dependente
            if ($request->is('dependent-area*') || $request->is('dependente/*')) {
                return route('dependent.login');
            }

            // 🔹 Rotas de ADMIN
            if ($request->is('admin*')) {
                return route('login');
            }

            // 🔹 Outras rotas
            return route('beneficiary.login');
        }
    }
}
