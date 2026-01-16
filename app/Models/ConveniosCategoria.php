<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ConveniosCategoria.
 *
 * @package namespace App\Models;
 */
class ConveniosCategoria extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'descricao',
        'usuario_atualizacao_id',
        'usuario_exclusao_id',
        'usuario_cadastro_id',
        'cadastro',
        'exclusao',
        'atualizacao'
    ];

    // public function convenios (){
    //     return $this->hasMany(Convenio::class, 'categoria_id');
    // }
}
