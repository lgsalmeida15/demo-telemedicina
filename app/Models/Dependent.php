<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class Dependent.
 *
 * @package namespace App\Models;
 */
class Dependent extends Authenticatable implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'docway_dependent_id',
        'beneficiary_id',
        'name',
        'birth_date',
        'gender',
        'cpf',
        'email',
        'password',
        'phone',
        'relationship',
        'deleted_at'
    ];


    public function beneficiary() {
        return $this->belongsTo(Beneficiary::class);
    }

    /**
     * 🔒 Um dependente é inadimplente se o titular estiver inadimplente
     */
    public function isInadimplente(): bool
    {
        // Segurança: se não houver beneficiário, bloqueia
        if (!$this->beneficiary) {
            return true;
        }
        return $this->beneficiary->isInadimplente();
    }

    /**
     *  se já tiver 4 dependentes, cancela o cadastro e retorna um erro
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($dependent) {

            // conta quantos dependentes esse beneficiário já possui
            $count = Dependent::where('beneficiary_id', $dependent->beneficiary_id)
                            ->whereNull('deleted_at') // conta so os que nao tiverem delet logico
                            ->count();

            if ($count >= 3) {
                throw new \Exception("Este beneficiário já possui o limite de 3 dependentes.");
            }
        });
    }
}
