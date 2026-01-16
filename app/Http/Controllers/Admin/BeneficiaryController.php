<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\BeneficiaryRepository;
use Illuminate\Support\Facades\Hash;
use App\Models\Beneficiary;
use App\Models\Company;
use App\Models\Plan;
use App\Models\PlanConvenio;
use App\Imports\BeneficiaryImport; // Adicionado
use Maatwebsite\Excel\Facades\Excel; // Adicionado
use Illuminate\Http\Request;
use Carbon\Carbon;
use Log; // Adicionado para logging de erros
use App\Services\PlanStatusService;

class BeneficiaryController extends Controller
{
    private $beneficiaryRepository;

    public function __construct(BeneficiaryRepository $beneficiaryRepository)
    {
        $this->beneficiaryRepository = $beneficiaryRepository;
    }

    public function index($company, PlanStatusService $planStatusService)
    {
        $company = Company::findOrFail($company);

        $plans = Plan::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        $beneficiaries = $this->beneficiaryRepository
            ->where('company_id', $company->id)
            ->whereNull('deleted_at')
            ->with(['plans']) // evita N+1
            ->get()
            ->map(function ($beneficiary) use ($planStatusService) {
                $status = $planStatusService->resolveForBeneficiary($beneficiary);
                $beneficiary->plan_status = $status;
                $beneficiary->plan_status_view = $planStatusService->label($status);
                return $beneficiary;
            });

        return view('pages.beneficiaries.index', compact(
            'company',
            'beneficiaries',
            'plans'
        ));
    }


    public function create($company){
        $company = Company::findOrFail($company);
        $plans = Plan::where('company_id',$company->id)->get()->sortBy('name');
        return view('pages.beneficiaries.create', compact('company','plans'));
    }


    public function store(Request $request)
    {
        $data = $request->all();
        
        try {
            $cpfInUse = $this->beneficiaryRepository->where('cpf',$request->cpf)->exists();

            if($cpfInUse){
                return redirect()->back()->withErrors('O CPF: '.$request->cpf.' já está sendo usado.');
            }

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $beneficiary = $this->beneficiaryRepository->create($data);
            return redirect()->route('beneficiary.show', ['beneficiary'=>$beneficiary->id])->with('sucesso', 'Beneficiário cadastrado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors('Erro ao cadastrar beneficiário: ' . $e->getMessage());
        }
    }

    public function show($beneficiary)
    {
        $beneficiary = $this->beneficiaryRepository->find($beneficiary);
        $plans = Plan::where('company_id',$beneficiary->company->id)->get()->sortBy('name');
        $beneficiaryPlans = $beneficiary->plans;
        
        // dd($planConveniences);
        return view('pages.beneficiaries.show', compact('beneficiary','plans', 'beneficiaryPlans'));
    }


    public function edit($beneficiary)
    {
        $beneficiary = $this->beneficiaryRepository->find($beneficiary);
        $plans = Plan::where('company_id',$beneficiary->company->id)->get()->sortBy('name');
        $companies = Company::whereNull('deleted_at')->get();
        return view('pages.beneficiaries.edit', compact('beneficiary', 'plans'));
    }


    public function update(Request $request, $beneficiary)
    {
        $data = $request->all();
        $beneficiary = $this->beneficiaryRepository->find($beneficiary);

        try {

            $cpfInUse = $this->beneficiaryRepository->where('cpf',$request->cpf)
            ->where('id', '!=', $beneficiary->id)
            ->exists();

            if($cpfInUse){
                return redirect()->back()->withErrors('O CPF: '.$request->cpf.' já está sendo usado.');
            }

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $beneficiary->update($data);

            return redirect()->route('beneficiary.show', ['beneficiary'=>$beneficiary->id])->with('sucesso', 'Beneficiário atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Erro ao atualizar beneficiário: ' . $e->getMessage());
        }
    }

    // Delete Logico
    public function softDelete($beneficiary)
    {
        $beneficiary = $this->beneficiaryRepository->find($beneficiary);
        try {
            $beneficiary->deleted_at = now();
            $beneficiary->action = 'E';
            $beneficiary->save();
            return redirect()->back()->with('sucesso', 'Beneficiário excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Erro ao excluir beneficiário: ' . $e->getMessage());
        }
    }

    /**
     * Processa o upload do arquivo Excel/CSV e importa os beneficiários.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importExcel(Request $request)
    {
        // 1. Validação do arquivo e plano
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB limite
            'plan_id' => 'required'
        ], [
            'excel_file.required' => 'Por favor, selecione um arquivo para importar.',
            'excel_file.file' => 'O arquivo enviado não é válido.',
            'excel_file.mimes' => 'O arquivo deve ser do tipo .xlsx, .xls ou .csv.',
            'excel_file.max' => 'O arquivo não pode exceder 10MB.',
            'plan_id.required' => 'Escolha um plano antes de importar os dados'
        ]);

        // pegando o id da empresa do plano
        $plan = Plan::findOrFail($request->plan_id);
        $companyId = $plan->company_id; 

        if (!$companyId) {
             return redirect()->back()->withErrors('Não foi possível determinar a empresa do plano selecionado para realizar a importação.');
        }

        try {
            // 3. Dispara a importação, passando a companyId para a classe Importer (CORREÇÃO APLICADA AQUI)
            // Usa 'new App\Imports\BeneficiaryImport($companyId)' ou apenas 'new BeneficiaryImport($companyId)' 
            // se o 'use App\Imports\BeneficiaryImport;' estiver no topo.
            Excel::import(new BeneficiaryImport($companyId, $plan->id), $request->file('excel_file'));

            // 4. Sucesso
            return redirect()->route('beneficiary.index', $companyId)->with('sucesso', 'Importação concluída com sucesso! Os beneficiários foram adicionados/atualizados.');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // 5. Erros de Validação da planilha (Erros de regras de negócio ou dados ausentes)
            $failures = $e->failures();

            $errorMessages = [];
            foreach ($failures as $failure) {
                 $row = $failure->row();
                 $attribute = $failure->attribute();
                 $errors = implode(', ', $failure->errors());

                 // Formato amigável para o usuário:
                 $errorMessages[] = "Linha {$row} (Coluna '{$attribute}'): {$errors}";
            }

            return redirect()->back()
                ->withErrors('Ocorreram erros durante a validação de algumas linhas do arquivo:')
                ->withErrors($errorMessages); // Passa a lista de erros

        } catch (\Exception $e) {
            // 6. Outros Erros (Arquivo corrompido, PHP esgotou memória, etc.)
            Log::error("Import error: " . $e->getMessage());
            return redirect()->back()->withErrors('Ocorreu um erro inesperado durante a importação. Detalhes: ' . $e->getMessage());
        }
    }


    /**
     * Index Geral de Beneficiários
     */
    public function generalIndex(Request $request, PlanStatusService $planStatusService)
    {
        $query = $this->beneficiaryRepository
            ->whereNull('deleted_at')
            ->with(['plans', 'company']); // evita N+1

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('cpf')) {
            $query->where('cpf', 'like', '%' . $request->cpf . '%');
        }

        $beneficiaries = $query
            ->orderBy('name', 'asc')
            ->paginate(10);

        $beneficiaries->getCollection()->transform(function ($beneficiary) use ($planStatusService) {
            $status = $planStatusService->resolveForBeneficiary($beneficiary);

            $beneficiary->plan_status = $status;
            $beneficiary->plan_status_view = $planStatusService->label($status);

            return $beneficiary;
        });

        return view('pages.beneficiaries.general.index', compact('beneficiaries'));
    }

}
