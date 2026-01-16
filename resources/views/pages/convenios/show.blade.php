@extends('layouts.app', ['activePage' => 'convenios', 'titlePage' => __('Serviços')])

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

    .info-label {
        font-weight: bold;
        color: #4081F6;
        font-size: 1.1em;
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
                        <h4 class="card-title">Detalhes do Serviço: {{ $convenio->nome_convenio }}</h4>
                        <p class="card-category">Informações completas do serviço</p>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h5 class="info-label">Parceiro</h5>
                                <p>{{ $convenio->partner->name }}</p>
                            </div>
                            <div class="col-md-3">
                                <h5 class="info-label">Categoria</h5>
                                <p>{{ $convenio->categoria->nome }}</p>
                            </div>
                            <div class="col-md-3">
                                <h5 class="info-label">Tipo</h5>
                                <p>{{ $convenio->type->name }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="info-label">Nome do Serviço</h5>
                                <p>{{ $convenio->nome_convenio }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="info-label">Descrição</h5>
                                <p>{{ $convenio->descricao ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <h5 class="info-label">Desconto Percentual</h5>
                                <p>{{ $convenio->desconto_percentual ?? '0' }}%</p>
                            </div>
                            <div class="col-md-3">
                                <h5 class="info-label">Data de Início</h5>
                                <p>{{ date('d/m/Y', strtotime($convenio->data_inicio)) }}</p>
                            </div>
                            <div class="col-md-3">
                                <h5 class="info-label">Data de Fim</h5>
                                <p>{{ $convenio->data_fim ? date('d/m/Y', strtotime($convenio->data_fim)) : 'Não definida' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="info-label">Contato</h5>
                                <p>{{ $convenio->contato ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="info-label">Status</h5>
                                <p>{{ $convenio->status }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="info-label">Data de Criação</h5>
                                <p>{{ $convenio->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="info-label">Última Atualização</h5>
                                <p>{{ $convenio->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        {{-- Botões de Ação --}}
                        <div class="btn-flutante d-flex justify-content-between gap-3">
                            <a href="{{ route('convenio.view_edit', $convenio->id) }}" class="btn btn-primary btn-lg w-50">EDITAR SERVIÇO</a>
                            <a href="{{ route('convenio.index') }}" class="btn btn-secondary text-primary btn-lg w-50">VOLTAR</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
