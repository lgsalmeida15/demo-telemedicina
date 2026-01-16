<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ConvenioTypeRepository;
use Illuminate\Http\Request;

class ConvenioTypeController extends Controller 
{
    public function __construct(ConvenioTypeRepository $convenioTypeRepository)
    {
        $this->convenioTypeRepository = $convenioTypeRepository;
    }


    public function index () {
        $types = $this->convenioTypeRepository->all()->sortBy('name');
        return view('pages.convenios_tipos.index', compact('types'));
    }


    public function store (Request $request) {
        $data = $request->validate(['name' => 'required']);
        try {
            $this->convenioTypeRepository->create($data);
            return redirect()->back()->with('sucesso', 'Tipo de Serviço adicionado com sucesso!');
        } catch (\Exception $e){
            return redirect()->back()->withErrors('Não foi possível concluir a ação: '.$e->getMessage());
        }
    }


    public function destroy ($type) {
        $type = $this->convenioTypeRepository->findOrFail($type);
        $type->delete();
        return redirect()->back()->with('sucesso', 'Tipo de Serviço apagado com sucesso!');
    }


    public function storeAjax(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        try {

            $type = $this->convenioTypeRepository->create([
                'name' => $request->name,
            ]);

            return response()->json($type);

        } catch (\Exception $e){

            return response()->json(['error' => 'Erro ao adicionar um tipo: ' . $e->getMessage()], 500);
        }
        

    }


}