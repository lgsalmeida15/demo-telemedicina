<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ConveniosCategoria;
use App\Models\ConvenioType;
use App\Models\Partner;
use App\Http\Controllers\Controller;
use App\Repositories\ConvenioRepository;

class ConvenioController extends Controller
{

    private $convenioRepository;

    public function __construct (ConvenioRepository $convenioRepository)
    {
        $this->convenioRepository = $convenioRepository;
    }

    public function index(Request $request)
    {
        $categorias = ConveniosCategoria::orderBy('nome')->get();
        $categoriaId = $request->input('categoria_id');

        $query = $this->convenioRepository->query();

        if ($categoriaId) {
            $query->where('convenio_categoria_id', $categoriaId);
        }

        $convenios = $query->orderBy('id')->get();
        return view('pages.convenios.index', compact(
            'convenios',
            'categoriaId',
            'categorias'
        ));
    }


    public function create ()
    {
        $categorias = ConveniosCategoria::whereNull('exclusao')->get();
        $partners = Partner::whereNull('deleted_at')->get();
        $types = ConvenioType::all()->sortBy('name');

        return view('pages.convenios.create', compact('categorias', 'partners', 'types'));
    }


    public function store (Request $request)
    {
        $dados = $request->all();

        $convenio = $this->convenioRepository->create($dados);

        return redirect()->route('convenio.show', ['convenio'=>$convenio])->with('sucesso', 'Serviço criado com sucesso!');
    }


    public function view_edit($convenio)
    {
        $categorias = ConveniosCategoria::whereNull('exclusao')->get();
        $partners = Partner::whereNull('deleted_at')->get();
        $types = ConvenioType::all()->sortBy('name');
        $convenio = $this->convenioRepository->findOrFail($convenio);
        return view('pages.convenios.view_edit', compact('convenio', 'partners', 'categorias', 'types'));
    }

    public function update (Request $request, $convenio)
    {
        $convenio = $this->convenioRepository->findOrFail($convenio);
        

        $dados = $request->all();

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $path = $request->file('logo')->store('convenios/imagens', 'public');
            $dados['logo'] = $path;
        }

        $convenio->update($dados);

        return redirect()->route('convenio.show', ['convenio'=>$convenio])->with('sucesso', 'Serviço atualizado com sucesso!');
    }


    public function show ($convenio)
    {
        $convenio = $this->convenioRepository->findOrFail($convenio);
        return view('pages.convenios.show', compact('convenio'));
    }


    public function delete($convenio)
    {
        $convenio = $this->convenioRepository->findOrFail($convenio);
        $convenio->delete();
        return redirect()->route('convenio.index')->with('sucesso', 'Serviço apagado com sucesso!');
    }
}
