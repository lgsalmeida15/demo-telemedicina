<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\PlanRepository;
use App\Models\Company;
use App\Models\Convenio;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function __construct(PlanRepository $planRepository){
        $this->planRepository = $planRepository;
    }



    public function index($company){
        $company = Company::findOrFail($company);
        $plans = $this->planRepository->where('company_id', $company->id)
        ->get()->sortBy('name');
        return view('pages.plans.index', compact('company','plans'));
    }



    public function create($company){
        $company = Company::findOrFail($company);
        $covenants = Convenio::all();
        return view('pages.plans.create', compact('company','covenants'));
    }



    public function store(Request $request){
        // dd($request);
        $data = $request->validate([
            'company_id'  => 'required',
            'is_telemedicine'  => 'required|boolean',
            'name'        => 'required|string',
            'value'       => 'required|string',
            'description' => 'nullable',
        ]);
        try{
            // Remove a vírgula e converte para float
            $data['value'] = str_replace(',', '.', $data['value']);
            $data['value'] = (float) $data['value'];
            $plan = $this->planRepository->create($data);
            return redirect()->route('plan.show', ['plan'=>$plan->id] )->with('sucesso', 'Plano adicionado com sucesso!');
        } catch(\Exception $e){
            return redirect()->back()->withErrors('Erro ao adicionar o plano: '.$e->getMessage());
        }
    }



    public function show($plan){
        $plan = $this->planRepository->findOrFail($plan);
        return view('pages.plans.show', compact('plan'));
    }



    public function edit($plan){
        $plan = $this->planRepository->findOrFail($plan);
        $covenants = Convenio::all();
        return view('pages.plans.edit', compact('plan', 'covenants'));
    }



    public function update(Request $request, $plan){
        $plan = $this->planRepository->findOrFail($plan);
        $data = $request->validate([
            'is_telemedicine'  => 'required|boolean',
            'name'        => 'required|string',
            'value'       => 'required|string',
            'description' => 'nullable',
        ]);
        try{
            // Remove a vírgula e converte para float
            $data['value'] = str_replace(',', '.', $data['value']);
            $data['value'] = (float) $data['value'];
            $plan->update($data);
            return redirect()->route('plan.show', ['plan'=>$plan->id] )->with('sucesso', 'Plano atualizado com sucesso!');
        } catch(\Exception $e){
            return redirect()->back()->withErrors('Erro ao atualizar o plano: '.$e->getMessage());
        }
    }



    public function destroy($plan){
        $plan = $this->planRepository->findOrFail($plan);
        try {
            $plan->delete();
            return redirect()->back()->with('sucesso', 'Plano apagado com sucesso!');
        } catch(\Exception $e){
            return redirect()->back()->withErrors('Erro ao apagar o plano: '.$e->getMessage());
        }
    }
}
