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

        if (Auth::guard('beneficiary')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('beneficiary.area.index'); // redireciona para index
        }

        return back()->withErrors([
            'email' => 'As credenciais informadas estão incorretas.',
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

