@extends('layouts.app', ['activePage' => 'beneficiaries', 'titlePage' => __('Beneficiários')])

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
                            <h4 class="card-title">Visão Geral de Beneficiários</h4>
                            <p class="card-category">Número Total de Beneficiários: {{ $beneficiaries->total() }}</p>
                        </div>
                        <div class="card-body">
                            {{-- FORMULÁRIO DE PESQUISA POR NOME E CPF --}}
                            <form action="{{route('beneficiary.general.index')}}" method="GET" class="mb-4">
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="search_name" class="bmd-label-floating">Buscar por Nome</label>
                                            <input type="text" name="name" id="search_name" class="form-control" value="{{ request('name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label for="search_cpf" class="bmd-label-floating">Buscar por CPF</label>
                                            <input type="text" name="cpf" id="search_cpf" class="form-control" value="{{ request('cpf') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center">
                                        <button type="submit" class="btn btn-primary btn-sm mt-3">Pesquisar</button>
                                        {{-- Botão para limpar a pesquisa --}}
                                        @if(request('name') || request('cpf'))
                                            <a href="{{route('beneficiary.general.index')}}" class="btn btn-link btn-sm mt-3 text-danger">Limpar</a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                            {{-- FIM DO FORMULÁRIO DE PESQUISA --}}

                            {{-- Container para alinhar botões de Importar e Cadastrar (mantido caso precise adicionar no futuro) --}}
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                                <div class="float-right d-flex flex-wrap">
                                    <div id="datatable-buttons" class="d-flex flex-wrap">
                                        {{-- DataTables não injetará mais nada aqui após a alteração no 'dom' --}}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="table-responsive">
                                @if($beneficiaries->isEmpty())
                                    <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                        Nenhum Beneficiário Encontrado
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
                                                    <td>{{ $beneficiary->name }}</td>
                                                    <td>{{ $beneficiary->cpf }}</td>
                                                    <td>{{ $beneficiary->email ?? '--'}}</td>
                                                    <td>{{ $beneficiary->company->plans->count() }}</td>
                                                    <td>{{\Carbon\Carbon::parse( $beneficiary->inclusion_date )->format('d/m/Y')}}</td>
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
                                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton"> {{-- ADICIONADO: dropdown-menu-right para alinhar à direita --}}
                                                                <a class="dropdown-item" href="{{route('dependent.index', ['beneficiaryId'=>$beneficiary->id])}}">Ver Dependentes</a>
                                                                <a class="dropdown-item" href="{{route('beneficiary.show', ['beneficiary'=>$beneficiary->id])}}">Detalhes</a>
                                                                <a class="dropdown-item" href="{{route('beneficiary.edit', ['beneficiary'=>$beneficiary->id])}}">Editar</a>
                                                                <button
                                                                    class="dropdown-item text-danger" {{-- ADICIONADO: text-danger para botão de Excluir --}}
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
                            
                            {{-- AJUSTADO: Renderiza os links de paginação do Laravel --}}
                            <div class="d-flex justify-content-center mt-4">
                                {{-- withQueryString garante que os filtros de name/cpf sejam mantidos ao trocar de página --}}
                                {{ $beneficiaries->withQueryString()->links('pagination::bootstrap-4') }} {{-- ADICIONADO: Usa o template bootstrap-4 para o estilo --}}
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
@endsection

@push('css')
    {{-- Mantive apenas os CSSs necessários para o DataTables sem os botões de exportação --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    
    {{-- ADICIONADO: Estilos para melhorar a aparência e centralização da paginação do Laravel --}}
    <style>
        /* Oculta a área de controle de paginação do DataTables */
        #beneficiaries-table_wrapper .row:last-child {
            display: none !important; 
        }

        /* Estilo para centralizar e melhorar a aparência da paginação do Laravel */
        .pagination {
            margin: 0;
            display: flex; /* Garante que os itens fiquem lado a lado */
            padding-left: 0;
            list-style: none;
            border-radius: .25rem;
        }

        .page-item .page-link {
            color: #0a6808; /* Cor primária do seu tema, geralmente roxo */
            border: 1px solid #dee2e6;
            margin: 0 2px;
            border-radius: 4px !important;
            padding: 8px 15px;
            transition: all 0.3s;
        }

        .page-item.active .page-link {
            background-color: #0a6808;
            border-color: #075205;
            color: #fff;
            box-shadow: 0 4px 20px 0 rgba(0, 0, 0,.14), 0 7px 10px -5px rgba(156, 39, 176,.4);
        }

        .page-item:not(.active) .page-link:hover {
            background-color: #f8f9fa;
            color: #23d316;
        }

        .page-item.disabled .page-link {
            color: #888;
            pointer-events: none;
            background-color: #eee;
            border-color: #dee2e6;
        }
    </style>
@endpush

@push('js')
    {{-- jQuery já vem com o template Material Dashboard --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function () {
            // Inicialização do DataTables
            const table = $('#beneficiaries-table').DataTable({
                // dom: 'rt' -> APENAS processamento e tabela. Isso desativa a paginação e a busca interna do DataTables.
                dom: 'rt', 
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                // Desabilitar ordenação na última coluna (Ações)
                columnDefs: [
                    { "orderable": false, "targets": [7] }
                ],
                order: [[0, "asc"]],
                responsive: false
            });

            // Garante que o container de botões DataTables não cause problemas de layout
            setTimeout(() => {
                $('#datatable-buttons').empty(); 
            }, 200);
        });
    </script>

    <script>
        // Lógica para preencher o formulário do modal de exclusão (Mantida)
        $('#deleteBeneficiaryModal').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget);
            let action = button.data('action');
            $(this).find('#deleteBeneficiaryForm').attr('action', action);
        });
    </script>
@endpush
