<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dependent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DependentAuthController extends Controller
{
    /**
     * Exibe o formulário de login do dependente
     */
    public function showLoginForm()
    {
        return view('pages.dependents.auth.login');
    }

    /**
     * Login do dependente usando CPF + data de nascimento (DDMMAAAA)
     */
    public function login(Request $request)
    {
        // Validação dos campos
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Busca dependente pelo e-mail
        $dependent = Dependent::where('email', $request->email)->first();

        if (! $dependent) {
            return back()->withErrors([
                'email' => 'E-mail não encontrado.',
            ])->withInput();
        }

        // Verifica se a senha está correta
        if (! Hash::check($request->password, $dependent->password)) {
            return back()->withErrors([
                'password' => 'Senha incorreta. Verifique e tente novamente.',
            ])->withInput();
        }

        // Autentica no guard "dependent"
        Auth::guard('dependent')->login($dependent);

        $request->session()->regenerate();

        return redirect()->route('dependent.area.index');
    }
    /**
     * Logout do dependente
     */
    public function logout(Request $request)
    {
        Auth::guard('dependent')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dependent.login');
    }
}
