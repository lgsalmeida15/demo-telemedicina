@extends('layouts.app', ['activePage' => 'companies', 'titlePage' => __('Empresas')])

@section('content')
@php
    // Mapeamento de Action para texto e classe de cor
    $actionMap = [
        'I' => ['label' => 'Inclusão', 'class' => 'text-white bg-success'], // Verde
        'M' => ['label' => 'Manutenção', 'class' => 'text-dark bg-warning'], // Amarelo
        'E' => ['label' => 'Exclusão', 'class' => 'text-white bg-danger'], // Vermelho
    ];
@endphp
    <div class="content">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Beneficiários da Empresa: {{$company->name}}</h4>
                            <p class="card-category">Número Total de Beneficiários: {{$beneficiaries->count()}}</p>
                        </div>
                        <div class="card-body">
                            {{-- Container para alinhar botões de Exportar, Importar e Cadastrar --}}
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                                <div id="datatable-buttons" class="mb-2 mb-md-0">
                                    <!-- Os botões DataTables serão injetados aqui via JS -->
                                </div>
                                <div class="float-right d-flex flex-wrap">
                                    <a href="{{route('company.index')}}" class="btn btn-secondary">
                                        <i class="material-icons">arrow_back</i> Voltar
                                    </a>
                                    {{-- NOVO BOTÃO DE IMPORTAR --}}
                                    {{-- <button type="button" class="btn btn-warning mr-2 mb-2" data-toggle="modal" data-target="#importBeneficiaryModal">
                                        <i class="material-icons">cloud_upload</i> Importar Excel
                                    </button> --}}

                                    <a href="{{route('beneficiary.form',['company'=>$company->id])}}" class="btn btn-primary mb-2">
                                        <i class="material-icons">person_add</i> Cadastrar Novo Beneficiário
                                    </a>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                @if($beneficiaries->isEmpty())
                                    <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                        Nenhum Beneficiário Cadastrado
                                    </div>
                                @else
                                    <table class="table" id="beneficiaries-table">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>CPF</th>
                                                <th>E-Mail</th>
                                                <th>Planos</th>
                                                <th>Data de Inclusão</th>
                                                <th>Status</th>
                                                <th style="text-align: end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($beneficiaries as $beneficiary)
                                                <tr>
                                                    <td>{{ $beneficiary->id }}</td>
                                                    <td>
                                                        {{ $beneficiary->name }}
                                                        @if(isset($beneficiary->is_demo) && $beneficiary->is_demo)
                                                            <span class="badge badge-warning ml-2">DEMO</span>
                                                            @if(isset($beneficiary->demo_expires_at) && $beneficiary->demo_expires_at)
                                                                @php
                                                                    try {
                                                                        $expiresAt = \Carbon\Carbon::parse($beneficiary->demo_expires_at);
                                                                        $isExpired = $expiresAt->isPast();
                                                                        $daysRemaining = now()->diffInDays($expiresAt, false);
                                                                    } catch (\Exception $e) {
                                                                        $isExpired = false;
                                                                        $daysRemaining = 0;
                                                                    }
                                                                @endphp
                                                                @if($isExpired)
                                                                    <span class="badge badge-danger ml-1">EXPIRADO</span>
                                                                @elseif($daysRemaining > 0)
                                                                    <span class="badge badge-info ml-1">{{ $daysRemaining }}d restantes</span>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td>{{ $beneficiary->cpf }}</td>
                                                    <td>{{ $beneficiary->email ?? '--'}}</td>
                                                    <td>{{ $beneficiary->plans->count() }}</td>
                                                    <td>{{\Carbon\Carbon::parse( $beneficiary->created_at )->format('d/m/Y')}}</td>
                                                    <td>
                                                        <span class="badge {{ $beneficiary->plan_status_view['class'] }}"
                                                            style="padding: 6px 10px; border-radius: 4px; font-weight: 600;">
                                                            {{ $beneficiary->plan_status_view['label'] }}
                                                        </span>
                                                    </td>
                                                    <td style="text-align: end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Opções
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <a class="dropdown-item" href="{{route('dependent.index', ['beneficiaryId'=>$beneficiary->id])}}">Ver Dependentes</a>
                                                                <a class="dropdown-item" href="{{route('beneficiary.show', ['beneficiary'=>$beneficiary->id])}}">Detalhes</a>
                                                                <a class="dropdown-item" href="{{route('beneficiary.edit', ['beneficiary'=>$beneficiary->id])}}">Editar</a>
                                                                <button
                                                                    class="dropdown-item"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#deleteBeneficiaryModal"
                                                                    data-id="{{ $beneficiary->id }}"
                                                                    data-action="{{ route('beneficiary.softdelete', ['beneficiary'=>$beneficiary->id]) }}">
                                                                    Excluir
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para Exclusão-->
    <div class="modal fade" id="deleteBeneficiaryModal" tabindex="-1" role="dialog" aria-labelledby="deleteBeneficiaryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="deleteBeneficiaryForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteBeneficiaryModalLabel">Confirmar Exclusão</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Tem certeza de que deseja excluir o beneficiário?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- NOVO MODAL PARA IMPORTAÇÃO -->
    <div class="modal fade" id="importBeneficiaryModal" tabindex="-1" role="dialog" aria-labelledby="importBeneficiaryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('beneficiary.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header card-header-primary">
                        <h5 class="modal-title text-white" id="importBeneficiaryModalLabel">Importar Beneficiários (Excel/CSV)</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p class="text-danger">
                            O arquivo deve seguir o cabeçalho padrão (AÇÃO, NOME, NASCIMENTO, CPF, SEXO, VALOR, VINCULO, NOME DA MÃE) para garantir a correta importação.
                        </p>

                        <!-- SELECT DE PLANOS -->
                        <div class="form-group">
                            <label for="plan_id">Selecione o Plano:</label>
                            <select name="plan_id" id="plan_id" class="form-control" required>
                                <option value="">-- Escolha um plano --</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- UPLOAD DE ARQUIVO -->
                        <div class="form-group mt-3">
                            <label for="excel_file">Selecione o arquivo (.xlsx ou .csv):</label>
                            <input type="file" name="excel_file" id="excel_file" class="form-control-file" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Importar Dados</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <style>
        /* Estilos para o alinhamento dos botões (compatível com Bootstrap 4) */
        .d-flex.justify-content-between {
            display: flex!important;
            justify-content: space-between!important;
        }
        .align-items-center {
            align-items: center!important;
        }
        .mb-4 {
            margin-bottom: 1.5rem!important;
        }
        /* Ajuste fino para os botões do DataTables */
        #datatable-buttons .dt-button {
            background-color: #28a745; /* Cor verde para o botão de sucesso/exportação */
            color: white;
            border: none;
            border-radius: .25rem;
            padding: .5rem 1rem;
            text-transform: uppercase;
            font-size: 0.75rem; /* Ajuste para o estilo Material Dashboard */
            font-weight: 400;
            box-shadow: 0 2px 2px 0 rgba(40, 167, 69, 0.14), 0 3px 1px -2px rgba(40, 167, 69, 0.2), 0 1px 5px 0 rgba(40, 167, 69, 0.12);
        }
        .mr-2 {
            margin-right: 0.5rem!important;
        }
        .mb-2 {
            margin-bottom: 0.5rem!important;
        }
    </style>
@endpush

@push('js')
    {{-- jQuery já vem com o template Material Dashboard --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    {{-- Dependências DataTables Buttons --}}
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function () {
            // Inicialização do DataTables com a configuração de Botões (B)
            const table = $('#beneficiaries-table').DataTable({
                // Adiciona 'B' para Buttons na estrutura DOM
                dom: 'lfrtipB', 
                buttons: [],
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                order: [[0, "asc"]],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                responsive: false
            });

            // Ajuste do layout após a inicialização do DataTables
            setTimeout(() => {
                // Estiliza a busca e o seletor de quantidade de itens por página
                $('input[type="search"]').addClass('form-control');
                $('select[name="beneficiaries-table_length"]').addClass('form-control');

                // Move os botões DataTables (Excel) para o container dedicado (#datatable-buttons)
                // Isso garante que ele fique alinhado com os botões de ação manual.
                table.buttons().container().appendTo('#datatable-buttons');
                
                // Remove o float e alinha os elementos dentro da div de botões
                $('#datatable-buttons').find('.dt-buttons').css({
                    'float': 'none',
                    'margin-right': '10px' 
                });

            }, 200);
        });
    </script>

    <script>
        // Lógica para preencher o formulário do modal de exclusão
        $('#deleteBeneficiaryModal').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget);
            let action = button.data('action');
            $(this).find('#deleteBeneficiaryForm').attr('action', action);
        });
    </script>
@endpush
