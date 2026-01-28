<?php

namespace App\Http\Controllers\Beneficiary;

use App\Services\Asaas\AsaasService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Plan;
use App\Models\BeneficiaryPlan;
use Illuminate\Http\Request;
use App\Models\Dependent;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Hash;
use App\Services\SubscriptionCancellationService;

class BeneficiaryAreaController extends Controller
{
    /**
     * Controller de gestão da área do Beneficiário
     */

    public function index()
    {
        $beneficiary = Auth::guard('beneficiary')->user();
        if ($beneficiary->isInadimplente()) {
            // Adicionar a logica caso esteja inadiplente
        }

        $cpf = preg_replace('/\D/', '', $beneficiary->cpf);
        $docwayUuid = null;

        // ✅ IBAM é opcional - se falhar, não bloqueia o acesso
        try {
            // INSTANCIA SERVIÇO IBAM
            $ibam = new \App\Services\IBAMService("https://sistema.ibambeneficios.com.br/api/external/");
            $ibam->login();

            // 1) CONSULTA NA API DO IBAM
            $exists = $ibam->findBeneficiary($cpf);
            
            if (
                isset($exists['response']['exists']) &&
                $exists['response']['exists'] === true &&
                isset($exists['response']['data']['docway_patient_id'])
            ) {
                // Já existe na IBAM
                $docwayUuid = $exists['response']['data']['docway_patient_id'];
            } else {
                // 2) NÃO EXISTE → CRIAR AUTOMATICAMENTE
                $create = $ibam->createBeneficiary([
                    "name" => $beneficiary->name,
                    "cpf" => $cpf,
                    "email" => $beneficiary->email,
                    "phone" => $beneficiary->phone,
                    "birth_date" => $beneficiary->birth_date,
                    "gender" => $beneficiary->gender,
                    "mother_name" => $beneficiary->mother_name,
                    "relationship" => "Titular"
                ]);

                // Reconsulta para obter ID correto
                $create = $ibam->findBeneficiary($cpf);

                if (isset($create['response']['success']) && $create['response']['success'] === true) {
                    $docwayUuid = $create['response']['uuid'] ?? null;

                    if ($docwayUuid) {
                        // 3) Atualiza beneficiário localmente (opcional)
                        $beneficiary->docway_patient_id = $docwayUuid;
                        $beneficiary->save();
                    }
                }
            }
        } catch (\Exception $e) {
            // ✅ Se IBAM falhar, apenas loga o erro mas não bloqueia acesso
            \Log::warning('Erro ao sincronizar com IBAM (não bloqueia acesso)', [
                'error' => $e->getMessage(),
                'beneficiary_id' => $beneficiary->id
            ]);
        }

        // CARREGA OS PLANOS
        $plans = BeneficiaryPlan::where('beneficiary_id', $beneficiary->id)
            ->with(['plan.conveniences.convenio.partner', 'plan.conveniences.convenio.type', 'plan.conveniences.convenio.categoria'])
            ->get()
            ->map(fn($bp) => $bp->plan);

        return view('pages.beneficiaries.area.index', compact('beneficiary', 'plans'));
    }


    /**
     * Redireciona para tela de edição de dados do titular
     */
    public function profileEdit()
    {
        $profile = Auth::guard('beneficiary')->user();

        return view('pages.beneficiaries.area.edit', compact('profile'));
    }


    /**
     * Atualiza os dados do perfil autenticado
     */
    public function profileUpdate(Request $request){

        $data = $request->validate(
            [
                'email' => 'required|string',
                'password' => 'nullable|string',
                'phone' => 'required',
                'birth_date' => 'required',
            ]
        );

        $profile = Auth::guard('beneficiary')->user();

        try {
            $profile = Beneficiary::findOrFail($profile->id);

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }
            
            $profile->update($data);

            return redirect()->route('beneficiary.area.index')
                    ->with('sucesso', 'Os dados do beneficiário foram atualizados com sucesso!');
        } catch(\Exception $e) {
            return redirect()->back()->withErrors('Erro ao editar seus dados: '.$e);
        }

    }



    /**
     * Exibir detalhes de um plano
     */
    public function planDetails($planId)
    {
        $plan = Plan::with([
            'company',
            'conveniences.convenio.partner',
            'conveniences.convenio.type',
            'conveniences.convenio.categoria'
        ])->findOrFail($planId);

        return view('pages.beneficiaries.area.planDetails', compact('plan'));
    }


    
    public function updateCreditCard(Request $request)
    {
        $beneficiary = auth('beneficiary')->user();

        // 🔍 Busca a assinatura (sub_...)
        $subscriptionInvoice = $beneficiary->invoices()
            ->where('asaas_payment_id', 'like', 'sub_%')
            ->latest()
            ->first();

        if (!$subscriptionInvoice) {
            return back()->withErrors('Assinatura não encontrada.');
        }

        $subscriptionId = $subscriptionInvoice->asaas_payment_id;

        // 🔒 Monta dados do cartão
        $creditCard = [
            'holderName'  => $request->card_holder,
            'number'      => preg_replace('/\D/', '', $request->card_number),
            'expiryMonth' => $request->card_month,
            'expiryYear'  => $request->card_year,
            'ccv'         => $request->ccv,
        ];

        // 🔒 Dados do titular
        $holderInfo = [
            'name'          => $beneficiary->name,
            'email'         => $beneficiary->email,
            'cpfCnpj'       => $beneficiary->cpf,
            'postalCode'    => preg_replace('/\D/', '', $request->postal_code),
            'addressNumber' => $request->address_number,
            'addressComplement' => null,
            'phone'         => $beneficiary->phone,
            'mobilePhone'   => $beneficiary->phone,
        ];

        try {
            app(AsaasService::class)->updateSubscriptionCreditCard(
                $subscriptionId,
                $creditCard,
                $holderInfo,
                $request->ip() // 🔥 remoteIp obrigatório
            );

            return back()->with('sucesso', 'Cartão atualizado com sucesso.');

        } catch (\Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    /**
     * Telemedicina
     */
    public function telemedicine(Request $request)
    {
        $beneficiary = Auth::guard('beneficiary')->user();
        $date = now()->format('Y-m-d');
        
        // ✅ Horários mockados para demonstração (hoje e amanhã)
        $availableHours = [
            'hours' => [
                now()->setTime(14, 0)->format('Y-m-d H:i:s'),
                now()->setTime(15, 30)->format('Y-m-d H:i:s'),
                now()->setTime(16, 0)->format('Y-m-d H:i:s'),
                now()->addDay()->setTime(9, 0)->format('Y-m-d H:i:s'),
                now()->addDay()->setTime(10, 30)->format('Y-m-d H:i:s'),
                now()->addDay()->setTime(14, 0)->format('Y-m-d H:i:s'),
                now()->addDay()->setTime(16, 30)->format('Y-m-d H:i:s'),
            ]
        ];
        
        return view('pages.beneficiaries.area.telemedicine', [
            'beneficiary' => $beneficiary,
            'specialtyId' => 1,
            'date' => $date,
            'availableHours' => $availableHours
        ]);
    }


    // redirect to docway service
    public function redirectToTelemedicine(Request $request)
    {
        $request->validate([
            'hour' => 'required'
        ]);

        $beneficiary = Auth::guard('beneficiary')->user();
        
        // ✅ Criar agendamento de demonstração na sessão
        $appointment = [
            'appointment_id' => uniqid('demo_'),
            'date' => $request->hour,
            'specialty' => 'Clínico Geral',
            'doctor_name' => 'Dr. ' . ['João Silva', 'Maria Santos', 'Carlos Oliveira', 'Ana Paula'][array_rand(['João Silva', 'Maria Santos', 'Carlos Oliveira', 'Ana Paula'])],
            'status' => 1, // 1 = Agendado
            'details_raw' => ['videoRoomLink' => 'https://meet.google.com/demo-consulta-' . uniqid()],
            'created_at' => now()->toDateTimeString()
        ];
        
        // Salva na sessão
        $appointments = session('demo_appointments', []);
        $appointments[] = $appointment;
        session(['demo_appointments' => $appointments]);
        
        // Redireciona de volta com mensagem de sucesso
        return redirect()
            ->route('beneficiary.area.schedule')
            ->with('sucesso', 'Agendamento realizado com sucesso! Você pode visualizá-lo na lista de agendamentos.');
    }




    /**
     * Lista de dependentes do beneficiário
     */
    public function dependents()
    {

        $beneficiary = Auth::guard('beneficiary')->user();
        $dependents = Dependent::whereNull('deleted_at')
            ->where('beneficiary_id', $beneficiary->id)
            ->orderBy('name', 'asc')
            ->get();

        return view('pages.beneficiaries.area.dependents', compact('beneficiary', 'dependents'));

    }



    /**
     * Tela de Agendamentos
     */
    public function schedules()
    {
        $beneficiary = Auth::guard('beneficiary')->user();
        
        // ✅ Agendamentos fixos de demonstração
        $appointmentsMock = [
            [
                'appointment_id' => 'demo-1',
                'date' => now()->addDays(2)->setTime(14, 30)->format('Y-m-d H:i:s'),
                'specialty' => 'Clínico Geral',
                'doctor_name' => 'Dr. João Silva',
                'status' => 1, // 1 = Agendado
                'details_raw' => ['videoRoomLink' => 'https://meet.google.com/demo-consulta-1']
            ],
            [
                'appointment_id' => 'demo-2',
                'date' => now()->subDays(5)->setTime(10, 0)->format('Y-m-d H:i:s'),
                'specialty' => 'Cardiologia',
                'doctor_name' => 'Dra. Maria Santos',
                'status' => 5, // 5 = Concluído
                'details_raw' => ['videoRoomLink' => '#']
            ],
            [
                'appointment_id' => 'demo-3',
                'date' => now()->addDays(7)->setTime(9, 0)->format('Y-m-d H:i:s'),
                'specialty' => 'Pediatria',
                'doctor_name' => 'Dr. Carlos Oliveira',
                'status' => 1, // 1 = Agendado
                'details_raw' => ['videoRoomLink' => 'https://meet.google.com/demo-consulta-2']
            ]
        ];
        
        // ✅ Busca agendamentos criados pelo usuário na sessão
        $sessionAppointments = session('demo_appointments', []);
        
        // ✅ Combina mockados + criados pelo usuário
        $appointments = array_merge($appointmentsMock, $sessionAppointments);

        return view('pages.beneficiaries.area.schedules', compact('appointments'));
    }

     /**
      * Summary of cancel
      * @param Request $request
      * @return \Illuminate\Http\RedirectResponse
      */
     public function cancel(Request $request)
    {
        try {
            $beneficiary = auth('beneficiary')->user();

            app(SubscriptionCancellationService::class)
                ->requestCancellation($beneficiary);

            return redirect()
                ->route('beneficiary.area.index')
                ->with(
                    'success',
                    'Sua assinatura foi cancelada. Você continuará com acesso até o fim do período pago.'
                );

        } catch (Exception $e) {
            return back()->withErrors($e->getMessage());
        }
    }

}
