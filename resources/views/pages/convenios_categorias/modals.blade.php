<!-- Modal de Criação de Categoria -->
<div class="modal fade" id="createCategoriaModal" tabindex="-1" role="dialog" aria-labelledby="createCategoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('convenio.categoria.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createCategoriaModalLabel">Cadastrar Nova Categoria</h5>
                </div>
                <div class="modal-body">
                    {{-- Nome --}}
                    <div class="form-group">
                        <label for="nome">Nome da Categoria <span style="color: red"> *</span></label>
                        <input type="text" class="form-control" name="nome" id="nome" required>
                    </div>
                    {{-- Descrição --}}
                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <textarea class="form-control" name="descricao" id="descricao" rows="3"></textarea>
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
