<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ $company->name }} - Telemedicina</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    {{-- Google Icons --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>

        /* Títulos com Bebas Neue */
        h1, h2, h3, h4 {
            font-family: "Bebas Neue", sans-serif !important;
            font-size: 70px;
        }

        body {
            margin: 0;
            font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #ffffff;
        }

        /* TOPO SUPERIOR */
        .top-bar {
            background: #ffffff;
            padding: 6px 0;
            font-size: 14px;
            color: #3a3a3a;
            border-bottom: 1px solid #eaeaea;
        }

        .top-bar a {
            color: inherit;
            text-decoration: none;
        }

        /* HEADER */
        .main-header {
            background: #ffffff;
            padding: 18px 0;
        }

        .logo-img {
            max-width: 190px;
            height: auto;
        }
        .logo-elo-img {
            max-width: 190px;
            height: auto;
            padding-right: 20px;
        }

        .nav-link-text {
            font-size: 16px;
            color: #000;
            text-decoration: none;
            margin: 0 12px;
            font-weight: 500;
        }

        .nav-link-text:hover {
            text-decoration: underline;
        }

        .btn-header-primary {
            background: #4d7fff;
            color: #fff;
            border-radius: 999px;
            padding: 10px 30px;
            font-weight: 700;
            border: none;
            font-size: 15px;
            text-transform: uppercase;
        }

        .btn-header-primary:hover {
            background: #3b67d8;
            color: #fff;
        }

        /* HERO */
        .hero-section {
            /* degradê vindo da  */
            /* background: linear-gradient(90deg, #4d8cff 0%, #4d8cff 50%, #4d8cff 50%, #4d8cff 100%); */
            color: #fff;
            padding: 64px 0 72px 0;
            height: 500px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::after {
            /* leve overlay azul na parte da imagem */
            content: "";
            position: absolute;
            inset: 0;
            /* background: linear-gradient(90deg, rgba(77,140,255,0.95) 0%, rgba(77,140,255,0.70) 42%, rgba(77,140,255,0) 85%); */
            pointer-events: none;
        }

        .hero-inner {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 22px;
            line-height: 1.5;
            font-weight: 600;
            max-width: 520px;
        }

        .hero-title b {
            font-weight: 800;
        }

        .hero-price-box {
            border: 2px solid #fff;
            border-radius: 6px;
            padding: 18px 26px;
            display: inline-flex;
            align-items: flex-end;
            gap: 4px;
            margin: 28px 0 30px 0;
        }

        .hero-price-box .valor-grande {
            font-size: 40px;
            font-weight: 800;
            line-height: 1;
        }

        .hero-price-box .centavos {
            font-size: 24px;
            font-weight: 700;
            align-self: flex-start;
        }

        .hero-price-box .texto-mensal {
            font-size: 18px;
            font-weight: 700;
        }

        .btn-hero-cta {
            background: transparent;
            border: 2px solid #fff;
            color: #fff;
            padding: 14px 40px;
            font-size: 18px;
            border-radius: 999px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .btn-hero-cta:hover {
            background: #ffffff;
            color: #4d8cff;
        }

        .hero-image {
            max-width: 460px;
            width: 100%;
            border-radius: 24px;
            object-fit: cover;
        }

        /* SEÇÃO MOTIVOS */
        .motivos-section {
            background: #efefef;
            padding: 80px 0 110px 0;
        }

        .motivos-title-wrapper {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: -53px;
            margin-bottom: 60px;
        }

        .motivos-title {
            font-size: 36px;
            color: #3c63ff;
            margin: 0;
            white-space: nowrap;
        }

        .motivos-title-line {
            flex: 1;
            height: 3px;
            background: #0f3b2e; /* tom exato da imagem */
        }

        .motivos-wrapper {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 40px;
            flex-wrap: nowrap;
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
        }


        .motivo-card {
            width: 260px;         /* Tamanho idêntico */
            position: relative;
            text-align: center;
        }

        .motivo-icon {
            width: 82px;
            height: 82px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 5px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: #000;
            position: absolute;
            top: -41px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
        }

        .motivo-top {
            border-top-left-radius: 38px;
            border-top-right-radius: 38px;
            color: #fff;
            padding: 75px 20px 30px 20px;
            font-size: 22px;
            font-weight: 700;
            min-height: 130px;
            line-height: 1.3;
        }

        .motivo-bottom {
            background: #fff;
            border-bottom-left-radius: 38px;
            border-bottom-right-radius: 38px;
            padding: 28px 22px 35px 22px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.18);
            font-size: 17px;
            min-height: 200px;
            line-height: 1.55;
            font-weight: 500;
        }

        /* FAQ */
        .faq-section {
            background: #ffffff;
            padding: 70px 0 60px 0;
        }

        .faq-title {
            font-size: 34px;
            font-weight: 700;
            color: #4d7fff;
            text-align: center;
            margin-bottom: 40px;
        }

        .faq-item {
            padding: 18px 0;
            border-bottom: 1px solid #e3e3e3;
            font-size: 17px;
            color: #222;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .faq-item i {
            font-size: 18px;
        }

        .faq-answer {
            display: none;
            padding: 0 0 14px 0;
            font-size: 15px;
            color: #555;
        }

        .faq-item.open + .faq-answer {
            display: block;
        }

        /* CTA MEIO */
        .cta-middle-wrapper {
            padding-bottom: 40px;
        }

        .btn-middle {
            display: inline-block;
            background: #4d7fff;
            color: #fff;
            padding: 14px 50px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 18px;
            text-transform: uppercase;
            border: none;
        }

        .btn-middle:hover {
            background: #365fdb;
            color: #fff;
        }

        /* FOOTER */
        .main-footer {
            border-top: 1px solid #eeeeee;
            padding: 45px 0 35px 0;
            background: #ffffff;
        }

        .footer-logo {
            max-width: 190px;
            height: auto;
        }

        .footer-text {
            max-width: 320px;
            font-size: 14px;
            line-height: 1.7;
            color: #444;
        }

        .footer-title {
            font-weight: 700;
            margin-bottom: 18px;
            font-size: 16px;
        }

        .footer-link {
            display: block;
            color: #222;
            text-decoration: none;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .footer-link:hover {
            text-decoration: underline;
        }

        .footer-cta-btn {
            @extend .btn-header-primary;
        }

        .social-icon {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            background: #e6e6e6;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 6px;
            font-size: 16px;
            color: #111;
        }

        /* BARRA FINAL */
        .bottom-bar {
            background: #152551;
            color: #fff;
            padding: 16px 0;
            font-size: 13px;
            text-align: center;
        }

        .carousel-control-next-icon, .carousel-control-prev-icon{
            background-color: rgba(0,0,0,.5);
            border-radius: 20%;
        }

        /*PROPORÇÃO PARA CELULARES*/
        @media (max-width: 768px) {

            /* --- LOGOS NO TOPO LADO A LADO --- */
            header .container {
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
                text-align: left !important;
            }

            header .logo-img {
                max-width: 140px !important;
                width: auto !important;
                height: auto !important;
            }

            header .container > div:first-child {
                display: flex !important;
                flex-direction: column !important;
                gap: 10px !important;
            }

            /* Ajuste fino: Elo à esquerda + BoxFarma à direita */
            header .container > div:first-child img:first-child {
                align-self: flex-start !important;
            }

            header .container > div:first-child img:last-child {
                align-self: flex-end !important;
                margin-top: -10px;
            }

            /* --- BOTÕES LADO A LADO --- */
            .nav-wrapper {
                flex-direction: row !important;
                flex-wrap: wrap !important;
                justify-content: center !important;
                gap: 10px !important;
                width: 100%;
                margin-top: 20px;
            }

            .nav-wrapper span.nav-link-text {
                width: 100%;
                text-align: center;
                font-size: 15px;
                margin-bottom: -5px;
            }

            .nav-wrapper .btn-header-primary {
                width: 100%;
                min-width: 130px;
                text-align: center;
                padding: 10px 5px !important;
                font-size: 14px !important;
                font-weight: 700;
            }

            /* Remove empilhamento vertical */
            .main-header .nav-wrapper {
                gap: 5px !important;
                margin-top: 0;
            }
        }

        /* =============================================
        RESPONSIVIDADE MOBILE — OTIMIZAÇÕES
        ============================================= */
        @media (max-width: 991px) {

            html, body { overflow-x: hidden; }
            /* ------ HEADER ------ */
            .main-header .nav-wrapper {
                flex-direction: column;
                text-align: center;
                gap: 8px;
                margin-top: 15px;
            }

            

            header .container {
                flex-direction: column;
                text-align: center;
                gap: 18px;
            }

            /* ------ HERO ------ */
            .hero-section {
                height: auto;
                padding: 40px 0 55px 0;
            }

            .hero-title {
                font-size: 26px !important;
                line-height: 32px !important;
                max-width: 100% !important;
                text-align: center;
            }

            .hero-price-box {
                margin-left: auto !important;
                margin-right: auto !important;
            }

            .hero-price-box span {
                display: inline-block;
            }

            .hero-price-box .valor-grande {
                font-size: 48px !important;
            }

            .hero-price-box .centavos {
                font-size: 26px !important;
            }

            .hero-price-box .texto-mensal {
                font-size: 18px !important;
            }

            .hero-section img {
                max-width: 330px !important;
                margin-top: 30px;
            }

            /* CTA HERO */
            .hero-section a {
                font-size: 18px !important;
                padding: 14px 35px !important;
            }

            /* ------ MOTIVOS ------ */
            .motivos-title {
                font-size: 38px !important;
                text-align: center;
            }

            .motivos-title-wrapper {
                flex-direction: column;
                text-align: center;
            }

            .motivos-title-line {
                display: none;
            }

            .motivo-card {
                width: 100% !important;
                max-width: 320px !important;
                margin-left: auto;
                margin-right: auto;
            }

            .motivo-top {
                padding-top: 70px !important;
                font-size: 20px !important;
            }

            .motivo-bottom {
                font-size: 15px !important;
                min-height: unset !important;
                padding-bottom: 25px !important;
            }

            /* ------ FAQ ------ */
            .faq-title {
                font-size: 40px !important;
                text-align: center !important;
            }

            .faq-item {
                font-size: 15px !important;
            }

            .faq-answer {
                font-size: 14px !important;
            }

            /* ------ CTA MIDDLE ------ */
            .btn-middle {
                padding: 14px 40px !important;
                font-size: 18px !important;
            }

            /* ------ FOOTER ------ */
            .main-footer .container {
                text-align: center;
            }

            .footer-logo {
                max-width: 260px !important;
                margin-left: auto;
                margin-right: auto;
            }

            .footer-title {
                margin-top: 25px;
            }

            .footer-text {
                max-width: 100% !important;
                font-size: 14px;
            }

            .social-icon {
                width: 38px !important;
                height: 38px !important;
                font-size: 18px !important;
            }

            .col-lg-4, .col-lg-3, .col-lg-2 {
                margin-bottom: 25px;
            }

            /* carrossel mobile */
            .carousel-inner {
                position: relative;
                width: 100%;
                overflow: hidden;
                height: 60vh;
                padding-top: 3rem;
                margin-bottom: -55px;
            }

            .nav-wrapper .btn-header-primary {
                width: 100%;
                min-width: 130px;
                text-align: center;
                padding: 10px 5px !important;
                font-size: 14px !important;
                font-weight: 700;
                margin: unset !important;
            }

            /* Coloca as duas logos na mesma linha, controla tamanhos e gaps */
            header .container > div:first-child {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: flex-start !important;
                gap: 12px !important;
                flex-wrap: nowrap !important;
            }

            header .container > div:first-child img.logo-elo-img,
            header .container > div:first-child img.logo-img {
                max-width: 100px !important;
                width: auto !important;
                height: auto !important;
                margin: 0 !important;
                align-self: center !important;
            }

            /* Ajustes finos: garantir ordem se quiser Elo à esquerda */
            header .container > div:first-child img.logo-elo-img { order: 0; }
            header .container > div:first-child img.logo-img { order: 1; }

            /* Se o header empurrar o nav para baixo em telas pequenas, reduz um pouco o gap */
            .main-header .nav-wrapper { margin-top: 8px !important; }

            a{
                text-decoration:none;
            }

            .caixa-de-preco{
                width: 100%;
                text-align: center;
                justify-content: center;
            }
            .botao-contrate-agora{
                width: 100%;
                text-align: center;
            }
            .imagem-bannerelo{
                width: 100%;
            }
            header .container > div:first-child{
                flex-direction: column !important;
                padding: 0 40px;
                justify-content: center !important;
            }
            .imagens-header img{
                margin: unset !important;
                padding: unset !important;
            }
            .badget-header{
                justify-content: center !important;
            }

            .faq-section{
                padding-left: 2rem;
                padding-right: 2rem;
            }
            .faq-answer{
                margin-top: 1rem;
                text-align: justify;
            }
        }
        /* CELULARES PEQUENOS (≤ 480px) */
        @media (max-width: 480px) {

            .hero-title {
                font-size: 22px !important;
                line-height: 28px !important;
            }

            .hero-price-box .valor-grande {
                font-size: 40px !important;
            }

            .hero-price-box .centavos {
                font-size: 22px !important;
            }

            .hero-section a {
                font-size: 16px !important;
                padding: 12px 28px !important;
            }

            .logo-img {
                max-width: 220px !important;
            }
        }

    </style>
</head>
<body>

    {{-- TOPO SUPERIOR --}}
    <div class="top-bar" data-aos="fade-left">
        <div class="container d-flex justify-content-between align-items-center badget-header">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-heart-pulse-fill text-primary me-2"></i>
                <span>TELEMEDICINA 24/7</span>
            </div>
        </div>
    </div>

    {{-- HEADER --}}
    <header class="main-header" data-aos="fade-right">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="justify-content-between imagens-header">
                <img src="{{asset('material/img/elo-logo-original.png')}}" style="height: 100px; width: 570px" alt="Logo da elo" class="logo-elo-img">
                <img src="{{asset('material/img/logo.png')}}" style="height: 50px; width: 570px ;" alt="Logo da BoxFarma" class="logo-img">
            </div>

            <div class="d-flex align-items-center nav-wrapper">
                <span class="nav-link-text me-2">Já é cliente?</span>
                <a href="{{route('beneficiary.login')}}" class="btn btn-header-primary me-4">Acesse o portal</a>

                <span class="nav-link-text me-2">Ainda não é cliente?</span>
                <a href="{{ route('checkout.page', $company->uuid) }}" class="btn btn-header-primary">
                    Contrate agora!
                </a>
            </div>
        </div>
    </header>

    {{-- HERO --}}
    <section class="hero-section" style="background:#4d8cff; padding:70px 0;">
        <div class="container" style="max-width:1250px;">
            <div class="row align-items-center">

                <!-- TEXTO ESQUERDA -->
                <div class="col-lg-6">

                    <h1 data-aos="fade-right"
                    style="
                        font-size:40px;
                        line-height:40px;
                        font-weight:600;
                        color:#fff;
                        max-width:480px;
                    ">
                        Use seu cartão <b style="font-weight:800; color: #152551">(Bandeira Card)</b> e tenha
                        acesso a <b style="color:#152551 ">telemedicina online 24h</b> com a 
                        <b style="font-weight:800;">BOXFARMA</b>
                        por apenas:
                    </h1>

                    <!-- CAIXA DE PREÇO -->
                    <div style="
                        border:3px solid #fff;
                        border-radius:12px;
                        padding:22px 28px;
                        display:inline-flex;
                        align-items:flex-end;
                        margin-top:30px;
                        margin-bottom:35px;
                    " class="caixa-de-preco">
                        <span style="font-size:62px; font-weight:800; line-height:1;">R$7</span>
                        <span style="font-size:34px; font-weight:700; margin-left:2px; line-height:1;">,80</span>
                        <span style="font-size:22px; font-weight:700; margin-left:6px;">mensais</span>
                    </div>

                    <!-- BOTÃO HERO -->
                    <div>
                        <a href="{{ route('checkout.page', $company->uuid) }}"
                            style="
                                display:inline-block;
                                background:transparent;
                                border:3px solid #fff;
                                color:#fff;
                                font-weight:800;
                                padding:18px 60px;
                                border-radius:50px;
                                font-size:22px;
                                text-decoration:none;
                                transition:0.2s;
                            "
                            class="botao-contrate-agora"
                            onmouseover="this.style.background='#fff'; this.style.color='#4d8cff';"
                            onmouseout="this.style.background='transparent'; this.style.color='#fff';"
                        >
                            CONTRATE AGORA!
                        </a>
                    </div>
                </div>

                <!-- IMAGEM DIREITA -->
                <div data-aos="fade-left" class="col-lg-6 text-end mt-4 mt-lg-0">
                    <img src="{{asset('material/img/2-cards-elo.png')}}"
                        alt="Teleconsulta veterinária"
                        class="imagem-bannerelo"
                        style="
                            max-width:520px !important;
                            width:95%;
                            border-radius:12px;
                            object-fit:cover;
                        ">
                </div>

            </div>
        </div>
    </section>



    {{-- MOTIVOS --}}
        <section class="motivos-section" id="motivos">
            <div class="container">

                <div class="motivos-title-wrapper">
                    <h2 data-aos="fade-left" class="motivos-title" style="font-size: 70px">Motivos <div style="color: #152551">para usar</div></h2>
                    <div data-aos="fade-right" class="motivos-title-line"></div>
                </div>

                <!-- DESKTOP: 5 CARDS EM UMA LINHA -->
                <div class="motivos-wrapper d-none d-xl-flex" data-aos="fade-right">

                    <!-- 1 -->
                    <div class="motivo-card" style="width: 350px;">
                        <div class="motivo-icon"><span class="material-icons-round">favorite</span></div>
                        <div class="motivo-top" style="background:#4d7fff;">Atendimento<br>24/7</div>
                        <div class="motivo-bottom" style="font-size: 15px;">
                            Com atendimento ilimitado por pessoa, você tem um médico online na hora.
                            De madrugada, feriado ou domingo a ajuda chega em minutos.
                        </div>
                    </div>

                    <!-- 2 -->
                    <div class="motivo-card" style="width: 350px;">
                        <div class="motivo-icon"><i class="bi bi-camera-video-fill"></i></div>
                        <div class="motivo-top" style="background:#4d7fff;">De onde você<br>estiver</div>
                        <div class="motivo-bottom" style="font-size: 15px;">
                            Na praia, em casa ou viajando: basta internet e celular para falar com um médico.
                        </div>
                    </div>

                    <!-- 3 -->
                    <div class="motivo-card" style="width: 350px;">
                        <div class="motivo-icon"><i class="bi bi-lightbulb"></i></div>
                        <div class="motivo-top" style="background:#4d7fff;">Cuidado<br>inteligente</div>
                        <div class="motivo-bottom" style="font-size: 15px;">
                            Cuidar bem de você e de quem você ama não precisa ser caro.
                            Com a BoxFarma, você tem atendimento de qualidade sempre que precisar.
                        </div>
                    </div>

                    <!-- 4 -->
                    <div class="motivo-card" style="width: 350px;">
                        <div class="motivo-icon"><span class="material-icons-round">monitor_heart</span></div>
                        <div class="motivo-top" style="background:#4d7fff;">Problemas<br>comuns?</div>
                        <div class="motivo-bottom" style="font-size: 15px;">
                            Mal estar, dor de barriga, enxaqueca?
                            Nossa equipe é especializada no que mais te preocupa.
                        </div>
                    </div>

                    <!-- 5 -->
                    <div class="motivo-card" style="width: 350px;">
                        <div class="motivo-icon"><i class="bi bi-clock-history"></i></div>
                        <div class="motivo-top" style="background:#4d7fff;">Acompanhamento<br>em todas as fases.</div>
                        <div class="motivo-bottom" style="font-size: 15px;">
                            Com atendimento para todas as idades, a BoxFarma acompanha cada etapa da sua vida.
                            Mais tranquilidade, saúde e segurança pra você.
                        </div>
                    </div>

                </div>

                <!-- MOBILE: CARROSSEL -->
                <div id="motivosCarousel" class="carousel slide d-xl-none" data-bs-ride="carousel">
                    <div class="carousel-inner">

                        <!-- 1 -->
                        <div class="carousel-item active">
                            <div class="motivos-wrapper mobile-center">
                                <div class="motivo-card" style="width: 350px;">
                                    <div class="motivo-icon"><span class="material-icons-round">favorite</span></div>
                                    <div class="motivo-top" style="background:#4d7fff;">Atendimento<br>24/7</div>
                                    <div class="motivo-bottom" style="font-size: 15px;">
                                        Com atendimento ilimitado por pessoa, você tem um médico online na hora.
                                        De madrugada, feriado ou domingo a ajuda chega em minutos.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2 -->
                        <div class="carousel-item">
                            <div class="motivos-wrapper mobile-center">
                                <div class="motivo-card" style="width: 350px;">
                                    <div class="motivo-icon"><i class="bi bi-camera-video-fill"></i></div>
                                        <div class="motivo-top" style="background:#4d7fff;">De onde você<br>estiver</div>
                                        <div class="motivo-bottom" style="font-size: 15px;">
                                            Na praia, em casa ou viajando: basta internet e celular para falar com um médico.
                                        </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3 -->
                        <div class="carousel-item">
                            <div class="motivos-wrapper mobile-center">
                                <div class="motivo-card" style="width: 350px;">
                                    <div class="motivo-icon"><i class="bi bi-lightbulb"></i></div>
                                    <div class="motivo-top" style="background:#4d7fff;">Cuidado<br>inteligente</div>
                                    <div class="motivo-bottom" style="font-size: 15px;">
                                        Cuidar bem de você e de quem você ama não precisa ser caro.
                                        Com a BoxFarma, você tem atendimento de qualidade sempre que precisar.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4 -->
                        <div class="carousel-item">
                            <div class="motivos-wrapper mobile-center">
                                <div class="motivo-card" style="width: 350px;">
                                    <div class="motivo-icon"><span class="material-icons-round">monitor_heart</span></div>
                                    <div class="motivo-top" style="background:#0aa2ff;">Problemas<br>comuns?</div>
                                    <div class="motivo-bottom" style="font-size: 15px;">
                                        Mal estar, dor de barriga, enxaqueca?
                                        Nossa equipe é especializada no que mais te preocupa.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5 -->
                        <div class="carousel-item">
                            <div class="motivos-wrapper mobile-center">
                                <div class="motivo-card" style="width: 350px;">
                                    <div class="motivo-icon"><i class="bi bi-clock-history"></i></div>
                                    <div class="motivo-top" style="background:#4d7fff;">Acompanhamento<br>em todas as fases.</div>
                                    <div class="motivo-bottom" style="font-size: 15px;">
                                        Com atendimento para todas as idades, a BoxFarma acompanha cada etapa da sua vida.
                                        Mais tranquilidade, saúde e segurança pra você.
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- CONTROLES -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#motivosCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#motivosCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </section>



    {{-- FAQ --}}
    <section class="faq-section" id="faq">
        <div class="container">
            <h2 data-aos="fade-right" class="faq-title" style="font-size: 70px"><b style="color: #152551">Perguntas</b> Frequentes</h2>

            <div class="row" data-aos="fade-left">
                <div class="col-md-6">

                    <!-- 1 -->
                    <div class="faq-item">
                        <span>Como funciona a Teleconsulta da BoxFarma?</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        A teleconsulta permite que você seja atendido por um profissional de saúde à distância,
                        por vídeo, áudio ou chat.  
                        O atendimento funciona 24 horas por dia, todos os dias da semana, e pode incluir
                        orientação médica, prescrição digital, encaminhamentos, pedidos de exames e atestados, quando necessário.
                    </div>

                    <!-- 2 -->
                    <div class="faq-item">
                        <span>A telemedicina substitui a consulta presencial?</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Em grande parte dos casos, sim.  
                        A telemedicina é indicada para condições leves, dúvidas de saúde, avaliações iniciais,
                        acompanhamento de tratamentos e suporte contínuo.  
                        Entretanto, situações de urgência, emergência ou que exijam exame físico devem ser avaliadas
                        presencialmente em uma unidade de saúde.
                    </div>

                    <!-- 3 -->
                    <div class="faq-item">
                        <span>Como funciona o cancelamento do serviço?</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        O plano pode ser cancelado a qualquer momento através do portal do cliente.  
                        O acesso à plataforma é interrompido imediatamente após o cancelamento.  
                        De acordo com o Código de Defesa do Consumidor, você pode solicitar reembolso total
                        dentro de até 7 dias após a contratação.
                    </div>

                    <!-- 4 -->
                    <div class="faq-item">
                        <span>O que eu preciso para realizar uma Teleconsulta?</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Você precisa apenas de:  
                        ● Internet estável (mínimo recomendado 20 Mbps);  
                        ● Smartphone, computador ou tablet com câmera e microfone funcionando;  
                        ● Navegador atualizado (Chrome, Safari, Firefox ou Edge) ou app da BoxFarma;  
                        ● Ambiente iluminado e silencioso para um atendimento adequado. <br><br>
                        Caso possua exames, histórico médico ou lista de medicamentos, mantenha-os por perto
                        para auxiliar o profissional durante a consulta.
                    </div>

                </div>

                <div class="col-md-6">

                    <!-- 5 -->
                    <div class="faq-item">
                        <span>Quais situações não podem ser atendidas por Telemedicina?</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        A telemedicina não é indicada para situações de emergência, como:  
                        ● Falta de ar intensa  
                        ● Dor no peito  
                        ● Perda de consciência  
                        ● Sangramentos intensos  
                        ● Convulsões  
                        ● Traumas graves  
                        ● Reações alérgicas severas  
                        Nesses casos, procure atendimento presencial imediatamente.
                    </div>

                    <!-- 6 -->
                    <div class="faq-item">
                        <span>A Telemedicina é autorizada no Brasil?</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        Sim.  
                        A prática é regulamentada pela Lei Federal nº 14.510/2022, que autoriza
                        o uso da telemedicina em todo o território nacional, seguindo normas éticas e técnicas.  
                        Todos os profissionais da BoxFarma atuam conforme as diretrizes do Conselho Federal de Medicina.
                    </div>

                    <!-- 7 -->
                    <div class="faq-item">
                        <span> Posso compartilhar o meu acesso com outra pessoa para que ela receba 
                            atendimento? 
                        </span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                         Não. O acesso é único e individual, vinculado ao CPF do titular. Caso deseje 
                        que outra pessoa tenha atendimento, basta cadastrá-la como seu beneficiário no 
                        Menu de Usuário do Titular. Todo o atendimento é vinculado ao CPF do usuário, 
                        desta forma o médico sempre saberá o histórico de atendimento. 
                    </div>
                    <!-- 8 -->
                    <div class="faq-item">
                        <span>Meus dados pessoais ficam protegidos?</span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        A BoxFarma segue integralmente a LGPD (Lei Geral de Proteção de Dados).  
                        Suas informações são criptografadas e utilizadas exclusivamente para fins de atendimento,
                        prescrição e acompanhamento médico.  
                        Nunca compartilhamos seus dados sem autorização ou fora das exigências legais.
                    </div>

                </div>

            </div>
        </div>
    </section>

    {{-- CTA CENTRAL --}}
    <section class="cta-middle-wrapper text-center">
        <a data-aos="fade-up" href="{{ route('checkout.page', $company->uuid) }}" class="btn btn-middle">
            Contrate agora!
        </a>
    </section>

    {{-- FOOTER --}}
    <footer class="main-footer">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4">
                    <img src="{{asset('material/img/elo-logo-original.png')}}" style="height: 100px; width: 570px" alt="Logo da elo" class="logo-img">
                    <img src="{{asset('material/img/logo.png')}}" style="height: 50px; width: 570px ;" alt="Logo da BoxFarma" class="logo-img">
                    <p class="footer-text">
                        Nosso serviço de telemedicina 24 horas está aqui para te orientar e tirar qualquer dúvida
                        sobre o sua saúde ou a saúde de seus entes queridos.
                    </p>
                </div>

                <div class="col-lg-2">
                    <p class="footer-title">Já é cliente?</p>
                    <a href="{{route('beneficiary.login')}}" class="btn btn-header-primary mb-3">ACESSE O PORTAL</a>

                    <p class="footer-title mt-3">Ainda não é cliente?</p>
                    <a href="{{ route('checkout.page', $company->uuid) }}" class="btn btn-header-primary">
                        CONTRATE AGORA
                    </a>
                </div>

                <div class="col-lg-3">
                    <p class="footer-title">Institucional</p>
                    <p class="footer-text" style="font-size: 12px; line-height: 1.5;">
                        BOXPHARMA 150 LTDA | 53.258.843/0001-59 | I.E 155974328117<br><br>
                        Av. Paulista, 2001 loja. 67 | Bela Vista | São Paulo – SP | CEP : 01311-931<br><br>
                        Telemedicina responsável: Docway | CNPJ 23.826.796/0001-31<br>
                    </p>
                </div>

                <div class="col-lg-3">
                    <p class="footer-title">Contato</p>
                    <p class="footer-text">
                        Ainda com dúvidas sobre nosso serviço de teleorientação médica?
                        Envie-nos uma mensagem.
                    </p>
                    <a href="mailto:telemedicina@boxfarma.co">telemedicina@boxfarma.co</a>
                    <div class="mt-2">
                        <a href="https://www.facebook.com/profile.php?id=61580970915037" target="_blank" rel="noopener noreferrer">
                        <span class="social-icon" style="background-color:#1e6591; color:white">
                            <i class="bi bi-facebook"></i>
                        </span>
                        </a>

                        <a href="https://www.instagram.com/boxfarma.ai/" target="_blank" rel="noopener noreferrer">
                            <span class="social-icon" style="background-color:#9b1947; color:white">
                                <i class="bi bi-instagram"></i>
                            </span>
                        </a>

                        <a href="https://www.linkedin.com/company/boxfarma/" target="_blank" rel="noopener noreferrer">
                            <span class="social-icon" style="background-color:#0797f6; color:white">
                                <i class="bi bi-linkedin"></i>
                            </span>
                        </a>

                        <a href="https://www.youtube.com/@boxfarmaco" target="_blank" rel="noopener noreferrer">
                            <span class="social-icon" style="background-color:red; color:white">
                                <i class="bi bi-youtube"></i>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    {{-- BARRA FINAL --}}
    <div class="bottom-bar">
        Feito por <a href="http://www.agildesenvolvimento.com" target="_blank">Ágil Desenvolvimento de Sistemas</a>,{{date('Y')}}.
    </div>

    {{-- JS Bootstrap + FAQ --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.querySelectorAll('.faq-item').forEach(function(item) {
            item.addEventListener('click', function () {
                // fecha outros
                document.querySelectorAll('.faq-item').forEach(i => {
                    if (i !== item) i.classList.remove('open');
                });

                if (item.classList.contains('open')) {
                    item.classList.remove('open');
                } else {
                    item.classList.add('open');
                }
            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 900,   // duração da animação
            offset: 120,     // distância para ativar
            easing: 'ease-out',
            once: true       // anima apenas 1 vez
        });
    </script>

</body>
</html>
