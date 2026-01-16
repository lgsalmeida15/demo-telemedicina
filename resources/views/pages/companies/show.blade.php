@extends('layouts.app', ['activePage' => 'companies', 'titlePage' => __('Companies')])

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
                        <h4 class="card-title">Detalhes da Empresa: {{ $company->name }}</h4>
                        <p class="card-category">Informações completas da empresa</p>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Nome da Empresa</h5>
                                <p>{{ $company->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">CNPJ</h5>
                                <p>{{ $company->cnpj }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">UF</h5>
                                <p>{{ $company->uf ?? '--'}}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Data de Faturamento</h5>
                                <p>{{ \Carbon\Carbon::parse($company->billing_date)->format('d/m/Y') ?? '--'}}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Dia de Vencimento</h5>
                                <p><strong>Todo dia:</strong> {{$company->due_day}}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">E-mail</h5>
                                <p>{{ $company->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Telefone</h5>
                                <p>{{ $company->phone ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Data de Criação</h5>
                                <p>{{ $company->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Última Atualização</h5>
                                <p>{{ $company->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <br>
                        <br>
                        {{-- <div class="row">
                            <div class="col-md-12">
                                <h5 class="font-weight-bold">Serviços que essa empresa usa:</h5>
                                <hr>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#planoModal">
                                    <i class="material-icons">add</i> Adicionar Serviço
                                </button>
                                <br>
                                <br>
                                <h6>Quantidade: {{$companyConvenios->count()}}</h6>
                                @forelse($companyConvenios as $companyConvenio)
                                    <div class="d-flex justify-content-between align-items-center p-3 mb-2 rounded-lg" style="border: 1px solid #e0e0e0;">
                                        <div>
                                            <strong>{{$companyConvenio->convenio->nome_convenio}}</strong>
                                        </div>
                                        <div>
                                            <form action="{{route('company.convenio.destroy', ['companyConvenio'=>$companyConvenio->id])}}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    Remover
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <h6>Essa empresa não esta usando nenhum serviço.</h6>
                                @endforelse
                            </div>
                        </div> --}}

                        {{-- Botões de Ação --}}
                        <div class="btn-flutante d-flex justify-content-between gap-3">
                            <a href="{{ route('company.edit', $company->id) }}" class="btn btn-primary btn-lg w-50">EDITAR EMPRESA</a>
                            <a href="{{ route('company.index') }}" class="btn btn-secondary text-primary btn-lg w-50">VOLTAR</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


