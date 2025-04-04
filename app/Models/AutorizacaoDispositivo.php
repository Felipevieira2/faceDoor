<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutorizacaoDispositivo extends Model
{
    protected $fillable = [
        'identificador_dispositivo', 
        'authorizable_id', 
        'authorizable_type',
        'user_id_externo',
        'status',
        'group_id_externo',
    
    ];

    // Relação polimórfica
    public function authorizable()
    {
        return $this->morphTo();
    }
}