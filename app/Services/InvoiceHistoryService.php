<?php

namespace App\Services;

use App\Models\InvoiceHistory;

class InvoiceHistoryService
{
    /**
     * Cria um histórico inicial para a invoice
     */
    public function createInvoiceHistory($invoice)
    {
        try {
            $history = InvoiceHistory::create(
                [
                    'invoice_id' => $invoice->id,
                    'transaction_code' => $invoice->asaas_payment_id,
                    'status_transaction' => $invoice->status,
                    'return_code' => null,
                    'return_message' => null,
                ]
            );

            return $history;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}