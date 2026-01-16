<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CompanyRepository;
use App\Models\Convenio;
use App\Models\Plan;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function index()
    {
        $companies = $this->companyRepository->whereNull('deleted_at')->get();
        return view('pages.companies.index', compact('companies'));
    }


    public function create(){
        return view('pages.companies.create');
    }


    public function store(Request $request)
    {
        $data = $request->all();
        try {
            $cnpjInUse = $this->companyRepository->where('cnpj',$request->cnpj)
            ->exists();
            if($cnpjInUse){
                return redirect()->back()->withErrors('O CNPJ: '.$request->cnpj.' já está sendo usado.');
            }
            $company = $this->companyRepository->create($data);
            return redirect()->route('company.show', ['company'=>$company->id])->with('sucesso', 'Empresa cadastrada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Erro ao cadastrar empresa: ' . $e->getMessage());
        }
    }

    public function show($company)
    {
        $company = $this->companyRepository->find($company);
        $convenios = Convenio::all()->sortBy('name');
        return view('pages.companies.show', compact('company', 'convenios'));
    }


    public function edit($company)
    {
        $company = $this->companyRepository->find($company);
        return view('pages.companies.edit', compact('company'));
    }


    public function update(Request $request, $company)
    {
        $data = $request->all();
        $company = $this->companyRepository->find($company);
        try {
            $cnpjInUse = $this->companyRepository->where('cnpj',$request->cnpj)
            ->where('id', '!=', $company->id)
            ->exists();
            if($cnpjInUse){
                return redirect()->back()->withErrors('O CNPJ: '.$request->cnpj.' já está sendo usado.');
            }
            $company->update($data);
            return redirect()->route('company.show', ['company'=>$company->id])->with('sucesso', 'Empresa atualizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Erro ao atualizar empresa: ' . $e->getMessage());
        }
    }

    // Delete Logico
    public function softDelete($company)
    {
        $company = $this->companyRepository->find($company);
        try {
            $company->deleted_at = now();
            $company->save();
            return redirect()->route('company.index')->with('sucesso', 'Empresa excluída com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Erro ao excluir empresa: ' . $e->getMessage());
        }
    }

    public function report(){
        $plans = Plan::with('conveniences.convenio.type')->get();
        return view('pages.companies.report', compact('plans'));
    }


    
}
