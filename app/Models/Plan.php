<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Support\Str;
/**
 * Class Plan.
 *
 * @package namespace App\Models;
 */
class Plan extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'is_telemedicine',
        'company_id',
        'name',
        'value',
        'description',
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

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Relacionamento com os convênios (serviços) do plano.
     */
    public function conveniences(){
        return $this->hasMany(PlanConvenio::class, 'plan_id');
    }

    /**
     * Função auxiliar para buscar o nome do convênio (serviço) pelo nome do seu Tipo.
     * * @param string $convenioTypeName O nome do tipo de convênio a ser procurado (Ex: 'ClickLife').
     * @return string|null Retorna o nome do serviço ou null se não encontrar.
     */
    private function getConvenioNameByType(string $convenioTypeName): ?string
    {
        // Usa o método 'first' da Collection para encontrar o primeiro PlanConvenio que satisfaz a condição.
        $planConvenioFound = $this->conveniences->first(function ($planConvenio) use ($convenioTypeName) {
            
            // USO DO OPERADOR NULLSAFE (?->)
            // Acessa: PlanConvenio -> Convênio -> Tipo -> Nome
            // Isso previne o erro "Call to a member function type() on null" se 'convenio' ou 'type' forem nulos.
            $typeName = $planConvenio->convenio?->type?->name;

            if (!$typeName) {
                // Se o nome do tipo for nulo (Convenio ou Type faltando), ignora este item.
                return false; 
            }
            
            // Verifica se o NOME DO TIPO do convênio é igual ao nome procurado (case-insensitive).
            return strcasecmp($typeName, $convenioTypeName) === 0;
        });

        // Se encontrou um PlanConvenio, retorna o nome do serviço (Convênio) associado.
        // Assumimos que o nome do serviço está no campo 'name' do Model Convenio.
        return $planConvenioFound ? $planConvenioFound->convenio->nome_convenio : null;
    }

    // --- Accessors (Funções que você usa no Blade) ---

    public function getHasSeguroAttribute(): ?string
    {
        return $this->getConvenioNameByType('Seguro');
    }

    public function getHasClicklifeAttribute(): ?string
    {
        return $this->getConvenioNameByType('ClickLife');
    }

    public function getHasTemSaudeAttribute(): ?string
    {
        // Se o nome do tipo é 'Saúde', usamos este.
        return $this->getConvenioNameByType('Saúde'); 
    }

    public function getHasOdontoAttribute(): ?string
    {
        return $this->getConvenioNameByType('Odonto');
    }

    public function getHasTelepsicologiaAttribute(): ?string
    {
        return $this->getConvenioNameByType('Telepsicologia');
    }

    public function getHasAppAttribute(): ?string
    {
        return $this->getConvenioNameByType('App');
    }
}
