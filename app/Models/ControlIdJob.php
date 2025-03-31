<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlIdJob extends Model
{
    protected $table = 'controlid_jobs';
    protected $connection = 'mysql';
    protected $primaryKey = 'id';

    use HasFactory;
    protected $fillable = [
        'endpoint',
        'status',
        'uuid',
        'response',
        'log',
        'identificador_dispositivo',
        'user_able_id',
        'user_able_type',
        'tentativas'
    ];
    
    public function user_able()
    {
        return $this->morphTo();
    }

    public function dispositivo()
    {
        return $this->belongsTo(Dispositivo::class, 'identificador_dispositivo', 'identificador_unico');
    }
}
