<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
// use App\Traits\BelongsToTenant;
class Apartamento extends Model
{
    use HasFactory;
    // use BelongsToTenant;
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'apartamentos';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'numero',
        'torre_id',
        'andar',
        'bloco',
        'ocupado',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'ocupado' => 'boolean',
    ];

    /**
     * Obtém a torre à qual o apartamento pertence.
     */
    public function torre(): BelongsTo
    {
        return $this->belongsTo(Torre::class);
    }

    /**
     * Obtém os moradores associados ao apartamento.
     */
    public function moradores(): HasMany
    {
        return $this->hasMany(Morador::class);
    }

    /**
     * Obtém os visitantes associados ao apartamento.
     */
    public function visitantes(): HasMany
    {
        return $this->hasMany(Visitante::class);
    }

    /**
     * Obtém os moradores responsáveis pelo apartamento.
     */
    public function moradoresResponsaveis(): BelongsToMany
    {
        return $this->belongsToMany(Morador::class, 'morador_responsavel')
                    ->withPivot('data_inicio', 'data_fim', 'ativo')
                    ->withTimestamps();
    }
}
