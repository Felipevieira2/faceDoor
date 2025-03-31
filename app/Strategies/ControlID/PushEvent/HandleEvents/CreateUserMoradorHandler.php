<?php

namespace App\Strategies\ControlID\PushEvent\HandleEvents;

use App\Models\Job;
use App\Models\ControlIdJob;

use Illuminate\Support\Facades\Log;
use App\Strategies\ControlID\PushEvent\Interfaces\ControlIdJobsHandlerStrategyInterface;

class CreateUserMoradorHandler implements ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipula jobs relacionados a controle de acesso
     *
     * @param ControlIdJob $job
     * @return array
     */
    public function handle(ControlIdJob $job): array
    {
        Log::info("PUSH - handle create_user_morador: {$job->id}");
        // como obter o nome do usuário pelo vinculo morph da tabel job.morador.user-name
               
        
        // Lógica específica para manipulação de acesso
        try {
            
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
                            "salt" => ""
                        ]
                    ]
                ]
            ]; 
                                  
            return $response;
        } catch (\Exception $e) {
            Log::error("Erro ao processar jobId: {$job->id} message: {$e->getMessage()} ");
            
            $job->status = 3;
            $job->log = $e->getMessage();
            $job->save();
                        
            return [];
        }
    }
} 