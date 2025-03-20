<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\BelongsToTenant;

class Morador extends Model
{
    use HasFactory;
    use BelongsToTenant;
    
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'moradores';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'apartamento_id',
        'data_inicio',
        'data_fim',
        'ativo',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'ativo' => 'boolean',
    ];

    /**
     * Obtém o usuário associado ao morador.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class, 'tenant_id');
    }

    public function torre(): BelongsTo
    {
        return $this->belongsTo(Torre::class, 'torre_id');
    }

    /**
     * Obtém o apartamento ao qual o morador pertence.
     */
    public function apartamento(): BelongsTo
    {
        return $this->belongsTo(Apartamento::class);
    }

    /**
     * Obtém os apartamentos pelos quais o morador é responsável.
     */
    public function apartamentosResponsavel(): BelongsToMany
    {
        return $this->belongsToMany(Apartamento::class, 'morador_responsavel')
                    ->withPivot('data_inicio', 'data_fim', 'ativo')
                    ->withTimestamps();
    }

    /**
     * Obtém os visitantes associados a este morador (como responsável).
     */
    public function visitantes(): HasMany
    {
        return $this->hasMany(Visitante::class, 'morador_responsavel_id');
    }

    /**
     * Verifica se o morador é responsável pelo apartamento especificado.
     */
    public function isResponsavelPor(Apartamento $apartamento): bool
    {
        return $this->apartamentosResponsavel()
                    ->where('apartamento_id', $apartamento->id)
                    ->where('ativo', true)
                    ->where(function ($query) {
                        $query->whereNull('data_fim')
                              ->orWhere('data_fim', '>=', now()->format('Y-m-d'));
                    })
                    ->exists();
    }
}
