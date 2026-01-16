@extends('layouts.app', ['activePage' => 'partners', 'titlePage' => __('Parceiros')])

@section('content')

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
@php
    use App\Models\PartnerCompany;
    $indications = PartnerCompany::where('partner_id', $partner->id)->get()->sortBy('created_at');
@endphp

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
                        <h4 class="card-title">Detalhes do parceiro: {{ $partner->name }}</h4>
                        <p class="card-category">Informações completas do parceiro</p>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Nome do parceiro</h5>
                                <p>{{ $partner->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">CNPJ</h5>
                                <p>{{ $partner->cnpj }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">E-mail</h5>
                                <p>{{ $partner->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Telefone</h5>
                                <p>{{ $partner->phone ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="font-weight-bold">Descrição</h5>
                                <p>{{ $partner->description ?? 'N/A' }}</p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="font-weight-bold">Centro de Custo</h5>
                                <p>{{ $partner->costCenter->name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Data de Criação</h5>
                                <p>{{ \Carbon\Carbon::parse($partner->created_at)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Última Atualização</h5>
                                <p>{{ \Carbon\Carbon::parse($partner->updated_at)->format('d/m/Y H:i')?? '--' }}</p>
                            </div>
                        </div>
                        <br>
                        <br>

                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="font-weight-bold">Indicações de {{$partner->name}}</h5>
                                <hr>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#indicacaoModal">
                                    <i class="material-icons">add</i> Adicionar Indicação
                                </button>
                                <br>
                                <br>
                                @forelse($indications as $indication)
                                    <div class="d-flex justify-content-between align-items-center p-3 mb-2 rounded-lg" style="border: 1px solid #e0e0e0;">
                                        <div>
                                            <strong>{{$indication->company->name}}</strong>
                                        </div>
                                        <div>
                                            <form action="{{route('partner.indication.destroy', ['indication'=>$indication->id])}}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Remover
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <h6>Nenhuma empresa foi indicada por esse parceiro</h6>
                                @endforelse
                            </div>
                        </div>


                        {{-- Botões de Ação --}}
                        <div class="btn-flutante d-flex justify-content-between gap-3">
                            <a href="{{ route('partner.edit', $partner->id) }}" class="btn btn-primary btn-lg w-50">EDITAR PARCEIRO</a>
                            <a href="{{ route('partner.index') }}" class="btn btn-secondary text-primary btn-lg w-50">VOLTAR</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal para Adicionar Indicação --}}
<div class="modal fade" id="indicacaoModal" tabindex="-1" role="dialog" aria-labelledby="indicacaoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="indicacaoModalLabel">Adicionar Indicação</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('partner.indication.store', ['partner'=>$partner->id])}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="company-select">Selecione a Empresa</label>
                        <select class="form-control select2" id="company-select" name="company_id" required style="width: 100%;">
                            <option></option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar Indicação</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js')
    {{-- Scripts para Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializa o Select2 para o campo de seleção de empresas
            $('.select2').select2({
                placeholder: "Selecione uma empresa",
                allowClear: true
            });
        });
    </script>
@endpush
