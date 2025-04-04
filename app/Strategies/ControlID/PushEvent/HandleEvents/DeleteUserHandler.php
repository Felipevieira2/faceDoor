<?php

namespace App\Strategies\ControlID\PushEvent\HandleEvents;

use App\Models\AutorizacaoDispositivo;
use App\Models\Job;
use App\Models\ControlIdJob;

use Illuminate\Support\Facades\Log;
use App\Strategies\ControlID\PushEvent\Interfaces\ControlIdJobsHandlerStrategyInterface;

class DeleteUserHandler implements ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipula jobs relacionados a exclusão de usuários
     *
     * @param ControlIdJob $job
     * @return array
     */
    public function handle(ControlIdJob $job): array
    {
        Log::info("PUSH - handle delete_user: {$job->id}");
        
        try {
            // Obter a autorização existente para o usuário no dispositivo          
            $autorizacao = $job->user_able->autorizacoeDispositivoByDispositivo($job->dispositivo);
            
            if (!$autorizacao || !$autorizacao->user_id_externo) {
                Log::warning("Não foi possível encontrar autorização com user_id_externo para: Usuário {$job->user_able->id}, Dispositivo {$job->dispositivo->identificador_unico}");
             
                $job->status = 3;
                $job->log = "Não foi encontrada autorização para este usuário no dispositivo";
                $job->save();
                
                return [];
            }
            
            Log::info("Removendo usuário externo com ID: {$autorizacao->user_id_externo}");
            
            // Configurar a requisição para o ControlID para remoção do usuário
            $response = [
                'verb' => 'POST',
                'endpoint' => 'destroy_objects',
                'body' => [
                    "object" => "users",
                    "where" => [
                        [
                            "object" => "users",
                            "field" => "id",
                            "operator" => "=",
                            "value" => $autorizacao->user_id_externo
                        ]
                    ]
                ]
            ];
            
            // Atualizar o status do job para processando
            $job->status = 2; // Processando
            $job->save();
            
            return $response;
        } catch (\Exception $e) {
            Log::error("Erro ao processar job de exclusão de usuário: {$e->getMessage()}");
            
            $job->status = 3; // Erro
            $job->log = $e->getMessage();
            $job->save();
            
            return [];
        }
    }
} 