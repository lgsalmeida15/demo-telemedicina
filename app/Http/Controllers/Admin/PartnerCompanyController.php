<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\PartnerCompanyRepository;
use Illuminate\Support\Facades\DB;

class PartnerCompanyController extends Controller
{
    protected $partnerCompanyRepository;

    public function __construct(PartnerCompanyRepository $partnerCompanyRepository)
    {
        $this->partnerCompanyRepository = $partnerCompanyRepository;
    }

    public function store(Request $request, $partner)
    {
        try {
            // 1. Validação dos dados de entrada
            $request->validate([
                'company_id' => 'required|integer|exists:companies,id',
            ]);

            // 2. Verifica se a indicação já existe para evitar duplicatas
            $existingIndication = $this->partnerCompanyRepository->firstWhere([
                'partner_id' => $partner,
                'company_id' => $request->input('company_id'),
            ]);

            if ($existingIndication) {
                return back()->with('sucesso', 'Esta indicação já existe para o parceiro.');
            }

            // 3. Cria a nova indicação
            $this->partnerCompanyRepository->create([
                'partner_id' => $partner,
                'company_id' => $request->input('company_id'),
            ]);

            // 4. Redireciona com uma mensagem de sucesso
            return back()->with('sucesso', 'Indicação adicionada com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retorna com erros de validação
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Captura qualquer outro erro e exibe uma mensagem genérica
            return back()->with('error', 'Ocorreu um erro ao adicionar a indicação. Por favor, tente novamente.');
        }
    }


    public function destroy($indication){
        $indication = $this->partnerCompanyRepository->findOrFail($indication);
        try {
            $indication->delete();
            return redirect()->back()->with('sucesso', 'Indicação removida com sucesso!');
        } catch(\Exception $e) {
            return redirect()->back()->withErrors('Erro ao apagar indicação: ' .$e->getMessage());
        }
    }
}
