@extends('layouts.app', ['activePage' => 'dependents', 'titlePage' => __('Dependentes')])

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
    @if(session('sucesso'))
        <div class="alert alert-success">
            {{ session('sucesso') }}
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
                        <h4 class="card-title">
                            Detalhes do Dependente: {{ $dependent->name }}
                        </h4>
                        <p class="card-category">
                            Informações completas do dependente
                        </p>
                    </div>

                    <div class="card-body">

                        {{-- LINHA 1: Beneficiário (titular) --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Beneficiário (Titular)</h5>
                                <p>{{ $dependent->beneficiary->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">CPF do Titular</h5>
                                <p>{{ $dependent->beneficiary->cpf }}</p>
                            </div>
                        </div>

                        {{-- LINHA 2: Nome e CPF --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Nome do Dependente</h5>
                                <p>{{ $dependent->name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">CPF</h5>
                                <p>{{ $dependent->cpf ?: 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">E-mail</h5>
                                <p>{{ $dependent->email ?: 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Contato</h5>
                                <p>{{ $dependent->phone ?: 'N/A' }}</p>
                            </div>
                        </div>

                        {{-- LINHA 3: Data de Nascimento e Sexo --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Data de Nascimento</h5>
                                <p>
                                    {{ $dependent->birth_date
                                        ? \Carbon\Carbon::parse($dependent->birth_date)->format('d/m/Y')
                                        : 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Sexo</h5>
                                <p>
                                    {{ $dependent->gender === 'M' ? 'Masculino' : ($dependent->gender === 'F' ? 'Feminino' : 'N/A') }}
                                </p>
                            </div>
                        </div>

                        {{-- LINHA 4: Parentesco --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Vínculo (Grau de Parentesco)</h5>
                                <p>{{ $dependent->relationship }}</p>
                            </div>
                        </div>

                        {{-- LINHA 5: Datas --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Criado em</h5>
                                <p>{{ $dependent->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="font-weight-bold">Última Atualização</h5>
                                <p>{{ $dependent->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @php
                            $isBeneficiary = Auth::guard('beneficiary')->check(); // verifica se é beneficiario
                        @endphp
                        {{-- Botões --}}
                        <div class="btn-flutante d-flex justify-content-between gap-3">
                            <a href="{{ route('dependent.edit', $dependent->id) }}"
                                class="btn btn-primary btn-lg w-50">EDITAR DEPENDENTE</a>

                            @if ($isBeneficiary)
                            <a href="{{ route('beneficiary.area.dependent') }}" class="btn btn-secondary btn-lg w-50">VOLTAR PARA ÁREA DO BENEFICIÁRIO</a>
                            @else
                            <a href="{{ route('dependent.index',['beneficiaryId'=>$dependent->beneficiary->id]) }}" class="btn btn-secondary btn-lg w-50">VOLTAR</a>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
