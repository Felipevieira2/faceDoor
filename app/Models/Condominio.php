<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Condominio extends Model
{
    use HasFactory;
   
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'condominios';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'endereco',
        'cep',
        'cidade',
        'estado',
        'telefone',
        'email',
    ];

    /**
     * Obtém as torres associadas ao condomínio.
     */
    public function torres(): HasMany
    {
        return $this->hasMany(Torre::class);
    }

    /**
     * Obtém os dispositivos associados ao condomínio.
     */
    public function dispositivos(): HasMany
    {
        return $this->hasMany(Dispositivo::class);
    }
}
