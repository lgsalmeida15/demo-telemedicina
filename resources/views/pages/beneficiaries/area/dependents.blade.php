@extends('layouts.app', ['activePage' => 'dependents', 'titlePage' => __('Meus Dependentes')])

@section('content')

<style>

    /* ======== GLASS UI ======== */
    .glass-card {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(12px);
        border-radius: 18px;
        border: 1px solid rgba(255,255,255,0.35);
        box-shadow: 0 8px 28px rgba(0,0,0,0.12);
        transition: .3s ease;
    }
    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.18);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f6f92;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* ======== DEPENDENT CARD ======== */
    .dep-box {
        padding: 1.3rem;
        border-left: 6px solid #4081F6;
        border-radius: 14px;
        background: #ffffff;
        transition: .3s ease;
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    }
    .dep-box:hover {
        transform: translateX(6px);
        box-shadow: 0 6px 22px rgba(0,0,0,0.12);
    }

    .dep-label {
        font-size: .8rem;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        margin-bottom: 4px;
    }

    .dep-value {
        font-size: 1.05rem;
        font-weight: 600;
        color: #0d2248;
    }

    .btn-add {
        background: #4081F6;
        color: white !important;
        border-radius: 12px;
        padding: 10px 18px;
        font-weight: 600;
        transition: .3s ease;
        box-shadow: 0 4px 12px rgba(64,129,246,0.35);
    }
    .btn-add:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(64,129,246,0.45);
    }
    /* Estilo adicional para os botões de ação serem compactos, como na outra view */
    .btn-action-dep {
        padding: 5px 8px; /* Reduz o padding para ser mais compacto */
        line-height: 1; /* Garante que o ícone fique centralizado */
        border-radius: 8px;
        margin-left: 5px;
    }
    .btn-danger {
        background-color: #f44336;
        color: white;
    }

</style>


<div class="content">
    <div class="container-fluid">

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="glass-card p-4 d-flex justify-content-between align-items-center">

                    <div>
                        <h3 class="section-title">
                            <i class="material-icons">group</i> Seus Dependentes
                        </h3>
                        <p class="text-muted mb-0">Gerencie os dependentes vinculados ao seu benefício.</p>
                    </div>

                    <div>
                        <a href="{{route('dependent.create', ['beneficiaryId' => $beneficiary->id])}}" class="btn btn-add">
                            <i class="material-icons">add</i> Adicionar
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="glass-card p-4">

                    @forelse ($dependents as $dep)

                    <div class="dep-box mb-4">

                        <div class="row align-items-center"> {{-- Adicionei align-items-center para alinhamento vertical --}}
                            <div class="col-md-4">
                                <div class="dep-label">Nome</div>
                                <div class="dep-value">{{ $dep->name }}</div>
                            </div>

                            <div class="col-md-3">
                                <div class="dep-label">CPF</div>
                                <div class="dep-value">{{ $dep->cpf ?: '-' }}</div>
                            </div>

                            <div class="col-md-3">
                                <div class="dep-label">Parentesco</div>
                                <div class="dep-value">{{ $dep->relationship ?: '-' }}</div>
                            </div>

                            <div class="col-md-2 actions d-flex align-items-center justify-content-end">

                                <a href="{{route('dependent.show', ['dependent' => $dep->id])}}" class="btn btn-primary btn-sm btn-action-dep" title="Ver">
                                    <i class="material-icons">visibility</i>
                                </a>

                                <a href="{{route('dependent.edit', ['dependent' => $dep->id])}}" class="btn btn-primary btn-sm btn-action-dep" title="Editar">
                                    <i class="material-icons">edit</i>
                                </a>

                                <button
                                    class="btn btn-danger btn-sm btn-action-dep"
                                    type="button"
                                    data-toggle="modal"
                                    data-target="#deleteDependentModal"
                                    data-id="{{ $dep->id }}"
                                    data-action="{{ route('dependent.softdelete', ['dependent'=>$dep->id]) }}"
                                    title="Excluir">
                                    <i class="material-icons">delete</i>
                                </button>
                                {{-- Removida a classe 'dropdown-item' e adicionado um ícone para consistência visual --}}

                            </div>
                        </div>

                    </div>

                    @empty

                    <p class="text-muted text-center mb-0">
                        Nenhum dependente cadastrado.
                    </p>

                    @endforelse

                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="deleteDependentModal" tabindex="-1" role="dialog" aria-labelledby="deleteDependentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="deleteDependentForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteDependentModalLabel">Confirmar Exclusão</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Tem certeza de que deseja excluir o dependente?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


@push('js')
<script>
// Lógica para preencher o formulário do modal de exclusão (Mantida, pois estava correta)
$('#deleteDependentModal').on('show.bs.modal', function (event) {
    let button = $(event.relatedTarget);
    let action = button.data('action');
    // Verifica se a ação está completa (contém o ID do dependente)
    if (action.indexOf('/delete') === -1) {
        console.error("Ação de delete incompleta ou incorreta:", action);
    }
    $(this).find('#deleteDependentForm').attr('action', action);
});
</script>
@endpush

@endsection