@extends('layouts.app', ['activePage' => 'companies', 'titlePage' => __('Empresas')])

@section('content')

{{-- Estilos adicionais para o Select2 --}}
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Adaptação para o estilo do Material Dashboard */
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px); /* Altura igual aos outros inputs */
        padding: .375rem 1rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        line-height: 1.5;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 26px;
        position: absolute;
        top: 1px;
        right: 1px;
        width: 20px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #444;
        line-height: 28px;
    }
    .select2-container--default .select2-selection--single {
        border-color: #d2d2d2;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #d2d2d2;
    }
</style>
@endpush

{{-- <style>
    /* Mantendo seus estilos */
    label {
        color: green;
    }

    h4 {
        background-color: green;
        color: white;
        font-weight: bold;
        border-radius: 8px;
        padding: 8px;
    }

    .form-check-input:checked + .form-check-label {
        background-color: green;
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        transition: 0.2s ease;
    }
    .btn-flutante{
        position: fixed;
        bottom: 10px;
        width: calc(100% - 260px - 100px);
        z-index: 1000;
        left: calc(260px + 50px);
        margin-bottom: 65px
    }
    .text-green {
        color: green !important;
    }
</style> --}}

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
                        <h4 class="card-title">Cadastrar Novo Beneficiário</h4>
                        <p class="card-category">Campos marcados com * são obrigatórios</p>
                    </div>

                    <div class="card-body">
                        <form action="{{route('beneficiary.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            {{-- Seção Dados Principais --}}
                            <h5 class="text-green mt-3 mb-4">Dados Principais</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <input type="hidden" value="{{$company->id}}" name="company_id" id="company_id">
                                    <div class="form-group">
                                        <label for="action" class="text-green">Ação *</label>
                                        <select name="action" id="action_select" class="form-control" required>
                                            <option value="I" {{ old('action') == 'I' ? 'selected' : '' }}>Inclusão</option>
                                            <option value="M" {{ old('action') == 'M' ? 'selected' : '' }}>Manutenção</option>
                                            <option value="E" {{ old('action') == 'E' ? 'selected' : '' }}>Exclusão</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="inclusion_date" class="text-green">Data de Inclusão</label>
                                        <input type="date" name="inclusion_date" id="inclusion_date" class="form-control" value="{{ old('inclusion_date') ?? date('Y-m-d') }}">
                                        <small class="form-text text-muted">Preenchida automaticamente, se vazia.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name" class="text-green">Nome Completo *</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cpf" class="text-green">CPF *</label>
                                        <input type="text" name="cpf" id="cpf" class="form-control" value="{{ old('cpf') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="birth_date" class="text-green">Data de Nascimento *</label>
                                        <input type="date" name="birth_date" id="birth_date" class="form-control" value="{{ old('birth_date') }}" placeholder="DD/MM/AAAA" required>
                                    </div>
                                </div>
                            </div>

                            <h5 class="text-green mt-5 mb-4">Informações de Login (Para área do Beneficiário)</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email" class="text-green">E-mail*</label>
                                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="password" class="text-green">Senha*</label>
                                        <input type="text" name="password" id="password" class="form-control" value="{{ old('password') }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Seção Detalhes e Contato (Novos Campos) --}}
                            <h5 class="text-green mt-5 mb-4">Detalhes Adicionais e Contato</h5>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gender" class="text-green">Sexo</label>
                                        <select name="gender" id="gender_select" class="form-control">
                                            <option value="">Selecione</option>
                                            <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculino</option>
                                            <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Feminino</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="relationship" class="text-green">Vínculo (Grau de Parentesco)</label>
                                        <input type="text" name="relationship" id="relationship" class="form-control" value="{{ old('relationship') }}" placeholder="Ex: Titular, Cônjuge, Filho(a)">
                                    </div>
                                </div>
                                
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mother_name" class="text-green">Nome da Mãe</label>
                                        <input type="text" name="mother_name" id="mother_name" class="form-control" value="{{ old('mother_name') }}">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="phone" class="text-green">Contato</label>
                                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- @php
                                $isCompany = Auth::guard('company')->check(); // verifica se é empresa
                            @endphp --}}

                            {{-- Botões de Ação --}}
                            <div class="btn-flutante d-flex justify-content-between gap-3">
                                <button type="submit" class="btn btn-primary btn-lg w-50">SALVAR BENEFICIÁRIO</button>
                                <a href="{{ route('beneficiary.index',['company'=>$company->id]) }}" class="btn btn-secondary btn-lg w-50">VOLTAR</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection


@push('js')
{{-- Para máscaras e Select2 --}}
<script src="https://unpkg.com/imask"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa o Select2 no dropdown de empresas
        $('#company_select').select2({
            placeholder: "Selecione a Empresa",
            allowClear: true
        });

        // --- Máscaras ---
        
        // Máscara para CPF
        const cpfInput = document.getElementById('cpf');
        if (cpfInput) {
            IMask(cpfInput, {
                mask: '000.000.000-00'
            });
        }
        
        // Máscara para Data de Nascimento (DD/MM/AAAA)
        // const birthDateInput = document.getElementById('birth_date');
        // if (birthDateInput) {
        //     IMask(birthDateInput, {
        //         mask: '00/00/0000'
        //     });
        // }

        // Máscara para Valor (Moeda BRL)
        // const valueInput = document.getElementById('value');
        // if (valueInput) {
        //      IMask(valueInput, {
        //         mask: 'R$ num',
        //         blocks: {
        //             num: {
        //                 mask: Number,
        //                 thousandsSeparator: '.',
        //                 radix: ',',
        //                 scale: 2,
        //                 signed: false,
        //                 padFractionalZeros: true,
        //                 normalizeZeros: true,
        //                 mapToRadix: ['.'],
        //             }
        //         }
        //     });
        // }

        // Máscara para Telefone (Corrigido para usar IMask)
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
