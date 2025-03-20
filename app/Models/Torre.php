<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Traits\BelongsToTenant;

class Torre extends Model
{
    use HasFactory;
    use BelongsToTenant;
    
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'torres';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'condominio_id',
        'descricao',
        'numero_andares',
    ];


    /**
     * Obtém o condomínio ao qual a torre pertence.
     */
    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    /**
     * Obtém os apartamentos associados à torre.
     */
    public function apartamentos(): HasMany
    {
        return $this->hasMany(Apartamento::class);
    }

    /**
     * Obtém o dispositivo associado à torre (0 ou 1).
     */
    public function dispositivo(): HasOne
    {
        return $this->hasOne(Dispositivo::class);
    }
}
