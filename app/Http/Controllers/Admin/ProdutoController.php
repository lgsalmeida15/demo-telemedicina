<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ProdutoRepository;
use App\Models\ProdutoCategorias;
use App\Models\ProdutoGrupo;
use App\Models\ProdutoUnidade;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function __construct(ProdutoRepository $produtoRepository){
        $this->produtoRepository = $produtoRepository;
    }


    public function index(){
        $produtos = $this->produtoRepository->whereNull('exclusao')
            ->get()
            ->sortBy('nome');

        return view('pages.produtos.index', compact('produtos'));
    }


    public function create(){
        $categorias = ProdutoCategorias::whereNull('exclusao')->get();
        $grupos = ProdutoGrupo::whereNull('exclusao')->get();
        $unidades = ProdutoUnidade::whereNull('exclusao')->get();

        return view('pages.produtos.create', compact(
            'categorias',
            'grupos',
            'unidades',
        ));
    }
}
