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
     * Manipula jobs relacionados a controle de acesso
     *
     * @param ControlIdJob $job
     * @return array
     */
    public function handle(ControlIdJob $job): array
    {
        Log::info("PUSH - handle create_user_visitante: {$job->id}");
        // como obter o nome do usuário pelo vinculo morph da tabel job.morador.user-name
               
        // Lógica específica para manipulação de acesso
        try {
            Log::info('data_inicio_visita create_user_visitantehandler');
            $data_inicio = Carbon::parse($job->user_able->data_inicio_visita)->timestamp;
            $data_fim = Carbon::parse($job->user_able->data_fim_visita)->timestamp;
            Log::info('data_fim_visita create_user_visitantehandler');
            
            $response =    [
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
  
            return $response;
        } catch (\Exception $e) {
            Log::error("Erro ao processar job de acesso: {$e->getMessage()}");
            
            
            $job->log = $e->getMessage();
            $job->save();
            
            return [];
        }
    }
} 