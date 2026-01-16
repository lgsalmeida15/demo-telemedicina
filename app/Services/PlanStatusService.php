<?php

namespace App\Services;

use App\Models\Beneficiary;

class PlanStatusService
{
    public function resolveForBeneficiary(Beneficiary $beneficiary): ?string
    {
        $plan = $beneficiary->currentPlan();

        if (!$plan) {
            return null;
        }

        if ($plan->isCanceledWaitingEnd()) {
            return 'cancel_waiting_end';
        }

        if ($plan->isExpired()) {
            return 'expired';
        }

        return 'active';
    }

    public function label(string|null $status): array
    {
        return match ($status) {
            'active' => ['label' => 'Ativo', 'class' => 'bg-success text-white'],
            'cancel_waiting_end' => ['label' => 'Cancelado (vigente)', 'class' => 'bg-warning text-dark'],
            'expired' => ['label' => 'Expirado', 'class' => 'bg-danger text-white'],
            default => ['label' => 'Sem plano', 'class' => 'bg-secondary text-white'],
        };
    }
}
