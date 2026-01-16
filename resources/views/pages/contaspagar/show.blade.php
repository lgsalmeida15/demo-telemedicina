@extends('layouts.app', ['activePage' => 'contas-pagar', 'titlePage' => __('Conta a Pagar')])

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
            <h4 class="card-title">Detalhes da Conta a Pagar: {{ $contaPagar->documento ?: '-' }}</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">

                <h4 class="mb-3 text-white">Informações Gerais</h4>
                <div class="row">
                  <div class="col-md-4 mb-3"><label>Documento</label><div><a href="{{ asset('storage/' . $contaPagar->documento) }}" target="_blank">{{ $contaPagar->documento ?: '-' }}</a></div></div>
                </div>
                <div class="row">
                  <div class="col-md-4 mb-3"><label>Status Autorização</label><div>{{ $contaPagar->status_autorizacao ?: '-' }}</div></div>
                </div>
                {{-- <div class="row">
                  <div class="col-md-4 mb-3"><label>Centro de Custo</label><div>{{ $contaPagar->centro_custo ?: '-' }}</div></div>
                </div> --}}

                <h4 class="mb-3 text-white">Datas</h4>
                <div class="row">
                  <div class="col-md-3 mb-3"><label>Emissão</label><div>{{ $contaPagar->emissao ? \Carbon\Carbon::parse($contaPagar->emissao)->format('d/m/Y') : '-' }}</div></div>
                </div>
                <div class="row">
                  <div class="col-md-3 mb-3"><label>Vencimento</label><div>{{ $contaPagar->vencimento ? \Carbon\Carbon::parse($contaPagar->vencimento)->format('d/m/Y') : '-' }}</div></div>
                </div>
                <div class="row">
                  <div class="col-md-3 mb-3"><label>Pagamento</label><div>{{ $contaPagar->pagamento ? \Carbon\Carbon::parse($contaPagar->pagamento)->format('d/m/Y') : '-' }}</div></div>
                </div>
                <div class="row">
                  <div class="col-md-3 mb-3"><label>Baixa</label><div>{{ $contaPagar->baixa ? \Carbon\Carbon::parse($contaPagar->baixa)->format('d/m/Y') : '-' }}</div></div>
                </div>

                <h4 class="mb-3 text-white">Valores</h4>
                <div class="row">
                  <div class="col-md-3 mb-3"><label>Valor</label><div>R$ {{ number_format($contaPagar->valor, 2, ',', '.') ?: '0,00' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Valor Pago</label><div>R$ {{ number_format($contaPagar->valor_pago, 2, ',', '.') ?: '0,00' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Desconto</label><div>R$ {{ number_format($contaPagar->valor_desconto, 2, ',', '.') ?: '0,00' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Multa</label><div>R$ {{ number_format($contaPagar->multa, 2, ',', '.') ?: '0,00' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Juros</label><div>R$ {{ number_format($contaPagar->juros, 2, ',', '.') ?: '0,00' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Valor Pago com Juros/Multa</label><div>R$ {{ number_format($contaPagar->valor_pago_juros_multa, 2, ',', '.') ?: '0,00' }}</div></div>
                </div>

                <h4 class="mb-3 text-white">Relacionamentos</h4>
                <div class="row">
                  <div class="col-md-4 mb-3"><label>Parceiro</label><div>{{ $contaPagar->partner->name ?? '-' }}</div></div>
                </div>
                <div class="row">
                  <div class="col-md-4 mb-3"><label>Plano de Contas</label><div>{{ $contaPagar->planoConta->descricao ?: '-' }} - {{$contaPagar->planoConta->codigo_reduzido}}</div></div>
                </div>
                <div class="row">
                  <div class="col-md-4 mb-3"><label>Caixa</label><div>{{ $contaPagar->caixa->descricao ?? '-' }}</div></div>
                </div>

                <h4 class="mb-3 text-white">Usuários</h4>
                <div class="row">
                  <div class="col-md-3 mb-3"><label>Cadastrado por</label><div>{{ $contaPagar->usuarioCadastro->name ?? '-' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Atualizado por</label><div>{{ $contaPagar->usuarioAtualizacao->name ?? '-' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Baixado por</label><div>{{ $contaPagar->usuarioBaixa->name ?? '-' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Excluído por</label><div>{{ $contaPagar->usuarioExclusao->name ?? '-' }}</div></div>
                </div>

                <h4 class="mb-3 text-white">Outros</h4>
                <div class="row">
                  <div class="col-md-12 mb-3"><label>Observações</label><div>{{ $contaPagar->obs ?: '-' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Tipo Baixa</label><div>{{ $contaPagar->tipo_baixa ?: '-' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Tipo Juros</label><div>{{ $contaPagar->tipo_juros ?: '-' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Cadastro</label><div>{{ $contaPagar->cadastro ? \Carbon\Carbon::parse($contaPagar->cadastro)->format('d/m/Y H:i') : '-' }}</div></div>
                  <div class="col-md-3 mb-3"><label>Última Atualização</label><div>{{ $contaPagar->atualizacao ? \Carbon\Carbon::parse($contaPagar->atualizacao)->format('d/m/Y H:i') : '-' }}</div></div>
                </div>

                <a class="btn btn-primary btn-block btn-lg btn-flutante" href="{{ route('conta_pagar.index') }}">Voltar</a>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
