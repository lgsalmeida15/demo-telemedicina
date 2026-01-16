@extends('layouts.app', ['activePage' => 'plano_contas', 'titlePage' => __('Planos de Contas')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    

                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Planos de Contas</h4>
                            <p class="card-category"></p>
                        </div>
                        <div class="card-body">
                            <div class="table">
                                {{-- Botão de Registro --}}
                                <div style="width: 100%; text-align: end; margin-bottom: 1rem;">
                                    <button class="btn btn-primary btn-round" data-toggle="modal" data-target="#addCostCenterModal">
                                        <i class="material-icons">add</i> Adicionar
                                    </button>
                                </div>
                                @if($costCenters->isEmpty())
                                    <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                        Sem Planos de Contas Cadastrados
                                    </div>
                                @else
                                    <table class="table" id="convenios-table">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>Código</th>
                                                <th>Usuário</th>
                                                <th>Descrição</th>
                                                <th>Data de Cadastro</th>
                                                <th style="text-align: end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($costCenters as $costCenter)
                                                <tr>
                                                    <td>{{$costCenter->id}}</td>
                                                    <td>{{$costCenter->usuario->name ?? '-'}}</td>
                                                    <td>{{$costCenter->descricao}}</td>
                                                    <td>{{\Carbon\Carbon::parse($costCenter->cadastro)->format('d/m/Y')}}</td>
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
                                                                    data-target="#showCostCenterModal"
                                                                    data-usuario="{{ $costCenter->usuario?->name }}"
                                                                    data-codigoreduzido="{{ $costCenter->codigo_reduzido }}"
                                                                    data-codigoconta="{{ $costCenter->codigo_conta }}"
                                                                    data-descricao="{{ $costCenter->descricao }}"
                                                                    data-tipo="{{ $costCenter->tipo }}"
                                                                    data-cadastro="{{ $costCenter->cadastro }}"
                                                                    data-atualizacao="{{ $costCenter->atualizacao }}"
                                                                    data-exclusao="{{ $costCenter->exclusao }}">
                                                                    Detalhes
                                                                </button>
                                                                <button
                                                                    class="dropdown-item"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#editCostCenterModal"
                                                                    data-id="{{ $costCenter->id }}"
                                                                    data-action="{{ route('costcenter.update', ['costcenter' => $costCenter->id]) }}"
                                                                    data-usuario="{{ $costCenter->usuario_id }}"
                                                                    data-codigoreduzido="{{ $costCenter->codigo_reduzido }}"
                                                                    data-codigoconta="{{ $costCenter->codigo_conta }}"
                                                                    data-descricao="{{ $costCenter->descricao }}"
                                                                    data-tipo="{{ $costCenter->tipo }}">
                                                                    Editar
                                                                </button>
                                                                <button
                                                                    class="dropdown-item"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#deleteModal"
                                                                    data-id="{{$costCenter->id}}"
                                                                    data-action="{{route('costcenter.delete', ['costcenter'=>$costCenter->id])}}">
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
                        Tem certeza de que deseja excluir a conta?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @include('pages.costcenters.modals')
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
                responsive: false // Desativa o modo responsivo que pode esconder colunas
            });

            // Estilizar o campo de busca com classe do Material
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

{{-- para edição --}}
@push('js')
<script>
    $('#editCostCenterModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);

        let modal = $(this);

        // Seta ação do formulário
        modal.find('form').attr('action', button.data('action'));

        // Preenche os campos
        modal.find('#edit_usuario_id').val(button.data('usuario'));
        modal.find('#editid_reduzido').val(button.data('codigoreduzido'));
        modal.find('#editid_conta').val(button.data('codigoconta'));
        modal.find('#edit_descricao').val(button.data('descricao'));

        // Tipo (D/C)
        let tipo = button.data('tipo');
        modal.find('input[name="tipo"]').prop('checked', false).parent().removeClass('active');

        if (tipo === 'D') {
            modal.find('#edit_tipo_d').prop('checked', true).parent().addClass('active');
        } else if (tipo === 'C') {
            modal.find('#edit_tipo_c').prop('checked', true).parent().addClass('active');
        }
    });
</script>
@endpush

{{-- para exibição --}}
@push('js')
<script>
    $('#showCostCenterModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);

        $('#show_usuario').text(button.data('usuario') ?? '—');
        $('#showid_reduzido').text(button.data('codigoreduzido') ?? '—');
        $('#showid_conta').text(button.data('codigoconta') ?? '—');
        $('#show_descricao').text(button.data('descricao') ?? '—');

        const tipo = button.data('tipo') === 'D' ? 'Débito' : 'Crédito';
        $('#show_tipo').text(tipo ?? '—');

        // Datas formatadas (opcional: se quiser melhorar visualmente)
        const formatDateTime = (value) => value ? new Date(value).toLocaleString('pt-BR') : '—';

        $('#show_cadastro').text(formatDateTime(button.data('cadastro')));
        $('#show_atualizacao').text(formatDateTime(button.data('atualizacao')));
        $('#show_exclusao').text(formatDateTime(button.data('exclusao')));
    });
</script>
@endpush


