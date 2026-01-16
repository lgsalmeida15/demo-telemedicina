<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ContaReceber.
 *
 * @package namespace App\Models;
 */
class ContaReceber extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;


    protected $fillable = [
        'documento',
        'plano_contas_id',
        'valor',
        'valor_pago',
        'valor_desconto',
        'juros',
        'multa',
        'tipo_juros',
        'valor_pago_juros_multa',
        'tipo_baixa',
        'status_pagamento',
        'status_autorizacao',
        'emissao',
        'vencimento',
        'pagamento',
        'caixa_id',
        'usuario_cadastro_id',
        'usuario_atualizacao_id',
        'usuario_exclusao_id',
        'usuario_baixa_id',
        'fonte_receita_id',
        'autorizacao_centro_custo_id',
        'forma_pagamento_id',
        'cadastro',
        'atualizacao',
        'exclusao',
        'obs',
    ];

    public function costCenters()
    {
        return $this->belongsTo(CostCenter::class, 'cost_centers_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
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

    public function usuarioExclusao()
    {
        return $this->belongsTo(User::class, 'usuario_exclusao_id');
    }

    public function usuarioBaixa()
    {
        return $this->belongsTo(User::class, 'usuario_baixa_id');
    }

}
