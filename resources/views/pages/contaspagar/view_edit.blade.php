@extends("layouts.app", ["activePage" => "contas_a_pagar", "titlePage" => __("Editar Conta a Pagar")])

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
        margin-bottom: 65px;
    }
</style>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-primary">
            <h4 class="card-title">Editar Conta a Pagar: {{ $conta->documento ?: '-' }}</h4>
          </div>

          <div class="card-body">
            <form method="POST" action="{{ route('conta_pagar.update', ['conta'=>$conta->id]) }}" enctype="multipart/form-data">
              @csrf
              @method('PUT')

              <div class="modal-body">
                <div class="row">
                  <div class="col-md-9">
                    <label for="documento">Documento/Boleto</label>
                    <div class="input-group">
                      <input type="text" class="form-control" readonly id="fileNameDisplay" value="{{ $conta->documento }}" required>
                      <div class="input-group-append">
                        <button type="button" class="btn btn-success btn-round" onclick="document.getElementById('inputFile').click()">
                          <i class="material-icons">attach_file</i>
                        </button>
                      </div>
                    </div>
                    <input type="file" id="inputFile" name="documento" accept="pdf/png/jpeg/jpg/csv/**" style="display: none;" onchange="previewImagem(this)">
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-4 mb-2">
                    <label class="mb-1 font-weight-bold">Código Plano de Contas <span style="color:red;"> *</span></label>
                    <select name="cost_center_id" class="form-control form-control-sm" required>
                      <option value="">Nenhum</option>
                      @foreach ($costCenters as $costCenter)
                        <option value="{{ $costCenter->id }}" {{ $conta->cost_center_id == $costCenter->id ? 'selected' : '' }}>
                          {{ $costCenter->descricao }} - Código da conta: {{ $costCenter->codigo_conta }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  {{-- Caixa --}}
                  <div class="col-md-4 mb-2">
                      <label class="mb-1 font-weight-bold">Caixa</label>
                      <select name="caixa_id" class="form-control form-control-sm">
                          @foreach ($caixas as $caixa)
                              <option value="{{ $caixa->id }}"
                                {{ old('caixa_id', $conta->caixa_id) == $caixa->id ? 'selected' : '' }}>
                                {{ $caixa->descricao }}
                              </option>
                          @endforeach
                      </select>
                  </div>
                  <div class="col-md-4 mb-2">
                    <label>Parceiro</label>
                    <select name="partner_id" class="form-control form-control-sm" required>
                      @foreach ($partners as $partner)
                        <option value="{{ $partner->id }}" {{ $conta->partner_id == $partner->id ? 'selected' : '' }}>
                          {{ $partner->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-4 mb-2">
                    <label>Valor Original (R$)</label>
                    <input type="number" step="0.01" name="valor" class="form-control form-control-sm" value="{{ old('valor', $conta->valor) }}">
                  </div>

                  <div class="col-md-4 mb-2">
                    <label>Status de Autorização</label>
                    <select name="status_autorizacao" class="form-control form-control-sm">
                      <option value="Aguardando" {{ $conta->status_autorizacao == 'Aguardando' ? 'selected' : '' }}>Aguardando</option>
                      <option value="Aprovado" {{ $conta->status_autorizacao == 'Aprovado' ? 'selected' : '' }}>Aprovado</option>
                      <option value="Recusado" {{ $conta->status_autorizacao == 'Recusado' ? 'selected' : '' }}>Recusado</option>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3 mb-2"><label>Data de Emissão <span style="color: red"> *</span></label><input type="date" name="emissao" class="form-control form-control-sm" value="{{ $conta->emissao }}" required></div>
                  <div class="col-md-3 mb-2"><label>Data de Vencimento <span style="color: red"> *</span></label><input type="date" name="vencimento" class="form-control form-control-sm" value="{{ $conta->vencimento }}" required></div>
                  <div class="col-md-3 mb-2"><label>Data de Pagamento</label><input type="date" name="pagamento" class="form-control form-control-sm" value="{{ $conta->pagamento }}"></div>
                  <div class="col-md-3 mb-2"><label>Tipo de Baixa</label><input type="text" name="tipo_baixa" maxlength="2" class="form-control form-control-sm" value="{{ $conta->tipo_baixa }}"></div>
                </div>

                <br>
                <h4>Informações de Pagamento</h4>
                <div class="row">
                  <div class="col-md-3 mb-2"><label>Juros (R$)</label><input type="number" step="0.01" name="juros" class="form-control form-control-sm" value="{{ $conta->juros }}"></div>
                  <div class="col-md-3 mb-2"><label>Multa (R$)</label><input type="number" step="0.01" name="multa" class="form-control form-control-sm" value="{{ $conta->multa }}"></div>
                  <div class="col-md-3 mb-2"><label>Desconto (R$)</label><input type="number" step="0.01" name="valor_desconto" class="form-control form-control-sm" value="{{ $conta->valor_desconto }}"></div>
                  <div class="col-md-3 mb-2"><label>Valor Pago (R$)</label><input type="number" step="0.01" name="valor_pago" class="form-control form-control-sm" value="{{ $conta->valor_pago }}"></div>
                </div>

                <div class="row">
                  <div class="col-md-4 mb-2"><label>Tipo de Juros</label><input type="text" name="tipo_juros" class="form-control form-control-sm" value="{{ $conta->tipo_juros }}"></div>
                  <div class="col-md-4 mb-2"><label>Valor Pago de Juros e Multa</label><input type="number" step="0.01" name="valor_pago_juros_multa" class="form-control form-control-sm" value="{{ $conta->valor_pago_juros_multa }}"></div>
                  
                </div>

                <div class="form-group mt-3">
                  <label>Observações</label>
                  <textarea name="obs" rows="3" class="form-control">{{ $conta->obs }}</textarea>
                </div>
              </div>

              <div class="btn-flutante d-flex justify-content-between gap-3">
                <button type="submit" class="btn btn-primary btn-lg w-50">SALVAR ALTERAÇÕES</button>
                <a href="{{ route('conta_pagar.index') }}" class="btn btn-secondary btn-lg w-50">VOLTAR</a>
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
        const fileInput = this;
        const fileNames = Array.from(fileInput.files).map(file => file.name).join(', ');
        document.getElementById('fileNameDisplay').value = fileNames;
    });

    document.getElementById('fileNameDisplay').addEventListener('click', function () {
        document.getElementById('inputFile').click();
    });
</script>
@endpush
