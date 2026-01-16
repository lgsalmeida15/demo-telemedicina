<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\PlanConvenioRepository;
use App\Models\Plan;
use App\Models\Convenio;
use Illuminate\Http\Request;

class PlanConvenioController extends Controller // Many to Many Planos com Convenios (Serviços)
{
    public function __construct(PlanConvenioRepository $planConvenioRepository){
        $this->planConvenioRepository = $planConvenioRepository;
    }

    public function index ($plan) {
        $plan = Plan::findOrFail($plan);
        $planConveniences = $this->planConvenioRepository->where('plan_id',$plan->id)
        ->get()->sortBy('name');
        $conveniences = Convenio::where('status','Ativo')->get();
        return view('pages.plans.conveniences', compact('plan','conveniences','planConveniences'));
    }

    // adiciona um serviço ao plano
    public function store(Request $request, $plan)
    {
        // Debugging: uncomment this line to see if the request reaches the controller
        // dd($request->all());

        try {
            // 1. Validação dos dados de entrada
            $request->validate([
                'convenio_id' => 'required|integer|exists:convenios,id',
            ]);

            // 2. Verifica se o convenio já existe para evitar duplicatas
            $existingPlan = $this->planConvenioRepository->firstWhere([
                'convenio_id' => $request->input('convenio_id'),
                'plan_id' => $plan,
            ]);

            if ($existingPlan) {
                return back()->withErrors('Este Serviço já existe para o Plano.');
            }

            // 3. Cria a nova indicação
            $this->planConvenioRepository->create([
                'plan_id' => $plan,
                'convenio_id' => $request->input('convenio_id'),
            ]);

            // 4. Redireciona com uma mensagem de sucesso
            return back()->with('sucesso', 'Serviço adicionado com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retorna com erros de validação
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Captura qualquer outro erro e exibe uma mensagem genérica
            return back()->with('error', 'Ocorreu um erro ao adicionar o Serviço: ' . $e->getMessage());
        }
    }

    // apaga registro
    public function destroy($plan_convenience){
        $planConvenience = $this->planConvenioRepository->findOrFail($plan_convenience);
        try {
            $planConvenience->delete();
            return redirect()->back()->with('sucesso', 'Serviço removido com sucesso!');
        } catch(\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao apagar Serviço: ' . $e->getMessage());
        }
    }
}
