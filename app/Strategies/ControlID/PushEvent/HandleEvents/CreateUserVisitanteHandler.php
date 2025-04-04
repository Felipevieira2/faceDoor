<?php

namespace App\Strategies\ControlID\PushEvent\HandleEvents;

use Carbon\Carbon;
use App\Models\Job;

use App\Models\ControlIdJob;
use Illuminate\Support\Facades\Log;
use App\Strategies\ControlID\PushEvent\Interfaces\ControlIdJobsHandlerStrategyInterface;

class CreateUserVisitanteHandler implements ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipula jobs relacionados ao cadastro de visitantes no ControlID
     *
     * @param ControlIdJob $job
     * @return array
     */
    public function handle(ControlIdJob $job): array
    {
        Log::info("PUSH - handle create_user_visitante: {$job->id}");
        
        try {
            // Obtém as datas de início e fim de validade do visitante
            $data_inicio = Carbon::parse($job->user_able->data_validade_inicio)->timestamp;
            Log::info("Data início para visitante {$job->user_able->id}: " . $job->user_able->data_validade_inicio);
            
            // Data fim pode ser nula (visita por tempo indeterminado)
            $data_fim = null;
            if ($job->user_able->data_validade_fim) {
                
                $data_fim = Carbon::parse($job->user_able->data_validade_fim)->timestamp;

                
                Log::info("Data fim para visitante {$job->user_able->id}: " . $job->user_able->data_validade_fim);
            } else {
                // Se não houver data de fim, define para um ano no futuro como padrão
                $data_fim = Carbon::now()->addYear()->timestamp;
                Log::info("Data fim não definida, usando um ano no futuro: " . Carbon::now()->addYear());
            }
            
            // Cria o payload para envio ao ControlID
            $response = [
                'verb' => 'POST',
                'endpoint' => 'create_objects',
                'body' => [
                    "object" => "users",
                    "values" => [
                        [
                            "name" => $job->user_able->user->name,
                            "registration" => "",
                            "password" => "",
                            "salt" => "",
                            "begin_time" => $data_inicio,
                            "end_time" => $data_fim
                        ]
                    ]
                ]
            ];
            
            // Atualiza o status do job para processando
            $job->status = 2; // Processando
            $job->save();
            
            return $response;
        } catch (\Exception $e) {
            Log::error("Erro ao processar job de cadastro de visitante: {$e->getMessage()}");
            
            $job->status = 3; // Erro
            $job->log = $e->getMessage();
            $job->save();
            
            return [];
        }
    }
} 