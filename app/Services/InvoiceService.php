<?php

namespace App\Services;

use App\Models\Invoice;
use App\Services\InvoiceHistoryService;

class InvoiceService
{
    public function createInvoice($beneficiary, $beneficiaryPlan, $payment, $request)
    {
        try {

            // Detecta se é ASSINATURA ou PAGAMENTO
            $isSubscription = isset($payment['object']) && $payment['object'] === 'subscription'
                || isset($payment['nextDueDate']);

            // Mapeamento dos campos dependendo do tipo
            if ($isSubscription) {
                // ASSINATURA (CREDIT_CARD)
                $asaasId   = $payment['id'];
                $value     = $payment['value'];
                $status    = $payment['status'] ?? 'PENDING';
                $dueDate   = $payment['nextDueDate'];  // assinatura usa nextDueDate
            } else {
                // PAGAMENTO (PIX/BOLETO)
                $asaasId   = $payment['id'];
                $value     = $payment['value'];
                $status    = $payment['status'];
                $dueDate   = $payment['dueDate']; // pagamento usa dueDate
            }

            // Cria invoice
            $invoice = Invoice::create([
                'beneficiary_plan_id' => $beneficiaryPlan->id,
                'beneficiary_id'      => $beneficiary->id,
                'asaas_payment_id'    => $asaasId,
                'competence_month'    => now()->format('m'),
                'competence_year'     => now()->format('Y'),
                'invoice_value'       => $value,
                'status'              => $status,
                'due_date'            => $dueDate,
                'payment_type'        => $request->payment_type,
                'payment_date'        => null,
            ]);

            // cria histórico inicial
            app(InvoiceHistoryService::class)
                ->createInvoiceHistory($invoice);

            return $invoice;

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
