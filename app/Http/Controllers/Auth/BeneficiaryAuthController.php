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

        // Busca o beneficiário
        $beneficiary = Beneficiary::where('email', $credentials['email'])->first();
        
        if (!$beneficiary) {
            return back()->withErrors([
                'email' => 'As credenciais informadas estão incorretas.',
            ])->withInput();
        }

        // Verifica se a senha está correta
        if (!Hash::check($credentials['password'], $beneficiary->password)) {
            return back()->withErrors([
                'email' => 'As credenciais informadas estão incorretas.',
            ])->withInput();
        }

        // ✅ Usa exatamente a mesma abordagem do DependentAuthController que funciona
        Auth::guard('beneficiary')->login($beneficiary);
        
        // ✅ Regenera a sessão (essencial para persistência)
        $request->session()->regenerate();
        
        // Redireciona para a área do beneficiário
        return redirect()->route('beneficiary.area.index')->with('success', 'Login realizado com sucesso!');
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

