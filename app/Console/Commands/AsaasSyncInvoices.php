<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Asaas\AsaasService;
use App\Models\Beneficiary;
use App\Models\Invoice;
use App\Models\InvoiceHistory;
use App\Models\BeneficiaryPlan;
use Carbon\Carbon;
use DB;
use Exception;

class AsaasSyncInvoices extends Command
{
    protected $signature = 'asaas:sync-invoices 
                            {--company= : ID da empresa (opcional)}
                            {--days=30 : Buscar cobranças dos últimos X dias}';

    protected $description = 'Sincroniza cobranças do Asaas com Invoices e histórico';

    public function handle()
    {
        $this->info('🔄 Iniciando sincronização de cobranças Asaas');

        $asaas = app(AsaasService::class);

        $days = (int) $this->option('days');
        $companyId = $this->option('company');

        $offset = 0;
        $limit = 100;

        try {
            do {
                $response = $asaas->getPayments([
                    'offset' => $offset,
                    'limit' => $limit,
                    'dateCreated[ge]' => now()->subDays($days)->format('Y-m-d')
                ]);

                foreach ($response['data'] as $payment) {
                    $this->syncPayment($payment, $companyId);
                }

                $offset += $limit;

            } while ($response['hasMore']);

            $this->info('✅ Sincronização finalizada');
            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error('❌ Erro: ' . $e->getMessage());
            report($e);
            return Command::FAILURE;
        }
    }

    /**
     * Sincroniza uma cobrança individual
     */
    protected function syncPayment(array $payment, ?int $companyId = null)
    {
        $beneficiary = Beneficiary::where('asaas_customer_id', $payment['customer'])
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->first();

        if (!$beneficiary) {
            $this->warn("⚠ Beneficiário não encontrado para customer {$payment['customer']}");
            return;
        }

        DB::transaction(function () use ($payment, $beneficiary) {

            $dueDate = Carbon::parse($payment['dueDate']);
            $newStatus = $payment['status'];
            /**
             * 🔧 CORREÇÃO AUTOMÁTICA
             * Se existir plano sem start_date, preencher com created_at
             */
            BeneficiaryPlan::where('beneficiary_id', $beneficiary->id)
                ->whereNull('start_date')
                ->get()
                ->each(function ($plan) {
                    $plan->update([
                        'start_date' => $plan->created_at->toDateString()
                    ]);
                });

            /**
             * Agora sim resolve o plano ativo corretamente
             */
            $beneficiaryPlan = $beneficiary->activePlanAt($dueDate);

            if (!$beneficiaryPlan) {
                logger()->warning('Invoice ignorada: sem plano ativo mesmo após correção', [
                    'asaas_payment_id' => $payment['id'],
                    'beneficiary_id'   => $beneficiary->id,
                    'due_date'         => $payment['dueDate'],
                ]);

                return;
            }

            $beneficiaryPlan = $beneficiary->activePlanAt($dueDate);

            $invoice = Invoice::where('asaas_payment_id', $payment['id'])->first();

            if (!$invoice) {

                $invoice = Invoice::create([
                    'asaas_payment_id' => $payment['id'],
                    'beneficiary_id' => $beneficiary->id,
                    'beneficiary_plan_id' => $beneficiaryPlan?->id,
                    'competence_month' => $dueDate->month,
                    'competence_year' => $dueDate->year,
                    'invoice_value' => $payment['value'],
                    'status' => $newStatus,
                    'due_date' => $payment['dueDate'],
                    'payment_type' => $payment['billingType'],
                    'payment_date' => $payment['paymentDate'] ?? null,
                ]);

                InvoiceHistory::create([
                    'invoice_id' => $invoice->id,
                    'transaction_code' => $payment['id'],
                    'status_transaction' => $newStatus,
                    'return_code' => $newStatus,
                    'return_message' => $payment['description'] ?? 'Criação via sync Asaas'
                ]);

                return;
            }

            // 🔁 Atualiza invoice
            $statusChanged = $invoice->status !== $newStatus;

            $invoice->update([
                'status' => $newStatus,
                'payment_date' => $payment['paymentDate'] ?? $invoice->payment_date,
            ]);

            // 🧾 Histórico só se mudou status
            if ($statusChanged) {
                InvoiceHistory::firstOrCreate(
                    [
                        'invoice_id' => $invoice->id,
                        'status_transaction' => $newStatus,
                    ],
                    [
                        'transaction_code' => $payment['id'],
                        'return_code' => $newStatus,
                        'return_message' => $payment['description'] ?? 'Atualização via sync Asaas'
                    ]
                );
            }
        });


        $this->line("✔ Invoice sincronizada: {$payment['id']} ({$payment['status']})");
    }
}
