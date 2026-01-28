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

        // ✅ ADICIONAR: Status do plano para a sidebar funcionar
        $currentPlan = $beneficiary->currentPlan();
        $planStatus = 'active'; // Para demonstração, sempre ativo

        return view('pages.beneficiaries.area.index', compact('beneficiary', 'plans', 'planStatus', 'currentPlan'));
    }


    /**
     * Redireciona para tela de edição de dados do titular
     */
    public function profileEdit()
    {
        $profile = Auth::guard('beneficiary')->user();
        
        // ✅ Variáveis para sidebar
        $currentPlan = $profile->currentPlan();
        $planStatus = 'active';

        return view('pages.beneficiaries.area.edit', compact('profile', 'planStatus', 'currentPlan'));
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

        // ✅ Variáveis para sidebar
        $beneficiary = Auth::guard('beneficiary')->user();
        $currentPlan = $beneficiary->currentPlan();
        $planStatus = 'active';

        return view('pages.beneficiaries.area.planDetails', compact('plan', 'planStatus', 'currentPlan'));
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
        
        // ✅ Horários dinâmicos para demonstração (apenas horários futuros)
        $now = now();
        $today = now();
        $tomorrow = now()->addDay();
        
        $hours = [];
        
        // Horários de HOJE (somente horários futuros - considerando hora E minutos)
        $todayHours = [9, 10, 11, 14, 15, 16, 17, 18];
        
        foreach ($todayHours as $hour) {
            // Verifica horário :00
            $timeSlot00 = $today->copy()->setTime($hour, 0, 0);
            if ($timeSlot00->isFuture()) {
                $hours[] = $timeSlot00->format('Y-m-d H:i:s');
            }
            
            // Verifica horário :30
            $timeSlot30 = $today->copy()->setTime($hour, 30, 0);
            if ($timeSlot30->isFuture()) {
                $hours[] = $timeSlot30->format('Y-m-d H:i:s');
            }
        }
        
        // Horários de AMANHÃ (todos os horários disponíveis)
        $tomorrowHours = [9, 10, 11, 14, 15, 16, 17, 18];
        foreach ($tomorrowHours as $hour) {
            $hours[] = $tomorrow->copy()->setTime($hour, 0)->format('Y-m-d H:i:s');
            $hours[] = $tomorrow->copy()->setTime($hour, 30)->format('Y-m-d H:i:s');
        }
        
        // Se não tiver horários suficientes, adicionar depois de amanhã
        if (count($hours) < 8) {
            $dayAfterTomorrow = now()->addDays(2);
            $extraHours = [9, 10, 11, 14, 15, 16];
            foreach ($extraHours as $hour) {
                $hours[] = $dayAfterTomorrow->copy()->setTime($hour, 0)->format('Y-m-d H:i:s');
                $hours[] = $dayAfterTomorrow->copy()->setTime($hour, 30)->format('Y-m-d H:i:s');
            }
        }
        
        $availableHours = ['hours' => $hours];
        
        // ✅ Variáveis para sidebar
        $currentPlan = $beneficiary->currentPlan();
        $planStatus = 'active';
        
        return view('pages.beneficiaries.area.telemedicine', [
            'beneficiary' => $beneficiary,
            'specialtyId' => 1,
            'date' => $date,
            'availableHours' => $availableHours,
            'planStatus' => $planStatus,
            'currentPlan' => $currentPlan
        ]);
    }


    // redirect to docway service
    public function redirectToTelemedicine(Request $request)
    {
        $request->validate([
            'hour' => 'required',
            'specialty' => 'required',
            'doctor' => 'required'
        ]);

        $beneficiary = Auth::guard('beneficiary')->user();
        
        // ✅ Criar agendamento de demonstração na sessão com dados escolhidos pelo usuário
        $appointment = [
            'appointment_id' => uniqid('demo_'),
            'date' => $request->hour,
            'specialty' => $request->specialty,  // ← Do formulário
            'doctor_name' => $request->doctor,   // ← Do formulário
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

        // ✅ Variáveis para sidebar
        $currentPlan = $beneficiary->currentPlan();
        $planStatus = 'active';

        return view('pages.beneficiaries.area.dependents', compact('beneficiary', 'dependents', 'planStatus', 'currentPlan'));
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

        // ✅ Variáveis para sidebar
        $currentPlan = $beneficiary->currentPlan();
        $planStatus = 'active';

        return view('pages.beneficiaries.area.schedules', compact('appointments', 'planStatus', 'currentPlan'));
    }

    /**
     * Cancelar um agendamento específico (demonstração)
     */
    public function cancelSchedule(Request $request)
    {
        $appointmentId = $request->input('appointment_id');
        
        // ✅ Busca agendamentos da sessão
        $appointments = session('demo_appointments', []);
        
        // ✅ Remove o agendamento específico
        $appointments = array_filter($appointments, function($app) use ($appointmentId) {
            return $app['appointment_id'] !== $appointmentId;
        });
        
        // ✅ Reindexar o array
        $appointments = array_values($appointments);
        
        // ✅ Atualiza a sessão
        session(['demo_appointments' => $appointments]);
        
        return redirect()
            ->route('beneficiary.area.schedule')
            ->with('sucesso', 'Agendamento cancelado com sucesso!');
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
