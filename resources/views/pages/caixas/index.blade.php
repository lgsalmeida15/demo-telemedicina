@extends('layouts.app', ['activePage' => 'caixa', 'titlePage' => __('Caixas')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    

                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Caixas</h4>
                        </div>
                        <div class="card-body">
                            <div class="table">
                                {{-- Botão de Registro --}}
                                <div style="width: 100%; text-align: end; margin-bottom: 1rem;">
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                                        <i class="material-icons">add</i> Adicionar Caixa
                                    </button>
                                </div>
                                @if($caixas->isEmpty())
                                    <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                        Sem Caixas Cadastrados
                                    </div>
                                @else
                                    <table class="table" id="caixas-table">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>ID</th>
                                                <th>Descrição</th>
                                                <th style="text-align: end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($caixas as $caixa)
                                                <tr>
                                                    <td>{{ $caixa->id }}</td>
                                                    <td>{{ $caixa->descricao }}</td>
                                                    <td style="text-align: end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Opções
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <button
                                                                    class="dropdown-item"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#showModal"
                                                                    data-id="{{$caixa->id}}"
                                                                    data-descricao="{{$caixa->descricao}}"
                                                                    data-obs="{{$caixa->obs}}"
                                                                    data-cadastro="{{ $caixa->cadastro }}"
                                                                    data-atualizacao="{{ $caixa->atualizacao }}"
                                                                    data-exclusao="{{ $caixa->exclusao }}"
                                                                    data-action=""
                                                                    >
                                                                    Detalhes
                                                                </button>
                                                                <button
                                                                    class="dropdown-item"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#updateModal"
                                                                    data-descricao = "{{$caixa->descricao}}"
                                                                    data-obs = "{{$caixa->obs}}"
                                                                    data-action="{{route('caixa.update', ['caixa'=>$caixa->id])}}"
                                                                    >
                                                                    Editar
                                                                </button>
                                                                <button
                                                                    class="dropdown-item"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#deleteModal"
                                                                    data-id="{{$caixa->id}}"
                                                                    data-action="{{route('caixa.delete', ['caixa'=>$caixa->id])}}"
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
            @include('pages.caixas.modals')

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
                        Tem certeza de que deseja excluir esse caixa?
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
            const table = $('#caixas-table').DataTable({
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
                $('select[name="beneficiaries-table_length"]').addClass('form-control');
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


{{-- Para Atualização --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#updateModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget); // Botão que acionou o modal
            const descricao = button.data('descricao');
            const obs = button.data('obs');
            const action = button.data('action');

            const modal = $(this);
            modal.find('#descricao').val(descricao);
            modal.find('#obs').val(obs);
            modal.find('#formUpdate').attr('action', action);
        });
    });
</script>


{{-- para exibição --}}
@push('js')
<script>
    $('#showModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);

        $('#show_descricao').text(button.data('descricao') ?? '—');
        $('#show_obs').text(button.data('obs') ?? '—');

        // Datas formatadas (opcional: se quiser melhorar visualmente)
        const formatDateTime = (value) => value ? new Date(value).toLocaleString('pt-BR') : '—';

        $('#show_cadastro').text(formatDateTime(button.data('cadastro')));
        $('#show_atualizacao').text(formatDateTime(button.data('atualizacao')));
        $('#show_exclusao').text(formatDateTime(button.data('exclusao')));
    });
</script>
@endpush
