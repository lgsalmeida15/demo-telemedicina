@extends("layouts.app", ["activePage" => "contas_a_receber", "titlePage" => __("Contas a Receber")])

@section("content")

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

    .btn-flutante {
        position: fixed;
        bottom: 10px;
        width: calc(100% - 260px - 100px);
        z-index: 1000;
        left: calc(260px + 50px);
        margin-bottom: 65px
    }
</style>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-primary">
            <h4 class="card-title">Adicionar nova conta a receber</h4>
            <p class="card-category">Campos marcados com * são obrigatórios</p>
          </div>

          <div class="card-body">
                <form action="{{route('conta_receber.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            {{-- Documento --}}
                            <div class="col-md-9">
                                <label for="documento" class="form-label fw-bold mb-2">Anexar o documento da conta<span style="color: red"> *</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Anexe um arquivo" readonly id="fileNameDisplay" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-success btn-round" onclick="document.getElementById('inputFile').click()">
                                            <i class="material-icons">attach_file</i>
                                        </button>
                                    </div>
                                </div>
                                <input type="file" id="inputFile" name="documento" accept="pdf/png/jpeg/jpg/csv/**" style="display: none;" onchange="previewImagem(this)">
                            </div>
                        </div>

                        <div class="row mt-3">
                            {{-- Plano de Contas --}}
                            <div class="col-md-4 mb-2">
                                <label class="mb-1 font-weight-bold">Código Plano de Contas <span style="color:red;"> *</span></label>
                                <select name="plano_contas_id" class="form-control form-control-sm" required>
                                    <option value="">Nenhum</option>
                                    @foreach ($costCenters as $costCenter)
                                    <option value="{{$costCenter->id}}">{{$costCenter->descricao}} - Código da conta: {{$costCenter->codigo_conta}}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Caixa --}}
                            <div class="col-md-4 mb-2">
                                <label class="mb-1 font-weight-bold">Caixa<span style="color:red;"> *</span> </label>
                                <select name="caixa_id" class="form-control form-control-sm">
                                    <option value="">Nenhum</option>
                                    @foreach ($caixas as $caixa)
                                    <option value="{{$caixa->id}}">{{$caixa->descricao}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Valor --}}
                            <div class="col-md-4 mb-2">
                                <label class="mb-1 font-weight-bold">Valor (R$) <span style="color: red"> *</span></label>
                                <input type="number" step="0.01" name="valor" class="form-control form-control-sm" required value="{{ old('valor', 0) }}">
                            </div>

                            {{-- Status de Autorização --}}
                            <div class="col-md-4 mb-2">
                                <label class="mb-1 font-weight-bold">Status de Autorização</label>
                                <select name="status_autorizacao" class="form-control form-control-sm">
                                    <option value="Aguardando">Aguardando</option>
                                    <option value="Aprovado">Aprovado</option>
                                    <option value="Recusado">Recusado</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Emissão --}}
                            <div class="col-md-3 mb-2">
                                <label class="mb-1 font-weight-bold">Data de Emissão <span style="color: red"> *</span></label>
                                <input type="date" name="emissao" class="form-control form-control-sm" required>
                            </div>

                            {{-- Vencimento --}}
                            <div class="col-md-3 mb-2">
                                <label class="mb-1 font-weight-bold">Data de Vencimento <span style="color: red"> *</span></label>
                                <input type="date" name="vencimento" class="form-control form-control-sm" required>
                            </div>

                            {{-- Pagamento --}}
                            <div class="col-md-3 mb-2">
                                <label class="mb-1 font-weight-bold">Data de Pagamento</label>
                                <input type="date" name="pagamento" class="form-control form-control-sm">
                            </div>

                            {{-- Tipo de Baixa --}}
                            <div class="col-md-3 mb-2">
                                <label class="mb-1 font-weight-bold">Tipo de Baixa</label>
                                <input type="text" name="tipo_baixa" maxlength="2" class="form-control form-control-sm">
                            </div>
                        </div>

                        <br>
                        <h4>Informações de Pagamento</h4>
                        <div class="row">
                            {{-- Juros --}}
                            <div class="col-md-3 mb-2">
                                <label class="mb-1 font-weight-bold">Juros (R$)</label>
                                <input type="number" step="0.01" name="juros" class="form-control form-control-sm">
                            </div>

                            {{-- Multa --}}
                            <div class="col-md-3 mb-2">
                                <label class="mb-1 font-weight-bold">Multa (R$)</label>
                                <input type="number" step="0.01" name="multa" class="form-control form-control-sm">
                            </div>

                            {{-- Desconto --}}
                            <div class="col-md-3 mb-2">
                                <label class="mb-1 font-weight-bold">Desconto (R$)</label>
                                <input type="number" step="0.01" name="valor_desconto" class="form-control form-control-sm">
                            </div>

                            {{-- Valor Pago --}}
                            <div class="col-md-3 mb-2">
                                <label class="mb-1 font-weight-bold">Valor Pago (R$)</label>
                                <input type="number" step="0.01" name="valor_pago" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="row">
                            {{-- Tipo de Juros --}}
                            <div class="col-md-6 mb-2">
                                <label class="mb-1 font-weight-bold">Tipo de Juros</label>
                                <input type="text" name="tipo_juros" class="form-control form-control-sm">
                            </div>

                            {{-- Valor Pago de Juros + Multa --}}
                            <div class="col-md-6 mb-2">
                                <label class="mb-1 font-weight-bold">Valor Pago de Juros e Multa (R$)</label>
                                <input type="number" step="0.01" name="valor_pago_juros_multa" class="form-control form-control-sm">
                            </div>
                        </div>

                        {{-- Observações --}}
                        <div class="form-group mt-3">
                            <label class="mb-1 font-weight-bold">Observações</label>
                            <textarea name="obs" rows="3" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="btn-flutante d-flex justify-content-between gap-3">
                        <button type="submit" class="btn btn-primary btn-lg w-50">SALVAR CONTA</button>
                        <a href="{{route('conta_receber.index')}}" class="btn btn-secondary btn-lg w-50">VOLTAR</a>
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
<script>
    document.getElementById('inputFile').addEventListener('change', function () {
        const fileNames = Array.from(this.files).map(file => file.name).join(', ');
        document.getElementById('fileNameDisplay').value = fileNames;
    });

    document.getElementById('fileNameDisplay').addEventListener('click', function () {
        document.getElementById('inputFile').click();
    });
</script>
@endpush
