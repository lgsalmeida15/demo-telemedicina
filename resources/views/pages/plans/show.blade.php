@extends('layouts.app', ['activePage' => 'companies', 'titlePage' => __('Empresas')])

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

    h5.font-weight-bold {
        color: #4081F6;
        margin-top: 15px;
        margin-bottom: 5px;
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
                        <h4 class="card-title">Detalhes do Plano: {{ $plan->name }}</h4>
                        <p class="card-category">Informações completas do plano</p>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Nome do Plano</h5>
                                <p>{{ $plan->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Empresa</h5>
                                <p>{{ $plan->company->name }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Valor</h5>
                                <p>R$ {{ number_format($plan->value, 2, ',', '.') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Descrição</h5>
                                <p>{{ $plan->description ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Atende Telemedicina?</h5>
                                <p>{{ $plan->is_telemedicine ? 'SIM' : 'NÃO' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Data de Criação</h5>
                                <p>{{ $plan->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Última Atualização</h5>
                                <p>{{ $plan->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        {{-- Botões de Ação --}}
                        <div class="btn-flutante d-flex justify-content-between gap-3">
                            <a href="{{route('plan.edit', ['plan'=>$plan->id])}}" class="btn btn-primary btn-lg w-50">EDITAR PLANO</a>
                            <a href="{{route('plan.index',['company'=>$plan->company->id])}}" class="btn btn-secondary text-primary btn-lg w-50">VOLTAR</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
