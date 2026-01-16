@extends('layouts.app', ['activePage' => 'partners', 'titlePage' => __('Parceiros')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Parceiros</h4>
                            <p class="card-category">Número Total de Parceiros: {{ $partners->count() }}</p>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                {{-- Botão de Registro --}}
                                <div style="width: 100%; text-align: end; margin-bottom: 1rem;">
                                    <a class="btn btn-primary" href="{{ route('partner.create') }}">
                                        <i class="material-icons">add</i> Cadastrar Novo Parceiro
                                    </a>
                                </div>
                                @if ($partners->isEmpty())
                                    <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                        Nenhum Parceiro Cadastrado
                                    </div>
                                @else
                                    <table class="table" id="partners-table">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>Código</th>
                                                <th>Nome</th>
                                                <th>CNPJ</th>
                                                <th>Email</th>
                                                <th>Data de Cadastro</th>
                                                <th style="text-align: end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($partners as $partner)
                                                <tr>
                                                    <td>{{ $partner->id }}</td>
                                                    <td>{{ $partner->name }}</td>
                                                    <td>{{ $partner->cnpj }}</td>
                                                    <td>{{ $partner->email ?? '-' }}</td>
                                                    <td>{{\Carbon\Carbon::parse( $partner->created_at )->format('d/m/Y')}}</td>
                                                    <td style="text-align: end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Opções
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <a class="dropdown-item" href="{{ route('partner.show', ['partner' => $partner]) }}">Detalhes</a>
                                                                <a class="dropdown-item" href="{{ route('partner.edit', ['partner' => $partner->id]) }}">Editar</a>
                                                                <button
                                                                    class="dropdown-item"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#deletePartnerModal"
                                                                    data-id="{{ $partner->id }}"
                                                                    data-action="{{ route('partner.softdelete', ['partner' => $partner->id]) }}"
                                                                >
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
    <div class="modal fade" id="deletePartnerModal" tabindex="-1" role="dialog" aria-labelledby="deletePartnerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="deletePartnerForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePartnerModalLabel">Confirmar Exclusão</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Tem certeza de que deseja excluir o fornecedor?
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
@endpush

@push('js')
    {{-- jQuery já vem com o template Material Dashboard --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function () {
            const table = $('#partners-table').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                order: [[0, "asc"]],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                responsive: false
            });

            // Estilizar o campo de busca com classe do Material
            setTimeout(() => {
                $('input[type="search"]').addClass('form-control');
                $('select[name="partners-table_length"]').addClass('form-control');
            }, 200);
        });

        $('#deletePartnerModal').on('show.bs.modal', function (event) {
            let button = $(event.relatedTarget);
            let action = button.data('action');
            $(this).find('#deletePartnerForm').attr('action', action);
        });
    </script>
@endpush
