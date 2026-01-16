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
    @if ($errors->any())
        <div class="alert alert-danger">
            <h4 class="alert-heading">Erro</h4>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Cadastrar Nova Empresa</h4>
                        <p class="card-category">Campos marcados com * são obrigatórios</p>
                    </div>

                    <div class="card-body">
                        <form action="{{route('company.store')}}" method="POST">
                            @csrf

                            {{-- Informações da Empresa --}}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">Nome da Empresa *</h5>
                                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">CNPJ *</h5>
                                        <input type="text" name="cnpj" id="cnpj" class="form-control" value="{{ old('cnpj') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">UF *</h5>
                                        <input type="text" name="uf" id="uf" class="form-control" value="{{ old('uf') }}" required
                                        placeholder="Ex: PA">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">Data de Faturamento *</h5>
                                        <input type="date" name="billing_date" id="billing_date" class="form-control" value="{{ old('billing_date') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">Dia de Vencimento * todo dia:</h5>
                                        <input type="number" min="1" name="due_day" id="due_day" class="form-control" value="{{ old('due_day') }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">Telefone</h5>
                                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">E-mail</h5>
                                        <input type="text" name="email" id="email" class="form-control" value="{{ old('email') }}">
                                    </div>
                                </div>
                            </div>

                            
                            {{-- Botões de Ação --}}
                            <div class="btn-flutante d-flex justify-content-between gap-3">
                                <button type="submit" class="btn btn-primary btn-lg w-50">SALVAR EMPRESA</button>
                                <a href="{{route('company.index')}}" class="btn btn-secondary text-primary btn-lg w-50">VOLTAR</a>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Para máscaras --}}
<script src="https://unpkg.com/imask"></script>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Máscara para CNPJ
            IMask(document.getElementById('cnpj'), {
                mask: '00.000.000/0000-00'
            });

            // Máscara para telefone
            const maskTelefone = {
                mask: [
                    { mask: '(00) 0000-0000' },
                    { mask: '(00) 00000-0000' }
                ]
            };
            IMask(document.getElementById('phone'), maskTelefone);
        });
    </script>
@endpush
