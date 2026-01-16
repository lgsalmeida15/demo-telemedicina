@extends('layouts.app', ['activePage' => 'companies', 'titlePage' => __('Empresas')])

@section('content')
    <div class="content">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
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
                            <h4 class="card-title">Empresas</h4>
                            <p class="card-category">Número Total de Empresas: {{$companies->count()}}</p>
                        </div>
                        <div class="card-body">
                            <div class="table">
                                
                                {{-- Botão de Registro --}}
                                <div style="width: 100%; text-align: end; margin-bottom: 1rem;">
                                    {{-- <a href="{{route('company.report')}}" class="btn btn-report-print" >
                                        <i class="material-icons">file_save</i> Relatório
                                    </a> --}}
                                    <a href="{{route('company.form')}}" class="btn btn-primary">
                                        <i class="material-icons">add_box</i> Cadastrar Nova Empresa
                                    </a>
                                </div>
                                
                                @if($companies->isEmpty())
                                    <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                        Sem Empresas Cadastradas
                                    </div>
                                @else
                                    <table class="table" id="companies-table">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>CNPJ</th>
                                                <th>E-mail</th>
                                                <th>UF</th>
                                                <th style="text-align: end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($companies as $company)
                                                <tr>
                                                    <td>{{ $company->id }}</td>
                                                    <td>{{ $company->name }}</td>
                                                    <td>{{ $company->cnpj }}</td>
                                                    <td>{{ $company->email }}</td>
                                                    <td>{{ $company->uf }}</td>
                                                    <td style="text-align: end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Opções
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <a class="dropdown-item" href="{{route('plan.index', ['company'=>$company->id])}}">Ver Planos</a>
                                                                <a class="dropdown-item" href="{{route('beneficiary.index',['company'=>$company->id])}}">Ver Beneficiários</a>
                                                                <a class="dropdown-item" href="{{route('company.show', ['company'=>$company->id])}}">Detalhes</a>
                                                                <a class="dropdown-item" href="{{route('company.edit', ['company'=>$company->id])}}">Editar</a>
                                                                <a class="dropdown-item" href="{{route('checkout.landing', ['uuid'=>$company->uuid])}}">Check-Out</a>
                                                                <button
                                                                    class="dropdown-item"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#deleteCompanyModal"
                                                                    data-id="{{ $company->id }}"
                                                                    data-action="{{ route('company.softdelete', ['company' => $company->id]) }}">
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
    
    <!-- Modal para Apagar-->
    <div class="modal fade" id="deleteCompanyModal" tabindex="-1" role="dialog" aria-labelledby="deleteCompanyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="deleteCompanyForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteCompanyModalLabel">Confirmar Exclusão</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Tem certeza de que deseja excluir esta empresa?
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <style>
        /* Estilos adicionais para o novo botão de relatório (opcional) */
        .btn-report-print {
            background-color: #ffc107 !important; /* Cor amarela do PDF */
            border-color: #ffc107 !important;
            color: #212529 !important;
        }
    </style>
@endpush

@push('js')
    {{-- jQuery já vem com o template Material Dashboard --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function () {
            const table = $('#companies-table').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                order: [[0, "asc"]],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                responsive: false
            });

            // Estilizar busca
            setTimeout(() => {
                $('input[type="search"]').addClass('form-control');
                $('select[name="companies-table_length"]').addClass('form-control');
            }, 200);
        });
    </script>
@endpush

{{-- Para exclusão --}}
@push('js')
<script>
    $('#deleteCompanyModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let action = button.data('action'); // já vem pronta
        $(this).find('#deleteCompanyForm').attr('action', action);
    });
</script>
@endpush
