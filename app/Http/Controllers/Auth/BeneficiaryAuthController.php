<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Hash;

class BeneficiaryAuthController extends Controller
{
    /**
     * Redireciona para a view de login
     */
    public function showLoginForm()
    {
        return view('pages.beneficiaries.auth.login');
    }

    /**
     * loga o beneficiario com email e senha (definidos no form de criação de beneficiario)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 🔍 DEBUG: Verifica se o beneficiário existe
        $beneficiary = Beneficiary::where('email', $credentials['email'])->first();
        
        if (!$beneficiary) {
            \Log::warning('Tentativa de login com email não encontrado: ' . $credentials['email']);
            return back()->withErrors([
                'email' => 'As credenciais informadas estão incorretas.',
            ]);
        }

        // 🔍 DEBUG: Verifica se a senha está correta
        if (!Hash::check($credentials['password'], $beneficiary->password)) {
            \Log::warning('Senha incorreta para beneficiário: ' . $credentials['email']);
            return back()->withErrors([
                'email' => 'As credenciais informadas estão incorretas.',
            ]);
        }

        // ✅ Autentica o beneficiário
        if (Auth::guard('beneficiary')->loginUsingId($beneficiary->id)) {
            $request->session()->regenerate();
            \Log::info('Beneficiário autenticado com sucesso: ' . $credentials['email']);
            return redirect()->route('beneficiary.area.index'); // redireciona para index
        }

        \Log::error('Falha ao autenticar beneficiário: ' . $credentials['email']);
        return back()->withErrors([
            'email' => 'Erro ao realizar login. Tente novamente.',
        ]);
    }

    /**
     * LogOut
     */
    public function logout(Request $request)
    {
        Auth::guard('beneficiary')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beneficiary.login');
    }
}

