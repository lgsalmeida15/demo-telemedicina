<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ConveniosCategoriaRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ConveniosCategoriaController extends Controller
{
    public function __construct(ConveniosCategoriaRepository $conveniosCategoriaRepository){
        $this->conveniosCategoriaRepository = $conveniosCategoriaRepository;
    }



    public function index(){
        $categorias = $this->conveniosCategoriaRepository->whereNull('exclusao')->get();
        return view('pages.convenios_categorias.index', compact('categorias'));
    }



    public function store(Request $request){
        $request->validate([
            'nome' => 'required',
            'descricao' => 'nullable'
        ]);

        $this->conveniosCategoriaRepository->create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'cadastro' => Carbon::now(),
            'usuario_cadastro' => auth()->id()
        ]);

        return redirect()->back()->with('sucesso', 'Categoria criada com sucesso!');
    }



    /**
     * Antigo delete logico,
     */
    public function softDelete($categoria){
        $categoria = $this->conveniosCategoriaRepository->findOrFail($categoria);
        $categoria->delete();

        return redirect()->back()->with('sucesso', 'Categoria excluída com sucesso!');
    }


    public function storeAjax(Request $request)
    {
        $request->validate([
            'nome' => 'required',
        ]);

        $categoria = $this->conveniosCategoriaRepository->create([
            'nome' => $request->nome,
            'cadastro' => Carbon::now(),
            'usuario_cadastro_id' => auth()->id()
        ]);

        return response()->json($categoria);
    }

}
