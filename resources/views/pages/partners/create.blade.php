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

<div class="content">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
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
                        <h4 class="card-title">Cadastrar Novo Parceiro</h4>
                        <p class="card-category">Campos marcados com * são obrigatórios</p>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('partner.store') }}" method="POST">
                            @csrf

                            {{-- Informações do Parceiro --}}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">Nome do Parceiro *</h5>
                                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">CNPJ *</h5>
                                        <input type="text" name="cnpj" id="cnpj" class="form-control" value="{{ old('cnpj') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">E-mail</h5>
                                        <input type="text" name="email" id="email" class="form-control" value="{{ old('email') }}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">Telefone</h5>
                                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">Descrição</h5>
                                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">Centro de Custo</h5>
                                        <select name="cost_center_id" class="form-control">
                                            <option value="">Selecione um Centro de Custo</option>
                                            @foreach ($costCenters as $costCenter)
                                                <option value="{{ $costCenter->id }}" {{ old('cost_center_id') == $costCenter->id ? 'selected' : '' }}>
                                                    {{ $costCenter->descricao }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Botões de Ação --}}
                            <div class="btn-flutante d-flex justify-content-between gap-3">
                                <button type="submit" class="btn btn-primary btn-lg w-50">SALVAR PARCEIRO</button>
                                <a href="{{ route('partner.index') }}" class="btn btn-secondary text-primary btn-lg w-50">VOLTAR</a>
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
