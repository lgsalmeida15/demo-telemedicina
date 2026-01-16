<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Dependente - Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Ubuntu:wght@300;400;500;700&display=swap"
        rel="stylesheet">

    <style>
        h1,
        h2,
        h3,
        h4 {
            font-family: "Bebas Neue", sans-serif !important;
            font-size: 50px;
        }

        :root {
            --primary: #4081F6;
            --secondary: #bdc8db;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Poppins', sans-serif;
        }

        .page-wrapper {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        /* ======================= LADO ESQUERDO ======================= */
        .left-panel {
            width: 55%;
            background: #f3f3f3;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .left-panel .preview-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            opacity: 1;
        }

        .left-text {
            position: absolute;
            top: 60px;
            left: 50px;
            color: #fff;
            font-size: 2.1rem;
            font-weight: 700;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        /* ======================= RIGHT PANEL (LOGIN) ======================= */
        .right-panel {
            width: 45%;
            background: #4D8CFE;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(8px);
            border-radius: 18px;
            padding: 2.8rem 2.3rem;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.15);
            animation: fadeInUp .8s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-icon i {
            font-size: 70px;
            color: var(--primary);
        }

        .login-card h3 {
            color: var(--primary);
            font-weight: 700;
            margin-top: 15px;
            margin-bottom: 1.4rem;
            text-align: center;
        }

        .form-control {
            border-radius: 12px;
            padding: 0.75rem;
            font-size: 1rem;
            border: 2px solid rgba(0, 0, 0, 0.08);
            transition: .25s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(64, 129, 246, 0.2);
        }

        .btn-login {
            margin-top: 10px;
            background: linear-gradient(135deg, var(--primary), #6fa8ff);
            border: none;
            padding: 0.75rem;
            font-weight: 700;
            font-size: 1.05rem;
            border-radius: 12px;
            width: 100%;
            color: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.20);
            transition: .3s;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 26px rgba(0, 0, 0, 0.28);
        }

        .alert {
            border-radius: 10px;
            font-size: .95rem;
        }

        /* ======================= MOBILE ======================= */
        @media (max-width: 991px) {

            .left-panel {
                display: none !important;
            }

            .right-panel {
                width: 100% !important;
                padding: 20px !important;
            }

            .login-card {
                width: 100% !important;
                max-width: 380px !important;
                padding: 2rem 1.6rem !important;
                box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);
            }

            .login-icon i {
                font-size: 55px;
            }

            .login-card h3 {
                font-size: 32px !important;
            }
        }
    </style>
    <style>
        .page-loading {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.85);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 99999;
        }

        .page-loading.hidden {
            display: none;
        }

        /* Spinner simples */
        .spinner {
            width: 48px;
            height: 48px;
            border: 5px solid #ccc;
            border-top-color: #0d6efd;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 10px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>

    <div id="page-loading" class="page-loading hidden">
        <div class="spinner"></div>
        <p>Carregando...</p>
    </div>

    <div class="page-wrapper">

        <!-- ======================== LEFT SIDE ======================== -->
        <div class="left-panel">

            <div class="left-text">
                Bem-vindo<br>
                à Área do Dependente
            </div>

            <img src="{{ asset('material/img/login-beneficiario-bg.png') }}" alt="Imagem Login" class="preview-image">

        </div>


        <!-- ======================== RIGHT SIDE / LOGIN ======================== -->
        <div class="right-panel">

            <div class="login-card">

                <div class="login-icon text-center mb-2">
                    <i class="material-icons">family_restroom</i>
                </div>

                <h3>Área do <span style="color: #152651">Dependente</span></h3>

                @if ($errors->any())
                    <div class="alert alert-danger text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger text-center">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('dependent.login.submit') }}">
                    @csrf

                    <div class="mb-3 text-start">
                        <label for="email" class="form-label fw-semibold">E-mail</label>
                        <input id="email" type="text" name="email" class="form-control"
                            placeholder="Digite seu E-mail" required autofocus>
                    </div>

                    <div class="input-group">
                        <input id="password" type="password" name="password" class="form-control"
                            placeholder="Sua senha de acesso" required>

                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()"
                            style="border-radius: 10px; margin-left: 6px;">
                            <i class="material-icons">visibility</i>
                        </button>
                    </div>
                    <a href="{{ route('forgot.form.password') }}">Esqueci minha senha</a>
                    <button type="submit" class="btn-login loading-link">
                        Entrar
                    </button>

                    <div style="width: 100%; text-align: center; margin-top:1rem">
                        <a href="{{ route('beneficiary.login') }}">Acesso do Beneficiário</a>
                    </div>

                </form>

            </div>

        </div>

    </div>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const icon = event.currentTarget.querySelector("i");

            if (input.type === "password") {
                input.type = "text";
                icon.textContent = "visibility_off";
            } else {
                input.type = "password";
                icon.textContent = "visibility";
            }
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document.querySelectorAll('.loading-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Ignora se abrir em nova aba
                    if (this.target === '_blank' || e.ctrlKey || e.metaKey) {
                        return;
                    }
                    // Mostra o loading
                    const loader = document.getElementById('page-loading');
                    if (loader) {
                        loader.classList.remove('hidden');
                    }
                });
            });
        });
    </script>
</body>

</html>
