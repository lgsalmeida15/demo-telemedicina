<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-mail Enviado</title>

    <style>
        body {
            background: linear-gradient(135deg, #dff1ff 0%, #b8dcff 100%);
            color: #0a2a4d;
            font-family: "Inter", sans-serif;
            margin: 0;
            padding: 0;
        }

        .success-container {
            max-width: 420px;
            margin: 90px auto;
            padding: 35px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 18px;
            backdrop-filter: blur(18px);
            box-shadow: 0 8px 30px rgba(0, 80, 160, 0.18);
            text-align: center;
        }

        .icon {
            font-size: 65px;
            color: #4ba8ff;
            margin-bottom: 15px;
        }

        .title {
            font-size: 1.7rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .message {
            font-size: 1rem;
            color: #2f4966;
            margin-bottom: 25px;
        }

        .btn-back {
            display: inline-block;
            padding: 12px 22px;
            background: #62a8ff;
            color: #fff;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .btn-back:hover {
            background: #4b92ea;
            box-shadow: 0 6px 16px rgba(75, 146, 234, 0.35);
        }
    </style>
</head>

<body>

    <div class="success-container">

        <div class="icon">📩</div>

        <h2 class="title">E-mail Enviado!</h2>

        <p class="message">
            O link de recuperação de senha foi enviado para o endereço informado.<br>
            Verifique sua caixa de entrada.
        </p>

        <a href="{{ route('dependent.login') }}" class="btn-back">
            Voltar ao Login
        </a>

    </div>

</body>
</html>
