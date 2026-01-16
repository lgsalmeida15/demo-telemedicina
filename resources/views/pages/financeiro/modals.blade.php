<!-- Modals de Lançamentos Financeiros -->

{{-- Modal: Novo Lançamento --}}
<div class="modal fade" id="addFinanceModal" tabindex="-1" role="dialog" aria-labelledby="addFinanceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2 px-3 bg-success text-white">
                <h5 class="modal-title d-flex align-items-center mb-0" id="addFinanceModalLabel">
                    <i class="material-icons mr-2">add_circle</i> Novo Lançamento
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <form id="formAddFinance" method="POST" action="{{ route('financial.store') }}">
                @csrf
                <div class="modal-body pb-0">
                    <div class="form-row mb-3 align-items-end">
                        <div class="col-md-6 mb-2">
                            <label class="small mb-1 font-weight-bold">Data/Hora <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" name="data_hora_evento" class="form-control form-control-sm"
                                required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="small mb-1 font-weight-bold d-block">Tipo <span
                                    class="text-danger">*</span></label>
                            <!-- Toggle Entrada / Saída -->
                            <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                                <label class="btn btn-outline-success btn-sm flex-fill mb-0 active">
                                    <input type="radio" name="tipo" value="entrada" autocomplete="off" checked>
                                    Entrada
                                </label>
                                <label class="btn btn-outline-danger btn-sm flex-fill mb-0">
                                    <input type="radio" name="tipo" value="saida" autocomplete="off"> Saída
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small mb-1 font-weight-bold">Descrição <span class="text-danger">*</span></label>
                        <textarea name="descricao" rows="3" class="form-control form-control-sm mt-3"
                            placeholder="Digite uma breve descrição" required></textarea>
                    </div>

                    {{-- Caixa --}}
                    <div class="form-group mb-3">
                        <div class="col-md-4 mb-2">
                            <label class="small mb-1 font-weight-bold">Caixa <span class="text-danger">*</span></label>
                            <select name="caixa_id" id="caixa_id" class="form-control" required>
                                @foreach ($caixas as $caixa)
                                <option value="{{$caixa->id}}">{{$caixa->descricao}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row mb-3 align-items-end">
                        <div class="col-md-8 mb-2">
                            <label class="small mb-1 font-weight-bold">Centro de Custo <span
                                    class="text-danger">*</span></label>
                            <select name="centro_custo_id" id="centroCustoAdd"
                                class="form-control form-control-sm select2" data-placeholder="Selecione ou digite..."
                                required style="width:100%">
                                @foreach ($centrosDeCusto as $centro)
                                    <option value="{{ $centro->id }}">{{ $centro->descricao }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Digite para buscar ou pressione <kbd>Enter</kbd> para
                                adicionar.</small>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="small mb-1 font-weight-bold">Valor (R$) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                                <input type="text" name="valor" class="form-control mascara-moeda"
                                    placeholder="0,00" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal"><i
                            class="material-icons align-middle">close</i> Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="material-icons align-middle">check_circle</i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Editar Lançamento --}}
<div class="modal fade" id="editFinanceModal" tabindex="-1" role="dialog" aria-labelledby="editFinanceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2 px-3 bg-info text-white">
                <h5 class="modal-title d-flex align-items-center mb-0" id="editFinanceModalLabel"><i
                        class="material-icons mr-2">edit</i> Editar Lançamento</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <form id="formEditFinance" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="id" id="editId">
                <div class="modal-body pb-0">
                    <div class="form-row mb-3 align-items-end">
                        <div class="col-md-6 mb-2">
                            <label class="small mb-1 font-weight-bold">Data/Hora <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" name="data_hora_evento" id="editDataHora"
                                class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="small mb-1 font-weight-bold d-block">Tipo <span
                                    class="text-danger">*</span></label>
                            <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons" id="editTipoGroup">
                                <label class="btn btn-outline-success btn-sm flex-fill mb-0">
                                    <input type="radio" name="tipo" value="entrada" autocomplete="off"> Entrada
                                </label>
                                <label class="btn btn-outline-danger btn-sm flex-fill mb-0">
                                    <input type="radio" name="tipo" value="saida" autocomplete="off"> Saída
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small mb-1 font-weight-bold">Descrição <span
                                class="text-danger">*</span></label>
                        <textarea name="descricao" id="editDescricao" rows="3" class="form-control form-control-sm" required></textarea>
                    </div>

                    {{-- Caixa --}}
                    <div class="form-group mb-3">
                        <div class="col-md-4 mb-2">
                            <label class="small mb-1 font-weight-bold">Caixa <span class="text-danger">*</span></label>
                            <select name="caixa_id" id="caixa_id" class="form-control" required>
                                @foreach ($caixas as $caixa)
                                <option value="{{$caixa->id}}">{{$caixa->descricao}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row mb-3 align-items-end">
                        <div class="col-md-8 mb-2">
                            <label class="small mb-1 font-weight-bold">Centro de Custo <span
                                    class="text-danger">*</span></label>
                            <select name="centro_custo_id" id="centroCustoEdit"
                                class="form-control form-control-sm select2" data-placeholder="Selecione ou digite..."
                                required style="width:100%">
                                @foreach ($centrosDeCusto as $centro)
                                    <option value="{{ $centro->id }}">{{ $centro->descricao }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="small mb-1 font-weight-bold">Valor (R$) <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                                <input type="text" name="valor" id="editValor"
                                    class="form-control mascara-moeda" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal"><i
                            class="material-icons align-middle">close</i> Cancelar</button>
                    <button type="submit" class="btn btn-info">
                        <i class="material-icons align-middle">save</i>
                        Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.2/dist/select2-bootstrap4.min.css"
        rel="stylesheet">
    <style>
        /* === INPUTS & TEXTAREAS ===================================== */
        .form-control,
        .input-group .form-control {
            border: 1px solid #ced4da !important;
            border-radius: .3rem !important;
            box-shadow: none !important;
            background: unset;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 .2rem rgba(0, 123, 255, .25) !important;
        }

        textarea.form-control {
            resize: vertical;
        }

        /* === SELECT2 (Centro de Custo) ============================== */
        .select2-container .select2-selection--single {
            height: calc(1.5em + .5rem + 2px) !important;
            border: 1px solid #ced4da !important;
            border-radius: .3rem !important;
            padding: .25rem .5rem !important;
        }

        .select2-selection__rendered {
            line-height: 1.5 !important;
        }

        .select2-selection__arrow {
            height: 100% !important;
        }

        /* === TOGGLE ENTRADA / SAÍDA ================================= */
        .btn-group-toggle .btn {
            border: 1px solid #ced4da !important;
            font-weight: 600;
        }

        .btn-outline-success.active,
        .btn-outline-success:active {
            background-color: #28a745 !important;
            color: #fff !important;
            border-color: #28a745 !important;
        }

        .btn-outline-danger.active,
        .btn-outline-danger:active {
            background-color: #dc3545 !important;
            color: #fff !important;
            border-color: #dc3545 !important;
        }

        /* === VALUE INPUT (R$) ====================================== */
        .input-group-prepend .input-group-text {
            background: #e9ecef;
            border: 1px solid #ced4da;
            border-right: 0;
        }

        /* === GENERAL =============================================== */
        .modal-body label {
            margin-bottom: .25rem;
        }

        .mascara-moeda{
            padding-left: 10px;
            font-size: 25px;
            font-weight: bold;  
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        function initModalComponents(modal) {
            modal.find('.select2').select2({
                theme: 'bootstrap4',
                tags: true,
                placeholder: 'Selecione ou digite...',
                dropdownParent: modal,
                width: '100%'
            });
            modal.find('.mascara-moeda').mask('#.##0,00', {
                reverse: true
            });
        }
        $('#addFinanceModal').on('shown.bs.modal', function() {
            initModalComponents($(this));
        });
        $('#editFinanceModal').on('shown.bs.modal', function() {
            initModalComponents($(this));
        });
        // Ajusta radio ativo no modal editar ao preencher via JS externamente
        function setTipoEdit(valor) {
            const group = $('#editTipoGroup');
            group.find('input[value="' + valor + '"]').prop('checked', true).parent().addClass('active').siblings()
                .removeClass('active');
        }
        window.setTipoEdit = setTipoEdit; // disponível globalmente para preenchimento
    </script>
@endpush
