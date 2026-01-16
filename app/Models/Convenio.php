<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Convenio. (Serviços)
 *
 * @package namespace App\Models;
 */
class Convenio extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome_convenio',
        'convenio_categoria_id',
        'partner_id',
        'descricao',
        'desconto_percentual',
        'data_inicio',
        'data_fim',
        'status',
        'email',
        'contato',
        'convenio_type_id'
    ];

    public function categoria () {
        return $this->belongsTo(ConveniosCategoria::class, 'convenio_categoria_id');
    }

    public function type () {
        return $this->belongsTo(ConvenioType::class, 'convenio_type_id');
    }

    public function partner(){
        return $this->belongsTo(Partner::class, 'partner_id');
    }

}
