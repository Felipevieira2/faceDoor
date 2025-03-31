<?php

namespace App\Strategies\ControlID\ResultEvent\HandleEvents;

use App\Models\Job;
use App\Models\ControlIdJob;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\AutorizacaoDispositivo;
use App\Strategies\ControlID\ControlIdStrategy;
use App\Strategies\ControlID\ResultEvent\Interfaces\ControlIdJobsHandlerStrategyInterface;

class AddGroupUserHandler implements ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipula jobs relacionados à adição de usuários a grupos
     *
     * @param ControlIdJob $job
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(ControlIdJob $job, Request $request): JsonResponse
    {
        Log::info("RESULT - handle add_group_user: {$job->id}");
        Log::info($request->all());

        try {
            $id = json_decode($request->response, true)['ids'][0];
            
            Log::info("id: {$id} add_group_user");
            // Processar a resposta do ControlID

            // Atualizar o status da autorização, se necessário
            $autorizacaoProcessando = $job->user_able->autorizacoeDispositivoByDispositivo($job->dispositivo);

            if ($autorizacaoProcessando) {
                $autorizacaoProcessando->group_id_externo = $id;
                $autorizacaoProcessando->save();
            }

            // Log::info("autorizacao: {$autorizacao} add_group_user");

            //cria um novo job para adicionar a imagem do morador/visitante      
            $strategy = new ControlIdStrategy();
            $strategy->createJobByEndpoint($job->dispositivo, $job->user_able, 'upload_image_user');

            // Marcar o job como concluído
            $job->status = 1;
            $job->response = $request->response;
            $job->save();

            return response()->json([], 200);
        } catch (\Exception $e) {
            Log::error("Erro ao processar jobId: {$job->id} message: {$e->getMessage()} ");

            $job->status = 3;
            $job->log = $e->getMessage();
            $job->save();

            return response()->json([
                'message' => 'Erro ao processar jobId: ' . $job->id . ' message: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }
}
