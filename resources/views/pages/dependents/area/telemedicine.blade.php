@extends('layouts.app', ['activePage' => 'telemedicine', 'titlePage' => __('Telemedicina')])

@section('content')

    <style>
        /* ================= GLASS + MODERNO ================= */

        .banner-box {
            background: linear-gradient(135deg, #4081F6, #8aacec);
            border-radius: 18px;
            color: white;
            padding: 2.8rem 2rem;
            text-align: center;
            box-shadow: 0 10px 26px rgba(0, 0, 0, 0.18);
        }

        .banner-icon i {
            font-size: 75px;
            opacity: .95;
        }

        .banner-title {
            font-size: 2.2rem;
            font-weight: 800;
            margin-top: 12px;
        }

        .glass-card {
            margin-top: -40px;
            background: rgba(255, 255, 255, 0.68);
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(12px);
            padding: 2rem;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12);
            transition: .3s;
        }

        .glass-card:hover {
            transform: translateY(-4px);
        }

        .section-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: #0f6f92;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-text {
            font-size: 1.05rem;
            color: #2b2b2b;
            line-height: 1.55rem;
            margin-bottom: 1.2rem;
            font-weight: 500;
        }

        .btn-access {
            background: linear-gradient(135deg, #4081F6, #8aacec);
            padding: 14px 28px;
            color: white;
            font-weight: 700;
            border-radius: 14px;
            font-size: 1.08rem;
            border: none;
            width: 100%;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.18);
            transition: .3s ease;
        }

        .btn-access:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 26px rgba(0, 0, 0, 0.22);
            color: #fff;
        }
    </style>

    <div class="content">
        <div class="container-fluid">
            @if ($errors->any())
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- ================================
                                         BANNER SUPERIOR
                                    ================================= -->
            <div class="row">
                <div class="col-md-12">
                    <div class="banner-box">

                        <div class="banner-icon mb-2">
                            <i class="material-icons">local_hospital</i>
                        </div>

                        <h1 class="banner-title">Telemedicina</h1>
                        <p style="opacity: .9; font-size:1.1rem;">
                            Acesso direto ao atendimento
                        </p>

                    </div>
                </div>
            </div>

            <!-- ================================
                                         GLASS CARD
                                    ================================= -->
            <div class="row justify-content-center">
                <div class="col-md-8">

                    <div class="glass-card">

                        <h4 class="section-title mb-3">
                            <i class="material-icons text-primary">healing</i>
                            Iniciar Telemedicina
                        </h4>

                        <!-- FORM para iniciar atendimento -->
                        <form action="{{ route('dependent.area.telemedicine.redirect') }}" method="POST" target="_blank">
                            @csrf

                            <!-- Especialidade fixa: Clínico Geral -->
                            <input type="hidden" name="especialidade" value="1">
                            <!-- Seleção de Horários Disponíveis -->
                            <label class="form-label mt-3">Horário Disponível</label>
                            <select name="hour" class="form-control" required>
                                @php
                                    use Carbon\Carbon;
                                    $today = now()->format('Y-m-d');
                                    $tomorrow = now()->addDay()->format('Y-m-d');
                                    $validDates = [$today, $tomorrow];
                                @endphp
                                @forelse (data_get($availableHours, 'hours', []) as $item)
                                    @php
                                        // Remove dd()
                                        // Se $item vier como array, pega primeiro valor
                                        if (is_array($item)) {
                                            $item = reset($item);
                                        }
                                        // Garante formato Carbon
                                        try {
                                            $carbon = Carbon::parse($item);
                                        } catch (\Exception $e) {
                                            continue; // se vier inválido, ignora
                                        }
                                        // 🔥 Filtrar HOJE e AMANHÃ
                                        if (!in_array($carbon->format('Y-m-d'), $validDates)) {
                                            continue;
                                        }
                                        $value = $carbon->format('Y-m-d H:i:s'); // para o value do option
                                        $label = $carbon->format('d/m - H:i'); // para exibição ao usuário
                                    @endphp
                                    <option value="{{ $value }}">
                                        {{ $label }}
                                    </option>
                                @empty
                                    <option disabled selected>Nenhum horário disponível</option>
                                @endforelse

                            </select>
                            <!-- BOTÃO PARA INICIAR CONSULTA -->
                            <button type="submit" class="btn-access mt-3" {{ empty($availableHours) ? 'disabled' : '' }}>
                                <i class="material-icons" style="vertical-align: middle;">video_call</i>
                                Acessar Consulta
                            </button>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
