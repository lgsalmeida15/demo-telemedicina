<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {

            // 🔹 Rotas da Área do Beneficiário
            if ($request->is('beneficiary-area*') || $request->is('beneficiario/*')) {
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
