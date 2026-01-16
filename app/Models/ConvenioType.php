<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ConvenioType.
 *
 * @package namespace App\Models;
 */
class ConvenioType extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];
    

    public function convenios () {
        return $this->hasMany(Convenio::class, 'convenio_type_id');
    }

}
