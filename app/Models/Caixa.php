<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Caixa.
 *
 * @package namespace App\Models;
 */
class Caixa extends Model implements Transformable
{
    use TransformableTrait;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'descricao',
        'obs',
        'cadastro',
        'exclusao',
        'atualizacao',
    ];

}
