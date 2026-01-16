<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ProdutoCategoriaRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProdutoCategoriasController extends Controller
{
    public function __construct(ProdutoCategoriaRepository $produtoCategoriaRepository){
        $this->produtoCategoriaRepository = $produtoCategoriaRepository;
    }

    public function index () {
        $categorias = $this->produtoCategoriaRepository
            ->whereNull('exclusao')
            ->get()
            ->sortBy('descricao');

        return view('pages.produtos.categorias.index', compact('categorias'));
    }

    public function store(Request $request){
        $request->validate([
            'descricao' => 'required|string'
        ]);

        $this->produtoCategoriaRepository->create([
            'descricao' => $request->descricao,
            'cadastro' => Carbon::now(),
            'usuario_cadastro' => auth()->id()
        ]);

        return redirect()->back()->with('sucesso', 'Categoria criada com sucesso!');
    }



    /**
     * Delete Lógico, não apaga o registro no banco de dados
     */
    public function softDelete($categoria){
        $categoria = $this->produtoCategoriaRepository->findOrFail($categoria);
        $categoria->exclusao = Carbon::now();
        $categoria->save();

        return redirect()->back()->with('sucesso', 'Categoria excluída com sucesso!');
    }


    // public function storeAjax(Request $request)
    // {
    //     $request->validate([
    //         'nome' => 'required',
    //     ]);

    //     $categoria = $this->produtoCategoriaRepository->create([
    //         'nome' => $request->nome,
    //         'cadastro' => Carbon::now(),
    //         'usuario_cadastro_id' => auth()->id()
    //     ]);

    //     return response()->json($categoria);
    // }
}
