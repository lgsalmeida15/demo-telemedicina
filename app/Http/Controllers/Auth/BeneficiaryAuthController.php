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
        // 🔍 LOG INICIAL - Verifica se o método está sendo chamado
        \Log::info('=== INÍCIO LOGIN BENEFICIÁRIO ===', [
            'ip' => $request->ip(),
            'email' => $request->input('email'),
            'has_password' => !empty($request->input('password')),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
        ]);

        try {
            // Validação manual para capturar erros
            $validator = \Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                \Log::warning('Validação falhou no login', [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $request->except('password')
                ]);
                return back()->withErrors($validator)->withInput();
            }

            $credentials = $request->only('email', 'password');
            
            \Log::info('Credenciais validadas', ['email' => $credentials['email']]);

            // 🔍 DEBUG: Verifica se o beneficiário existe
            $beneficiary = Beneficiary::where('email', $credentials['email'])->first();
            
            if (!$beneficiary) {
                \Log::warning('Tentativa de login com email não encontrado: ' . $credentials['email']);
                return back()->withErrors([
                    'email' => 'As credenciais informadas estão incorretas.',
                ])->withInput();
            }

            \Log::info('Beneficiário encontrado', [
                'id' => $beneficiary->id,
                'email' => $beneficiary->email,
                'has_password' => !empty($beneficiary->password)
            ]);

            // 🔍 DEBUG: Verifica se a senha está correta
            if (!Hash::check($credentials['password'], $beneficiary->password)) {
                \Log::warning('Senha incorreta para beneficiário', [
                    'email' => $credentials['email'],
                    'password_provided' => !empty($credentials['password']),
                    'password_hash_exists' => !empty($beneficiary->password)
                ]);
                return back()->withErrors([
                    'email' => 'As credenciais informadas estão incorretas.',
                ])->withInput();
            }

            \Log::info('Senha verificada com sucesso');

            // ✅ CORRIGIDO: Faz login SEM regenerar sessão primeiro
            // O regenerate() está causando perda de autenticação
            Auth::guard('beneficiary')->login($beneficiary, false);
            
            // Salva a sessão explicitamente antes de qualquer coisa
            $request->session()->save();
            
            \Log::info('Beneficiário autenticado (sem regenerate)', [
                'email' => $credentials['email'],
                'session_id' => $request->session()->getId(),
                'is_authenticated' => Auth::guard('beneficiary')->check(),
                'user_id' => Auth::guard('beneficiary')->id(),
                'beneficiary_id' => $beneficiary->id
            ]);
            
            // Redireciona para a área do beneficiário
            // O regenerate será feito automaticamente na próxima requisição se necessário
            return redirect()->route('beneficiary.area.index')->with('success', 'Login realizado com sucesso!');

            \Log::error('Falha ao autenticar beneficiário - loginUsingId retornou false', [
                'email' => $credentials['email'],
                'beneficiary_id' => $beneficiary->id
            ]);
            return back()->withErrors([
                'email' => 'Erro ao realizar login. Tente novamente.',
            ])->withInput();

        } catch (\Exception $e) {
            \Log::error('EXCEÇÃO no login do beneficiário', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->input('email')
            ]);
            return back()->withErrors([
                'email' => 'Erro inesperado ao realizar login. Tente novamente.',
            ])->withInput();
        }
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

