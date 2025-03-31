<?php

namespace App\Strategies\ControlID\PushEvent\HandleEvents;

use App\Models\Job;
use App\Models\ControlIdJob;

use Illuminate\Support\Facades\Log;
use App\Models\AutorizacaoDispositivo;
use App\Strategies\ControlID\PushEvent\Interfaces\ControlIdJobsHandlerStrategyInterface;

class AddGroupUserHandle implements ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipula jobs relacionados a controle de acesso
     *
     * @param ControlIdJob $job
     * @return array
     */
    public function handle(ControlIdJob $job): array
    {
        Log::info("PUSH - handle add_group_user");
        // como obter o nome do usuário pelo vinculo morph da tabel job.morador.user-name

        // Lógica específica para manipulação de acesso
        try {
            $user_id_externo = $job->user_able->autorizacoeDispositivoByDispositivo($job->dispositivo)->user_id_externo;
            //criar ou atualizar o grupo
         
            $response =   [
                'verb' => 'POST',
                'endpoint' => 'create_objects',
                'body' => [
                    "object" => "user_groups",
                    "fields" => ["user_id", "group_id"],
                    "values" => [
                        [
                            "user_id" => (int) $user_id_externo,
                            "group_id" => 1,
                        ]
                    ]
                ]
            ];

            return $response;
        } catch (\Exception $e) {
            Log::error("Erro ao processar jobId: {$job->id} message: {$e->getMessage()}  add_group_user linha: 52");

            $job->status = 3;
            $job->log = $e->getMessage();
            $job->save();

            return [];
        }
    }
}
