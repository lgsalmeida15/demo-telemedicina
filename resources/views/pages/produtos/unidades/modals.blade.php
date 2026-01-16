<!-- Modal de Criação de Unidade -->
<div class="modal fade" id="createUnidadeModal" tabindex="-1" role="dialog" aria-labelledby="createUnidadeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{route('produtos.unidades.store')}}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createUnidadeModalLabel">Cadastrar Nova Unidade</h5>
                </div>
                <div class="modal-body">
                    {{-- Sigla --}}
                    <div class="form-group">
                        <label for="sigla">Sigla <span style="color: red">*</span></label>
                        <input type="text" class="form-control" name="sigla" id="sigla" placeholder="LT, KG..." required>
                    </div>
                    {{-- Descrição --}}
                    <div class="form-group">
                        <label for="descricao">Descrição <span style="color: red">*</span></label>
                        <input type="text" class="form-control" name="descricao" id="descricao" placeholder="Litro, Grama..." required>
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


<!-- Modal de Edição de Unidade -->
<div class="modal fade" id="editUnidadeModal" tabindex="-1" role="dialog" aria-labelledby="editUnidadeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="editUnidadeForm">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editUnidadeModalLabel">Editar Unidade</h5>
                </div>
                <div class="modal-body">
                    {{-- Sigla --}}
                    <div class="form-group">
                        <label for="edit_sigla">Sigla <span style="color: red">*</span></label>
                        <input type="text" class="form-control" name="sigla" id="edit_sigla" required>
                    </div>
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

