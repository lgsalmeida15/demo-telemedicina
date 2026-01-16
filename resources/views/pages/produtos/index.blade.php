@extends('layouts.app', ['activePage' => 'produtos_lista', 'titlePage' => __('Lista de Produtos')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title"><i class="material-icons">shopping_bag</i> Produtos</h4>
                        </div>
                        <div class="card-body">
                            <div class="table">
                                {{-- Botão de Registro --}}
                                <div style="width: 100%; text-align: end; margin-bottom: 1rem;">
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#createProdutoModal">
                                        <i class="material-icons">add</i> Adicionar Produto
                                    </button>
                                </div>
                                @if($produtos->isEmpty())
                                    <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                        Sem Produtos Cadastrados
                                    </div>
                                @else
                                    <table class="table" id="produtos-table">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>Valor</th>
                                                <th>Cód. de Barras</th>
                                                <th style="text-align: end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($produtos as $produto)
                                                <tr>
                                                    <td>{{ $produto->id }}</td>
                                                    <td>{{ $produto->nome}}</td>
                                                    <td>{{ $produto->valor}}</td>
                                                    <td>{{ $produto->codigo_de_barras}}</td>
                                                    <td style="text-align: end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Opções
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <a
                                                                    class="dropdown-item"
                                                                    href=""
                                                                    >
                                                                    Detalhes
                                                                </a>
                                                                <a
                                                                    class="dropdown-item"
                                                                    href=""
                                                                    >
                                                                    Editar
                                                                </a>
                                                                <button
                                                                    class="dropdown-item"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#deleteModal"
                                                                    data-id="{{$produto->id}}"
                                                                    data-action=""
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
                        Tem certeza de que deseja excluir a produto do produto?
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
@endpush



