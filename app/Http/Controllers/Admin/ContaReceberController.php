<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ContaReceberRepository;
use App\Models\ContaReceber;
use App\Models\CostCenter;
use App\Models\Partner;
use App\Models\Financial;
use App\Models\Caixa;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContaReceberController extends Controller
{
    /**
     * @var ContaReceberRepository
     */
    protected $contaReceberRepository;

    /**
     * ContaReceberController constructor.
     *
     * @param ContaReceberRepository $contaReceberRepository
     */
    public function __construct(ContaReceberRepository $contaReceberRepository)
    {
        $this->contaReceberRepository = $contaReceberRepository;
    }

    public function index()
    {
        $contas = $this->contaReceberRepository->whereNull('exclusao')->get();
        return view('pages.contasreceber.index', compact('contas'));
    }

    public function create () {
        $costCenters = CostCenter::whereNull('exclusao')->get();
        $partners = Partner::whereNull('deleted_at')->get();
        $caixas = Caixa::whereNull('exclusao')->get();

        return view ('pages.contasreceber.create', compact(
            'costCenters',
            'partners',
            'caixas'
        ));
    }

    public function store (Request $request) {
        $validated = $request->validate([
            'documento' => 'nullable|file',
            'plano_contas_id' => 'nullable|integer',
            'centro_custo' => 'nullable|string',
            'partner_id' => 'nullable|integer',
            'valor' => 'nullable',
            'valor_pago' => 'nullable|numeric',
            'valor_desconto' => 'nullable|numeric',
            'juros' => 'nullable|numeric',
            'multa' => 'nullable|numeric',
            'tipo_juros' => 'nullable|string',
            'valor_pago_juros_multa' => 'nullable|numeric',
            'tipo_baixa' => 'nullable|string|max:2',
            'status_autorizacao' => 'nullable|in:Aguardando,Aprovado,Recusado',
            'emissao' => 'nullable|date',
            'vencimento' => 'nullable|date',
            'pagamento' => 'nullable|date',
            'caixa_id' => 'nullable|integer',
            'obs' => 'nullable|string',
        ]);

        // Upload do documento
        if ($request->hasFile('documento')) {
            $documentoPath = $request->file('documento')->store('contasreceber_documentos', 'public');
            $validated['documento'] = $documentoPath;
        }

        $validated['usuario_cadastro_id'] = auth()->id();
        $validated['cadastro'] = Carbon::now();

        $this->contaReceberRepository->create($validated);

        return redirect()->route('conta_receber.index')->with('sucesso', 'Conta a receber criada com sucesso!');
    }


    public function show ($conta) {
        $contaReceber = $this->contaReceberRepository->findOrFail($conta);
        return view('pages.contasreceber.show', compact('contaReceber'));
    }


    public function view_edit($conta) {
        $contaReceber = $this->contaReceberRepository->findOrFail($conta);
        $costCenters = CostCenter::whereNull('exclusao')->get();
        $partners = Partner::whereNull('deleted_at')->get();
        $caixas = Caixa::whereNull('exclusao')->get();

        return view ('pages.contasreceber.view_edit', compact(
            'contaReceber',
            'costCenters',
            'partners',
            'caixas'
        ));
    }


    public function update (Request $request, $conta) {

        $conta = $this->contaReceberRepository->findOrFail($conta);

        $validated = $request->validate([
            'documento' => 'nullable|file',
            'plano_contas_id' => 'nullable|integer',
            'centro_custo' => 'nullable|string',
            'partner_id' => 'nullable|integer',
            'valor' => 'nullable',
            'valor_pago' => 'nullable|numeric',
            'valor_desconto' => 'nullable|numeric',
            'juros' => 'nullable|numeric',
            'multa' => 'nullable|numeric',
            'tipo_juros' => 'nullable|string',
            'valor_pago_juros_multa' => 'nullable|numeric',
            'tipo_baixa' => 'nullable|string|max:2',
            'status_autorizacao' => 'nullable|in:Aguardando,Aprovado,Recusado',
            'emissao' => 'nullable|date',
            'vencimento' => 'nullable|date',
            'pagamento' => 'nullable|date',
            'caixa_id' => 'nullable|integer',
            'obs' => 'nullable|string',
        ]);

        // Upload do documento
        if ($request->hasFile('documento')) {
            $documentoPath = $request->file('documento')->store('contasreceber_documentos', 'public');
            $validated['documento'] = $documentoPath;
        }

        $validated['usuario_atualizacao_id'] = auth()->id();
        $validated['atualizacao'] = Carbon::now();

        $conta->update($validated);

        return redirect()->route('conta_receber.index')->with('sucesso', 'Conta a receber atualizada com sucesso!');
    }

    public function softDelete($conta) {
        $conta = $this->contaReceberRepository->findOrFail($conta);
        $conta->usuario_exclusao_id = auth()->id();
        $conta->exclusao = Carbon::now();
        $conta->save();

        return redirect()->route('conta_receber.index')->with('sucesso', 'Conta excluída com sucesso!');
    }

    /**
     * Paga conta e cria um lançamento sobre ela.
     */
    public function pay ($conta) {
        $conta = $this->contaReceberRepository->findOrFail($conta);
        $conta->status_pagamento = "Pago";
        $conta->pagamento = Carbon::now();
        $conta->save();

        Financial::create([
            'data_hora_evento' => $conta->pagamento,
            'tipo' => 'entrada',
            'descricao' => 'Conta a Receber Efetivada | ID: ' .$conta->id. ' | Data: ' .Carbon::now(),
            'valor' => $conta->valor,
            'cost_center_id' => $conta->plano_contas_id,
            'user_id' => auth()->id()
        ]);

        return redirect()->back()->with('sucesso', 'O pagamento da conta foi recebido com sucesso. Um lançamento foi registrado 
        com os dados dessa conta.');
    }
}
