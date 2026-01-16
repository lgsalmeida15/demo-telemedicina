@extends('layouts.app', ['activePage' => 'produtos_grupos', 'titlePage' => __('Grupos')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    

                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title"><i class="material-icons">view_list</i> Grupos de Produtos</h4>
                        </div>
                        <div class="card-body">
                            <div class="table">
                                {{-- Botão de Registro --}}
                                <div style="width: 100%; text-align: end; margin-bottom: 1rem;">
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#createGrupoModal">
                                        <i class="material-icons">add</i> Adicionar Grupo
                                    </button>
                                </div>
                                @if($grupos->isEmpty())
                                    <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                        Sem Grupos Cadastrados
                                    </div>
                                @else
                                    <table class="table" id="grupos-table">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>ID</th>
                                                <th>Descrição</th>
                                                <th style="text-align: end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($grupos as $grupo)
                                                <tr>
                                                    <td>{{ $grupo->id }}</td>
                                                    <td>{{ $grupo->descricao}}</td>
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
                                                                    data-target="#editGrupoModal"
                                                                    data-id="{{ $grupo->id }}"
                                                                    data-descricao="{{ $grupo->descricao }}"
                                                                    data-action="{{ route('produtos.grupos.update', ['grupo' => $grupo->id]) }}"
                                                                >
                                                                    Editar
                                                                </a>
                                                                <button
                                                                    class="dropdown-item"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#deleteModal"
                                                                    data-id="{{$grupo->id}}"
                                                                    data-action="{{route('produtos.grupos.softdelete', ['grupo'=>$grupo->id])}}"
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
            @include('pages.produtos.grupos.modals')

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
                        Tem certeza de que deseja excluir a grupo do produto?
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
    $('#editGrupoModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget); // botão que abriu o modal

        let id = button.data('id');
        let descricao = button.data('descricao');
        let action = button.data('action');

        let modal = $(this);
        modal.find('#edit_descricao').val(descricao);
        modal.find('#editGrupoForm').attr('action', action);
    });
</script>
@endpush



