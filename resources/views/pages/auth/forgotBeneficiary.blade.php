<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>

    <style>
        body {
            background: linear-gradient(135deg, #dff1ff 0%, #b8dcff 100%);
            color: #0a2a4d;
            font-family: "Inter", sans-serif;
            margin: 0;
            padding: 0;
        }

        .auth-container {
            max-width: 420px;
            margin: 70px auto;
            padding: 35px;
            background: rgba(255, 255, 255, 0.75);
            border-radius: 18px;
            backdrop-filter: blur(18px);
            box-shadow: 0 8px 30px rgba(0, 80, 160, 0.18);
        }

        .auth-title {
            font-size: 1.9rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 8px;
            color: #0a2a4d;
        }

        .auth-subtitle {
            text-align: center;
            font-size: 0.96rem;
            color: #2f4966;
            margin-bottom: 28px;
        }

        .alert {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        .alert-danger {
            background: rgba(255, 0, 0, 0.12);
            border-left: 4px solid #ff4d4d;
            color: #9e1c1c;
        }

        .alert-success {
            background: rgba(0, 180, 0, 0.12);
            border-left: 4px solid #38c172;
            color: #1e6532;
        }

        .form-control {
            width: 100%;
            padding: 13px;
            background: #ffffff;
            border: 1px solid #a7c9f5;
            color: #0a2a4d;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 1rem;
            transition: 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #62a8ff;
            box-shadow: 0 0 0 3px rgba(98, 168, 255, 0.25);
        }

        .btn-primary {
            width: 100%;
            padding: 13px;
            font-weight: 600;
            background-color: #62a8ff;
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.2s ease;
            font-size: 1rem;
        }

        .btn-primary:hover {
            background-color: #4b92ea;
            box-shadow: 0 6px 16px rgba(75, 146, 234, 0.35);
        }

        .back-link {
            text-align: center;
            display: block;
            margin-top: 22px;
            color: #0a54d6;
            font-weight: 500;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .back-link:hover {
            color: #003eaa;
        }
    </style>

</head>
<body>

    <div class="auth-container">

        <h2 class="auth-title">Recuperar Senha</h2>
        <p class="auth-subtitle">Informe seu e-mail para enviarmos o link de redefinição.</p>

        {{-- Mensagens de erro --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Mensagem de sucesso --}}
        @if (session('sucesso'))
            <div class="alert alert-success">
                {{ session('sucesso') }}
            </div>
        @endif

        <form action="{{ route('forgot.password') }}" method="POST">
            @csrf
            @method('post')
            <input 
                type="email" 
                name="email"
                class="form-control"
                placeholder="Digite seu e-mail"
                required
            >

            <button type="submit" class="btn-primary">
                Enviar Link de Recuperação
            </button>
        </form>

        <a href="{{ route('beneficiary.login') }}" class="back-link">Voltar para o login</a>

    </div>

</body>
</html>
