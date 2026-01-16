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

<style>
    label {
        color: #4081F6;
    }

    h4 {
        background-color: #4081F6;
        color: white;
        font-weight: bold;
        border-radius: 8px;
        padding: 8px;
    }

    .form-check-input:checked + .form-check-label {
        background-color: #4081F6;
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
    /* Estilos para o botão toggle */
    .toggle-btn {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s ease;
        color: white;
    }

    .toggle-btn.on {
        background-color: #28a745 !important; /* verde */
    }

    .toggle-btn.off {
        background-color: #dc3545 !important; /* vermelho */
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
                        <h4 class="card-title">Cadastrar Novo Plano</h4>
                        <p class="card-category">Campos marcados com * são obrigatórios</p>
                    </div>

                    <div class="card-body">
                        <form action="{{route('plan.update', ['plan'=>$plan->id])}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <h5 >Nome do Plano *</h5>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">Valor (R$) *</h5>
                                        <input type="text" name="value" id="value" class="form-control" value="{{ old('value', $plan->value) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">Atende Telemedicina?</h5>
                                        <input type="hidden" name="is_telemedicine" id="is_telemedicine_edit" value="{{ $plan->is_telemedicine }}">
                                        <button 
                                            type="button" 
                                            id="telemedicineToggle_edit"
                                            class="toggle-btn {{ $plan->is_telemedicine ? 'on' : 'off' }}">
                                            {{ $plan->is_telemedicine ? 'SIM' : 'NÃO' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <h5 class="bmd-label-floating">Descrição</h5>
                                        <textarea name="description" class="form-control" rows="4">{{ old('description', $plan->description) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Botões de Ação --}}
                            <div class="btn-flutante d-flex justify-content-between gap-3">
                                <button type="submit" class="btn btn-primary btn-lg w-50">SALVAR PLANO</button>
                                <a href="{{route('plan.index',['company'=>$plan->company->id])}}" class="btn btn-secondary btn-lg w-50">VOLTAR</a>
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
        // Inicializa o Select2 no dropdown de serviços
        $('#covenant_select').select2({
            placeholder: "Selecione o Serviço",
            allowClear: true
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputElement = document.getElementById('value');

        // Adiciona um listener para o evento 'input', que é disparado sempre que o valor do campo muda
        inputElement.addEventListener('input', function(event) {
            let value = event.target.value;

            // Remove todos os caracteres que não sejam um número (0-9) ou uma vírgula (,)
            // A regex `/[^0-9,]/g` encontra qualquer caractere que NÃO esteja no conjunto de 0 a 9 e a vírgula.
            value = value.replace(/[^0-9,]/g, '');

            // Garante que haja apenas uma vírgula no campo
            const parts = value.split(',');
            if (parts.length > 2) {
                // Se houver mais de uma vírgula, junta a primeira parte com o restante, removendo as vírgulas extras.
                value = parts[0] + ',' + parts.slice(1).join('');
            }

            // Atualiza o valor do campo com a string filtrada
            event.target.value = value;
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const toggleBtnEdit = document.getElementById('telemedicineToggle_edit');
        const hiddenInputEdit = document.getElementById('is_telemedicine_edit');

        if (toggleBtnEdit) {
            toggleBtnEdit.addEventListener('click', function () {

                if (toggleBtnEdit.classList.contains('off')) {
                    // Mudar para SIM
                    toggleBtnEdit.classList.remove('off');
                    toggleBtnEdit.classList.add('on');
                    toggleBtnEdit.textContent = "SIM";
                    hiddenInputEdit.value = 1;
                } else {
                    // Mudar para NÃO
                    toggleBtnEdit.classList.remove('on');
                    toggleBtnEdit.classList.add('off');
                    toggleBtnEdit.textContent = "NÃO";
                    hiddenInputEdit.value = 0;
                }

            });
        }

    });
</script>
@endpush
