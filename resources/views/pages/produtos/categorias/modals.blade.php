<!-- Modal de Criação de Categoria -->
<div class="modal fade" id="createCategoriaModal" tabindex="-1" role="dialog" aria-labelledby="createCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('produtos.categorias.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createCategoriaModalLabel">Cadastrar Nova Categoria</h5>
                </div>
                <div class="modal-body">
                    {{-- Descrição --}}
                    <div class="form-group">
                        <label for="descricao">Descrição <span style="color: red">*</span></label>
                        <input type="text" class="form-control" name="descricao" id="descricao" rows="3" required>
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
