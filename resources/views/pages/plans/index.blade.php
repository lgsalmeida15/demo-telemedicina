@extends('layouts.app', ['activePage' => 'companies', 'titlePage' => __('Empresas')])

@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">Planos da Empresa: {{$company->name}}</h4>
                            <p class="card-category">Número Total de Planos: {{ $plans->count() }}</p>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                {{-- Botão de Registro --}}
                                <div style="width: 100%; text-align: end; margin-bottom: 1rem;">
                                    <a href="{{route('company.index')}}" class="btn btn-secondary">
                                        <i class="material-icons">arrow_back</i> Voltar
                                    </a>
                                    <a href="{{route('plan.create',['company'=>$company->id])}}" class="btn btn-primary">
                                        <i class="material-icons">add_box</i> Cadastrar Novo Plano
                                    </a>
                                </div>
                                @if($plans->isEmpty())
                                    <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                        Nenhum Plano Cadastrado
                                    </div>
                                @else
                                    <table class="table" id="plans-table">
                                        <thead class="text-primary">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>Valor</th>
                                                <th>Data de Cadastro</th>
                                                <th style="text-align: end">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($plans as $plan)
                                                <tr>
                                                    <td>{{ $plan->id }}</td>
                                                    <td>{{ $plan->name }}</td>
                                                    <td>R$ {{ number_format($plan->value, 2, ',', '.') }}</td>
                                                    <td>{{\Carbon\Carbon::parse( $plan->created_at )->format('d/m/Y')}}</td>
                                                    <td style="text-align: end">
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                Opções
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                <a class="dropdown-item" href="{{route('plan.show', ['plan' => $plan->id])}}">Detalhes</a>
                                                                <a class="dropdown-item" href="{{route('plan.convenience.index', ['plan' => $plan->id])}}">Serviços</a>
                                                                <a class="dropdown-item" href="{{route('plan.edit', ['plan' => $plan->id])}}">Editar</a>
                                                                <button
                                                                    class="dropdown-item"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#deletePlanModal"
                                                                    data-id="{{ $plan->id }}"
                                                                    data-action="{{route('plan.destroy', ['plan' => $plan->id])}}">
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
    <div class="modal fade" id="deletePlanModal" tabindex="-1" role="dialog" aria-labelledby="deletePlanModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="deletePlanForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePlanModalLabel">Confirmar Exclusão</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Tem certeza de que deseja excluir o plano?
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
            const table = $('#plans-table').DataTable({
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
                $('select[name="plans-table_length"]').addClass('form-control');
            }, 200);
        });
    </script>
@endpush

@push('js')
<script>
    $('#deletePlanModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let action = button.data('action');
        $(this).find('#deletePlanForm').attr('action', action);
    });
</script>
@endpush
