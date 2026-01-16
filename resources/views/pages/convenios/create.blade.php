@extends("layouts.app", ["activePage" => "convenios", "titlePage" => __("Serviços")])

@section("content")

{{-- Estilos adicionais para o Select2 --}}
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Adaptação para o estilo do Material Dashboard */
    .select2-container .select2-selection--single {
        height: calc(2.25rem + 2px);
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
                        <h4 class="card-title">Cadastrar Novo Serviço</h4>
                        <p class="card-category">Campos marcados com * são obrigatórios</p>
                    </div>

                    <div class="card-body">
                        <form action="{{route('convenio.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Informações do Serviço --}}
                            <div class="row mb-3">
                                {{-- Parceiro --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h5 for="partner_id" class="form-label">Parceiro <span style="color: red"> *</span></h5>
                                        <select name="partner_id" id="partner_select" class="form-control" required>
                                            <option value="" disabled selected>Selecione o parceiro</option>
                                            @foreach($partners as $partner)
                                                <option value="{{ $partner->id }}" {{ old('partner_id') == $partner->id ? 'selected' : '' }}>
                                                    {{ $partner->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {{-- Categoria de Serviço --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h5 for="convenio_categoria_id" class="form-label">Categoria <span style="color: red"> *</span></h5>
                                        <select name="convenio_categoria_id" id="convenio_categoria_id" class="form-control" required>
                                            <option value="" disabled selected>Selecione ou crie uma categoria</option>
                                            @foreach($categorias as $categoria)
                                                <option value="{{ $categoria->id }}" {{ old('convenio_categoria_id') == $categoria->id ? 'selected' : '' }}>
                                                    {{ $categoria->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                {{-- Categoria de Serviço --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h5 for="convenio_type_id" class="form-label">Tipo <span style="color: red"> *</span></h5>
                                        <select name="convenio_type_id" id="convenio_type_id" class="form-control" required>
                                            <option value="" disabled selected>Selecione ou crie um tipo</option>
                                            @foreach($types as $type)
                                                <option value="{{ $type->id }}" {{ old('convenio_type_id') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h5 for="nome_convenio" class="form-label">Nome do Serviço<span style="color: red"> *</span></h5>
                                        <input type="text" name="nome_convenio" id="nome_convenio" class="form-control" value="{{ old("nome_convenio") }}" maxlength="255" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-group">
                                    <h5 for="descricao" class="form-label">Descrição</h5>
                                    <textarea name="descricao" id="descricao" rows="3" class="form-control">{{ old("descricao") }}</textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <h5 for="desconto_percentual" class="form-label">Desconto Percentual (%)</h5>
                                        <input type="number" step="0.01" name="desconto_percentual" id="desconto_percentual" class="form-control" value="{{ old("desconto_percentual", 0) }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <h5 for="data_inicio" class="form-label">Data de Início<span style="color: red"> *</span></h5>
                                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ old("data_inicio") }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <h5 for="data_fim" class="form-label">Data de Fim</h5>
                                        <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ old("data_fim") }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Contato --}}
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <h5 for="contato" class="form-label">Contato</h5>
                                        <input type="text" name="contato" id="contato" class="form-control" maxlength="255" value="{{ old("contato") }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Status --}}
                            <h4 class="mb-3">Status</h4>
                            <div class="mb-3">
                                <div class="form-group">
                                    <h5 for="status" class="form-label">Status</h5>
                                    <select name="status" id="status" class="form-control" required>
                                        @foreach(["Ativo", "Inativo"] as $statusOption)
                                            <option value="{{ $statusOption }}" {{ old("status", "Ativo") == $statusOption ? "selected" : "" }}>{{ $statusOption }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br>

                            <div class="btn-flutante d-flex justify-content-between gap-3">
                                <button type="submit" class="btn btn-primary btn-lg w-50">SALVAR SERVIÇO</button>
                                <a href="{{ route("convenio.index") }}" class="btn btn-secondary btn-lg w-50">VOLTAR</a>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // Inicializa o Select2 para o parceiro
        $('#partner_select').select2({
            placeholder: "Selecione o parceiro",
            allowClear: true,
            width: '100%',
        });

        // Inicializa o Select2 com tags para a categoria de serviço
        $('#convenio_categoria_id').select2({
            tags: true,
            placeholder: 'Selecione ou crie uma categoria apertando ENTER',
            width: '100%',
            language: {
                noResults: () => 'Nenhum resultado encontrado',
                inputTooShort: () => 'Digite para buscar ou criar nova categoria',
            },
            createTag: function (params) {
                const term = $.trim(params.term);
                if (term === '') return null;
                return {
                    id: '__new__:' + term,
                    text: term,
                    newOption: true
                };
            },
            templateResult: function (data) {
                const $result = $("<span></span>");
                $result.text(data.text);
                if (data.newOption) {
                    $result.append(" <em>(novo)</em>");
                }
                return $result;
            }
        });
        // Inicializa o Select2 com tags para a categoria de serviço
        $('#convenio_type_id').select2({
            tags: true,
            placeholder: 'Selecione ou crie um tipo apertando ENTER depois de escrever o nome',
            width: '100%',
            language: {
                noResults: () => 'Nenhum resultado encontrado',
                inputTooShort: () => 'Digite para buscar ou criar novo tipo',
            },
            createTag: function (params) {
                const term = $.trim(params.term);
                if (term === '') return null;
                return {
                    id: '__new__:' + term,
                    text: term,
                    newOption: true
                };
            },
            templateResult: function (data) {
                const $result = $("<span></span>");
                $result.text(data.text);
                if (data.newOption) {
                    $result.append(" <em>(novo)</em>");
                }
                return $result;
            }
        });

        // Manipula a seleção de uma nova categoria via AJAX
        $('#convenio_categoria_id').on('select2:select', function (e) {
            const data = e.params.data;
            if (data.id.startsWith('__new__:')) {
                const nomeCategoria = data.text;
                $.ajax({
                    url: '{{ route("convenio.categoria.store_ajax") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        nome: nomeCategoria
                    },
                    success: function (response) {
                        const newOption = new Option(response.nome, response.id, true, true);
                        $('#convenio_categoria_id').append(newOption).trigger('change');
                    },
                    error: function () {
                        alert('Erro ao criar nova categoria.');
                        $('#convenio_categoria_id').val(null).trigger('change');
                    }
                });
            }
        });
        // Manipula a seleção de uma novo tipo via AJAX
        $('#convenio_type_id').on('select2:select', function (e) {
            const data = e.params.data;
            if (data.id.startsWith('__new__:')) {
                const typeNome = data.text;
                $.ajax({
                    url: '{{ route("convenio.type.store.ajax") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        name: typeNome
                    },
                    success: function (response) {
                        const newOption = new Option(response.name, response.id, true, true);
                        $('#convenio_type_id').append(newOption).trigger('change');
                    },
                    error: function () {
                        alert('Erro ao criar novo type.');
                        $('#convenio_type_id').val(null).trigger('change');
                    }
                });
            }
        });
    });
</script>
@endpush
