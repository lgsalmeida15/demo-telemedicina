<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\DependentRepository;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Mail\DependentAccessMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Services\BrevoMailService;


class DependentController extends Controller
{
    protected $dependentRepository;

    public function __construct(DependentRepository $dependentRepository)
    {
        $this->dependentRepository = $dependentRepository;
    }


    public function index ($beneficiaryId) {

        $dependents = $this->dependentRepository->where('beneficiary_id', $beneficiaryId)
            ->whereNull('deleted_at')
            ->orderBy('name', 'asc')
            ->get();

        $beneficiary = Beneficiary::findOrFail($beneficiaryId);

        return view('pages.dependents.index', compact('dependents', 'beneficiary'));

    }


    public function create ($beneficiaryId) {
        $beneficiary = Beneficiary::findOrFail($beneficiaryId);
        return view('pages.dependents.create', compact('beneficiary'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'beneficiary_id' => 'required',
            'name'           => 'required|string',
            'birth_date'     => 'nullable|date',
            'gender'         => 'nullable|string',
            'cpf'            => 'nullable|string|unique:dependents,cpf',
            'email'          => 'required|email',
            'password'       => 'nullable|string',
            'phone'          => 'required',
            'relationship'   => 'nullable|string',
        ]);

        // Senha
        $plainPassword = $data['password'] ?? Str::random(5);
        $data['password'] = Hash::make($plainPassword);

        try {
            $dependent = $this->dependentRepository->create($data);

            // Logo base64
            $logo = asset('material/img/logo.png');
            // $logo = file_get_contents($logo);
            // $logo = 'data:image/png;base64,' . base64_encode($logo);

            // HTML do email
            $html = view('emails.dependent-access', [
                'name'     => $dependent->name,
                'email'    => $dependent->email,
                'password' => $plainPassword,
                'loginUrl' => route('dependent.login'),
                'logo'     => $logo,
            ])->render();

            BrevoMailService::send(
                $dependent->email,
                'Acesso ao sistema',
                $html
            );

            return redirect()
                ->route('dependent.show', $dependent->id)
                ->with('sucesso', 'Dependente criado e e-mail enviado com sucesso.');

        } catch (\Throwable $e) {
            \Log::error('Erro ao criar dependente', ['erro' => $e->getMessage()]);

            return back()->withErrors('Erro ao salvar dependente.');
        }
    }


    public function show ($dependent) {
        $dependent = $this->dependentRepository->findOrFail($dependent);
        return view('pages.dependents.show', compact('dependent'));
    }



    public function edit ($dependent) {
        $dependent = $this->dependentRepository->findOrFail($dependent);
        return view('pages.dependents.edit', compact('dependent'));
    }


    public function update (Request $request, $dependent) {

        $dependent = $this->dependentRepository->findOrFail($dependent);

        $data = $request->validate(
            [
                'name' => 'required|string',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|string',
                'cpf' => [
                    'required',
                    'string',
                    Rule::unique('dependents', 'cpf')->ignore($dependent->id),
                ],
                'email' => 'required|email',
                'password' => 'nullable|string',
                'phone' => 'required',
                'relationship' => 'nullable|string',
            ],
            [
                'cpf.unique'   => 'CPF já está sendo usado.',
                'email.unique' => 'Email já está sendo usado.',
            ]
        );


        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        try {
            $dependent->update($data);
            return redirect()->route('dependent.show', ['dependent' => $dependent->id])
                ->with('sucesso', 'Dependente atualizado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Erro ao atualizar dependente: ' . $e->getMessage());
        }
    }


    public function softDelete ($dependent)
    {
        $dependent = $this->dependentRepository->findOrFail($dependent);

        $uniqueSuffix = '-' . $dependent->id . '-' . Str::uuid();

        try {
            $dependent->deleted_at = now();
            $dependent->email = null;
            $dependent->password = null;
            $dependent->phone = null;
            $dependent->cpf = $dependent->cpf . $uniqueSuffix;
            $dependent->save();

            return redirect()->back()
                ->with('sucesso', 'Dependente excluído com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Erro ao excluir dependente: ' . $e->getMessage());
        }
    }
}
