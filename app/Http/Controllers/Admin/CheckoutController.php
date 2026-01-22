<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Beneficiary;
use App\Models\Plan;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
// serviços
use App\Services\BeneficiaryService;
use App\Services\BeneficiaryPlanService;
use App\Services\AsaasCustomerService;
use App\Services\AsaasPaymentService;
use App\Services\InvoiceService;
use App\Services\InvoiceHistoryService;
use Illuminate\Support\Facades\DB;


class CheckoutController extends Controller
{
    protected $uuid;

    public function __construct()
    {
        $this->uuid = env('ELO_UUID');
    }
    /**
     * landing page inicial antes de mandar para o checkout
     */
    public function landingPage($uuid = null)
    {
        if (!$uuid) {
            $uuid = $this->uuid;
        }
        $company = Company::where('uuid', $uuid)->first();
        if (!$company) {
            return redirect()->route('login')->withErrors('Empresa não encontrada.');
        }
        $plans = Plan::where('company_id', $company->id)
            ->get();
        return view('pages.checkout.landingPage', compact('company', 'plans'));
    }


    /**
     * Página de Check-Out da Empresa
     */
    public function checkout($uuid)
    {
        $company = Company::where('uuid', $uuid)->first();
        if (!$company) {
            return redirect()->back()->withErrors('Empresa não encontrada.');
        }
        $plans = Plan::where('company_id', $company->id)
            ->get();
        return view('pages.checkout.index', compact('company', 'plans'));
    }


    public function checkoutProcess(Request $request)
    {
        // 🔒 REGRA DE NEGÓCIO — SOMENTE ELO
        if ($request->payment_type === 'CREDIT_CARD') {
            if (! $this->isCartaoElo($request->card_number)) {
                return back()
                    ->withErrors('Aceitamos apenas cartões da bandeira ELO.')
                    ->withInput()
                    ->with('selected_plan', $request->plan_uuid)
                    ->with('step', 3);
            }
        }
        try {
            // 1. cria beneficiário e customer do Asaas
            $beneficiary = app(BeneficiaryService::class)
                ->createBeneficiary($request, $request->company_uuid);

            // 2. cria o relacionamento beneficiário-plano
            $beneficiaryPlan = app(BeneficiaryPlanService::class)
                ->createBeneficiaryPlan($beneficiary, $request->plan_uuid);

            // 3. cria pagamento no ASAAS (boleto/pix/cartão)
            if ($request->payment_type === 'CREDIT_CARD') {

                $asaas = app(AsaasPaymentService::class);

                $payment = $asaas->createSubscription(
                    customer: $beneficiary->asaas_customer_id,
                    value: Plan::where('uuid', $request->plan_uuid)->first()->value,
                    description: "Assinatura de Plano na BoxFarma: " . Plan::where('uuid', $request->plan_uuid)->first()->name,
                    creditCard: [
                        'holderName' => $request->card_holder,
                        'number' => $request->card_number,
                        'expiryMonth' => $request->card_month,
                        'expiryYear' => $request->card_year,
                        'ccv' => $request->ccv,
                    ],
                    holderInfo: [
                        'name' => $beneficiary->name,
                        'email' => $beneficiary->email,
                        'cpfCnpj' => $beneficiary->cpf,
                        'postalCode' => $request->postal_code,
                        'addressNumber' => $request->address_number,
                        'addressComplement' => $request->address_complement,
                        'phone' => $beneficiary->phone,
                        'mobilePhone' => $beneficiary->phone,
                    ]
                );

            } else {
                // boleto ou pix
                $payment = app(AsaasPaymentService::class)
                    ->createPayment($beneficiary, $request->plan_uuid, $request->payment_type);
            }

            // 4. cria invoice local com payment_id
            $invoice = app(InvoiceService::class)
                ->createInvoice($beneficiary, $beneficiaryPlan, $payment, $request);

            return redirect()->route('checkout.confirmation', ['invoiceUuid' => $invoice->uuid])
                ->with('sucesso', 'Pagamento iniciado com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->withErrors('Erro ao processar o checkout: ' . $e->getMessage())
                ->withInput()
                ->with('selected_plan', $request->plan_uuid)
                ->with('step', 2); // volta pro step 2 ou 3 conforme erro
        }
    }




    public function checkoutConfirmation($invoiceUuid)
    {
        $invoice = Invoice::where('uuid', $invoiceUuid)
            ->with('beneficiary', 'plan')
            ->firstOrFail();

        DB::transaction(function () use ($invoice) {

            $now = Carbon::now();

            // 🧾 Atualiza payment_date da fatura (se ainda não existir)
            if (is_null($invoice->payment_date)) {
                $invoice->update([
                    'payment_date' => $now,
                    'status' => 'CONFIRMED'
                ]);
            }

            // 📅 Atualiza start_date do plano (se existir e ainda não tiver start_date)
            if ($invoice->beneficiary_plan_id && $invoice->plan) {

                $beneficiaryPlan = $invoice->beneficiary->plans()
                    ->where('id', $invoice->beneficiary_plan_id)
                    ->first();

                if ($beneficiaryPlan && is_null($beneficiaryPlan->start_date)) {
                    $beneficiaryPlan->update([
                        'start_date' => $now->toDateString()
                    ]);
                }
            }
        });

        // 🔐 Autentica automaticamente o beneficiário
        Auth::guard('beneficiary')->login($invoice->beneficiary);

        // 🔄 Regenera a sessão (segurança)
        session()->regenerate();

        return view('pages.checkout.confirmation', compact('invoice'));
    }


    function isCartaoElo(string $numeroCartao): bool
    {
        // Remove espaços e caracteres não numéricos
        $numeroCartao = preg_replace('/\D/', '', $numeroCartao);

        $regexElo = '/^4011(78|79)|^43(1274|8935)|^45(1416|7393|763(1|2))|^50(4175|6699|67[0-6][0-9]|677[0-8]|9[0-8][0-9]{2}|99[0-8][0-9]|999[0-9])|^627780|^63(6297|6368|6369)|^65(0(0(3([1-3]|[5-9])|4([0-9])|5[0-1])|4(0[5-9]|[1-3][0-9]|8[5-9]|9[0-9])|5([0-2][0-9]|3[0-8]|4[1-9]|[5-8][0-9]|9[0-8])|7(0[0-9]|1[0-8]|2[0-7])|9(0[1-9]|[1-6][0-9]|7[0-8]))|16(5[2-9]|[6-7][0-9])|50(0[0-9]|1[0-9]|2[1-9]|[3-4][0-9]|5[0-8]))/';

        return preg_match($regexElo, $numeroCartao) === 1;
    }
}
