<?php

namespace App\Strategies\ControlID\ResultEvent\HandleEvents;

use Carbon\Carbon;
use App\Models\Job;

use App\Models\ControlIdJob;
use Illuminate\Support\Facades\Log;
use App\Strategies\ControlID\ResultEvent\Interfaces\ControlIdJobsHandlerStrategyInterface;

class CreateUserVisitanteHandler implements ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipula jobs relacionados a controle de acesso
     *
     * @param ControlIdJob $job
     * @param array $response Resposta do processamento
     * @return array
     */
    public function handle(ControlIdJob $job, array $response = []): array
    {
        Log::info("Processando job de acesso: {$job->id}");
        // como obter o nome do usuário pelo vinculo morph da tabel job.morador.user-name
               
        // Lógica específica para manipulação de acesso
        try {
           
            $data_inicio = Carbon::parse($job->user_able->data_inicio_visita)->timestamp;
            $data_fim = Carbon::parse($job->user_able->data_fim_visita)->timestamp;
            
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
            Log::error("Erro ao processar job de acesso: {$e->getMessage()} create_user_visitante");
            
            
            $job->log = $e->getMessage();
            $job->save();
            
            return [
                'success' => false,
                'message' => 'Falha ao processar acesso: ' . $e->getMessage(),
                'jobId' => $job->id
            ];
        }
    }
} 