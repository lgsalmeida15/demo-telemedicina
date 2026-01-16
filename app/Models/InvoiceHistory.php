<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Support\Str;

/**
 * Class InvoiceHistory.
 *
 * @package namespace App\Models;
 */
class InvoiceHistory extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'invoice_id',
        'transaction_code',
        'status_transaction',
        'return_code',
        'return_message'
    ];

    // gerar uuid automatico
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

}
