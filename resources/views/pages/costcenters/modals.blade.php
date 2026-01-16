{{-- Modal: Adicionar Plano de Contas --}}
<div class="modal fade" id="addCostCenterModal" tabindex="-1" role="dialog" aria-labelledby="addCostCenterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2 px-3 bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center mb-0" id="addCostCenterModalLabel">
                    <i class="material-icons mr-2">add_circle</i> Novo Plano de Contas
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formAddCostCenter" method="POST" action="{{route('costcenter.store')}}">
                @csrf
                <div class="modal-body pb-0">
                    <div class="form-row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="small mb-1 font-weight-bold">Usuário</label>
                            <input type="text" name="usuario_id" class="form-control form-control-sm" list="usuariosList" required>

                            <datalist id="usuariosList">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>
                    
                    <div class="form-row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="small mb-1 font-weight-bold">Código Reduzido <span class="text-danger">*</span></label>
                            <input type="text" name="codigo_reduzido" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="small mb-1 font-weight-bold">Código da Conta <span class="text-danger">*</span></label>
                            <input type="text" name="codigo_conta" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small mb-1 font-weight-bold">Descrição <span class="text-danger">*</span></label>
                        <input type="text" name="descricao" class="form-control form-control-sm" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small mb-1 font-weight-bold d-block">Tipo <span class="text-danger">*</span></label>
                        <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                            <label class="btn btn-outline-success btn-sm flex-fill mb-0 active">
                                <input type="radio" name="tipo" value="D" autocomplete="off" checked> Débito
                            </label>
                            <label class="btn btn-outline-danger btn-sm flex-fill mb-0">
                                <input type="radio" name="tipo" value="C" autocomplete="off"> Crédito
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">
                        <i class="material-icons align-middle">close</i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="material-icons align-middle">check_circle</i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal de edição --}}
<div class="modal fade" id="editCostCenterModal" tabindex="-1" role="dialog" aria-labelledby="editCostCenterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2 px-3 bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center mb-0" id="editCostCenterModalLabel">
                    <i class="material-icons mr-2">edit</i> Editar Plano de Contas
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formEditCostCenter" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body pb-0">
                    <div class="form-row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="small mb-1 font-weight-bold">Usuário</label>
                            <input type="text" name="usuario_id" id="edit_usuario_id" class="form-control form-control-sm" list="usuariosList" required>
                            <datalist id="usuariosList">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>

                    <div class="form-row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="small mb-1 font-weight-bold">Código Reduzido <span class="text-danger">*</span></label>
                            <input type="text" name="codigo_reduzido" id="editid_reduzido" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="small mb-1 font-weight-bold">Código da Conta <span class="text-danger">*</span></label>
                            <input type="text" name="codigo_conta" id="editid_conta" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small mb-1 font-weight-bold">Descrição <span class="text-danger">*</span></label>
                        <input type="text" name="descricao" id="edit_descricao" class="form-control form-control-sm" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small mb-1 font-weight-bold d-block">Tipo <span class="text-danger">*</span></label>
                        <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                            <label class="btn btn-outline-success btn-sm flex-fill mb-0" id="label_tipo_d">
                                <input type="radio" name="tipo" id="edit_tipo_d" value="D" autocomplete="off"> Débito
                            </label>
                            <label class="btn btn-outline-danger btn-sm flex-fill mb-0" id="label_tipo_c">
                                <input type="radio" name="tipo" id="edit_tipo_c" value="C" autocomplete="off"> Crédito
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">
                        <i class="material-icons align-middle">close</i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary text-white">
                        <i class="material-icons align-middle">check_circle</i> Atualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Visualizar Plano de Contas --}}
<div class="modal fade" id="showCostCenterModal" tabindex="-1" role="dialog"
    aria-labelledby="showCostCenterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2 px-3 bg-primary text-white">
                <h5 class="modal-title mb-0" id="showCostCenterModalLabel">
                    <i class="material-icons mr-2">visibility</i> Plano de Contas
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">Usuário</dt>
                    <dd class="col-sm-8" id="show_usuario"></dd>

                    <dt class="col-sm-4">Código Reduzido</dt>
                    <dd class="col-sm-8" id="showid_reduzido"></dd>

                    <dt class="col-sm-4">Código da Conta</dt>
                    <dd class="col-sm-8" id="showid_conta"></dd>

                    <dt class="col-sm-4">Descrição</dt>
                    <dd class="col-sm-8" id="show_descricao"></dd>

                    <dt class="col-sm-4">Tipo</dt>
                    <dd class="col-sm-8" id="show_tipo"></dd>

                    <dt class="col-sm-4">Cadastro</dt>
                    <dd class="col-sm-8" id="show_cadastro"></dd>

                    <dt class="col-sm-4">Atualização</dt>
                    <dd class="col-sm-8" id="show_atualizacao"></dd>

                    <dt class="col-sm-4">Exclusão</dt>
                    <dd class="col-sm-8" id="show_exclusao"></dd>
                </dl>
            </div>

            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <i class="material-icons align-middle">close</i> Fechar
                </button>
            </div>
        </div>
    </div>
</div>


