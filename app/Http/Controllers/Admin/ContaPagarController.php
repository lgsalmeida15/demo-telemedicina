<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ContaPagarRepository;
use App\Models\CostCenter;
use App\Models\Partner;
use App\Models\Caixa;
use App\Models\Financial;
use Carbon\Carbon;

use Illuminate\Http\Request;

class ContaPagarController extends Controller
{
    private $contaPagarRepository;

    public function __construct(ContaPagarRepository $contaPagarRepository) {
        $this->contaPagarRepository = $contaPagarRepository;
    }


    public function index () {
        $contas = $this->contaPagarRepository->whereNull('exclusao')->get();

        return view('pages.contaspagar.index', compact('contas'));
    }


    public function create () {
        $costCenters = CostCenter::whereNull('exclusao')->get();
        $partners = Partner::whereNull('deleted_at')->get();
        $caixas = Caixa::whereNull('exclusao')->get();

        return view ('pages.contaspagar.create', compact(
            'costCenters',
            'partners',
            'caixas'
        ));
    }

    public function store(Request $request){
        // dd($request->all());

        $validated = $request->validate([
            'documento' => 'nullable|file',
            'cost_center_id' => 'nullable|integer',
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
            $documentoPath = $request->file('documento')->store('contas_documentos', 'public');
            $validated['documento'] = $documentoPath;
        }

        $validated['usuario_cadastro_id'] = auth()->id();
        $validated['cadastro'] = Carbon::now();

        $this->contaPagarRepository->create($validated);

        return redirect()->route('conta_pagar.index')->with('sucesso', 'Conta a pagar criada com sucesso!');    
    
    }


    public function show($conta) {

        $contaPagar = $this->contaPagarRepository->findOrFail($conta);
    
        return view('pages.contaspagar.show', compact('contaPagar'));
    }

    

    public function view_edit($conta) {

        $conta = $this->contaPagarRepository->findOrFail($conta);
        $costCenters = CostCenter::whereNull('exclusao')->get();
        $partners = Partner::whereNull('deleted_at')->get();
        $caixas = Caixa::whereNull('exclusao')->get();

        return view('pages.contaspagar.view_edit', compact(
            'conta',
            'costCenters',
            'partners',
            'caixas'
        ));
    }


    public function update(Request $request, $conta){

        $conta = $this->contaPagarRepository->findOrFail($conta);

        $validated = $request->validate([
            'documento' => 'nullable|file',
            'cost_center_id' => 'nullable|integer',
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
            $documentoPath = $request->file('documento')->store('contas_documentos', 'public');
            $validated['documento'] = $documentoPath;
        }

        $validated['usuario_atualizacao_id'] = auth()->id();
        $validated['atualizacao'] = Carbon::now();


        $conta->update($validated);

        return redirect()->route('conta_pagar.show', ['conta'=>$conta->id])->with('sucesso', 'Dados da conta foram atualizados com sucesso!');

    }


    public function softDelete($conta){
        $conta = $this->contaPagarRepository->findOrFail($conta);
        $conta->usuario_exclusao_id = auth()->id();
        $conta->exclusao = Carbon::now();
        $conta->save();
        return redirect()->route('conta_pagar.index')->with('sucesso', 'Conta excluída com sucesso!');
    }

    /**
     * Paga conta e cria um lançamento sobre ela.
     */
    public function pay ($conta) {
        $conta = $this->contaPagarRepository->findOrFail($conta);
        $conta->status_pagamento = "Pago";
        $conta->pagamento = Carbon::now();
        $conta->save();

        Financial::create([
            'data_hora_evento' => $conta->pagamento,
            'tipo' => 'saida',
            'descricao' => 'Conta a Pagar Efetivada | ID: ' .$conta->id. ' | Data: ' .Carbon::now(),
            'valor' => $conta->valor,
            'cost_center_id' => $conta->cost_center_id,
            'user_id' => auth()->id()
        ]);

        return redirect()->back()->with('sucesso', 'A conta foi paga com sucesso. Um lançamento foi registrado 
        com os dados dessa conta.');
    }

}
