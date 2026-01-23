<?php

namespace App\Services;

use App\Models\Beneficiary;
use App\Models\BeneficiaryPlan;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoBeneficiaryService
{
    /**
     * Cria um beneficiário demo completo
     *
     * @param array $data
     * @return Beneficiary
     */
    public function createDemoBeneficiary(array $data): Beneficiary
    {
        $company = Company::findOrFail($data['company_id']);
        $plan = Plan::findOrFail($data['plan_id']);
        
        // Definir expiração do demo (padrão: 30 dias)
        $demoExpiresAt = now()->addDays($data['demo_days'] ?? 30);
        
        // Preparar senha padrão (formato DDMMAAAA se não informada)
        $defaultPassword = $data['password'] ?? null;
        if (!$defaultPassword && isset($data['birth_date'])) {
            // Converter data de nascimento (Y-m-d) para formato DDMMAAAA
            $defaultPassword = Carbon::createFromFormat('Y-m-d', $data['birth_date'])->format('dmY');
        }
        
        // Criar beneficiário
        $beneficiary = Beneficiary::create([
            'company_id' => $company->id,
            'name' => $data['name'],
            'cpf' => $data['cpf'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'birth_date' => $data['birth_date'],
            'gender' => $data['gender'] ?? 'M',
            'relationship' => $data['relationship'] ?? 'Titular',
            'mother_name' => $data['mother_name'] ?? null,
            'password' => Hash::make($defaultPassword), // Senha padrão: data de nascimento no formato DDMMAAAA
            'is_demo' => true,
            'demo_expires_at' => $demoExpiresAt,
            'asaas_customer_id' => null, // Não precisa de customer no Asaas
            'action' => 'I',
            'inclusion_date' => now()->toDateString(),
        ]);
        
        // Criar relacionamento com plano
        $beneficiaryPlan = BeneficiaryPlan::create([
            'beneficiary_id' => $beneficiary->id,
            'plan_id' => $plan->id,
            'start_date' => now()->toDateString(),
            'end_date' => $demoExpiresAt->toDateString(),
            'is_demo' => true,
        ]);
        
        // Criar invoice fictícia (para manter consistência)
        Invoice::create([
            'uuid' => Str::uuid(),
            'beneficiary_id' => $beneficiary->id,
            'beneficiary_plan_id' => $beneficiaryPlan->id,
            'asaas_payment_id' => null, // Sem ID do Asaas
            'competence_month' => now()->format('m'),
            'competence_year' => now()->format('Y'),
            'invoice_value' => 0.00, // Valor zero para demo
            'status' => 'CONFIRMED', // Status confirmado automaticamente
            'due_date' => $demoExpiresAt->toDateString(),
            'payment_date' => now(),
            'payment_type' => 'DEMO',
            'is_demo' => true,
        ]);
        
        return $beneficiary->load(['company', 'plans.plan']);
    }
    
    /**
     * Atualiza período demo de um beneficiário
     *
     * @param Beneficiary $beneficiary
     * @param int $days
     * @return Beneficiary
     */
    public function extendDemo(Beneficiary $beneficiary, int $days): Beneficiary
    {
        if (!$beneficiary->isDemo()) {
            throw new \Exception('Este beneficiário não está em modo demo.');
        }
        
        $newExpirationDate = $beneficiary->demo_expires_at->copy()->addDays($days);
        
        $beneficiary->update([
            'demo_expires_at' => $newExpirationDate,
        ]);
        
        // Atualizar end_date dos planos
        $beneficiary->plans()
            ->where('is_demo', true)
            ->update(['end_date' => $newExpirationDate->toDateString()]);
        
        return $beneficiary->fresh();
    }
    
    /**
     * Lista beneficiários demo que expiraram
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getExpiredDemos()
    {
        return Beneficiary::where('is_demo', true)
            ->where('demo_expires_at', '<', now())
            ->with(['company', 'plans.plan'])
            ->get();
    }
    
    /**
     * Remove beneficiários demo expirados
     *
     * @param int $daysAfterExpiration
     * @return int Quantidade de beneficiários removidos
     */
    public function cleanupExpiredDemos(int $daysAfterExpiration = 7): int
    {
        $beneficiaries = Beneficiary::where('is_demo', true)
            ->where('demo_expires_at', '<', now()->subDays($daysAfterExpiration))
            ->get();
        
        $count = $beneficiaries->count();
        
        foreach ($beneficiaries as $beneficiary) {
            // Remover relacionamentos
            $beneficiary->plans()->delete();
            $beneficiary->invoices()->delete();
            $beneficiary->delete();
        }
        
        return $count;
    }
}

