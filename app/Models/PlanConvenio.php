<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class CompanyPlan.
 *
 * @package namespace App\Models;
 */
class PlanConvenio extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_id',
        'convenio_id',
    ];

    // Join com plano
    public function plan(){
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    
    public function convenio(){
        return $this->belongsTo(Convenio::class, 'convenio_id');
    }

}
