<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DemoBeneficiaryService;
use App\Models\Company;
use App\Models\Plan;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoBeneficiaryController extends Controller
{
    protected $demoService;
    
    public function __construct(DemoBeneficiaryService $demoService)
    {
        $this->demoService = $demoService;
    }
    
    /**
     * Exibe formulário de criação de beneficiário demo
     */
    public function create()
    {
        // Buscar empresa do admin logado
        $companies = Company::all();
        $plans = Plan::all();
        
        return view('pages.admin.demo-beneficiary.create', compact('companies', 'plans'));
    }
    
    /**
     * Armazena novo beneficiário demo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'plan_id' => 'required|exists:plans,id',
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|unique:beneficiaries,cpf',
            'email' => 'required|email|unique:beneficiaries,email',
            'phone' => 'nullable|string',
            'birth_date' => 'required|date',
            'gender' => 'required|in:M,F',
            'relationship' => 'required|string',
            'mother_name' => 'nullable|string',
            'password' => 'nullable|string|min:6',
            'demo_days' => 'nullable|integer|min:1|max:365',
        ]);
        
        try {
            $beneficiary = $this->demoService->createDemoBeneficiary($validated);
            
            // Se solicitado, fazer login automático
            if ($request->has('auto_login')) {
                Auth::guard('beneficiary')->login($beneficiary);
                session()->regenerate();
                
                return redirect()
                    ->route('beneficiary.area.index')
                    ->with('success', 'Beneficiário demo criado e logado com sucesso!');
            }
            
            return redirect()
                ->route('admin.demo-beneficiary.show', $beneficiary->id)
                ->with('success', 'Beneficiário demo criado com sucesso!');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar beneficiário demo: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Exibe detalhes do beneficiário demo
     */
    public function show(Beneficiary $beneficiary)
    {
        if (!$beneficiary->isDemo()) {
            return redirect()
                ->route('admin.beneficiary.index')
                ->withErrors('Este beneficiário não está em modo demo.');
        }
        
        return view('pages.admin.demo-beneficiary.show', compact('beneficiary'));
    }
    
    /**
     * Estende período demo
     */
    public function extend(Request $request, Beneficiary $beneficiary)
    {
        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);
        
        try {
            $this->demoService->extendDemo($beneficiary, $validated['days']);
            
            return back()->with('success', "Período demo estendido por {$validated['days']} dias.");
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    /**
     * Converte beneficiário demo para real
     */
    public function convertToReal(Beneficiary $beneficiary)
    {
        if (!$beneficiary->isDemo()) {
            return back()->withErrors('Este beneficiário não está em modo demo.');
        }
        
        // Redirecionar para fluxo de checkout/pagamento
        return redirect()
            ->route('checkout.page', ['uuid' => $beneficiary->company->uuid])
            ->with('convert_demo', $beneficiary->id)
            ->with('info', 'Complete o pagamento para converter este beneficiário demo.');
    }
    
    /**
     * Lista beneficiários demo
     */
    public function index()
    {
        $demoBeneficiaries = Beneficiary::where('is_demo', true)
            ->with(['company', 'plans.plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('pages.admin.demo-beneficiary.index', compact('demoBeneficiaries'));
    }
    
    /**
     * Remove beneficiário demo
     */
    public function destroy(Beneficiary $beneficiary)
    {
        if (!$beneficiary->isDemo()) {
            return back()->withErrors('Apenas beneficiários demo podem ser removidos por aqui.');
        }
        
        try {
            $beneficiary->plans()->delete();
            $beneficiary->invoices()->delete();
            $beneficiary->delete();
            
            return redirect()
                ->route('admin.demo-beneficiary.index')
                ->with('success', 'Beneficiário demo removido com sucesso.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao remover: ' . $e->getMessage()]);
        }
    }

    /**
     * Faz login automático do beneficiário demo
     */
    public function loginAs(Beneficiary $beneficiary)
    {
        if (!$beneficiary->isDemo()) {
            return back()->withErrors('Apenas beneficiários demo podem fazer login automático.');
        }

        Auth::guard('beneficiary')->login($beneficiary);
        session()->regenerate();

        return redirect()
            ->route('beneficiary.area.index')
            ->with('success', 'Login realizado com sucesso!');
    }
}

