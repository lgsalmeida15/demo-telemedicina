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
        \Log::info('=== TENTATIVA DE LOGIN BENEFICIÁRIO ===', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'session_id' => $request->session()->getId()
        ]);

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        \Log::info('Credenciais validadas', ['email' => $credentials['email']]);

        // Busca o beneficiário
        $beneficiary = Beneficiary::where('email', $credentials['email'])->first();
        
        if (!$beneficiary) {
            \Log::warning('Beneficiário não encontrado', ['email' => $credentials['email']]);
            return back()->withErrors([
                'email' => 'As credenciais informadas estão incorretas.',
            ])->withInput();
        }

        \Log::info('Beneficiário encontrado', [
            'id' => $beneficiary->id,
            'email' => $beneficiary->email,
            'has_password' => !empty($beneficiary->password)
        ]);

        // Verifica se a senha está correta
        if (!Hash::check($credentials['password'], $beneficiary->password)) {
            \Log::warning('Senha incorreta', ['beneficiary_id' => $beneficiary->id]);
            return back()->withErrors([
                'email' => 'As credenciais informadas estão incorretas.',
            ])->withInput();
        }

        \Log::info('Senha verificada com sucesso');

        // ✅ Faz login do beneficiário (mesma ordem do DependentAuthController que funciona)
        Auth::guard('beneficiary')->login($beneficiary);
        
        // ✅ Regenera a sessão DEPOIS do login (essencial para persistência)
        $request->session()->regenerate();
        
        \Log::info('Beneficiário autenticado e sessão regenerada', [
            'beneficiary_id' => $beneficiary->id,
            'email' => $beneficiary->email,
            'session_id' => $request->session()->getId(),
            'is_authenticated' => Auth::guard('beneficiary')->check(),
            'auth_id' => Auth::guard('beneficiary')->id()
        ]);
        
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

