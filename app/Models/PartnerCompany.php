<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class PartnerCompany.
 *
 * @package namespace App\Models;
 */
class PartnerCompany extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'partner_id',
        'company_id'
    ];

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function partner(){
        return $this->belongsTo(Partner::class, 'partner_id');
    }

}
