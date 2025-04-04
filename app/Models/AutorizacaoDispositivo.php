<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutorizacaoDispositivo extends Model
{
    protected $fillable = [
        'identificador_dispositivo', 
        'localizacao',
        'tipo',
        'fabricante',
        'authorizable_id', 
        'authorizable_type',
        'user_id_externo',
        'dispositivo_id',
        'status',
        'group_id_externo',
    ];

    // Relação polimórfica
    public function authorizable()
    {
        return $this->morphTo();
    }
}