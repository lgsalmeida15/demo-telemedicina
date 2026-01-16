<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\CostCenterRepository;
use App\Models\CostCenter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{

    private $costCenterRepository;


    public function __construct (CostCenterRepository $costCenterRepository)
    {
        $this->costCenterRepository = $costCenterRepository;
    }


    public function index ()
    {
        $costCenters = $this->costCenterRepository->whereNull('exclusao')->get();
        $users = User::all();

        return view('pages.costcenters.index', compact('costCenters', 'users'));
    }


    public function store (Request $request)
    {
        $request->validate([
            'usuario_id' => 'nullable',
            'codigo_reduzido' => 'required',
            'codigo_conta'    => 'required',
            'descricao'       => 'required',
            'tipo'            => 'required',
        ]);

        $this->costCenterRepository->create([
            'usuario_id' => $request->usuario_id,
            'cadastro'       => Carbon::now(),
            'exclusao'       => null,
            'atualizacao'    => null,
            'codigo_conta'   => $request->codigo_conta,
            'codigo_reduzido'   => $request->codigo_reduzido,
            'descricao'      => $request->descricao,
            'tipo'           => $request->tipo
        ]);

        return redirect()->back()->with('sucesso', 'Conta adicionada com sucesso!');
    }

    public function update(Request $request, $costCenter)
    {
        $costCenter = $this->costCenterRepository->findOrFail($costCenter);

        $request->validate([
            'usuario_id' => 'nullable',
            'codigo_reduzido' => 'required',
            'codigo_conta'    => 'required',
            'descricao'       => 'required',
            'tipo'            => 'required',
        ]);

        $costCenter->update([
            'usuario_id' => $request->usuario_id,
            'atualizacao'    => Carbon::now(),
            'codigo_conta'   => $request->codigo_conta,
            'codigo_reduzido' => $request->codigo_reduzido,
            'descricao'      => $request->descricao,
            'tipo'           => $request->tipo
        ]);

        return redirect()->back()->with('sucesso', 'Conta atualizada com sucesso!');
    }

    public function delete(Request $request, $costCenter)
    {
        $costCenter = $this->costCenterRepository->findOrFail($costCenter);
        $costCenter->exclusao = Carbon::now();
        $costCenter->save();

        return redirect()->back()->with('sucesso', 'Conta excluída com sucesso!');
    }
}
