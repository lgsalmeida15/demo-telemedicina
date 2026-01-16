<!-- Modal de Criação de Grupo -->
<div class="modal fade" id="createGrupoModal" tabindex="-1" role="dialog" aria-labelledby="createGrupoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{route('produtos.grupos.store')}}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createGrupoModalLabel">Cadastrar Novo Grupo</h5>
                </div>
                <div class="modal-body">
                    {{-- Descrição --}}
                    <div class="form-group">
                        <label for="descricao">Descrição <span style="color: red">*</span></label>
                        <input type="text" class="form-control" name="descricao" id="descricao" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- Modal de Edição de Grupo -->
<div class="modal fade" id="editGrupoModal" tabindex="-1" role="dialog" aria-labelledby="editGrupoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="editGrupoForm">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editGrupoModalLabel">Editar Grupo</h5>
                </div>
                <div class="modal-body">
                    {{-- Descrição --}}
                    <div class="form-group">
                        <label for="edit_descricao">Descrição <span style="color: red">*</span></label>
                        <input type="text" class="form-control" name="descricao" id="edit_descricao" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar</button>
                </div>
            </div>
        </form>
    </div>
</div>

