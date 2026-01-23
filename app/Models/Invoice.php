<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Support\Str;

/**
 * Class Invoice.
 *
 * @package namespace App\Models;
 */
class Invoice extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'beneficiary_plan_id',
        'beneficiary_id',
        // 'asaas_customer_id',
        'asaas_payment_id',
        'competence_month',
        'competence_year',
        'invoice_value',
        'status',
        'due_date',
        'payment_type',
        'payment_date'
    ];

    /**
     * Get the beneficiary that owns the Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id', 'id');
    }

    /**
     * Summary of plan
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough<Plan, BeneficiaryPlan, Invoice>
     */
    public function plan()
    {
        return $this->hasOneThrough(
            Plan::class,            // modelo final
            BeneficiaryPlan::class, // modelo intermediário
            'id',                   // chave do BeneficiaryPlan
            'id',                   // chave do Plan
            'beneficiary_plan_id',  // FK de Invoice → BeneficiaryPlan
            'plan_id'               // FK de BeneficiaryPlan → Plan
        );
    }


    // gerar uuid automatico
    protected static function boot()
    {
        parent::boot();

        // Gerar UUID automaticamente ao criar
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
