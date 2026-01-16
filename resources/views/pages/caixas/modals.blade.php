{{-- Modal: Adicionar Caixa --}}
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2 px-3 bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center mb-0" id="createModalLabel">
                    <i class="material-icons mr-2">add_circle</i> Novo Caixa
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formCreate" method="POST" action="{{route('caixa.store')}}">
                @csrf
                <div class="modal-body pb-0">
                    <div class="form-row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="small mb-1 font-weight-bold">Descrição <span style="color: red"> *</span></label>
                            <input type="text" name="descricao" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="small mb-1 font-weight-bold">Observação</label>
                            <input type="text" name="obs" class="form-control form-control-sm">
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




{{-- Modal: Atualizar Caixa --}}
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2 px-3 bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center mb-0" id="updateModalLabel">
                    <i class="material-icons mr-2">add_circle</i> Atualizar Caixa
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formUpdate" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body pb-0">
                    <div class="form-row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="small mb-1 font-weight-bold">Descrição <span style="color: red"> *</span></label>
                            <input type="text" name="descricao" id="descricao" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="small mb-1 font-weight-bold">Observação</label>
                            <input type="text" name="obs" id="obs" class="form-control form-control-sm">
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





{{-- Modal: Visualizar Plano de Contas --}}
<div class="modal fade" id="showModal" tabindex="-1" role="dialog"
    aria-labelledby="showModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header py-2 px-3 bg-primary text-white">
                <h5 class="modal-title mb-0" id="showModalLabel">
                    <i class="material-icons mr-2">visibility</i> Detalhes do Caixa
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">Descrição</dt>
                    <dd class="col-sm-8" id="show_descricao"></dd><br>

                    <dt class="col-sm-4">Observação</dt>
                    <dd class="col-sm-8" id="show_obs"></dd><br>

                    <dt class="col-sm-4">Cadastro</dt>
                    <dd class="col-sm-8" id="show_cadastro"></dd><br>

                    <dt class="col-sm-4">Atualização</dt>
                    <dd class="col-sm-8" id="show_atualizacao"></dd><br>

                    <dt class="col-sm-4">Exclusão</dt>
                    <dd class="col-sm-8" id="show_exclusao"></dd><br>
                </dl>
            </div>
        </div>
    </div>
</div>


