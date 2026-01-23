@extends('layouts.app', ['activePage' => 'beneficiary_dashboard', 'titlePage' => __('Área do Beneficiário')])

@section('content')
    <style>
        /* ======== ESTILO MODERNO / GLASS UI ======== */

        .glass-card {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12);
            transition: .3s ease;
            border: 1px solid rgba(255, 255, 255, 0.35);
        }

        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
        }

        .welcome-bg {
            background: linear-gradient(135deg, #4081F6 0%, #8aacec 100%);
            color: white;
            border-radius: 18px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
        }

        .welcome-icon i {
            font-size: 64px;
            opacity: .9;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #0f6f92;
        }

        /* ===== INFO CARD ===== */

        .info-label {
            font-size: .85rem;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 1.05rem;
            font-weight: 600;
            color: #0d2248;
        }

        /* ===== PLANO ===== */

        .plan-box {
            padding: 1.5rem;
            border-left: 6px solid #4081F6;
            border-radius: 14px;
            background: #fdfdfd;
            transition: .3s ease;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        .plan-box:hover {
            transform: translateX(6px);
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.12);
        }

        .plan-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #4081F6;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .plan-tag {
            background: #4081F6;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: .8rem;
        }

        .service-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .95rem;
        }

        .service-item i {
            font-size: 18px;
            color: #4081F6;
        }

        /* BOTÃO FLUTUANTE PARA REVER O TUTORIAL */
        .tutorial-float-btn {
            position: fixed;
            bottom: 15px;
            right: 25px;
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4081F6, #6fa8ff);
            color: #fff;
            border: none;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999998;
            transition: .3s;
        }

        .tutorial-float-btn i {
            font-size: 32px;
        }

        .tutorial-float-btn:hover {
            transform: scale(1.08);
            box-shadow: 0 10px 26px rgba(0, 0, 0, 0.32);
        }

        @media (max-width: 991px) {
            .tutorial-float-btn {
                top: 15px !important;
                /* distância do topo */
                left: 50% !important;
                /* centraliza horizontalmente */
                right: auto !important;
                /* remove o right */
                transform: translateX(-50%);
                /* ajusta o alinhamento */
            }
        }
    </style>

    @include('pages.beneficiaries.area.tutorial')
    <!-- BOTÃO FIXO PARA REABRIR O TUTORIAL -->
    <button id="openTutorialBtn" class="tutorial-float-btn" title="Ver Tutorial">
        <i class="material-icons">help_outline</i>
    </button>
    <div class="content">
        <div class="container-fluid">

            <!-- ==========================
                         BOAS-VINDAS
                    ===========================-->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="welcome-bg p-4 text-center">
                        <div class="welcome-icon mb-2">
                            <i class="material-icons">emoji_emotions</i>
                        </div>

                        <h2 class="fw-bold mb-1">Olá, {{ $beneficiary->name }}</h2>
                        <p class="mb-0" style="font-size: 1.05rem; opacity: .9;">
                            Bem-vindo(a) à área do beneficiário
                        </p>
                    </div>
                </div>
            </div>

            @if ($planStatus === 'cancel_waiting_end' && $currentPlan)
                <div class="row">
                    <div class="col-12 mt-3 mb-3">
                        <div class="alert alert-warning d-flex align-items-start" role="alert">
                            <div class="me-2">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <strong>Cancelamento solicitado</strong><br>
                                Seu plano foi cancelado, mas você ainda tem acesso até
                                <strong>
                                    {{ \Carbon\Carbon::parse($currentPlan->end_date)->format('d/m/Y') }}
                                </strong>.
                                Após essa data, seu acesso será bloqueado automaticamente.
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($planStatus === 'expired')
                <div class="row">
                    <div class="col-12 mt-3 mb-3">
                        <div class="alert alert-danger d-flex align-items-start" role="alert">
                            <div class="me-2">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div>
                                <strong>Plano expirado</strong><br>
                                Seu plano expirou em
                                <strong>
                                    {{ \Carbon\Carbon::parse($currentPlan->end_date)->format('d/m/Y') }}
                                </strong>.
                                Para continuar utilizando o sistema, é necessário renovar o plano.
                            </div>
                        </div>
                    </div>
                </div>
            @endif


            @if ($planStatus === 'active')
                <div>
                    <a href="{{ route('beneficiary.area.telemedicine') }}" class="btn btn-primary btn-block btn-lg">
                        <i class="material-icons" style="vertical-align: middle;">video_call</i>
                        Iniciar atendimento
                    </a>
                </div>
            @endif
            <br>
            <br>
            <!-- ==========================
                         DADOS DO BENEFICIÁRIO
                    ===========================-->
            <div class="row">
                <div class="col-md-12">
                    <div class="glass-card p-4 mb-4">

                        <h4 class="section-title mb-3">
                            <i class="material-icons">account_circle</i> Seus Dados
                            <a href="{{ route('beneficiary.area.profile.edit') }}" class="btn btn-primary"> Editar
                                <i class="material-icons">edit</i>
                            </a>
                        </h4>

                        <div class="row g-4">

                            <div class="col-md-4">
                                <div class="info-label">NOME COMPLETO</div>
                                <div class="info-value">{{ $beneficiary->name ?? '-' }}</div>
                            </div>

                            <div class="col-md-4">
                                <div class="info-label">E-MAIL</div>
                                <div class="info-value">{{ $beneficiary->email ?? '-' }}</div>
                            </div>

                            <div class="col-md-4">
                                <div class="info-label">CPF</div>
                                <div class="info-value">{{ $beneficiary->cpf ?? '-' }}</div>
                            </div>

                        </div>

                        <hr class="my-4">

                        <div class="row g-4">

                            <div class="col-md-6">
                                <div class="info-label">EMPRESA</div>
                                <div class="info-value">{{ $beneficiary->company->name ?? '-' }}</div>
                            </div>

                            {{-- <div class="col-md-6">
                            <div class="info-label">CNPJ</div>
                            <div class="info-value">{{ $beneficiary->company->cnpj ?? '-' }}</div>
                        </div> --}}

                        </div>
                    </div>
                </div>
            </div>

            <!-- ==========================
                         PLANOS E BENEFÍCIOS
                    ===========================-->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="glass-card p-4">

                        <h4 class="section-title mb-4">
                            <i class="material-icons">layers</i> Seus Planos e Benefícios
                        </h4>

                        @forelse ($plans as $plan)
                            <div class="plan-box mb-4">

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="plan-title">
                                        <i class="material-icons text-primary">check_circle</i>
                                        {{ $plan->name }}
                                    </span>
                                </div>

                                <div class="text-muted mb-2">
                                    Valor mensal:
                                    <strong class="text-dark">R$
                                        {{ number_format($plan->value ?? 0, 2, ',', '.') }}</strong>
                                </div>

                                @if ($plan->description)
                                    <p>{{ $plan->description }}</p>
                                @endif



                                <a href="{{ route('beneficiary.area.plan.details', ['plan' => $plan->id]) }}"
                                    class="btn btn-primary btn-sm mt-3">
                                    Ver mais detalhes
                                </a>

                            </div>
                        @empty
                            <p class="text-muted">Nenhum plano vinculado ao seu perfil.</p>
                        @endforelse

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
