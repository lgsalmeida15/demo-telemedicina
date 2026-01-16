<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ProdutoGrupoRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProdutoGrupoController extends Controller
{
    public function __construct(ProdutoGrupoRepository $produtoGrupoRepository)
    {
        $this->produtoGrupoRepository = $produtoGrupoRepository;
    }


    public function index (){
        $grupos = $this->produtoGrupoRepository->whereNull('exclusao')
        ->get()
        ->sortBy('descricao');

        return view('pages.produtos.grupos.index', compact('grupos'));
    }

    public function store(Request $request){
        $request->validate([
            'descricao' => 'required|string',
        ]);

        $this->produtoGrupoRepository->create([
            'descricao' => $request->descricao,
            'cadastro' => Carbon::now(),
            'usuario_id' => auth()->id()
        ]);

        return redirect()->back()->with('sucesso', 'Grupo criado com sucesso!');
    }


    public function update(Request $request, $grupo){

        $grupo = $this->produtoGrupoRepository->findOrFail($grupo);

        $request->validate([
            'descricao' => 'required|string',
        ]);

        $grupo->update([
            'descricao' => $request->descricao,
            'atualizacao' => Carbon::now(),
        ]);

        return redirect()->back()->with('sucesso', 'Grupo atualizado com sucesso!');
    }



    /**
     * Delete Lógico, não apaga o registro no banco de dados
     */
    public function softDelete($grupo){
        $grupo = $this->produtoGrupoRepository->findOrFail($grupo);
        $grupo->exclusao = Carbon::now();
        $grupo->save();

        return redirect()->back()->with('sucesso', 'Grupo excluído com sucesso!');
    }
}
