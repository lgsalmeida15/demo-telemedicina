<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {

            $planStatus = null;
            $plan = null;

            // Beneficiário logado
            if (auth('beneficiary')->check()) {
                $beneficiary = auth('beneficiary')->user();
                $plan = $beneficiary->currentPlan();
            }

            // Dependente logado
            if (auth('dependent')->check()) {
                $dependent = auth('dependent')->user();
                $beneficiary = $dependent->beneficiary;
                $plan = $beneficiary?->currentPlan();
            }

            if ($plan) {
                if ($plan->isCanceledWaitingEnd()) {
                    $planStatus = 'cancel_waiting_end';
                } elseif ($plan->isExpired()) {
                    $planStatus = 'expired';
                } else {
                    $planStatus = 'active';
                }
            }

            $view->with([
                'planStatus' => $planStatus,
                'currentPlan' => $plan
            ]);
        });
    }
}
