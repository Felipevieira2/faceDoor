<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BelongsToTenant;

class Dispositivo extends Model
{
    use HasFactory;
    use BelongsToTenant;
    
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'dispositivos';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'identificador_unico',
        'condominio_id',
        'torre_id',
        'localizacao',
        'ativo',
        'fabricante',
        'username',
        'password',
        'ip',
    ];

    protected $appends = ['localizacao_string'];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Obtém o condomínio ao qual o dispositivo pertence.
     */
    public function condominio(): BelongsTo
    {
        return $this->belongsTo(Condominio::class);
    }

    /**
     * Obtém a torre à qual o dispositivo pertence (se aplicável).
     */
    public function torre(): BelongsTo
    {
        return $this->belongsTo(Torre::class);
    }


    public function getLocalizacaoStringAttribute()
    {
        $localizacoes = [
            'entrada_condominio' => 'Entrada do Condomínio',
            'saida_condominio' => 'Saída do Condomínio',
            'torre' => 'Torre',
        ];

        return $localizacoes[$this->localizacao] ?? $this->localizacao;
    }
}
