<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Support\Str;

class Company extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'cnpj',
        'email',
        'phone',
        'uf',
        'billing_date',
        'due_day',
        'uuid',
    ];

    protected static function boot()
    {
        parent::boot();

        // Gerar UUID automaticamente ao criar
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // se for necessário usar UUID como chave da rota (opcional)
    // public function getRouteKeyName()
    // {
    //     return 'uuid';
    // }

    public function indications()
    {
        return $this->hasMany(PartnerCompany::class, 'company_id');
    }

    public function plans()
    {
        return $this->hasMany(Plan::class, 'company_id');
    }

    public function beneficiaries()
    {
        return $this->hasMany(Beneficiary::class, 'company_id');
    }
}
