<!-- Modal de Criação de Tipo -->
<div class="modal fade" id="createTipoModal" tabindex="-1" role="dialog" aria-labelledby="createTipoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('convenio.type.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="createTipoModalLabel">Cadastrar Novo Tipo</h5>
                </div>
                <div class="modal-body">
                    {{-- Nome --}}
                    <div class="form-group">
                        <label for="name">Nome do Tipo <span style="color: red"> *</span></label>
                        <input type="text" class="form-control" name="name" id="name" required>
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
