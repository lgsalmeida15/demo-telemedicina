<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ProdutoUnidadeRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProdutoUnidadeController extends Controller
{
    public function __construct(ProdutoUnidadeRepository $produtoUnidadeRepository)
    {
        $this->produtoUnidadeRepository = $produtoUnidadeRepository;
    }


    public function index (){
        $unidades = $this->produtoUnidadeRepository->whereNull('exclusao')
        ->get()
        ->sortBy('sigla');

        return view('pages.produtos.unidades.index', compact('unidades'));
    }

    public function store(Request $request){
        $request->validate([
            'descricao' => 'required|string',
            'sigla' => 'required|string'
        ]);

        $this->produtoUnidadeRepository->create([
            'descricao' => $request->descricao,
            'sigla' => $request->sigla,
            'cadastro' => Carbon::now(),
            'usuario_id' => auth()->id()
        ]);

        return redirect()->back()->with('sucesso', 'Unidade criada com sucesso!');
    }


    public function update(Request $request, $unidade){

        $unidade = $this->produtoUnidadeRepository->findOrFail($unidade);

        $request->validate([
            'descricao' => 'required|string',
            'sigla' => 'required|string'
        ]);

        $unidade->update([
            'descricao' => $request->descricao,
            'sigla' => $request->sigla,
            'atualizacao' => Carbon::now(),
        ]);

        return redirect()->back()->with('sucesso', 'Unidade atualizada com sucesso!');
    }



    /**
     * Delete Lógico, não apaga o registro no banco de dados
     */
    public function softDelete($unidade){
        $unidade = $this->produtoUnidadeRepository->findOrFail($unidade);
        $unidade->exclusao = Carbon::now();
        $unidade->save();

        return redirect()->back()->with('sucesso', 'Unidade excluída com sucesso!');
    }
}
