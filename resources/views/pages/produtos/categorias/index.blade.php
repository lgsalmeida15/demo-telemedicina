@extends('layouts.app', ['activePage' => 'produtos_categorias', 'titlePage' => __('Categorias')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    

                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title"><i class="material-icons">category</i> Categorias de Produtos</h4>
                        </div>
                        <div class="card-body">
                            <div class="table">
                                {{-- Botão de Registro --}}
                                <div style="width: 100%; text-align: end; margin-bottom: 1rem;">
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#createCategoriaModal">
                                        <i class="material-icons">add</i> Adicionar Categoria
                                    </button>
                                </div>
                                @if($categorias->isEmpty())
                                    <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                        Sem Categorias Cadastradas
                                    </div>
                                @else
                                    <table class="table" id="convenios-table">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>ID</th>
                                                <th>Descrição</th>
                                                <th style="text-align: end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categorias as $categoria)
                                                <tr>
                                                    <td>{{ $categoria->id }}</td>
                                                    <td>{{ $categoria->descricao}}</td>
                                                    <td>
                                                        <button
                                                            class="btn btn-danger"
                                                            type="button"
                                                            data-toggle="modal"
                                                            data-target="#deleteModal"
                                                            data-id="{{$categoria->id}}"
                                                            data-action="{{route('produtos.categorias.softdelete', ['categoria'=>$categoria->id])}}">
                                                            Excluir
                                                        </button>
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
            @include('pages.produtos.categorias.modals')

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
                        Tem certeza de que deseja excluir a categoria do produto?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </div>
                </div>
            </form>

            @include('pages.produtos.categorias.modals')

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
@endpush

