@extends('layouts.app', ['activePage' => 'dependent_dashboard', 'titlePage' => __('Área do Dependente')])

@section('content')

<style>
    /* ======== ESTILO MODERNO / GLASS UI ======== */

    .glass-card {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(12px);
        border-radius: 18px;
        box-shadow: 0 8px 28px rgba(0,0,0,0.12);
        transition: .3s ease;
        border: 1px solid rgba(255,255,255,0.35);
    }
    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.18);
    }

    .welcome-bg {
        background: linear-gradient(135deg, #4081F6 0%, #8aacec 100%);
        color: white;
        border-radius: 18px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.18);
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

    .plan-box {
        padding: 1.5rem;
        border-left: 6px solid #4081F6;
        border-radius: 14px;
        background: #fdfdfd;
        transition: .3s ease;
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    }
    .plan-box:hover {
        transform: translateX(6px);
        box-shadow: 0 6px 22px rgba(0,0,0,0.12);
    }

    .plan-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #4081F6;
        display: flex;
        align-items: center;
        gap: 6px;
    }
</style>


<div class="content">
    <div class="container-fluid">

        <!-- ==========================
             BOAS-VINDAS
        ===========================-->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="welcome-bg p-4 text-center">
                    <div class="welcome-icon mb-2">
                        <i class="material-icons">face</i>
                    </div>

                    <h2 class="fw-bold mb-1">Olá, {{ $dependent->name }}</h2>
                    <p class="mb-0" style="font-size: 1.05rem; opacity: .9;">
                        Bem-vindo(a) à área do dependente
                    </p>
                </div>
            </div>
        </div>

        <!-- ==========================
             DADOS DO DEPENDENTE
        ===========================-->
        <div class="row">
            <div class="col-md-12">
                <div class="glass-card p-4 mb-4">

                    <h4 class="section-title mb-3">
                        <i class="material-icons">account_circle</i> Seus Dados
                        <a href="{{route('dependent.area.profile.edit')}}" class="btn btn-primary"> Editar 
                            <i class="material-icons">edit</i>
                        </a>
                    </h4>

                    <div class="row g-4">

                        <div class="col-md-4">
                            <div class="info-label">NOME COMPLETO</div>
                            <div class="info-value">{{ $dependent->name }}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-label">CPF</div>
                            <div class="info-value">{{ $dependent->cpf }}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="info-label">DATA DE NASCIMENTO</div>
                            <div class="info-value">
                                {{ \Carbon\Carbon::parse($dependent->birth_date)->format('d/m/Y') }}
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>


        <!-- ==========================
             DADOS DO TITULAR
        ===========================-->
        <div class="row">
            <div class="col-md-12">
                <div class="glass-card p-4 mb-4">

                    <h4 class="section-title mb-3">
                        <i class="material-icons">groups</i> Titular
                    </h4>

                    <div class="row g-4">

                        <div class="col-md-4">
                            <div class="info-label">NOME</div>
                            <div class="info-value">{{ $dependent->beneficiary->name }}</div>
                        </div>

                    </div>

                    <hr class="my-4">

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-label">EMPRESA</div>
                            <div class="info-value">{{ $dependent->beneficiary->company->name }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <!-- ==========================
             PLANOS DO TITULAR (HERDADOS)
        ===========================-->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="glass-card p-4">

                    <h4 class="section-title mb-4">
                        <i class="material-icons">layers</i> Planos Disponíveis
                    </h4>

                    @php
                        $plans = $dependent->beneficiary->plans->pluck('plan');
                    @endphp

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
                                <strong class="text-dark">
                                    R$ {{ number_format($plan->value ?? 0, 2, ',', '.') }}
                                </strong>
                            </div>

                            @if ($plan->description)
                                <p>{{ $plan->description }}</p>
                            @endif

                            <button class="btn btn-primary btn-sm mt-3" disabled>
                                Benefício disponível
                            </button>

                        </div>
                    @empty
                        <p class="text-muted">Nenhum plano disponível para este dependente.</p>
                    @endforelse

                </div>
            </div>
        </div>

    </div>
</div>

@endsection
