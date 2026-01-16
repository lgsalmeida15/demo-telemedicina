<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Partner extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'partners'; // define o nome da tabela (boa prática)

    protected $fillable = [
        'name',
        'cnpj',
        'description',
        'cost_center_id',
        'email',
        'phone',
        'deleted_at'
    ];

    /**
     * Relação com plano de contas (cost_centers).
     */
    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id', 'id');
    }
}
