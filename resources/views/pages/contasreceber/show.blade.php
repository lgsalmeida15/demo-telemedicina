@extends('layouts.app', ['activePage' => 'contas-receber', 'titlePage' => __('Conta a Receber')])

@section('content')

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
    }
</style>

<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header card-header-primary">
            <h4 class="card-title">Detalhes da Conta a Receber: {{ $contaReceber->documento ?: '-' }}</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">

                <h4 class="mb-3 text-white">Informações Gerais</h4>
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label>Documento</label>
                    <div>
                      @if ($contaReceber->documento)
                        <a href="{{ asset('storage/' . $contaReceber->documento) }}" target="_blank">{{ $contaReceber->documento }}</a>
                      @else
                        -
                      @endif
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-4 mb-3"><label>Status Autorização</label><div>{{ $contaReceber->status_autorizacao ?: '-' }}</div></div>
                </div>

                <h4 class="mb-3 text-white">Datas</h4>
                <div class="row">
                  <div class="col-md-3 mb-3"><label>Emissão</label><div>{{ $contaReceber->emissao ? \Carbon\Carbon::parse($contaReceber->emissao)->format('d/m/Y') : '-' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Vencimento</label><div>{{ $contaReceber->vencimento ? \Carbon\Carbon::parse($contaReceber->vencimento)->format('d/m/Y') : '-' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Pagamento</label><div>{{ $contaReceber->pagamento ? \Carbon\Carbon::parse($contaReceber->pagamento)->format('d/m/Y') : '-' }}</div></div>
                </div>

                <h4 class="mb-3 text-white">Valores</h4>
                <div class="row">
                  <div class="col-md-3 mb-3"><label>Valor</label><div>R$ {{ number_format($contaReceber->valor, 2, ',', '.') ?: '0,00' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Valor Pago</label><div>R$ {{ number_format($contaReceber->valor_pago, 2, ',', '.') ?: '0,00' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Desconto</label><div>R$ {{ number_format($contaReceber->valor_desconto, 2, ',', '.') ?: '0,00' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Multa</label><div>R$ {{ number_format($contaReceber->multa, 2, ',', '.') ?: '0,00' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Juros</label><div>R$ {{ number_format($contaReceber->juros, 2, ',', '.') ?: '0,00' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Valor Pago com Juros/Multa</label><div>R$ {{ number_format($contaReceber->valor_pago_juros_multa, 2, ',', '.') ?: '0,00' }}</div></div>
                </div>

                <h4 class="mb-3 text-white">Relacionamentos</h4>
                <div class="row">
                  <div class="col-md-4 mb-3"><label>Plano de Contas</label><div>{{ $contaReceber->costCenter->descricao ?? '-' }}</div></div>
                  <div class="col-md-4 mb-3"><label>Caixa</label><div>{{ $contaReceber->caixa->descricao ?? '-' }}</div></div>
                </div>

                <h4 class="mb-3 text-white">Outros</h4>
                <div class="row">
                  <div class="col-md-12 mb-3"><label>Observações</label><div>{{ $contaReceber->obs ?: '-' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Tipo Baixa</label><div>{{ $contaReceber->tipo_baixa ?: '-' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Tipo Juros</label><div>{{ $contaReceber->tipo_juros ?: '-' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Cadastro</label><div>{{ $contaReceber->cadastro ? \Carbon\Carbon::parse($contaReceber->cadastro)->format('d/m/Y H:i') : '-' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Última Atualização</label><div>{{ $contaReceber->atualizacao ? \Carbon\Carbon::parse($contaReceber->atualizacao)->format('d/m/Y H:i') : '-' }}</div></div>
                </div>

                <a class="btn btn-primary btn-block btn-lg btn-flutante" href="{{ route('conta_receber.index') }}">Voltar</a>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
