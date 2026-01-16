<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class CostCenter. //Plano de Contas!
 *
 * @package namespace App\Models;
 */
class CostCenter extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'usuario_id',
        'cadastro',
        'exclusao',
        'atualizacao',
        'codigo_conta',
        'codigo_reduzido',
        'descricao',
        'tipo'
    ];

    public function financials()
    {
        return $this->hasMany(Financial::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

}
