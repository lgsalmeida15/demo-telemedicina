<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BeneficiaryPlan;

class BeneficiaryPlanController extends Controller
{
    public function __construct(BeneficiaryPlan $beneficiaryPlanRepository)
    {
        $this->beneficiaryPlanRepository = $beneficiaryPlanRepository;
    }


    public function store (Request $request, $beneficiary) {

        try {

            $existingPlan = $this->beneficiaryPlanRepository->firstWhere([
                'beneficiary_id' => $beneficiary,
                'plan_id' => $request->input('plan_id'),
            ]);

            if ($existingPlan) {
                return back()->withErrors('O beneficiário já possui esse plano.');
            }

            $this->beneficiaryPlanRepository->create(
                [
                    'beneficiary_id' => $beneficiary,
                    'plan_id' => $request->plan_id
                ]
            );

            return redirect()->back()->with('sucesso', 'Plano adicionado com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Erro ao adicionar o plano: '.$e);
        }

    }


    public function destroy ($plan) {

        $plan = $this->beneficiaryPlanRepository->findOrFail($plan);
        $plan->delete();
        
        return redirect()->back()->with('sucesso', 'Plano removido com sucesso!');

    }
}
