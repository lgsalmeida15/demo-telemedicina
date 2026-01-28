@extends('layouts.app', ['activePage' => 'schedules', 'titlePage' => __('Meus Agendamentos')])

@section('content')
    <style>
        /* Estilos CSS (Atualizados para o novo layout de grid) */

        .glass-card {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12);
            transition: .3s ease;
        }

        .glass-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f6f92;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ======== APPOINTMENT CARD (Card real) ======== */
        .app-card {
            /* Novo Card que usará o grid */
            padding: 1.3rem;
            border-left: 6px solid #4081F6;
            /* Cor de destaque */
            border-radius: 14px;
            background: #ffffff;
            transition: .3s ease;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            height: 100%;
            /* Garante que todos os cards na linha tenham a mesma altura */
        }

        .app-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.12);
        }

        /* Estilo para agendamentos CANCELADOS ou Concluídos */
        .app-card.cancelled {
            border-left-color: #f44336;
            opacity: 0.7;
        }

        .app-card.completed {
            border-left-color: #4caf50;
        }

        .app-label {
            font-size: .8rem;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .app-value {
            font-size: 1.05rem;
            font-weight: 600;
            color: #0d2248;
        }

        /* Estilos para botões de ação (Agora botões circulares apenas com ícone) */
        .actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .btn-action {
            padding: 0;
            margin-left: 10px;
            font-size: 0.8rem;
            font-weight: 600;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-view {
            background: #128643;
            /* Cor Verde */
            height: 40px;
            /* Reduzi o tamanho para caber melhor no card */
            width: 40px;
            color: white !important;
            border-radius: 50%;
        }

        .btn-cancel {
            background: #a81308;
            /* Cor Vermelha */
            height: 40px;
            width: 40px;
            color: white !important;
            border-radius: 50%;
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .btn-add {
            background: #4081F6;
            color: white !important;
            border-radius: 12px;
            padding: 10px 18px;
            font-weight: 600;
            transition: .3s ease;
            box-shadow: 0 4px 12px rgba(64, 129, 246, 0.35);
            display: flex;
            align-items: center;
            gap: 6px;
        }
    </style>

    <div class="content">
        <div class="container-fluid">

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="glass-card p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="section-title">
                                <i class="material-icons">event</i> Seus Agendamentos
                            </h3>
                            <p class="text-muted mb-0">Visualização e gestão das suas teleconsultas.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">


                @forelse ($appointments as $app)
                    @php
                        // Extrair informação
                        $id = $app['appointment_id'];
                        $dateRaw = $app['date'];
                        $specialty = $app['specialty'] ?? 'Especialidade não informada';
                        $doctorName = $app['doctor_name'] ?? 'A definir';
                        $canceledAt = $app['canceled_at'] ?? null;

                        // Normalizar status
                        if ($app['status'] == 1){
                            $statusColor = "#4081F6";
                        }else if ($app['status'] == 5){
                            $statusColor = "#4caf50";
                        }else{
                            $statusColor = "#f44336";
                        }
                        // Formatar data/hora
                        $carbonDate = \Carbon\Carbon::parse($dateRaw);
                        $dateFormat = $carbonDate->format('d/m/Y');
                        $timeFormat = $carbonDate->format('H:i');

                        // Link da sala (se existir)
                        $videoRoom = $app['details_raw']['videoRoomLink'] ?? null;
                    @endphp

                    <div class="col-12 col-sm-6 col-md-4 mb-4">

                        <div class="app-card  @if ($app['status'] == 1)
                                        scheduled
                                    @elseif ($app['status'] == 5)
                                        completed
                                    @else
                                        cancelled
                                    @endif">

                            <div class="d-flex justify-content-between align-items-start mb-3">

                                <div class="text-truncate">
                                    <div class="app-label">Profissional</div>
                                    <div class="app-value">{{ $doctorName }}</div>
                                </div>

                                <span class="status-badge" style="background: {{ $statusColor }}; color:white;">
                                    @if ($app['status'] == 1)
                                        Agendado
                                    @elseif ($app['status'] == 5)
                                        Concluído
                                    @else
                                        Cancelado
                                    @endif
                                </span>

                            </div>


                            <div class="row">
                                <div class="col-6">
                                    <div class="app-label">Especialidade</div>
                                    <div class="app-value">{{ $specialty }}</div>
                                </div>

                                <div class="col-6">
                                    <div class="app-label">Data e Hora</div>
                                    <div class="app-value">
                                        {{ $dateFormat }}<br>às {{ $timeFormat }}
                                    </div>
                                </div>
                            </div>


                            <div class="actions">

                                {{-- ACESSAR SALA E CANCELAR (se agendado) --}}
                                @if ($app['status'] == 1)
                                   <div class="row">
                                        <div class="col-12 mb-2" style="text-align: center;">
                                            <a href="{{ $videoRoom }}" class="btn btn-success btn-action btn-view" target="_blank"
                                                title="Acessar Consulta" style="width: 100%; border-radius: 8px; padding: 10px;">
                                                <i class="material-icons" style="vertical-align: middle;">videocam</i>
                                                Acessar Consulta
                                            </a>
                                        </div>
                                        <div class="col-12" style="text-align: center;">
                                            {{-- BOTÃO CANCELAR --}}
                                            <form method="POST" action="{{ route('beneficiary.area.schedule.cancel') }}" style="display: inline; width: 100%;">
                                                @csrf
                                                <input type="hidden" name="appointment_id" value="{{ $id }}">
                                                <button type="submit" class="btn btn-danger btn-action" 
                                                        onclick="return confirm('Tem certeza que deseja cancelar esta consulta?')"
                                                        title="Cancelar Consulta"
                                                        style="width: 100%; border-radius: 8px; padding: 10px;">
                                                    <i class="material-icons" style="vertical-align: middle;">cancel</i>
                                                    Cancelar Consulta
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty

                    <div class="col-md-12">
                        <div class="glass-card p-4 text-center">
                            <p class="text-muted mb-0">
                                Você não possui agendamentos.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>
            {{-- FIM ROW (LISTA DE AGENDAMENTOS) --}}

        </div>
    </div>

    {{-- Modal de Cancelamento (Mantido) --}}
    <div class="modal fade" id="cancelAppointmentModal" tabindex="-1" role="dialog"
        aria-labelledby="cancelAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="cancelAppointmentForm" method="POST" action="#api-endpoint-cancelar">
                @csrf
                <input type="hidden" name="appointment_id" id="modalAppointmentId">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelAppointmentModalLabel">Confirmar Cancelamento</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-danger">Tem certeza de que deseja **cancelar** esta teleconsulta?</p>
                        <p class="text-muted">Verifique a política de cancelamento antes de confirmar.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Manter Agendamento</button>
                        <button type="submit" class="btn btn-danger">Confirmar Cancelamento</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('js')
        <script>
            // Lógica para preencher o formulário do modal de cancelamento com o ID do agendamento.
            $('#cancelAppointmentModal').on('show.bs.modal', function(event) {
                let button = $(event.relatedTarget);
                let appointmentId = button.data('id');

                // Atualiza o ID no input hidden.
                $(this).find('#modalAppointmentId').val(appointmentId);
            });
        </script>
    @endpush
@endsection
