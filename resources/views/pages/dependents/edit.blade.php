@extends('layouts.app', ['activePage' => 'dependents', 'titlePage' => __('Empresas')])

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

<style>
    .btn-flutante{
        position: fixed;
        bottom: 10px;
        width: calc(100% - 260px - 100px);
        z-index: 1000;
        left: calc(260px + 50px);
        margin-bottom: 65px
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
                        <h4 class="card-title">Atualizar dependente: {{ $dependent->name }}</h4>
                        <p class="card-category">Campos marcados com * são obrigatórios</p>
                    </div>

                    <div class="card-body">
                        <form action="{{route('dependent.update', ['dependent' => $dependent->id])}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            {{-- Seção Dados Principais --}}
                            <h5 class="text-green mt-3 mb-4">Dados Principais</h5>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name" class="text-green">Nome Completo *</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $dependent->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cpf" class="text-green">CPF *</label>
                                        <input type="text" name="cpf" id="cpf" class="form-control" value="{{ old('cpf', $dependent->cpf ) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cpf" class="text-green">E-Mail *</label>
                                        <input type="text" name="email" id="email" class="form-control" value="{{ old('email', $dependent->email ) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cpf" class="text-green">Senha *</label>
                                        <input type="text" name="password" id="password" class="form-control" value="{{ old('password') }}" 
                                        placeholder="Preencha para resetar a senha">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="phone" class="text-green">Telefone *</label>
                                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $dependent->phone) }}" 
                                        required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="birth_date" class="text-green">Data de Nascimento *</label>
                                        <input type="date" name="birth_date" id="birth_date" class="form-control" value="{{ old('birth_date', $dependent->birth_date) }}" placeholder="DD/MM/AAAA" required>
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
                                            <option value="M" {{ old('gender', $dependent->gender) == 'M' ? 'selected' : '' }}>Masculino</option>
                                            <option value="F" {{ old('gender', $dependent->gender) == 'F' ? 'selected' : '' }}>Feminino</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="relationship" class="text-green">Vínculo (Grau de Parentesco) *</label>
                                        <input type="text" name="relationship" id="relationship" class="form-control" value="{{ old('relationship', $dependent->relationship) }}" placeholder="Ex: Titular, Cônjuge, Filho(a)"
                                        required>
                                    </div>
                                </div>
                                
                            </div>
                            @php
                                $isBeneficiary = Auth::guard('beneficiary')->check(); // verifica se é beneficiario
                            @endphp

                            {{-- Botões de Ação --}}
                            <div class="btn-flutante d-flex justify-content-between gap-3">
                                <button type="submit" class="btn btn-primary btn-lg w-50">ATUALIZAR</button>
                                @if ($isBeneficiary)
                                <a href="{{ route('beneficiary.area.dependent') }}" class="btn btn-secondary btn-lg w-50">VOLTAR PARA ÁREA DO BENEFICIÁRIO</a>
                                @else
                                <a href="{{ route('dependent.index',['beneficiaryId'=>$dependent->beneficiary->id]) }}" class="btn btn-secondary btn-lg w-50">VOLTAR</a>
                                @endif
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
