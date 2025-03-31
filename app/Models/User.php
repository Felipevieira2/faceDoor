<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\ControlIdJob;
use App\Traits\BelongsToTenant;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    use BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo_usuario',
        'telefone',
        'cpf',
        'foto',
        'data_nascimento',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];



    /**
     * Obtém o morador associado ao usuário (se aplicável).
     */
    public function morador(): HasOne
    {
        return $this->hasOne(Morador::class);
    }

    /**
     * Obtém o visitante associado ao usuário (se aplicável).
     */
    public function visitante(): HasOne
    {
        return $this->hasOne(Visitante::class);
    }

   
}
