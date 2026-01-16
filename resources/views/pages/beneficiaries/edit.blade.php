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
                        <h4 class="card-title">Editar Beneficiário: {{ $beneficiary->name }}</h4>
                        <p class="card-category">Campos marcados com * são obrigatórios</p>
                    </div>

                    <div class="card-body">
                        {{-- ATENÇÃO: Form Action e Method ajustados para UPDATE --}}
                        <form action="{{ route('beneficiary.update', $beneficiary) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            {{-- Seção Dados Principais --}}
                            <h5 class="text-green mt-3 mb-4">Dados Principais</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="action" class="text-green">Ação *</label>
                                        <select name="action" id="action_select" class="form-control" required>
                                            {{-- Preenche com o valor existente --}}
                                            <option value="I" {{ old('action', $beneficiary->action) == 'I' ? 'selected' : '' }}>Inclusão</option>
                                            <option value="M" {{ old('action', $beneficiary->action) == 'M' ? 'selected' : '' }}>Manutenção</option>
                                            <option value="E" {{ old('action', $beneficiary->action) == 'E' ? 'selected' : '' }}>Exclusão</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="inclusion_date" class="text-green">Data de Inclusão</label>
                                        {{-- Converte a data do DB (Y-m-d) para o formato do input date --}}
                                        <input type="date" name="inclusion_date" id="inclusion_date" class="form-control" 
                                            value="{{ old('inclusion_date', $beneficiary->inclusion_date ? \Carbon\Carbon::parse($beneficiary->inclusion_date)->format('Y-m-d') : '') }}">
                                        <small class="form-text text-muted">A data original de inclusão.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name" class="text-green">Nome Completo *</label>
                                        {{-- Preenche com o valor existente --}}
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $beneficiary->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cpf" class="text-green">CPF *</label>
                                        {{-- Preenche com o valor existente (a máscara do JS será aplicada) --}}
                                        <input type="text" name="cpf" id="cpf" class="form-control" value="{{ old('cpf', $beneficiary->cpf) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="birth_date" class="text-green">Data de Nascimento *</label>
                                        {{-- Converte a data do DB (Y-m-d) para o formato do input date --}}
                                        <input type="date" name="birth_date" id="birth_date" class="form-control" 
                                            value="{{ old('birth_date', $beneficiary->birth_date ? \Carbon\Carbon::parse($beneficiary->birth_date)->format('Y-m-d') : '') }}" 
                                            placeholder="DD/MM/AAAA" required>
                                    </div>
                                </div>
                            </div>
                            <h5 class="text-green mt-5 mb-4">Informações de Login (Para área do Beneficiário)</h5>
                            <div class="row">
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="email" class="text-green">E-mail*</label>
                                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $beneficiary->email) }}" required>
                                        </div>
                                    </div>
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="password" class="text-green">Senha</label>
                                            <input type="text" name="password" id="password" class="form-control" placeholder="Edite para redefinir a senha" value="{{ old('password') }}">
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
                                            <option value="M" {{ old('gender', $beneficiary->gender) == 'M' ? 'selected' : '' }}>Masculino</option>
                                            <option value="F" {{ old('gender', $beneficiary->gender) == 'F' ? 'selected' : '' }}>Feminino</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="relationship" class="text-green">Vínculo (Grau de Parentesco)</label>
                                        <input type="text" name="relationship" id="relationship" class="form-control" 
                                            value="{{ old('relationship', $beneficiary->relationship) }}" placeholder="Ex: Titular, Cônjuge, Filho(a)">
                                    </div>
                                </div>
                                
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mother_name" class="text-green">Nome da Mãe</label>
                                        <input type="text" name="mother_name" id="mother_name" class="form-control" value="{{ old('mother_name', $beneficiary->mother_name) }}">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="phone" class="text-green">Contato</label>
                                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $beneficiary->phone) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exclusion_date" class="text-green">Data de Exclusão</label>
                                        {{-- Converte a data do DB (Y-m-d) para o formato do input date --}}
                                        <input type="date" name="exclusion_date" id="exclusion_date" class="form-control" 
                                            value="{{ old('exclusion_date', $beneficiary->exclusion_date ? \Carbon\Carbon::parse($beneficiary->exclusion_date)->format('Y-m-d') : '') }}">
                                    </div>
                                </div>
                            </div>

                            {{-- @php
                                $isCompany = Auth::guard('company')->check(); // verifica se é empresa
                            @endphp --}}

                            {{-- Botões de Ação --}}
                            <div class="btn-flutante d-flex justify-content-between gap-3">
                                <button type="submit" class="btn btn-primary btn-lg w-50">ATUALIZAR BENEFICIÁRIO</button>
                                <a href="{{ route('beneficiary.index',['company'=>$beneficiary->company->id]) }}" class="btn btn-secondary btn-lg w-50">VOLTAR</a>
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
        
        // Máscara para Valor (Moeda BRL)
        // const valueInput = document.getElementById('value');
        // if (valueInput) {
        //      IMask(valueInput, {
        //          mask: 'R$ num',
        //          blocks: {
        //              num: {
        //                  mask: Number,
        //                  thousandsSeparator: '.',
        //                  radix: ',',
        //                  scale: 2,
        //                  signed: false,
        //                  padFractionalZeros: true,
        //                  normalizeZeros: true,
        //                  mapToRadix: ['.'],
        //              }
        //          }
        //      });
        // }

        // Máscara para Telefone (Corrigido para usar IMask)
        const maskTelefone = {
            mask: [
                { mask: '(00) 0000-0000' },
                { mask: '(00) 00000-0000' }
            ]
        };
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            IMask(phoneInput, maskTelefone);
        }

    });
</script>
@endpush
