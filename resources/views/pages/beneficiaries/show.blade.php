@extends('layouts.app', ['activePage' => 'companies', 'titlePage' => __('Empresas')])

@section('content')

{{-- Variáveis de Mapeamento e Formatação --}}
@php
    $actionMap = ['I' => 'Inclusão', 'M' => 'Manutenção', 'E' => 'Exclusão'];
    $genderMap = ['M' => 'Masculino', 'F' => 'Feminino'];
@endphp

<style>
    .btn-flutante {
        position: fixed;
        bottom: 10px;
        width: calc(100% - 260px - 100px);
        z-index: 1000;
        left: calc(260px + 50px);
        margin-bottom: 65px;
    }
</style>

<div class="content">
    @if($sucesso ?? false)
        <div class="alert alert-success">
            {{ $sucesso }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Detalhes do beneficiário: {{ $beneficiary->name }}</h4>
                        <p class="card-category">Informações completas do beneficiário</p>
                    </div>

                    <div class="card-body">
                        {{-- LINHA 1: Empresa e Plano --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Empresa</h5>
                                <p>{{ $beneficiary->company->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Ação</h5>
                                <p>{{ $actionMap[$beneficiary->action] ?? $beneficiary->action }}</p>
                            </div>
                            
                        </div>

                        {{-- LINHA 3 (ORIGINAL): Nome do Beneficiário e CPF --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Nome do Beneficiário</h5>
                                <p>{{ $beneficiary->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">CPF</h5>
                                <p>{{ $beneficiary->cpf }}</p>
                            </div>
                        </div>

                        {{-- LINHA 4 (NOVA): Nome da Mãe e Data de Nascimento --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Nome da Mãe</h5>
                                <p>{{ $beneficiary->mother_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Data de Nascimento</h5>
                                <p>{{ $beneficiary->birth_date ? \Carbon\Carbon::parse($beneficiary->birth_date)->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- LINHA 5 (NOVA): Sexo e Vínculo --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Sexo</h5>
                                <p>{{ $genderMap[$beneficiary->gender] ?? $beneficiary->gender ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Vínculo</h5>
                                <p>{{ $beneficiary->relationship ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- LINHA 6 (ORIGINAL): E-mail e Telefone --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">E-mail</h5>
                                <p>{{ $beneficiary->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Telefone</h5>
                                <p>{{ $beneficiary->phone ?? 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- LINHA 7 (NOVA): Valor e Data de Exclusão --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Data de Inclusão</h5>
                                <p>{{ $beneficiary->inclusion_date ? \Carbon\Carbon::parse($beneficiary->inclusion_date)->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Data de Exclusão</h5>
                                <p>{{ $beneficiary->exclusion_date ? \Carbon\Carbon::parse($beneficiary->exclusion_date)->format('d/m/Y') : 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- LINHA 8 (ORIGINAL): Data de Criação e Última Atualização --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Data de Criação</h5>
                                <p>{{ $beneficiary->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Última Atualização</h5>
                                <p>{{ $beneficiary->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>


                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="font-weight-bold">Planos que esse beneficiário possui</h5>
                                <hr>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#planModal">
                                    <i class="material-icons">add</i>
                                </button>
                                <br>
                                <br>
                                @forelse($beneficiaryPlans as $bp)
                                    <div class="d-flex justify-content-between align-items-center p-3 mb-2 rounded-lg" style="border: 1px solid #e0e0e0;">
                                        <div>
                                            <strong>
                                                {{ $bp->plan->name }} – R$ {{ number_format($bp->plan->value, 2, ',', '.') }}
                                                <span class="services-list">
                                                    (
                                                        {{ $bp->plan->conveniences->pluck('convenio.nome_convenio')->implode(', ') ?: 'Sem serviços' }}
                                                    )
                                                </span>
                                                
                                            </strong>
                                        </div>
                                    
                                    <div>
                                        <form action="{{route('beneficiary.plan.destroy', ['plan'=>$bp->id])}}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            {{-- <a href="{{route('plan.show', ['plan'=>$bp->plan->id])}}" class="btn btn-primary btn-sm">
                                                Ver Plano
                                            </a> --}}
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Remover
                                            </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <h6>Esse beneficiário não possui nenhum plano.</h6>
                                @endforelse
                            </div>
                        </div>

                        {{-- @php
                            $isCompany = Auth::guard('company')->check(); // verifica se é empresa
                        @endphp --}}

                        {{-- Botões de Ação --}}
                        <div class="btn-flutante d-flex justify-content-between gap-3">
                            <a href="{{ route('beneficiary.edit', $beneficiary->id) }}" class="btn btn-primary btn-lg w-50">EDITAR BENEFICIÁRIO</a>
                            <a href="{{ route('beneficiary.index',['company'=>$beneficiary->company->id]) }}" class="btn btn-secondary text-primary btn-lg w-50">VOLTAR</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



{{-- Modal para Adicionar Indicação --}}
<div class="modal fade" id="planModal" tabindex="-1" role="dialog" aria-labelledby="planModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="planModalLabel">Adicionar Plano ao Beneficiário</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('beneficiary.plan.store', ['beneficiary'=>$beneficiary->id])}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="plan-select">Selecione o plano</label>
                        <select class="form-control select2" id="plan-select" name="plan_id" required style="width: 100%;">
                            <option value=""></option>
                            @foreach ($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('js')
    {{-- Scripts para Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializa o Select2 para o campo de seleção de empresas
            $('.select2').select2({
                placeholder: "Selecione um plano",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#planModal')
            });
        });
    </script>
@endpush