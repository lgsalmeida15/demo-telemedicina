@extends('layouts.app', ['activePage' => 'convenios', 'titlePage' => __('Serviços')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    

                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Serviços</h4>
                            <p class="card-category">Lista de serviços cadastrados</p>
                        </div>
                        <div class="card-body">
                            <div class="table">
                                {{-- Botão de Registro --}}
                                <div style="width: 100%; text-align: end; margin-bottom: 1rem;">
                                    <a href="{{route('convenio.create')}}" class="btn btn-primary">
                                        <i class="material-icons">person_add</i> Cadastrar Novo Serviço
                                    </a>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <form method="GET" action="{{ route('convenio.index') }}" class="mb-3">
                                            <label for="categoria_id" style="color:#4081F6;"><strong>Filtrar por Categoria:</strong></label>
                                            <select name="categoria_id" id="categoria_id" class="form-control" onchange="this.form.submit()">
                                                <option value="">Todas as Categorias</option>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria->id }}" {{ (isset($categoriaId) && $categoriaId == $categoria->id) ? 'selected' : '' }}>
                                                        {{ $categoria->nome }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="statusFilter" style="color:#4081F6;"><strong>Filtrar por Status:</strong></label>
                                        <select id="statusFilter" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="Ativo" selected>Ativo</option>
                                            <option value="Inativo">Inativo</option>
                                        </select>
                                    </div>
                                </div>
                                
                                @if($convenios->isEmpty())
                                    <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                        Sem Serviços Cadastrados
                                    </div>
                                @else
                                    <table class="table" id="convenios-table">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>Data de Início</th>
                                                <th>Status</th>
                                                <th style="text-align: end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($convenios as $convenio)
                                                <tr>
                                                    <td>{{ $convenio->id }}</td>
                                                    <td>{{ $convenio->nome_convenio }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($convenio->data_inicio)->format('d/m/Y') }}</td>
                                                    <td>
                                                        @php
                                                            $status = $convenio->status;
                                                            $cores = [
                                                            'Ativo'     => 'success',
                                                            'Inativo'   => 'danger'
                                                            ];
                                                        @endphp

                                                        <span class="badge badge-{{ $cores[$status] ?? 'secondary' }}">
                                                            {{ $status }}
                                                        </span>
                                                    </td>
                                                    <td style="text-align: end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Opções
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <a class="dropdown-item" href="{{route('convenio.show', ['convenio'=>$convenio->id])}}">Detalhes</a>
                                                                <a class="dropdown-item" href="{{route('convenio.view_edit', ['convenio'=>$convenio->id])}}">Editar</a>
                                                                <button
                                                                    class="dropdown-item"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#deleteModal"
                                                                    data-id="{{$convenio->id}}"
                                                                    data-action="{{route('convenio.delete', ['convenio'=>$convenio->id])}}">
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
                        Tem certeza de que deseja excluir o serviço?
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
            const table = $('#convenios-table').DataTable({
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                },
                order: [[0, "asc"]],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                responsive: false
            });

            
            table.column(3).search('Ativo').draw();

            // Adiciona evento ao dropdown para filtrar
            $('#statusFilter').on('change', function () {
                let selected = $(this).val();

                // Filtra apenas a coluna de status
                table.column(3).search(selected).draw();
            });

            // Estilizar busca
            setTimeout(() => {
                $('input[type="search"]').addClass('form-control');
                $('select[name="convenios-table_length"]').addClass('form-control');
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


@push('css')
    <style>
        #statusFilter, 
        #categoria_id {
            width: 250px;          /* Largura maior */
            height: 40px;          /* Altura maior */
            background-color: #4081F6; /* Verde */
            color: white;          /* Texto branco */
            border: 2px solid #4081F6; /* Borda verde escuro */
            border-radius: 8px;    /* Bordas arredondadas */
            padding: 6px 12px;     /* Espaçamento interno */
            font-size: 1rem;       /* Fonte um pouco maior */
            font-weight: 600;      /* Fonte mais forte */
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        #statusFilter:hover, 
        #categoria_id:hover {
            background-color: #4081F6; /* Verde um pouco mais escuro ao passar o mouse */
            border-color: #4081F6;
        }

        #statusFilter:focus, 
        #categoria_id:focus {
            outline: none;
            box-shadow: 0 0 5px #4081F6;
            border-color: #4081F6;
        }
    </style>
@endpush
