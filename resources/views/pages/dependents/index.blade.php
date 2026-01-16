@extends('layouts.app', ['activePage' => 'companies', 'titlePage' => __('Empresas')])

@section('content')
<div class="content">
    
    {{-- ALERTAS --}}
    @if($errors->any())
        <div class="alert alert-danger alert-with-icon">
            <i class="material-icons" data-notify="icon">error</i>
            <span data-notify="message">
                <strong>Foram encontrados erros:</strong>
                <ul class="mt-2 mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </span>
        </div>
    @endif

    <div class="container-fluid">

        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card shadow-lg" style="border-radius: 12px;">
                    
                    {{-- CABEÇALHO --}}
                    <div class="card-header card-header-primary d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0">
                                <i class="material-icons">group</i>
                                Dependentes do Beneficiário: 
                                <strong>{{ $beneficiary->name }}</strong>
                            </h4>
                            <p class="card-category mt-1">
                                Total de Dependentes: 
                                <strong>{{ $dependents->count() }}</strong>
                            </p>
                        </div>

                        
                    </div>

                    <div class="card-body">
                        <div class="float-right d-flex flex-wrap">
                            <a href="{{route('beneficiary.index', ['company' => $beneficiary->company->id])}}" class="btn btn-secondary">
                                <i class="material-icons">arrow_back</i> Voltar
                            </a>
                            <a href="{{route('dependent.create',['beneficiaryId'=>$beneficiary->id])}}" class="btn btn-primary mb-2">
                                <i class="material-icons">person_add</i>
                            </a>
                        </div>
                        
                        {{-- SE NÃO HOVER DEPENDENTES --}}
                        @if($dependents->isEmpty())
                            <div class="mt-5">
                                <div class="alert alert-primary" role="alert" style="text-align: center; font-weight: bold; text-transform: uppercase;">
                                    Nenhum Beneficiário Cadastrado
                                </div>
                            </div>
                        @else

                        {{-- TABELA --}}
                        <div class="table-responsive mt-4">
                            <table class="table table-hover" id="dependents-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>CPF</th>
                                        <th>Gênero</th>
                                        <th>Parentesco</th>
                                        <th>Nascimento</th>
                                        <th class="text-right">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dependents as $dependent)
                                        <tr>
                                            <td>{{ $dependent->id }}</td>
                                            <td>{{ $dependent->name }}</td>
                                            <td>{{ $dependent->cpf }}</td>
                                            <td>{{ $dependent->gender ?? '--'}}</td>
                                            <td>{{ $dependent->relantioship }}</td>
                                            <td>{{ \Carbon\Carbon::parse($dependent->date_of_birth)->format('d/m/Y') }}</td>

                                            <td class="text-right">
                                                <button class="btn btn-info btn-sm btn-round" 
                                                        onclick="window.location='{{ route('dependent.show', $dependent->id) }}'">
                                                    <i class="material-icons">visibility</i>
                                                </button>

                                                <button class="btn btn-warning btn-sm btn-round"
                                                        onclick="window.location='{{ route('dependent.edit', $dependent->id) }}'">
                                                    <i class="material-icons">edit</i>
                                                </button>

                                                <button class="btn btn-danger btn-sm btn-round"
                                                        data-toggle="modal"
                                                        data-target="#deleteDependentModal"
                                                        data-id="{{ $dependent->id }}"
                                                        data-action="{{ route('dependent.softdelete', ['dependent'=>$dependent->id]) }}">
                                                    <i class="material-icons">delete</i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @endif {{-- endif dependents --}}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- MODAL DELETE --}}
<div class="modal fade" id="deleteDependentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">

        <form id="deleteDependentForm" method="POST">
            @csrf
            @method('DELETE')

            <div class="modal-content">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">
                    <i class="material-icons text-danger" style="font-size: 45px;">warning</i>
                    <p class="mt-3">
                        Tem certeza de que deseja remover esse dependente?<br>
                        <strong>Essa ação não pode ser desfeita.</strong>
                    </p>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Excluir
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>
@endsection


@push('js')
<script>
    $('#deleteDependentModal').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let action = button.data('action');
        $('#deleteDependentForm').attr('action', action);
    });
</script>
@endpush
