<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Financial.
 *
 * @package namespace App\Models;
 */
class Financial extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'data_hora_evento',
        'tipo',
        'descricao',
        'valor',
        'cost_center_id',
        'caixa_id',
        'user_id',
    ];

    protected $casts = [
        'data_hora_evento' => 'datetime',
        'valor'            => 'decimal:2',
    ];

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function caixa()
    {
        return $this->belongsTo(Caixa::class);
    }

}
