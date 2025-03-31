<?php

namespace App\Strategies\ControlID\PushEvent\HandleEvents;

use App\Models\ControlIdJob;
use Illuminate\Support\Facades\Log;
use App\Strategies\ControlID\PushEvent\Interfaces\ControlIdJobsHandlerStrategyInterface;

class DefaultHandler implements ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipulador padrão para tipos de jobs não especificados
     *
     * @param ControlIdJob $job
     * @return array
     */
    public function handle(ControlIdJob $job): array
    {
        Log::warning("PUSH - Utilizando manipulador padrão para job tipo: {$job->method}");
        
        // Implementação genérica para jobs sem manipulador específico
        try {
            // Lógica para lidar com jobs de tipo desconhecido ou genéricos
            
            // Atualiza o status do job
            $job->status = 3;
            $job->log = "Job processado pelo manipulador padrão";
            $job->save();
            
            return [
                'success' => true,
                'message' => 'Job processado pelo manipulador padrão',
                'jobId' => $job->id
            ];
        } catch (\Exception $e) {
            Log::error("Erro ao processar job genérico: {$e->getMessage()}");
            
            $job->status = 3;
            $job->log = $e->getMessage();
            $job->save();
            
            return [ ];
        }
    }
} 