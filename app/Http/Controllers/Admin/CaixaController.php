<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CaixaRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CaixaController extends Controller
{
    public function __construct (CaixaRepository $caixaRepository){
        $this->caixaRepository = $caixaRepository;
    }

    public function index (){
        $caixas = $this->caixaRepository->whereNull('exclusao')->get();
        return view('pages.caixas.index', compact('caixas'));
    }


    public function store (Request $request){

        $request->validate([
            'descricao' => 'required',
            'obs'       => 'nullable' 
        ]);

        $this->caixaRepository->create([
            'descricao' => $request->descricao,
            'obs'       => $request->obs,
            'cadastro'  => Carbon::now()
        ]);

        return redirect()->back()->with('sucesso', 'Caixa adicionado com sucesso!');
    }



    public function update (Request $request, $caixa){

        $caixa = $this->caixaRepository->findOrFail($caixa);

        $request->validate([
            'descricao' => 'required',
            'obs'       => 'nullable' 
        ]);

        $caixa->update([
            'descricao' => $request->descricao,
            'obs'       => $request->obs,
            'atualizacao'  => Carbon::now()
        ]);

        return redirect()->back()->with('sucesso', 'Caixa atualizado com sucesso!');
    }

    


    public function softDelete ($caixa) {

        $caixa = $this->caixaRepository->findOrFail($caixa);
        $caixa->exclusao = Carbon::now();
        $caixa->save();

        return redirect()->back()->with('sucesso', 'Caixa excluído com sucesso!');
    }
}
