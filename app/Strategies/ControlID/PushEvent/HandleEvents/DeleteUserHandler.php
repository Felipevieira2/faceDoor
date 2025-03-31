<?php

namespace App\Strategies\ControlID\PushEvent\HandleEvents;

use App\Models\Job;
use App\Models\ControlIdJob;

use Illuminate\Support\Facades\Log;
use App\Strategies\ControlID\PushEvent\Interfaces\ControlIdJobsHandlerStrategyInterface;

class DeleteUserHandler implements ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipula jobs relacionados a controle de acesso
     *
     * @param ControlIdJob $job
     * @return array
     */
    public function handle(ControlIdJob $job): array
    {
        Log::info("PUSH - handle delete_user: {$job->id}");
        // como obter o nome do usuário pelo vinculo morph da tabel job.morador.user-name        
        $autorizacao = $job->user_able->autorizacoeDispositivoByDispositivo($job->dispositivo);
        
        // Lógica específica para manipulação de acesso
        try {
                        
            $response =    [
                'verb' => 'POST',
                'endpoint' => 'destroy_objects',
                'body' => [
                    "object" => "users",
                    "where" => [
                        [
                            "object" => "users",
                            "field" => "id",
                            "operator" => "=" ,
                            "value" => $autorizacao->user_id_externo
                        ]
                    ]
                ]

            ];
            
           
            
            return $response;
        } catch (\Exception $e) {
            Log::error("Erro ao processar job de acesso: {$e->getMessage()}");
            
            $job->status = 3;
            $job->log = $e->getMessage();
            $job->save();
            
            return [];
        }
    }
} 