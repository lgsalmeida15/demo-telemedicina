<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\FinancialRepository;
use App\Repositories\CostCenterRepository;
use App\Models\Caixa;

class FinancialController extends Controller
{
    private $financialRepository;
    private $costCenterRepository;

    /**
     * Summary of __construct
     * @param \App\Repositories\FinancialRepository $financialRepository
     * @param \App\Repositories\CostCenterRepository $costCenterRepository
     */
    public function __construct(
        FinancialRepository $financialRepository,
        CostCenterRepository $costCenterRepository
    ) {
        $this->financialRepository = $financialRepository;
        $this->costCenterRepository = $costCenterRepository;
    }

    public function index(Request $request)
    {
        /* -----------------------------------------------------------
        | 1. Centros de Custo (para o filtro e para os modais)
        |------------------------------------------------------------*/
        $centrosDeCusto = $this->costCenterRepository
            ->orderBy('id', 'asc')
            ->all();

        /* -----------------------------------------------------------
        | 2. Filtros (datas e centro de custo) vindos do formulário
        |------------------------------------------------------------*/
        $inicio = $request->filled('data_inicio')
            ? Carbon::parse($request->input('data_inicio'))->startOfDay()
            : null;

        $fim = $request->filled('data_fim')
            ? Carbon::parse($request->input('data_fim'))->endOfDay()
            : null;
        $centroID = $request->input('centro_custo_id');
        $caixaID = $request->input('caixa_id');

        /* --- Query base -------------------------------------------------------------- */
        $baseQuery = $this->financialRepository->model()   // devolve Eloquent\Model
            ::with('costCenter')                           // eager loading
            ->when($inicio && $fim, fn($q) => $q->whereBetween('data_hora_evento', [$inicio, $fim]))
            ->when($inicio && !$fim, fn($q) => $q->where('data_hora_evento', '>=', $inicio))
            ->when(!$inicio && $fim, fn($q) => $q->where('data_hora_evento', '<=', $fim))
            ->when($centroID, fn($q) => $q->where('cost_center_id', $centroID))
            ->when($caixaID, fn($q) => $q->where('caixa_id', $caixaID))
            ->orderByDesc('data_hora_evento');

        // paginação (10 por página) – altera se quiser
        $lancamentos = $baseQuery->get();   // ① devolve todos

        /* -----------------------------------------------------------
        | 4. Totais (para os cards verde/vermelho/azul)
        |------------------------------------------------------------*/
        $entradas = $this->financialRepository->where('tipo', 'entrada')->sum('valor');
        $saidas = $this->financialRepository->where('tipo', 'saida')->sum('valor');
        $saldo = $entradas - $saidas;
        $caixas = Caixa::whereNull('exclusao')->get()->sortBy('nome'); // adição de caixas para o select no modal

        /* -----------------------------------------------------------
        | 5. View
        |------------------------------------------------------------*/
        return view('pages.financeiro.index', [
            'centrosDeCusto' => $centrosDeCusto,
            'lancamentos' => $lancamentos,
            'entradas' => $entradas,
            'saidas' => $saidas,
            'saldo' => $saldo,
            'caixas' => $caixas,
            // preserva filtros no blade
            'filtro' => [
                'data_inicio' => date('Y-m-d', strtotime($inicio)),
                'data_fim' => date('Y-m-d', strtotime($fim)),
                'centro_custo_id' => $centroID,
                'caixa_id' => $caixaID,
            ],
        ]);
    }

    /**
     * Salva um novo lançamento financeiro.
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        /* 1) Validação -------------------------------------------------- */
        $data = $request->validate([
            'data_hora_evento' => 'required|date',
            'tipo' => 'required|in:entrada,saida',
            'descricao' => 'required|string|max:255',
            'centro_custo_id' => 'required',
            'caixa_id' => 'required',
            'valor' => 'required',
        ]);

        /* 2) Normalizar valor  (“1.234,56” → 1234.56) ------------------- */
        $valor = (float) str_replace(['.', ','], ['', '.'], $data['valor']);

        try {
            /* 3) Centro de Custo ---------------------------------------- */
            if (is_numeric($data['centro_custo_id'])) {
                // já existe
                $costCenterId = (int) $data['centro_custo_id'];
            } else {
                // cria dinamicamente (evita duplicar nome com firstOrCreate)
                $novoCentro = $this->costCenterRepository
                    ->firstOrCreate(['descricao' => trim($data['centro_custo_id'])]);
                $costCenterId = $novoCentro->id;
            }


            /* 4) Grava Lançamento -------------------------------------- */
            $LANCAMENTO = $this->financialRepository->create([
                'data_hora_evento' => $data['data_hora_evento'],
                'tipo' => $data['tipo'],
                'descricao' => $data['descricao'],
                'valor' => $valor,
                'cost_center_id' => $costCenterId,
                'caixa_id' => $data['caixa_id'],
                'user_id' => auth()->id(),     // opcional
            ]);

            /* 5) Sucesso ------------------------------------------------ */
            return redirect()
                ->back()
                ->with('sucesso', 'Lançamento cadastrado com sucesso!');

        } catch (\Throwable $e) {
            dd($e);
            /* 6) Falha -------------------------------------------------- */
            \Log::error('Erro ao salvar lançamento: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()                       // devolve dados para o form
                ->with('error', 'Ocorreu um erro ao salvar o lançamento. Tente novamente.');
        }
    }

    /**
     * Atualiza um lançamento.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'data_hora_evento' => 'required|date',
            'tipo' => 'required|in:entrada,saida',
            'descricao' => 'required|string|max:255',
            'centro_custo_id' => 'required',
            'caixa_id' => 'required',
            'valor' => 'required',
        ]);

        /* Converte “1.234,56” → 1234.56 */
        $valor = (float) str_replace(['.', ','], ['', '.'], $data['valor']);

        /* Resolve centro de custo (cria se texto) */
        $costCenterId = is_numeric($data['centro_custo_id'])
            ? (int) $data['centro_custo_id']
            : $this->costCenterRepository
                ->firstOrCreate(['descricao' => trim($data['centro_custo_id'])])
                ->id;

        try {
            $this->financialRepository->update([
                'data_hora_evento' => $data['data_hora_evento'],
                'tipo' => $data['tipo'],
                'descricao' => $data['descricao'],
                'valor' => $valor,
                'caixa_id' =>$data['caixa_id'],
                'cost_center_id' => $costCenterId,
            ], $id);

            return back()->with('success', 'Lançamento atualizado com sucesso!');
        } catch (\Throwable $e) {
            \Log::error('Erro ao atualizar lançamento: ' . $e->getMessage());
            return back()->with('error', 'Não foi possível atualizar o lançamento.');
        }
    }

    /**
     * Remove um lançamento.
     * @param  int  $id
     */
    public function destroy(int $id)
    {
        try {
            $this->financialRepository->delete($id);
            return back()->with('success', 'Lançamento excluído com sucesso!');
        } catch (\Throwable $e) {
            \Log::error('Erro ao excluir lançamento: ' . $e->getMessage());
            return back()->with('error', 'Não foi possível excluir o lançamento.');
        }
    }

    /**
     * Summary of print
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function print(Request $request)
    {
        /* -----------------------------------------------------------
        | 1. Filtros recebidos
        |------------------------------------------------------------*/
        $inicio   = $request->filled('data_inicio')
            ? Carbon::parse($request->input('data_inicio'))->startOfDay()
            : null;

        $fim      = $request->filled('data_fim')
            ? Carbon::parse($request->input('data_fim'))->endOfDay()
            : null;

        $centroID = $request->input('centro_custo_id');

        /* -----------------------------------------------------------
        | 2. Query de lançamentos (já com eager loading)
        |------------------------------------------------------------*/
        $baseQuery = $this->financialRepository->model()
            ::with('costCenter')
            ->when($inicio && $fim,   fn($q) => $q->whereBetween('data_hora_evento', [$inicio, $fim]))
            ->when($inicio && !$fim,  fn($q) => $q->where('data_hora_evento', '>=', $inicio))
            ->when(!$inicio && $fim,  fn($q) => $q->where('data_hora_evento', '<=', $fim))
            ->when($centroID,         fn($q) => $q->where('cost_center_id', $centroID))
            ->orderByDesc('data_hora_evento');

        $lancamentos = $baseQuery->get();

        /* -----------------------------------------------------------
        | 3. Totais
        |------------------------------------------------------------*/
        $entradas = (clone $baseQuery)->where('tipo', 'entrada')->sum('valor');
        $saidas   = (clone $baseQuery)->where('tipo', 'saida')->sum('valor');
        $saldo    = $entradas - $saidas;

        /* -----------------------------------------------------------
        | 4. Período legível para cabeçalho
        |------------------------------------------------------------*/
        $periodo = [
            'inicio' => $inicio ? $inicio->format('d/m/Y') : null,
            'fim'    => $fim    ? $fim->format('d/m/Y')    : null,
        ];

        /* -----------------------------------------------------------
        | 5. Centros de Custo (para exibir nome no cabeçalho, se filtro)
        |------------------------------------------------------------*/
        $centrosDeCusto = $this->costCenterRepository
                            ->orderBy('descricao')
                            ->all();

        $centroSelecionado = $centroID
            ? $centrosDeCusto->firstWhere('id', $centroID)?->descricao
            : 'Todos';

        /* -----------------------------------------------------------
        | 6. Renderiza view de impressão
        |------------------------------------------------------------*/
        return view('pages.financeiro.print', [
            'lancamentos'        => $lancamentos,
            'entradas'           => $entradas,
            'saidas'             => $saidas,
            'saldo'              => $saldo,
            'periodo'            => $periodo,
            'centroSelecionado'  => $centroSelecionado,
        ]);
    }

}
