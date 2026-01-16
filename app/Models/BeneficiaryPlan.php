<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class BeneficiaryPlan.
 *
 * @package namespace App\Models;
 */
class BeneficiaryPlan extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'beneficiary_id',
        'plan_id',
        'start_date',
        'end_date',
        'transaction_code'
    ];


    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class, 'beneficiary_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }


    public function scopeActiveAt($query, Carbon $date)
    {
        return $query
            ->whereDate('start_date', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', $date);
            });
    }

     /**
     * 🔓 Plano ativo (ainda válido)
     */
    public function isActive(): bool
    {
        return is_null($this->end_date)
            || Carbon::parse($this->end_date)->isFuture()
            || Carbon::parse($this->end_date)->isToday();
    }

    /**
     * ⚠️ Plano cancelado, mas ainda válido
     */
    public function isCanceledWaitingEnd(): bool
    {
        return !is_null($this->end_date)
            && Carbon::parse($this->end_date)->isFuture();
    }

    /**
     * ❌ Plano expirado
     */
    public function isExpired(): bool
    {
        return !is_null($this->end_date)
            && Carbon::parse($this->end_date)->isPast();
    }

}
