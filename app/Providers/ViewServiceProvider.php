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

            try {
                // Beneficiário logado
                if (auth('beneficiary')->check()) {
                    $beneficiary = auth('beneficiary')->user();
                    if ($beneficiary && method_exists($beneficiary, 'currentPlan')) {
                        $plan = $beneficiary->currentPlan();
                    }
                }

                // Dependente logado
                if (auth('dependent')->check()) {
                    $dependent = auth('dependent')->user();
                    if ($dependent && method_exists($dependent, 'beneficiary')) {
                        $beneficiary = $dependent->beneficiary;
                        if ($beneficiary && method_exists($beneficiary, 'currentPlan')) {
                            $plan = $beneficiary->currentPlan();
                        }
                    }
                }

                if ($plan) {
                    if (method_exists($plan, 'isCanceledWaitingEnd') && $plan->isCanceledWaitingEnd()) {
                        $planStatus = 'cancel_waiting_end';
                    } elseif (method_exists($plan, 'isExpired') && $plan->isExpired()) {
                        $planStatus = 'expired';
                    } else {
                        $planStatus = 'active';
                    }
                }
            } catch (\Exception $e) {
                // Em caso de erro, apenas loga e continua
                \Log::error('Erro no ViewServiceProvider: ' . $e->getMessage());
            }

            $view->with([
                'planStatus' => $planStatus,
                'currentPlan' => $plan
            ]);
        });
    }
}
