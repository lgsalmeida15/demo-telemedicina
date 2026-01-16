@extends('layouts.app', ['activePage' => 'beneficiary_dashboard', 'titlePage' => __('Detalhes do Plano')])

@section('content')

<style>
    /* ====== GLASS UI GLOBAL ====== */
    .glass-card {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(12px);
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 8px 26px rgba(0,0,0,0.12);
        transition: 0.3s ease;
    }
    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 34px rgba(0, 0, 0, 0.18);
    }

    .header-box {
        background: linear-gradient(135deg, #4081F6, #8aacec);
        padding: 2rem;
        border-radius: 18px;
        color: white;
        box-shadow: 0 8px 18px rgba(0,0,0,0.18);
    }

    .header-icon i {
        font-size: 70px;
        opacity: .9;
    }

    .section-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #0f6f92;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Info labels */
    .info-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
    }

    .info-value {
        font-size: 1.05rem;
        font-weight: 600;
        color: #0e3d4f;
    }

    /* PLANO */
    .plan-box {
        padding: 1.5rem;
        border-left: 6px solid #4081F6;
        border-radius: 14px;
        background: #fafafa;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        transition: .3s;
    }
    .plan-box:hover {
        transform: translateX(6px);
    }

    .plan-tag {
        background: #4081F6;
        color: #fff;
        padding: 4px 12px;
        font-size: .8rem;
        border-radius: 18px;
        text-transform: uppercase;
    }

    /* SERVIÇOS */
    .service-card {
        padding: 1.2rem;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        border-left: 5px solid #8aacec;
        transition: .3s ease;
    }
    .service-card:hover {
        transform: translateX(6px);
        box-shadow: 0 10px 26px rgba(0,0,0,0.12);
    }

    .service-title {
        font-size: 1.15rem;
        font-weight: bold;
        color: #4081F6;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .service-list li {
        margin-bottom: 4px;
        font-size: .95rem;
    }

    .btn-back {
        color: white;
        background: rgba(255,255,255,0.25);
        padding: 8px 18px;
        border-radius: 30px;
        transition: .25s;
    }
    .btn-back:hover {
        background: rgba(255,255,255,0.4);
        color: #fff;
        transform: translateY(-3px);
    }
</style>

<div class="content">
    <div class="container-fluid">

        <!-- ===========================
            HEADER
        ============================ -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="header-box text-center">

                    <div class="header-icon mb-2">
                        <i class="material-icons">layers</i>
                    </div>

                    <h2 class="fw-bold mb-0">{{ $plan->name }}</h2>

                    <p class="mt-1" style="font-size:1.05rem;">
                        Plano oferecido por <strong>BoxFarma Telemedicina</strong> em parceria com <strong>Elo Serviços S.A.</strong>
                    </p>

                    <a href="{{ route('beneficiary.area.index') }}" class="btn-back mt-3">
                        <i class="material-icons" style="vertical-align: middle;">arrow_back</i>
                        Voltar
                    </a>

                </div>
            </div>
        </div>

        <!-- ===========================
            INFORMAÇÕES DO PLANO
        ============================ -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="glass-card p-4">

                    <h4 class="section-title mb-3">
                        <i class="material-icons text-primary">info</i> Informações do Plano
                    </h4>

                    <div class="row g-4">

                        <div class="col-md-6">
                            <div class="info-label">Nome do Plano</div>
                            <div class="info-value">{{ $plan->name }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-label">Valor Mensal</div>
                            <div class="info-value">
                                R$ {{ number_format($plan->value ?? 0, 2, ',', '.') }}
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="info-label">Descrição</div>
                            <div class="info-value">
                                {{ $plan->description ?? 'Nenhuma descrição informada.' }}
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="info-label">Empresa Responsável</div>
                            <div class="info-value">{{ $plan->company->name ?? '-' }}</div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <div class="info-label">CNPJ</div>
                            <div class="info-value">{{ $plan->company->cnpj ?? '-' }}</div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <!-- ===========================
            SERVIÇOS / CONVÊNIOS
        ============================ -->
        <div class="row mb-4">
            <div class="col-md-12">

                <div class="glass-card p-4">

                    <h4 class="section-title mb-4">
                        <i class="material-icons text-blue">local_offer</i> Serviços Disponíveis
                    </h4>

                    @php
                        $convenios = $plan->conveniences;
                    @endphp

                    @forelse ($convenios as $item)
                        @php
                            $conv = $item->convenio;
                        @endphp

                        <div class="service-card mb-4">

                            <div class="d-flex justify-content-between mb-2">
                                <span class="service-title">
                                    <i class="material-icons">check_circle</i>
                                    {{ $conv->nome_convenio ?? 'Convênio sem nome' }}
                                </span>

                                <span class="plan-tag">
                                    {{ $conv->status ?? 'Ativo' }}
                                </span>
                            </div>

                            <div class="text-muted mb-2">
                                Categoria: <strong>{{ $conv->categoria->nome ?? '-' }}</strong>
                            </div>

                            @if ($conv->descricao)
                                <p class="mb-2">{{ $conv->descricao }}</p>
                            @endif

                            <ul class="service-list list-unstyled">
                                <li><i class="material-icons text-blue" style="font-size:16px;">business</i> Parceiro: {{ $conv->partner->name ?? '-' }}</li>
                                <li><i class="material-icons text-blue" style="font-size:16px;">percent</i> Desconto: {{ $conv->desconto_percentual ? $conv->desconto_percentual.'%' : 'Não informado' }}</li>
                                <li>
                                    <i class="material-icons text-blue" style="font-size:16px;">event</i>
                                    Vigência:  
                                    @if($conv->data_inicio) {{ \Carbon\Carbon::parse($conv->data_inicio)->format('d/m/Y') }} @endif
                                     até 
                                    @if($conv->data_fim) {{ \Carbon\Carbon::parse($conv->data_fim)->format('d/m/Y') }} @endif
                                </li>
                                <li><i class="material-icons text-blue" style="font-size:16px;">call</i> Contato: {{ $conv->contato ?? '-' }}</li>
                                <li><i class="material-icons text-blue" style="font-size:16px;">email</i> Email: {{ $conv->email ?? '-' }}</li>
                                <li><i class="material-icons text-blue" style="font-size:16px;">category</i> Tipo: {{ $conv->type->name ?? '-' }}</li>
                            </ul>

                        </div>

                    @empty
                        <p class="text-muted fst-italic">Nenhum serviço vinculado a este plano.</p>
                    @endforelse

                </div>

            </div>
        </div>

    </div>
</div>

@endsection
