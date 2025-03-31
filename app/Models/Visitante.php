<?php

namespace App\Models;

use App\Models\ControlIdJob;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Visitante extends Model
{
    use HasFactory;
    use BelongsToTenant;
    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'visitantes';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'apartamento_id',
        'morador_responsavel_id',
        'data_hora_entrada',
        'data_hora_saida',
        'data_validade_inicio',
        'data_validade_fim',
        'recorrente',
        'dias_semana',
        'horario_inicio',
        'horario_fim',
        'ativo',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'data_hora_entrada' => 'datetime',
        'data_hora_saida' => 'datetime',
        'data_validade_inicio' => 'date',
        'data_validade_fim' => 'date',
        'recorrente' => 'boolean',
        'horario_inicio' => 'datetime',
        'horario_fim' => 'datetime',
        'ativo' => 'boolean',
    ];

    public function autorizacoesDispositivos()
    {
        return $this->morphMany(AutorizacaoDispositivo::class, 'authorizable');
    }
    
    /**
     * Obtém o usuário associado ao visitante.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtém o apartamento que o visitante está visitando.
     */
    public function apartamento(): BelongsTo
    {
        return $this->belongsTo(Apartamento::class);
    }

    /**
     * Obtém o morador responsável pelo visitante.
     */
    public function moradorResponsavel(): BelongsTo
    {
        return $this->belongsTo(Morador::class, 'morador_responsavel_id');
    }

    /**
     * Verifica se o visitante tem permissão para entrar no momento atual.
     */
    public function podeEntrar(): bool
    {
        // Verifica se o visitante está ativo
        if (!$this->ativo) {
            return false;
        }

        $agora = now();
        $dataAtual = $agora->format('Y-m-d');
        $diaSemanaAtual = $agora->dayOfWeek; // 0 (domingo) a 6 (sábado)

        // Verifica se está dentro do período de validade
        if ($dataAtual < $this->data_validade_inicio) {
            return false;
        }

        if ($this->data_validade_fim && $dataAtual > $this->data_validade_fim) {
            return false;
        }

        // Se não for recorrente, apenas verifica a data
        if (!$this->recorrente) {
            return true;
        }

        // Verifica se o dia da semana atual está permitido
        $diasPermitidos = explode(',', $this->dias_semana);
        if (!in_array((string) $diaSemanaAtual, $diasPermitidos)) {
            return false;
        }

        // Verifica se está dentro do horário permitido
        if ($this->horario_inicio && $this->horario_fim) {
            $horaAtual = $agora->format('H:i:s');
            $horaInicio = date('H:i:s', strtotime($this->horario_inicio));
            $horaFim = date('H:i:s', strtotime($this->horario_fim));

            if ($horaAtual < $horaInicio || $horaAtual > $horaFim) {
                return false;
            }
        }

        return true;
    }

    public function controlid_jobs(): MorphMany
    {
        return $this->morphMany(ControlIdJob::class, 'user_able');
    }
}
