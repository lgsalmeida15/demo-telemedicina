@extends('layouts.app', ['activePage' => 'produtos_unidades', 'titlePage' => __('Unidades')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    

                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title"><i class="material-icons">scale</i> Unidades de Produtos</h4>
                        </div>
                        <div class="card-body">
                            <div class="table">
                                {{-- Botão de Registro --}}
                                <div style="width: 100%; text-align: end; margin-bottom: 1rem;">
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#createUnidadeModal">
                                        <i class="material-icons">add</i> Adicionar Unidade
                                    </button>
                                </div>
                                @if($unidades->isEmpty())
                                    <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                        Sem Unidades Cadastradas
                                    </div>
                                @else
                                    <table class="table" id="unidades-table">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>ID</th>
                                                <th>Sigla</th>
                                                <th>Descrição</th>
                                                <th style="text-align: end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($unidades as $unidade)
                                                <tr>
                                                    <td>{{ $unidade->id }}</td>
                                                    <td>{{ $unidade->sigla }}</td>
                                                    <td>{{ $unidade->descricao}}</td>
                                                    <td style="text-align: end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Opções
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <a
                                                                    class="dropdown-item"
                                                                    href="#"
                                                                    data-toggle="modal"
                                                                    data-target="#editUnidadeModal"
                                                                    data-id="{{ $unidade->id }}"
                                                                    data-sigla="{{ $unidade->sigla }}"
                                                                    data-descricao="{{ $unidade->descricao }}"
                                                                    data-action="{{ route('produtos.unidades.update', ['unidade' => $unidade->id]) }}"
                                                                >
                                                                    Editar
                                                                </a>
                                                                <button
                                                                    class="dropdown-item"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#deleteModal"
                                                                    data-id="{{$unidade->id}}"
                                                                    data-action="{{route('produtos.unidades.softdelete', ['unidade'=>$unidade->id])}}"
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

            <!-- Modais (Adicionar e Editar) -->
            @include('pages.produtos.unidades.modals')

        </div>
    </div>


    <!-- Modal para Apagar-->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Tem certeza de que deseja excluir a unidade do produto?
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


{{-- Para exclusão --}}
@push('js')
<script>
    $('#deleteModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let action = button.data('action'); // já vem pronta
        $(this).find('#deleteForm').attr('action', action);
    });
</script>

<script>
    $('#editUnidadeModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget); // botão que abriu o modal

        let id = button.data('id');
        let sigla = button.data('sigla');
        let descricao = button.data('descricao');
        let action = button.data('action');

        let modal = $(this);
        modal.find('#edit_sigla').val(sigla);
        modal.find('#edit_descricao').val(descricao);
        modal.find('#editUnidadeForm').attr('action', action);
    });
</script>
@endpush



