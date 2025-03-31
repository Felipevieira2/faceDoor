<?php

namespace App\Strategies\ControlID\PushEvent\HandleEvents;

use App\Models\Job;
use App\Models\ControlIdJob;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Strategies\ControlID\PushEvent\Interfaces\ControlIdJobsHandlerStrategyInterface;

class UploadImageUserHandler implements ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipula jobs relacionados a controle de acesso
     *
     * @param ControlIdJob $job
     * @return array
     */
    public function handle(ControlIdJob $job): array
    {
        Log::info("PUSH - handle upload_image_user: {$job->id}");
        // como obter o nome do usuário pelo vinculo morph da tabel job.morador.user-name

        // Lógica específica para manipulação de acesso
        try {
          
            $user_id_externo = $job->user_able->autorizacoeDispositivoByDispositivo($job->dispositivo)->user_id_externo;                      
            $base64Content = base64_encode(file_get_contents(Storage::path('public/'.$job->user_able->user->foto)));   
            $response = [
                'verb' => 'POST',
                'endpoint' => 'user_set_image',
                'body' => $base64Content,
                'queryString' => http_build_query([
                    'user_id' => $user_id_externo,
                    'match' => 1,
                    'timestamp' => time()
                ]),
                'contentType' => 'application/octet-stream'
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
