<?php

namespace App\Strategies\ControlID\ResultEvent\HandleEvents;

use App\Models\Job;
use App\Models\ControlIdJob;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Strategies\ControlID\ResultEvent\Interfaces\ControlIdJobsHandlerStrategyInterface;

class DeleteUserHandler implements ControlIdJobsHandlerStrategyInterface
{
    /**
     * Manipula jobs relacionados a exclusão de usuários
     *
     * @param ControlIdJob $job
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(ControlIdJob $job, Request $request): JsonResponse
    {
        Log::info("RESULT - handle delete_user: {$job->id}");
        Log::info($request->all());
        
        try {            
            $autorizacao = $job->user_able->autorizacoeDispositivoByDispositivo($job->dispositivo);
            
            $response = json_decode($request->response, true);
            //{"changes":0}
            if($response['changes'] == 0){
                
                $autorizacao->status = 'bloqueado';
                $autorizacao->messagem_erro = 'Usuário não encontrado para bloquear no ControlID';
                $autorizacao->save();
            }

            if ($autorizacao) {
                // Atualizar o status da autorização para 'revogado'
                $autorizacao->status = 'bloqueado';
                $autorizacao->messagem_erro = '';
                $autorizacao->save();
                
                Log::info("Autorização revogada com sucesso para o usuário: {$job->user_able->id} no dispositivo: {$job->dispositivo->identificador_unico}");
            } else {
                Log::warning("Nenhuma autorização encontrada para revogar para o usuário: {$job->user_able->id} no dispositivo: {$job->dispositivo->identificador_unico}");
            }
            
            // Marcar o job como concluído
            $job->status = 1;
            $job->response = $request->response;
            $job->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Usuário removido com sucesso',
                'jobId' => $job->id
            ], 200);
            
        } catch (\Exception $e) {
            Log::error("Erro ao processar job de exclusão de usuário: {$e->getMessage()}");
            
            $job->status = 3;
            $job->log = $e->getMessage();
            $job->save();
            
            return response()->json([
                'success' => false,
                'message' => 'Falha ao processar exclusão de usuário: ' . $e->getMessage(),
                'jobId' => $job->id
            ], 500);
        }
    }
} 