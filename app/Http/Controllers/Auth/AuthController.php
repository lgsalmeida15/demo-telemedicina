<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Services\BrevoMailService;

// MODELS
use App\Models\Beneficiary;
use App\Models\Dependent;

// MAIL
use App\Mail\ResetPasswordMail;

class AuthController extends Controller
{


    /**
     * Mostra o formulário de preenchimento de email 
     */
    public function showForgotForm()
    {
        return view('pages.auth.forgot');
    }


    public function showForgotFormBeneficiary () 
    {
        return view('pages.auth.forgotBeneficiary');
    }


    public function confirm ()
    {
        return view('pages.auth.confirm');
    }

    /**
     * Envia email de recuperação de senha
     */
        public function forgotPassword(Request $request)
        {
            $request->validate([
                'email' => 'required|email'
            ]);

            $email = $request->email;

            if ($beneficiary = Beneficiary::where('email', $email)->first()) {
                $this->resetPasswordBeneficiary($beneficiary);

                return back()->with(
                    'sucesso',
                    'Uma nova senha foi enviada para seu e-mail.'
                );
            }

            if ($dependent = Dependent::where('email', $email)->first()) {
                $this->resetPasswordDependent($dependent);

                return back()->with(
                    'sucesso',
                    'Uma nova senha foi enviada para seu e-mail.'
                );
            }

            return back()->withErrors([
                'email' => 'E-mail não encontrado.'
            ]);
        }




    private function resetPasswordBeneficiary(Beneficiary $user)
    {
        $newPassword = $this->generatePassword();

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        $logo = asset('material/img/logo.png');
        // $logo = file_get_contents($logo);
        // $logo = 'data:image/png;base64,' . base64_encode($logo);

        $html = view('emails.new_password', [
            'name'     => $user->name ?? 'Usuário',
            'password' => $newPassword,
            'type'     => 'beneficiário',
            'logo'     => $logo,
        ])->render();

        BrevoMailService::send(
            $user->email,
            'Sua nova senha de acesso',
            $html
        );
    }


    private function resetPasswordDependent(Dependent $user)
    {
        $newPassword = $this->generatePassword();

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        $logo = asset('material/img/logo.png');
        // $logo = file_get_contents($logo);
        // $logo = 'data:image/png;base64,' . base64_encode($logo);

        $html = view('emails.new_password', [
            'name'     => $user->name ?? 'Usuário',
            'password' => $newPassword,
            'type'     => 'dependente',
            'logo'     => $logo,
        ])->render();

        BrevoMailService::send(
            $user->email,
            'Sua nova senha de acesso',
            $html
        );
    }

    /**
     * Exibe o formulário de redefinição
     */
    public function showResetForm(Request $request)
    {
        return view('pages.auth.reset', [
            'token' => $request->token,
            'email' => $request->email
        ]);
    }

    /**
     * Processa a redefinição de senha
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        // Busca token na tabela password_resets
        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (! $record) {
            return back()->withErrors(['email' => 'Solicitação inválida.']);
        }

        if (! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'Token inválido ou expirado.']);
        }

        // Verifica em qual tabela está o usuário
        if ($record->user_type === 'beneficiaries') {
            $user = Beneficiary::where('email', $request->email)->first();
        } else {
            $user = Dependent::where('email', $request->email)->first();
        }

        if (! $user) {
            return back()->withErrors(['email' => 'Usuário não encontrado.']);
        }

        // Atualiza senha
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Remove o token
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Redireciona para login
        return redirect()->route('login')->with('sucesso', 'Senha redefinida com sucesso!');
    }




    // cria a senha aleatoria
    private function generatePassword(int $length = 5): string
    {
        return Str::upper(Str::random($length));
    }
}
