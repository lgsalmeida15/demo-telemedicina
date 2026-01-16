<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\PartnerRepository;
use App\Models\CostCenter;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PartnerCompany;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PartnerController extends Controller
{
    protected $partnerRepository;

    public function __construct (PartnerRepository $partnerRepository) {
        $this->partnerRepository = $partnerRepository;
    }


    public function index () 
    {
        $partners = $this->partnerRepository->whereNull('deleted_at')->get();

        return view('pages.partners.index', compact('partners'));
    }


    public function create()
    {
        $costCenters = CostCenter::all();

        return view('pages.partners.create', compact('costCenters'));
    }


    public function store (Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'required|string|max:20|',
            'email' => 'nullable',
            'phone' => 'nullable',
            'description' => 'nullable|string',
            'cost_center_id' => 'nullable|integer|exists:cost_centers,id',
        ]);

        try{
            $partner = $this->partnerRepository->create($data);

            return redirect()->route('partner.show', ['partner'=>$partner->id])->with('sucesso', 'Parceiro Cadastrado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erro ao cadastrar Parceiro: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit ($partner)
    {
        $partner = $this->partnerRepository->findOrFail($partner);
        $costCenters = CostCenter::all();

        return view('pages.partners.edit', compact('partner','costCenters'));
    }

    public function update (Request $request, $partner){
    
        $partner = $this->partnerRepository->findOrFail($partner);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'required|string|max:20|',
            'email' => 'nullable',
            'phone' => 'nullable',
            'description' => 'nullable|string',
            'cost_center_id' => 'nullable|integer|exists:cost_centers,id',
        ]);

        try{
            $partner->update($data);
            return redirect()->route('partner.show', ['partner'=>$partner->id])->with('sucesso', 'Parceiro Atualizado com sucesso!');
        } catch(\Exception $e){
            return redirect()->back()->withErrors(['error' => 'Erro ao atualizar Parceiro: ' . $e->getMessage()])->withInput();
        }
        
    }


    public function show ($partner) {
        $partner = $this->partnerRepository->findOrFail($partner);
        $companies = Company::whereNull('deleted_at')->get()->sortBy('name');
        return view('pages.partners.show', compact('partner', 'companies'));
    }


    public function softDelete ($partner) {
        $partner = $this->partnerRepository->findOrFail($partner);
        $partner->deleted_at = Carbon::now();
        $partner->save();
        return redirect()->back()->with('sucesso', 'Parceiro excluído com sucesso!');
    }
    
}
