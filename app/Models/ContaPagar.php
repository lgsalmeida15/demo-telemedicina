<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ContaPagar.
 *
 * @package namespace App\Models;
 */
class ContaPagar extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;

    protected $fillable = [
        'cost_center_id',
        'centro_custo',
        'status_autorizacao',
        'partner_id',
        'usuario_exclusao_id',
        'usuario_baixa_id',
        'usuario_cadastro_id',
        'usuario_atualizacao_id',
        'cadastro',
        'exclusao',
        'atualizacao',
        'emissao',
        'vencimento',
        'baixa',
        'pagamento',
        'documento',
        'valor',
        'valor_pago',
        'valor_desconto',
        'juros',
        'multa',
        'obs',
        'tipo_juros',
        'valor_pago_juros_multa',
        'tipo_baixa',
        'caixa_id',
    ];


    public function planoConta()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id', 'id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id', 'id');
    }

    public function caixa()
    {
        return $this->belongsTo(Caixa::class, 'caixa_id');
    }

    public function usuarioCadastro()
    {
        return $this->belongsTo(User::class, 'usuario_cadastro_id');
    }

    public function usuarioAtualizacao()
    {
        return $this->belongsTo(User::class, 'usuario_atualizacao_id');
    }

    public function usuarioBaixa()
    {
        return $this->belongsTo(User::class, 'usuario_baixa_id');
    }

    public function usuarioExclusao()
    {
        return $this->belongsTo(User::class, 'usuario_exclusao_id');
    }


}
