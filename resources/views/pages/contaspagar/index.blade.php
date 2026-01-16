@extends('layouts.app', ['activePage' => 'contas_a_pagar', 'titlePage' => __('Contas a Pagar')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Contas a Pagar</h4>
                            <p class="card-category">Lista de Contas a Pagar cadastradas</p>
                        </div>
                        <div class="card-body">
                            <div class="table">
                                {{-- Botão de Registro --}}
                                <div style="width: 100%; text-align: end; margin-bottom: 1rem;">
                                    <a href="{{route('conta_pagar.create')}}" class="btn btn-primary">
                                        <i class="material-icons">add</i> Nova Conta a Pagar
                                    </a>
                                </div>
                                @if($contas->isEmpty())
                                    <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                        Sem Contas a Pagar Cadastradas
                                    </div>
                                @else
                                    <table class="table" id="contas-table">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>ID</th>
                                                <th>Documento</th>
                                                <th>Valor</th>
                                                <th>Status</th>
                                                <th>Pagar Conta</th>
                                                <th style="text-align: end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($contas as $conta)
                                                <tr>
                                                    <td>{{ $conta->id }}</td>
                                                    <td><a href="{{ asset('storage/' . $conta->documento) }}" target="_blank">
                                                            Ver Conta
                                                        </a>
                                                    </td>
                                                    <td>R${{ $conta->valor }}</td>
                                                    <td>
                                                        @php
                                                            $status = $conta->status_pagamento;
                                                            $cores = [
                                                                'Pago'      => 'success',
                                                                'Não Pago'   => 'danger',
                                                            ];
                                                        @endphp

                                                        <span class="badge badge-{{ $cores[$status] ?? 'secondary' }}">
                                                            {{ $status }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if ($conta->status_pagamento!=='Pago')
                                                            <form action="{{ route('conta_pagar.pay', ['conta' => $conta->id]) }}" method="POST" style="display:inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-primary">
                                                                    Pagar
                                                                </button>
                                                            </form>
                                                        @else
                                                            Já foi Pago
                                                        @endif
                                                        
                                                    </td>
                                                    <td style="text-align: end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Opções
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <a class="dropdown-item" href="{{ route('conta_pagar.show', ['conta'=>$conta->id]) }}">Detalhes</a>
                                                                <a class="dropdown-item" href="{{ route('conta_pagar.view_edit',['conta'=>$conta->id]) }}">Editar</a>
                                                                <button
                                                                    class="dropdown-item"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#deleteModal"
                                                                    data-id="{{ $conta->id }}"
                                                                    data-action="{{ route('conta_pagar.softdelete', ['conta'=>$conta->id]) }}">
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

    <!-- Modal para Apagar -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Tem certeza de que deseja excluir esta conta a pagar?
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
            const table = $('#contas-table').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                order: [[0, "asc"]],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                responsive: false // Desativa o modo responsivo que pode esconder colunas
            });

            // Estilizar o campo de busca com classe do Material
            setTimeout(() => {
                $('input[type="search"]').addClass('form-control');
                $('select[name="contas-table_length"]').addClass('form-control');
            }, 200);
        });
    </script>
@endpush

{{-- Para exclusão --}}
@push('js')
<script>
    $('#deleteModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let action = button.data('action'); // já vem pronta
        $(this).find('#deleteForm').attr('action', action);
    });
</script>
@endpush
