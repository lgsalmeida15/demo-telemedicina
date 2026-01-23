<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Beneficiary extends Authenticatable implements Transformable
{
    use TransformableTrait;

    protected $fillable = [
        'company_id',
        // 'plan_id',
        'asaas_customer_id',
        'name',
        'cpf',
        'email',
        'password',
        'phone',
        'action',
        'birth_date',
        'gender',
        'relationship',
        'mother_name',
        'inclusion_date',
        'exclusion_date'
    ];

    protected $hidden = ['password'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // pode ter muitos planos
    public function plans()
    {
        return $this->hasMany(BeneficiaryPlan::class, 'beneficiary_id');
    }


    public function activePlanAt(Carbon $date): ?BeneficiaryPlan
    {
        return $this->plans()
            ->activeAt($date)
            ->orderByDesc('start_date')
            ->first();
    }


    public function isInadimplente(): bool
    {
        return $this->invoices()
            ->whereDate('due_date', '<', now())
            ->whereIn('status', ['PENDING', 'OVERDUE', 'AWAITING_PAYMENT'])
            ->exists();
    }

    /**
     * Retorna o plano atual do beneficiário
     */
    public function currentPlan(): ?BeneficiaryPlan
    {
        return $this->plans()
            ->orderByDesc('start_date')
            ->first();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'beneficiary_id');
    }


    // pode ter muitos planos
    // public function plans()
    // {
    //     return $this->belongsToMany(Plan::class, 'plan_id');
    // }


    /**
     * verifica se o beneficiario tem plano com telemedicina
     */

    public function hasTelemedicina()
    {
        return $this->plans() // pivot
            ->whereHas('plan', function ($query) {
                $query->where('is_telemedicine', true);
            })
            ->exists();
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($beneficiary) {
            if ($beneficiary->action === 'E' && is_null($beneficiary->exclusion_date)) {
                $beneficiary->exclusion_date = Carbon::now();
            } else {
                $beneficiary->exclusion_date = null;
            }
        });
    }


    /**
     * Sempre que o beneficiário for criado ou atualizado,
     * se o campo password estiver vazio, ele será preenchido
     * automaticamente com a data de nascimento no formato DDMMAAAA.
     */
    public function setBirthDateAttribute($value)
    {
        // Salva o valor original no banco
        $this->attributes['birth_date'] = $value;

        // Gera senha somente se ainda não existir
        if (!isset($this->attributes['password']) || empty($this->attributes['password'])) {

            // Converte a data enviada ("Y-m-d") para objeto Carbon
            $date = Carbon::createFromFormat('Y-m-d', $value);

            // Gera a senha no formato solicitado: DDMMAAAA
            $rawPassword = $date->format('dmY');

            // Aplica hash
            $this->attributes['password'] = bcrypt($rawPassword);
        }
    }
}


